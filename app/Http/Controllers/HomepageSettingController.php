<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\HomepageSetting;
use Illuminate\Support\Facades\Storage;

class HomepageSettingController extends Controller
{
    public function index()
    {
        $settings = HomepageSetting::all()->keyBy('key');
        return view('admin.homepage-settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            $setting = HomepageSetting::firstOrCreate(['key' => $key]);

            // Handle file uploads specially
            if ($request->hasFile($key)) {
                $file = $request->file($key);
                $filename = time() . '_' . $file->getClientOriginalName();
                // Move directly to public directory instead of storage to keep it simple for the user setup
                $file->move(public_path('assets/images'), $filename);
                $setting->value = '/assets/images/' . $filename;
            } else {
                $setting->value = $value;
            }

            $setting->save();
        }

        // Clear the cache so the frontend gets the latest settings immediately
        \Illuminate\Support\Facades\Cache::forget('homepage_settings');

        return redirect()->back()->with('success', 'Homepage settings updated successfully.');
    }
}
