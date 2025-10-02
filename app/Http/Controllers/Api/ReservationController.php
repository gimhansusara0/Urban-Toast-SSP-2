<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{
    // List reservations (with optional filter)
    public function index(Request $request)
    {
        $query = Reservation::query();

        if ($request->has('status')) {
            if ($request->status === 'expired') {
                $query->where('date', '<', Carbon::today()->toDateString());
            } else {
                $query->where('status', $request->status);
            }
        }

        return response()->json($query->orderBy('date', 'asc')->get());
    }

    // Store a new reservation
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'mobile' => 'required|string|max:15',
            'date'   => 'required|date',
            'time'   => 'required'
        ]);

        $reservation = Reservation::create([
            'name'   => $request->name,
            'mobile' => $request->mobile,
            'date'   => $request->date,
            'time'   => $request->time,
            'status' => 'pending'
        ]);

        return response()->json($reservation, 201);
    }

    // Update reservation (cancel or edit if pending)
    public function update(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($request->status === 'canceled') {
            $reservation->status = 'canceled';
            $reservation->save();
            return response()->json($reservation);
        }

        if ($reservation->status === 'pending') {
            $reservation->update($request->only(['date', 'time']));
            return response()->json($reservation);
        }

        return response()->json(['error' => 'Cannot update this reservation'], 403);
    }
}
