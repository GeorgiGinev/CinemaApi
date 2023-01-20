<?php

namespace Modules\Bookings\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Bookings\Entities\Booking;
use Modules\Movies\Entities\MovieSlot;

class BookingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function get(Request $request)
    {
        $user = $request->user();

        $bookings = null;

        if($request->input('with_trashed')) {
            $bookings = Booking::where('user_id', $user->id)->onlyTrashed()->with(['cinema', 'movieSlot.movie'])->get();
        } else {
            $bookings = Booking::where('user_id', $request->user()->id)->with(['cinema', 'movieSlot.movie'])->get();
        }

        $bookings = collect($bookings)->transform(function ($booking) {
            $movie = $booking->movieSlot->movie;
            $booking->movie = $movie;

            return $booking->transform(['cinema', 'movieSlot', 'movie']);
        });

        return response()->json([
            'data' => $bookings
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     */
    public function store(Request $request, $cinemaId, $slotId)
    {
        $places = $request->input('attributes')['places'];

        $booking = new Booking();
        $booking->movie_slot_id = (int)$slotId;
        $booking->cinema_id = (int)$cinemaId;
        $booking->user_id = $request->user()->id;
        $booking->places = $places;

        return $booking->push();
    }

    /**
     * Show the specified resource.
     */
    public function show($cinemaId, $slotId)
    {
        $bookings = Booking::where('cinema_id', $cinemaId)->where('movie_slot_id', $slotId)->get();
        $bookings = collect($bookings)->transform(function ($booking) {
            $newBooking = new Booking($booking->toArray());
            return $newBooking->transform();
        });

        return response()->json([
            'data' => $bookings
        ]);
    }

    /**
     * Show all resources
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $bookings = Booking::with([
            'cinema' => function ($q) use ($user) {
                $q->where('owner_id', $user->id);
            },
            'movieSlot.movie', 'user'
        ])->get();

        $bookings = collect($bookings)->transform(function ($booking) {
            $movie = $booking->movieSlot->movie;
            $booking->movie = $movie;

            return $booking->transform(['cinema', 'movieSlot', 'movie', 'user']);
        });

        return response()->json([
            'data' => $bookings
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $booking =  Booking::findOrFail($id);

        if (!$booking) {
            return null;
        }

        return $booking->delete();
    }
}
