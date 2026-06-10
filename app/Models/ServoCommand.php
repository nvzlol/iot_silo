<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServoCommand extends Model
{
    protected $table    = 'servo_commands';
    protected $fillable = ['device_id', 'command'];
}