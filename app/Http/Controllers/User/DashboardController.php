<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user bookings
        $bookings = Booking::where('user_id', $user->id)
            ->with(['details.roomType', 'details.room', 'payments'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get upcoming bookings
        $upcomingBookings = Booking::where('user_id', $user->id)
            ->where('check_in_date', '>=', now())
            ->whereIn('status', ['confirmed', 'pending'])
            ->with(['details.roomType'])
            ->orderBy('check_in_date', 'asc')
            ->take(5)
            ->get();

        return view('user.dashboard', compact('user', 'bookings', 'upcomingBookings'));
    }

    /**
     * Show user profile.
     */
    public function profile()
    {
        $user = Auth::user()->load('role');
        return view('user.profile', compact('user'));
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $user->update($validated);

        return redirect()->route('user.profile')
            ->with('success', 'Profile updated successfully.');
    }
}
