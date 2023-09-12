<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetsModel extends Model
{
    use HasFactory;

    protected $table = 'lets';
    protected $fillable = ['user_id', 'acceptor_id', 'event_name', 'duration', 'creator_selfie', 'creator_longitude', 'creator_latitude', 'acceptor_longitude', 'acceptor_latitude', 'handshake_status', 'status', 'created_at', 'updated_at'];
}
