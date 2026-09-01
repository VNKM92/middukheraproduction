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
        // Only completed / paid bookings appear as confirmed studio sessions
        $bookings = Booking::with(['package', 'latestTransaction'])
            ->where('user_id', Auth::id())
            ->where('payment_status', 'completed')
            ->latest()
            ->get();

        // Pending / unpaid bookings
        $pendingBookings = Booking::with(['package', 'latestTransaction'])
            ->where('user_id', Auth::id())
            ->where('payment_status', '!=', 'completed')
            ->latest()
            ->get();

        $transactions = Transaction::with('booking.package')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('client.dashboard', compact('bookings', 'pendingBookings', 'transactions'));
    }
}
