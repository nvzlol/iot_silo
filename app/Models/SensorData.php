<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    protected $table = 'sensor_datas';

    protected $fillable = ['device_id', 'sensor_name', 'value'];
}