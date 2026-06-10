<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensorData;
use App\Models\ServoCommand;

class DashboardController extends Controller
{
    // GET /dashboard — halaman utama
    public function index()
    {
        $data = SensorData::latest()->get(); // preserve logika lama

        $latestCommand = ServoCommand::where('device_id', 'esp32-silo-01')
                            ->latest()
                            ->first();

        return view('dashboard', compact('data', 'latestCommand'));
    }

    // POST /servo/command — tombol dari dashboard
    public function setServoCommand(Request $request)
    {
        $request->validate(['command' => 'required|in:open,close']);

        ServoCommand::create([
            'device_id' => 'esp32-silo-01',
            'command'   => $request->command,
        ]);

        return response()->json(['message' => 'OK', 'command' => $request->command]);
    }
}