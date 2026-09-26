<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use App\Models\Booking;
use App\Models\CancellationRequest;
use App\Models\Downpayment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /** Report tabs, in display order. */
    private const TABS = ['overview', 'bookings', 'performance'];

    public function index(Request $request)
    {
        $tab = (string) $request->query('tab', 'overview');

        if (!in_array($tab, self::TABS, true)) {
            $tab = 'overview';
        }

        [$range, $from, $to] = $this->resolveRange($request);

        $fromDate = $from->format('Y-m-d');
        $toDate = $to->format('Y-m-d');

        $rangeQuery = $range === 'custom'
            ? ['range' => 'custom', 'from' => $fromDate, 'to' => $toDate]
            : ['range' => $range];

        $payload = compact('range', 'from', 'to', 'rangeQuery', 'tab');
        $payload['url'] = route('reports.index', array_merge(['tab' => $tab], $rangeQuery));
        $payload['rangeLabel'] = $from->format('M d, Y') . ' – ' . $to->format('M d, Y');

        if ($tab === 'overview') {
            $payload['summary'] = $this->summary($from, $to, $fromDate, $toDate);
            $payload['trend'] = $this->buildTrend($from, $to);
        } elseif ($tab === 'bookings') {
            $payload['summary'] = $this->summary($from, $to, $fromDate, $toDate);
            $payload['bookings'] = $this->bookings($request, $fromDate, $toDate);
        } else {
            $payload['topPackages'] = $this->topPackages($fromDate, $toDate);
            $payload['topAddons'] = $this->topAddons($fromDate, $toDate);
        }

        if ($request->ajax()) {
            $content = (string) $request->query('content', '');

            if ($content === 'tab') {
                return response()->json([
                    'html' => view('dashboard.partials.report-' . $tab . '-tab', $payload)->render(),
                    'tab' => $tab,
                    'url' => $payload['url'],
                    'rangeLabel' => $payload['rangeLabel'],
                    'range' => $range,
                    'from' => $fromDate,
                    'to' => $toDate,
                ]);
            }

            if ($content === 'rows' && $tab === 'bookings') {
                $rows = $payload['bookings'];

                return response()->json([
                    'rows' => view('dashboard.partials.report-booking-rows', ['bookings' => $rows])->render(),
                    'mobileRows' => view('dashboard.partials.report-booking-mobile-rows', ['bookings' => $rows])->render(),
                    'pagination' => $rows->hasPages() ? $rows->links('vendor.pagination.bootstrap-5')->render() : '',
                    'total' => $rows->total(),
                ]);
            }
        }

        return view('dashboard.reports', $payload);
    }

    /**
     * Booking counts, booked value, revenue, refunds and outstanding balance.
     */
    private function summary(Carbon $from, Carbon $to, string $fromDate, string $toDate): array
    {
        // Bookings are counted by event date within the range.
        $bookingsInRange = Booking::whereBetween('event_date', [$fromDate, $toDate]);
        $countableBookings = (clone $bookingsInRange)->whereNotIn('status', ['cancelled', 'rejected']);

        $summary = [
            'totalBookings' => (clone $bookingsInRange)->count(),
            'completedBookings' => (clone $bookingsInRange)->whereIn('status', ['completed', 'delivered'])->count(),
            'cancelledBookings' => (clone $bookingsInRange)->where('status', 'cancelled')->count(),
            'pendingBookings' => (clone $bookingsInRange)->where('status', 'pending')->count(),
            'activeBookings' => (clone $bookingsInRange)->whereIn('status', ['approved', 'ongoing'])->count(),
            'rejectedBookings' => (clone $bookingsInRange)->where('status', 'rejected')->count(),
            'bookedValue' => (float) (clone $countableBookings)->sum('total_price'),
        ];

        // Revenue is real money in: verified payments bucketed by verification date.
        $verifiedInRange = Downpayment::where('status', 'verified')
            ->whereNotNull('verified_at')
            ->whereBetween('verified_at', [$from, $to]);

        $summary['revenue'] = (float) (clone $verifiedInRange)->sum('amount');
        $summary['downpayments'] = (float) (clone $verifiedInRange)->where('payment_type', 'downpayment')->sum('amount');
        $summary['finalPayments'] = (float) (clone $verifiedInRange)->where('payment_type', 'final')->sum('amount');
        $summary['pendingPayments'] = Downpayment::where('status', 'pending')
            ->whereBetween('submitted_at', [$from, $to])
            ->count();

        // Money collected against the bookings that fall inside this range.
        $collectedForRange = (float) Downpayment::where('status', 'verified')
            ->whereHas('booking', function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('event_date', [$fromDate, $toDate])
                    ->whereNotIn('status', ['cancelled', 'rejected']);
            })
            ->sum('amount');

        $summary['outstandingBalance'] = max(0, $summary['bookedValue'] - $collectedForRange);

        // Refunds are bucketed by the date the refund was processed.
        $refundsInRange = CancellationRequest::where('status', 'refunded')
            ->whereBetween('processed_at', [$from, $to]);

        $summary['refunds'] = (float) (clone $refundsInRange)->sum('refund_amount');
        $summary['refundCount'] = (clone $refundsInRange)->count();

        return $summary;
    }

    private function bookings(Request $request, string $fromDate, string $toDate)
    {
        return Booking::with('package')
            ->select('bookings.*')
            ->selectSub(
                Downpayment::selectRaw('COALESCE(SUM(amount), 0)')
                    ->whereColumn('downpayments.booking_id', 'bookings.id')
                    ->where('status', 'verified'),
                'paid_amount'
            )
            ->whereBetween('event_date', [$fromDate, $toDate])
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim((string) $request->query('q'));

                $query->where(function ($q) use ($term) {
                    $q->where('booking_ref', 'like', "%{$term}%")
                        ->orWhere('client_name', 'like', "%{$term}%")
                        ->orWhere('client_email', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $status = (string) $request->query('status');

                if (in_array($status, ['pending', 'approved', 'ongoing', 'completed', 'delivered', 'cancelled', 'rejected'], true)) {
                    $query->where('status', $status);
                }
            })
            ->orderBy('event_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();
    }

    /**
     * Work out the reporting window from the request.
     *
     * @return array{0: string, 1: Carbon, 2: Carbon}
     */
    private function resolveRange(Request $request): array
    {
        $today = Carbon::today();
        $range = (string) $request->query('range', 'month');

        $presets = [
            'today' => [$today->copy(), $today->copy()],
            'week' => [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()],
            'month' => [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()],
            'quarter' => [$today->copy()->startOfQuarter(), $today->copy()->endOfQuarter()],
            'year' => [$today->copy()->startOfYear(), $today->copy()->endOfYear()],
        ];

        if ($range === 'custom' && $request->filled('from') && $request->filled('to')) {
            try {
                $from = Carbon::parse((string) $request->query('from'))->startOfDay();
                $to = Carbon::parse((string) $request->query('to'))->endOfDay();
            } catch (\Throwable $e) {
                $from = $presets['month'][0]->copy();
                $to = $presets['month'][1]->copy();
            }

            if ($from->gt($to)) {
                [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
            }

            // Keep the window sane so a hand typed range cannot melt the database.
            if ($from->diffInYears($to) > 5) {
                $from = $to->copy()->subYears(5)->startOfDay();
            }

            return ['custom', $from, $to];
        }

        if (!isset($presets[$range])) {
            $range = 'month';
        }

        [$from, $to] = $presets[$range];

        return [$range, $from->copy()->startOfDay(), $to->copy()->endOfDay()];
    }

    /**
     * Bookings and revenue per day (short ranges) or per month (long ranges).
     */
    private function buildTrend(Carbon $from, Carbon $to): array
    {
        $fromDate = $from->format('Y-m-d');
        $toDate = $to->format('Y-m-d');
        $byDay = $from->diffInDays($to) <= 62;
        $bucket = $byDay ? 'day' : 'month';

        $bookingExpr = $byDay ? 'DATE(event_date)' : "DATE_FORMAT(event_date, '%Y-%m')";
        $revenueExpr = $byDay ? 'DATE(verified_at)' : "DATE_FORMAT(verified_at, '%Y-%m')";

        $bookingsPerBucket = Booking::whereBetween('event_date', [$fromDate, $toDate])
            ->selectRaw("{$bookingExpr} as bucket, COUNT(*) as total")
            ->groupBy('bucket')
            ->pluck('total', 'bucket');

        $revenuePerBucket = Downpayment::where('status', 'verified')
            ->whereNotNull('verified_at')
            ->whereBetween('verified_at', [$from, $to])
            ->selectRaw("{$revenueExpr} as bucket, COALESCE(SUM(amount), 0) as total")
            ->groupBy('bucket')
            ->pluck('total', 'bucket');

        $rows = [];
        $cursor = $byDay ? $from->copy()->startOfDay() : $from->copy()->startOfMonth();

        while ($cursor->lte($to)) {
            $key = $byDay ? $cursor->format('Y-m-d') : $cursor->format('Y-m');
            $rows[] = [
                'key' => $key,
                'label' => $byDay ? $cursor->format('M d') : $cursor->format('M Y'),
                'bookings' => (int) ($bookingsPerBucket[$key] ?? 0),
                'revenue' => (float) ($revenuePerBucket[$key] ?? 0),
            ];

            $byDay ? $cursor->addDay() : $cursor->addMonth();
        }

        return [
            'rows' => $rows,
            'maxBookings' => max(1, (int) collect($rows)->max('bookings')),
            'maxRevenue' => max(1, (float) collect($rows)->max('revenue')),
            'granularity' => $bucket,
            'firstLabel' => $rows[0]['label'] ?? '',
            'lastLabel' => $rows ? $rows[count($rows) - 1]['label'] : '',
        ];
    }

    private function topPackages(string $fromDate, string $toDate)
    {
        return Booking::query()
            ->join('packages', 'packages.id', '=', 'bookings.package_id')
            ->whereBetween('bookings.event_date', [$fromDate, $toDate])
            ->whereNotIn('bookings.status', ['cancelled', 'rejected'])
            ->groupBy('packages.id', 'packages.name')
            ->select('packages.id', 'packages.name')
            ->selectRaw('COUNT(*) as bookings_count')
            ->selectRaw('COALESCE(SUM(bookings.total_price), 0) as revenue')
            ->orderByDesc('bookings_count')
            ->limit(8)
            ->get();
    }

    private function topAddons(string $fromDate, string $toDate)
    {
        return Addon::query()
            ->join('booking_addons', 'booking_addons.addon_id', '=', 'addons.id')
            ->join('bookings', 'bookings.id', '=', 'booking_addons.booking_id')
            ->whereBetween('bookings.event_date', [$fromDate, $toDate])
            ->whereNotIn('bookings.status', ['cancelled', 'rejected'])
            ->groupBy('addons.id', 'addons.name')
            ->select('addons.id', 'addons.name')
            ->selectRaw('COUNT(*) as times_booked')
            ->selectRaw('COALESCE(SUM(booking_addons.price), 0) as revenue')
            ->orderByDesc('times_booked')
            ->limit(8)
            ->get();
    }
}
