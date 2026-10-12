<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'cafe_name' => Setting::get('cafe_name', 'Sip N Bite Café & Bistro'),
            'cafe_tagline' => Setting::get('cafe_tagline', 'Good Food. Great Coffee.'),
            'cafe_phone' => Setting::get('cafe_phone', '+91 9876543210'),
            'cafe_email' => Setting::get('cafe_email', 'contact@sipnbite.com'),
            'cafe_address' => Setting::get('cafe_address', '108 Gourmet Avenue, Sector 18'),
            'opening_hours' => Setting::get('opening_hours', '09:00 AM - 11:00 PM'),
            'tax_percentage' => Setting::get('tax_percentage', '5'),
            'flat_delivery_fee' => Setting::get('flat_delivery_fee', '40'),
        ];
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            Setting::set($key, $value);
        }
        return back()->with('success', 'Café settings updated successfully!');
    }
}
