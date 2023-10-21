<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CoinController extends Controller
{
    //
    public function updateCoin($user, $amount, $action)
    {
        if ($action == 'add') {
            User::where('id', $user)->increment('coin', $amount);
        } else {
            User::where('id', $user)->decrement('coin', $amount);
        }
    }
}
