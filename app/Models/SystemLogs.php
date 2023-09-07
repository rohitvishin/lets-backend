<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLogs extends Model
{
    //
    protected $table = 'system_log';
    protected $fillable =['logs', 'module', 'user_id', 'created_at', 'updated_at'];
}
