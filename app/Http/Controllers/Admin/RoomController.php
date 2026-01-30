<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ChecksPermissions;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\RoomImage;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    use ChecksPermissions;
    /**
     * Display a listing of room types.
     */
    public function index()
    {
        $this->authorizePermission('rooms.view');
        
        $roomTypes = RoomType::with(['primaryImage', 'images', 'amenities'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.rooms.index', compact('roomTypes'));
    }

    /**
     * Show the form for creating a new room type.
     */
    public function create()
    {
        $this->authorizePermission('rooms.create');
        
        $amenities = Amenity::orderBy('name')->get();
        return view('admin.rooms.create', compact('amenities'));
    }

    /**
     * Store a newly created room type.
     */
    public function store(Request $request)
    {
        $this->authorizePermission('rooms.create');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_per_night' => 'required|numeric|min:0',
            'max_guests' => 'required|integer|min:1',
            'max_adults' => 'required|integer|min:1',
            'max_children' => 'nullable|integer|min:0',
            'total_rooms' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        DB::beginTransaction();

        try {
            // Create room type
            $roomType = RoomType::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price_per_night' => $validated['price_per_night'],
                'max_guests' => $validated['max_guests'],
                'max_adults' => $validated['max_adults'],
                'max_children' => $validated['max_children'] ?? 0,
                'total_rooms' => $validated['total_rooms'],
                'is_active' => $request->has('is_active'),
            ]);

            // Attach amenities
            if (isset($validated['amenities'])) {
                $roomType->amenities()->attach($validated['amenities']);
            }

            // Upload and save images
            if ($request->hasFile('images')) {
                $primarySet = false;
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('room-images', 'public');
                    
                    RoomImage::create([
                        'room_type_id' => $roomType->id,
                        'image_path' => $path,
                        'display_order' => $index,
                        'is_primary' => !$primarySet, // First image as primary
                    ]);
                    
                    $primarySet = true;
                }
            }

            // Create individual rooms
            for ($i = 1; $i <= $validated['total_rooms']; $i++) {
                Room::create([
                    'room_type_id' => $roomType->id,
                    'room_number' => $validated['name'] . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'status' => 'available',
                ]);
            }

            DB::commit();

            return redirect()->route('admin.rooms.index')
                ->with('success', 'Room type created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the room type.');
        }
    }

    /**
     * Display the specified room type.
     */
    public function show($id)
    {
        $this->authorizePermission('rooms.view');
        
        $roomType = RoomType::with(['images', 'amenities', 'rooms'])
            ->findOrFail($id);

        return view('admin.rooms.show', compact('roomType'));
    }

    /**
     * Show the form for editing the specified room type.
     */
    public function edit($id)
    {
        $this->authorizePermission('rooms.edit');
        
        $roomType = RoomType::with(['images', 'amenities'])->findOrFail($id);
        $amenities = Amenity::orderBy('name')->get();
        $selectedAmenities = $roomType->amenities->pluck('id')->toArray();

        return view('admin.rooms.edit', compact('roomType', 'amenities', 'selectedAmenities'));
    }

    /**
     * Update the specified room type.
     */
    public function update(Request $request, $id)
    {
        $this->authorizePermission('rooms.edit');
        
        $roomType = RoomType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_per_night' => 'required|numeric|min:0',
            'max_guests' => 'required|integer|min:1',
            'max_adults' => 'required|integer|min:1',
            'max_children' => 'nullable|integer|min:0',
            'total_rooms' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'delete_images' => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            // Update room type
            $roomType->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price_per_night' => $validated['price_per_night'],
                'max_guests' => $validated['max_guests'],
                'max_adults' => $validated['max_adults'],
                'max_children' => $validated['max_children'] ?? 0,
                'total_rooms' => $validated['total_rooms'],
                'is_active' => $request->has('is_active'),
            ]);

            // Sync amenities
            if (isset($validated['amenities'])) {
                $roomType->amenities()->sync($validated['amenities']);
            } else {
                $roomType->amenities()->detach();
            }

            // Adjust room count
            $currentRooms = $roomType->rooms()->count();
            $newTotal = $validated['total_rooms'];

            if ($newTotal > $currentRooms) {
                // Add new rooms
                for ($i = $currentRooms + 1; $i <= $newTotal; $i++) {
                    Room::create([
                        'room_type_id' => $roomType->id,
                        'room_number' => $roomType->name . '-' . $i,
                        'status' => 'available',
                    ]);
                }
            } elseif ($newTotal < $currentRooms) {
                // Remove excess available rooms
                $excessCount = $currentRooms - $newTotal;
                $roomsToDelete = $roomType->rooms()
                    ->where('status', 'available')
                    ->orderBy('id', 'desc')
                    ->take($excessCount)
                    ->get();

                foreach ($roomsToDelete as $room) {
                    $room->delete();
                }
            }

            // Delete selected images
            if (isset($validated['delete_images'])) {
                foreach ($validated['delete_images'] as $imageId) {
                    $image = RoomImage::find($imageId);
                    if ($image) {
                        Storage::disk('public')->delete($image->image_path);
                        $image->delete();
                    }
                }
            }

            // Upload new images
            if ($request->hasFile('images')) {
                $lastOrder = $roomType->images()->max('display_order') ?? -1;
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('room-images', 'public');
                    
                    RoomImage::create([
                        'room_type_id' => $roomType->id,
                        'image_path' => $path,
                        'display_order' => ++$lastOrder,
                        'is_primary' => false,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.rooms.index')
                ->with('success', 'Room type updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the room type.');
        }
    }

    /**
     * Remove the specified room type.
     */
    public function destroy($id)
    {
        $this->authorizePermission('rooms.delete');
        
        $roomType = RoomType::with('rooms')->findOrFail($id);

        // Check if there are any bookings for this room type
        $hasBookings = $roomType->bookingDetails()->exists();
        
        if ($hasBookings) {
            return redirect()->back()
                ->with('error', 'Cannot delete room type with existing bookings.');
        }

        DB::beginTransaction();

        try {
            // Delete images
            foreach ($roomType->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }

            // Delete rooms
            $roomType->rooms()->delete();

            // Detach amenities
            $roomType->amenities()->detach();

            // Delete room type
            $roomType->delete();

            DB::commit();

            return redirect()->route('admin.rooms.index')
                ->with('success', 'Room type deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the room type.');
        }
    }

    /**
     * Update room status.
     */
    public function updateRoomStatus(Request $request, $roomId)
    {
        $this->authorizePermission('rooms.edit');
        
        $validated = $request->validate([
            'status' => 'required|in:available,occupied,maintenance,reserved',
        ]);

        $room = Room::findOrFail($roomId);
        $room->update(['status' => $validated['status']]);

        return redirect()->back()
            ->with('success', 'Room status updated successfully.');
    }
}
