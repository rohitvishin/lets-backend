<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlansModel extends Model
{
    use HasFactory;

    protected $table = 'plans';
    protected $fillable =['package_name', 'amount', 'lets_count', 'validity', 'validity_type', 'isCarryforward', 'isReferralBonus', 'isRadiusChange', 'isGenderSelect', 'isAgeSelect', 'status', 'created_at', 'updated_at'];
}
