<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientDashboardController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['package', 'latestTransaction'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $transactions = Transaction::with('booking.package')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('client.dashboard', compact('bookings', 'transactions'));
    }
}
