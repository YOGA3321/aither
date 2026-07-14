<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Device;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Auth::user()->devices;
        return view('devices.index', compact('devices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'api_key' => 'required|string|max:255|unique:devices,api_key',
            'secret_key' => 'required|string|max:255',
        ]);

        Auth::user()->devices()->create($validated);

        return redirect()->route('devices.index')->with('success', 'Device added successfully.');
    }

    public function destroy(Device $device)
    {
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }

        $device->delete();
        return redirect()->route('devices.index')->with('success', 'Device removed successfully.');
    }
}
