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
        $schedules = StaffSchedule::with(['staff', 'booking.package'])
            ->orderBy('event_date')
            ->orderBy('event_time')
            ->get();

        $events = $schedules->map(function ($schedule) {
            $booking = $schedule->booking;

            return [
                'id'           => $schedule->id,
                'staff_id'     => $schedule->staff_id,
                'staff_name'   => $schedule->staff->name ?? '—',
                'booking_id'   => $schedule->booking_id,
                'booking_ref'  => $booking->booking_ref ?? '—',
                'package_name' => $booking->package->name ?? '—',
                'event_type'   => $booking->event_type ?? '—',
                'client_name'  => $booking->client_name ?? '—',
                'client_email' => $booking->client_email ?? '—',
                'client_phone' => $booking->client_phone ?? '—',
                'date'         => $schedule->event_date->format('Y-m-d'),
                'time'         => $schedule->event_time ? date('H:i', strtotime($schedule->event_time)) : '09:00',
                'time_display' => $schedule->event_time ? date('g:i A', strtotime($schedule->event_time)) : '—',
                'venue'        => $booking->event_venue ?? '—',
                'address'      => $booking->event_address ?? '—',
                'status'       => $schedule->status,
            ];
        })->values()->toArray();

        $allStaff = Staff::where('status', 'active')->orderBy('name')->get()
            ->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])
            ->values()
            ->toArray();

        $eventTypes = Booking::whereNotNull('event_type')
            ->pluck('event_type')
            ->map(fn ($t) => ucfirst(trim($t)))
            ->unique()
            ->sort()
            ->values();

        return view('dashboard.schedules', compact('events', 'allStaff', 'eventTypes'));
    }

    public function update(Request $request, StaffSchedule $schedule)
    {
        $validated = $request->validate([
            'event_date' => 'required|date',
            'event_time' => 'required|string',
        ]);

        $schedule->update($validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Schedule updated.']);
        }

        return back()->with('success', 'Schedule updated successfully.');
    }

    public function calendar(Request $request)
    {
        return redirect()->route('staff-schedule.index');
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
