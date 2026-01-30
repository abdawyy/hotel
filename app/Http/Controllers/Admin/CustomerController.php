<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ChecksPermissions;
use App\Models\User;
use App\Models\Booking;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    use ChecksPermissions;
    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        $this->authorizePermission('customers.create');
        
        return view('admin.customers.create');
    }

    /**
     * Store a newly created customer.
     */
    public function store(Request $request)
    {
        $this->authorizePermission('customers.create');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $customerRole = Role::where('name', 'customer')->firstOrFail();

            $customer = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => $customerRole->id,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);

            DB::commit();

            return redirect()->route('admin.customers.show', $customer->id)
                ->with('success', 'Customer created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the customer.');
        }
    }

    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        $this->authorizePermission('customers.view');
        
        $query = User::whereHas('role', function($q) {
            $q->where('name', 'customer');
        })->with(['role', 'bookings']);

        // Search customers
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit($id)
    {
        $this->authorizePermission('customers.edit');
        
        $customer = User::whereHas('role', function($q) {
            $q->where('name', 'customer');
        })->findOrFail($id);

        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Display the specified customer profile.
     */
    public function show($id)
    {
        $this->authorizePermission('customers.view');
        
        $customer = User::whereHas('role', function($q) {
            $q->where('name', 'customer');
        })->with(['role', 'bookings' => function($query) {
            $query->with(['details.roomType.images', 'details.roomType.primaryImage', 'details.room', 'payments'])
                  ->orderBy('created_at', 'desc');
        }])->findOrFail($id);

        // Calculate customer statistics
        $totalBookings = $customer->bookings->count();
        $totalSpent = $customer->bookings()
            ->join('payments', 'bookings.id', '=', 'payments.booking_id')
            ->where('payments.status', 'completed')
            ->sum('payments.amount');
        
        $upcomingBookings = $customer->bookings()
            ->where('check_in_date', '>=', now())
            ->whereIn('status', ['confirmed', 'pending'])
            ->count();

        return view('admin.customers.show', compact('customer', 'totalBookings', 'totalSpent', 'upcomingBookings'));
    }

    /**
     * Update customer information.
     */
    public function update(Request $request, $id)
    {
        $this->authorizePermission('customers.edit');
        
        $customer = User::whereHas('role', function($q) {
            $q->where('name', 'customer');
        })->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()->back()
            ->with('success', 'Customer information updated successfully.');
    }

    /**
     * Remove the specified customer.
     */
    public function destroy($id)
    {
        $this->authorizePermission('customers.delete');
        
        $customer = User::whereHas('role', function($q) {
            $q->where('name', 'customer');
        })->with('bookings')->findOrFail($id);

        // Check if customer has bookings
        if ($customer->bookings->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete customer with existing bookings.');
        }

        DB::beginTransaction();

        try {
            $customer->delete();

            DB::commit();

            return redirect()->route('admin.customers.index')
                ->with('success', 'Customer deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the customer.');
        }
    }
}
