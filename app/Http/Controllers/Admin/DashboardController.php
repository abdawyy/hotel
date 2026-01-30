<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ChecksPermissions;
use App\Models\Booking;
use App\Models\RoomType;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use ChecksPermissions;
    /**
     * Display the admin dashboard with analytics.
     */
    public function index()
    {
        $this->authorizePermission('dashboard.view');
        
        // Total statistics
        $totalBookings = Booking::count();
        $todayCheckIns = Booking::todayCheckIns()->count();
        $todayCheckOuts = Booking::todayCheckOuts()->count();
        $totalRooms = RoomType::sum('total_rooms');
        $totalAvailableRooms = $totalRooms;
        $totalCustomers = User::whereHas('role', function($q) {
            $q->where('name', 'customer');
        })->count();

        // Revenue statistics
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $monthlyRevenue = Payment::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
        $todayRevenue = Payment::where('status', 'completed')
            ->whereDate('paid_at', today())
            ->sum('amount');

        // Booking status counts
        $pendingBookings = Booking::where('status', 'pending')->count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $checkedInBookings = Booking::where('status', 'checked_in')->count();
        $cancelledBookings = Booking::where('status', 'cancelled')->count();

        // Monthly revenue for last 12 months (for Chart.js)
        $monthlyRevenueData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenue = Payment::where('status', 'completed')
                ->whereYear('paid_at', $month->year)
                ->whereMonth('paid_at', $month->month)
                ->sum('amount');
            
            $monthlyRevenueData[] = [
                'month' => $month->format('M Y'),
                'revenue' => (float) $revenue,
            ];
        }

        // Daily bookings for last 30 days
        $dailyBookingsData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = Booking::whereDate('created_at', $date)->count();
            
            $dailyBookingsData[] = [
                'date' => $date->format('M d'),
                'count' => $count,
            ];
        }

        // Recent bookings
        $recentBookings = Booking::with(['user', 'details.roomType.images', 'details.roomType.primaryImage'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Room type occupancy
        $roomTypeOccupancy = RoomType::withCount(['bookingDetails as bookings_count' => function($query) {
            $query->whereHas('booking', function($q) {
                $q->whereIn('status', ['confirmed', 'checked_in']);
            });
        }])->get();

        $user = auth()->user();
        
        // Check permissions for conditional display
        $canViewBookings = $user->hasPermission('bookings.view');
        $canViewRooms = $user->hasPermission('rooms.view');
        $canViewPayments = $user->hasPermission('payments.view');
        $canViewCustomers = $user->hasPermission('customers.view');

        return view('admin.dashboard', compact(
            'totalBookings',
            'todayCheckIns',
            'todayCheckOuts',
            'totalRooms',
            'totalAvailableRooms',
            'totalCustomers',
            'totalRevenue',
            'monthlyRevenue',
            'todayRevenue',
            'pendingBookings',
            'confirmedBookings',
            'checkedInBookings',
            'cancelledBookings',
            'monthlyRevenueData',
            'dailyBookingsData',
            'recentBookings',
            'roomTypeOccupancy',
            'canViewBookings',
            'canViewRooms',
            'canViewPayments',
            'canViewCustomers'
        ));
    }
}
