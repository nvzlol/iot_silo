<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use App\Models\ServoCommand;
use Illuminate\Http\Request;

class SiloController extends Controller
{
    // POST /api/sensor-data  ← dipanggil ESP32
    public function storeSensor(Request $request)
    {
        $request->validate([
            'device_id'   => 'required|string',
            'sensor_name' => 'required|string',
            'value'       => 'required|numeric',
        ]);

        $data = SensorData::create($request->only('device_id', 'sensor_name', 'value'));

        return response()->json(['message' => 'OK', 'data' => $data], 201);
    }

    // GET /api/servo/command  ← di-poll ESP32 tiap 5 detik
    public function getServoCommand()
    {
        $latest = ServoCommand::where('device_id', 'esp32-silo-01')
                              ->latest()
                              ->first();

        $command = $latest ? $latest->command : 'close';

        return response()->json(['command' => $command]);
    }

    // POST /api/servo/command  ← dikirim dari dashboard
    public function setServoCommand(Request $request)
    {
        $request->validate(['command' => 'required|in:open,close']);

        $cmd = ServoCommand::create([
            'device_id' => 'esp32-silo-01',
            'command'   => $request->command,
        ]);

        return response()->json(['message' => 'Perintah dikirim', 'command' => $cmd->command]);
    }

    // GET /dashboard  ← halaman web
    public function dashboard()
    {
        $latestBerat = SensorData::where('device_id', 'esp32-silo-01')
                                 ->where('sensor_name', 'load_cell')
                                 ->latest()
                                 ->first();

        $latestCommand = ServoCommand::where('device_id', 'esp32-silo-01')
                                     ->latest()
                                     ->first();

        $riwayat = SensorData::where('device_id', 'esp32-silo-01')
                             ->latest()
                             ->take(10)
                             ->get();

        return view('dashboard', compact('latestBerat', 'latestCommand', 'riwayat'));
    }
}