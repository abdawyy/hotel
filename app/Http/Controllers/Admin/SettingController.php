<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ChecksPermissions;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    use ChecksPermissions;
    /**
     * Display the settings page.
     */
    public function index()
    {
        $this->authorizePermission('settings.view');
        
        // Get all settings or create defaults
        $settings = Setting::all()->keyBy('key');
        
        // Default settings if not exists
        $defaultSettings = [
            'hotel_name' => 'Grand Hotel',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'tax_rate' => '10',
            'check_in_time' => '14:00',
            'check_out_time' => '12:00',
            'contact_email' => 'info@hotel.com',
            'contact_phone' => '+1 234 567 8900',
            'contact_address' => '123 Hotel Street, City, Country',
        ];

        // Ensure all default settings exist
        foreach ($defaultSettings as $key => $value) {
            if (!$settings->has($key)) {
                Setting::setValue($key, $value, is_numeric($value) ? 'decimal' : 'string');
            }
        }

        // Reload settings
        $settings = Setting::all()->keyBy('key');

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        $this->authorizePermission('settings.edit');
        
        $validated = $request->validate([
            'hotel_name' => 'required|string|max:255',
            'currency' => 'required|string|max:10',
            'currency_symbol' => 'required|string|max:5',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'check_in_time' => 'required|string|max:10',
            'check_out_time' => 'required|string|max:10',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_address' => 'nullable|string',
        ]);

        foreach ($validated as $key => $value) {
            $type = is_numeric($value) ? 'decimal' : 'string';
            Setting::setValue($key, $value, $type);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }
}
