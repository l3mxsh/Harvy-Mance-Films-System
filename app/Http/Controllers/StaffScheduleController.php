<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffSchedule;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StaffScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = StaffSchedule::with(['staff', 'booking.package']);

        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        if ($request->filled('month')) {
            $month = Carbon::parse($request->month);
            $query->whereMonth('event_date', $month->month)
                  ->whereYear('event_date', $month->year);
        } else {
            $query->where('event_date', '>=', now()->startOfMonth())
                  ->where('event_date', '<=', now()->endOfMonth());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $schedules = $query->orderBy('event_date')->orderBy('event_time')->paginate(20)->withQueryString();
        $allStaff = Staff::where('status', 'active')->orderBy('name')->get();

        return view('dashboard.schedules', compact('schedules', 'allStaff'));
    }

    public function calendar(Request $request)
    {
        $month = $request->filled('month') ? Carbon::parse($request->month) : Carbon::now();
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();

        $schedules = StaffSchedule::with(['staff', 'booking.package'])
            ->whereBetween('event_date', [$startOfMonth, $endOfMonth])
            ->orderBy('event_date')
            ->orderBy('event_time')
            ->get();

        $calendarData = [];
        foreach ($schedules as $schedule) {
            $day = $schedule->event_date->format('Y-m-d');
            $calendarData[$day][] = $schedule;
        }

        $allStaff = Staff::where('status', 'active')->orderBy('name')->get();

        return view('dashboard.calendar', compact('calendarData', 'month', 'startOfMonth', 'endOfMonth', 'allStaff'));
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'team_id'    => 'required|exists:teams,id',
            'event_date' => 'required|date',
        ]);

        $team = \App\Models\Team::with('members')->find($request->team_id);
        $eventDate = $request->event_date;

        $unavailableMembers = [];
        foreach ($team->members as $member) {
            $conflict = StaffSchedule::where('staff_id', $member->id)
                ->whereDate('event_date', $eventDate)
                ->whereIn('status', ['assigned', 'confirmed'])
                ->whereHas('booking', fn($q) => $q->whereNotIn('status', ['cancelled', 'rejected']))
                ->with('booking')
                ->first();

            if ($conflict) {
                $unavailableMembers[] = [
                    'id'          => $member->id,
                    'name'        => $member->name,
                    'booking_ref' => $conflict->booking->booking_ref ?? 'N/A',
                ];
            }
        }

        return response()->json([
            'available'           => empty($unavailableMembers),
            'message'             => empty($unavailableMembers)
                ? 'All team members are available on this date.'
                : 'Some team members have scheduling conflicts.',
            'unavailable_members' => $unavailableMembers,
        ]);
    }
}
