<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\SensorData;
use App\Models\ServoCommand;

// POST /api/sensor-data ← dipanggil ESP32 tiap 30 detik
Route::post('/sensor-data', function (Request $request) {
    $request->validate([
        'device_id'   => 'required|string',
        'sensor_name' => 'required|string',
        'value'       => 'required|numeric',
    ]);

    $data = SensorData::create($request->only('device_id', 'sensor_name', 'value'));

    return response()->json(['message' => 'OK', 'data' => $data], 201);
});

// GET /api/sensor ← dipakai dashboard (chart & tabel)
Route::get('/sensor', function () {
    return SensorData::where('device_id', 'esp32-silo-01')
                     ->latest()
                     ->take(10)
                     ->get();
});

// GET /api/servo/command ← di-poll ESP32 tiap 5 detik
Route::get('/servo/command', function () {
    $latest = ServoCommand::where('device_id', 'esp32-silo-01')
                          ->latest()
                          ->first();

    return response()->json(['command' => $latest ? $latest->command : 'close']);
});

// GET /api/sensor/latest ← dipakai Roblox
Route::get('/sensor/latest', function () {
    $latest = SensorData::where('device_id', 'esp32-silo-01')
                        ->where('sensor_name', 'load_cell')
                        ->latest()
                        ->first();

    if (!$latest) {
        return response()->json(['status' => 'error', 'message' => 'No data'], 404);
    }

    return response()->json(['status' => 'success', 'data' => $latest]);
});