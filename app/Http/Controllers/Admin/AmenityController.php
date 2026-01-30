<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ChecksPermissions;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AmenityController extends Controller
{
    use ChecksPermissions;
    /**
     * Display a listing of amenities.
     */
    public function index(Request $request)
    {
        $this->authorizePermission('amenities.view');
        
        $query = Amenity::query();

        // Search amenities
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $amenities = $query->orderBy('name')->paginate(20);

        return view('admin.amenities.index', compact('amenities'));
    }

    /**
     * Show the form for creating a new amenity.
     */
    public function create()
    {
        $this->authorizePermission('amenities.create');
        
        return view('admin.amenities.create');
    }

    /**
     * Store a newly created amenity.
     */
    public function store(Request $request)
    {
        $this->authorizePermission('amenities.create');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:amenities,name',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $amenity = Amenity::create($validated);

            return redirect()->route('admin.amenities.show', $amenity->id)
                ->with('success', 'Amenity created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the amenity.');
        }
    }

    /**
     * Display the specified amenity.
     */
    public function show($id)
    {
        $this->authorizePermission('amenities.view');
        
        $amenity = Amenity::with('roomTypes')->findOrFail($id);

        return view('admin.amenities.show', compact('amenity'));
    }

    /**
     * Show the form for editing the specified amenity.
     */
    public function edit($id)
    {
        $this->authorizePermission('amenities.edit');
        
        $amenity = Amenity::findOrFail($id);

        return view('admin.amenities.edit', compact('amenity'));
    }

    /**
     * Update the specified amenity.
     */
    public function update(Request $request, $id)
    {
        $this->authorizePermission('amenities.edit');
        
        $amenity = Amenity::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:amenities,name,' . $amenity->id,
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $amenity->update($validated);

            return redirect()->route('admin.amenities.show', $amenity->id)
                ->with('success', 'Amenity updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the amenity.');
        }
    }

    /**
     * Remove the specified amenity.
     */
    public function destroy($id)
    {
        $this->authorizePermission('amenities.delete');
        
        $amenity = Amenity::with('roomTypes')->findOrFail($id);

        // Check if amenity is used by any room types
        if ($amenity->roomTypes->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete amenity that is assigned to room types.');
        }

        try {
            $amenity->delete();

            return redirect()->route('admin.amenities.index')
                ->with('success', 'Amenity deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the amenity.');
        }
    }
}
