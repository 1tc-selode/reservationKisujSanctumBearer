<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;

class ReservationController extends Controller
{
    public function index(Request $request){

        $user = $request->user();

        if ($user->is_admin){
            $reservations = Reservation::all();
        } else{
            $reservations = Reservation::where('user_id', $user->id)->get();
        }

        return response()->json($reservations, 200);
    }

    public function show(request $request, $id){
        $user = $request->user();

        $reservation = Reservation::findOrFail($id);

        if (!$user->is_admin && $reservation->user_id != $user->id){
            return response()->json(['message' => 'unauthorized'], 403);
        }

            return response()->json($reservation, 200);

    }

    public function store(Request $request){
        $validated = $request->validate([
            'reservation_time' => 'required|date',
            'guests' => 'required|integer|min:1',
            'note' => 'nullable|string',
        ]);
        $reservation = Reservation::create([
            'user_id' => $request->user()->id,
            'reservation_time' => $validated['reservation_time'],
            'guests' => $validated['guests'],
            'note' => $validated['note'] ?? null,
        ]);
        return response()->json($reservation, 201);
    }

    public function update(request $request, $id){
        $user = $request->user();
        $reservation = Reservation::findOrFail($id);
        
        if (!$user->is_admin && $reservation->user_id != $user->id){
            return response()->json(['message' => 'unauthorized'], 403);
        }

        $validated = $request->validate([
            'reservation_time' => 'sometimes|required|date',
            'guests' => 'sometimes|required|integer|min:1',
            'note' => 'nullable|string',
        ]);

        $reservation -> update($validated);
        return response()->json($reservation, 200);
    }

    public function destroy(request $request, $id){
        $user = $request->user();
        $reservation = Reservation::findOrFail($id);

        if (!$user->is_admin && $reservation->user_id != $user->id){
            return response()->json(['message' => 'unauthorized'], 403);
        }

        $reservation->delete();
        return response()->json(['message'=>'Foglalás törölve.'],200);

    }


}
