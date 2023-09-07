<?php

namespace App\Helpers;

use App\Models\SystemLogs;
use Illuminate\Support\Facades\Auth;

class Common {

    public static function print_r_custom($data) {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        die();
    }

    public static function logSystemActivity($logs='', $module='') {
        $user = Auth::user();

        $userId = $user->id;

        $log = [];
    	$log['logs'] = $logs;
    	$log['module'] = $module;
    	$log['user_id'] = $userId;
        $log['created_at'] = date('Y-m-d H:i:s', time());

        SystemLogs::create($log);
    }
}


?>