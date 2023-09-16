<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetsReceiverLogModel extends Model
{
    use HasFactory;

    protected $table = 'lets_receiver_log';
    protected $fillable = ['user_id', 'lets_id', 'user_longitude', 'user_latitude', 'action', 'created_at', 'updated_at'];
}
