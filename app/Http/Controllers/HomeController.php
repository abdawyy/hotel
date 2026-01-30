<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use App\Models\Amenity;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page.
     */
    public function index()
    {
        // Get featured/active room types
        $featuredRooms = RoomType::active()
            ->with(['primaryImage', 'images', 'amenities'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // Get all amenities for the amenities section
        $amenities = Amenity::orderBy('name')->get();

        return view('public.home', compact('featuredRooms', 'amenities'));
    }
}
