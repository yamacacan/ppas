<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\FirmSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FirmSettingsController extends Controller
{
    /**
     * Show the form for editing the firm settings.
     */
    public function edit()
    {
        $settings = FirmSettings::instance();
        return view('management.firm_settings.edit', compact('settings'));
    }

    /**
     * Update the firm settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'firm_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'work_start_time' => 'required|date_format:H:i',
            'work_end_time' => 'required|date_format:H:i',
        ]);

        $settings = FirmSettings::instance();
        
        $data = $request->only([
            'firm_name', 
            'address', 
            'email', 
            'work_start_time', 
            'work_end_time'
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            
            $path = $request->file('logo')->store('firm_logos', 'public');
            $data['logo_path'] = $path;
        }

        if ($settings->exists) {
            $settings->update($data);
        } else {
            $settings = FirmSettings::create($data);
        }

        return redirect()->back()->with('success', 'Firma ayarları başarıyla güncellendi.');
    }
}
