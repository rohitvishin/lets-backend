<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlansModel extends Model
{
    use HasFactory;

    protected $table = 'plans';
    protected $fillable =['name', 'amount', 'user_id', 'created_at', 'updated_at'];
}
