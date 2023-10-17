<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportModel extends Model
{
    use HasFactory;

    protected $table = 'reports';

    protected $fillable = ['user_id', 'reported_by', 'report_type', 'reason', 'status', 'created_at', 'updated_at'];
}