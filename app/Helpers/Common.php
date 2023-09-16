<?php

namespace App\Helpers;

use App\Models\SystemLogs;
use App\Models\LetsReceiverLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Common {

    public static function print_r_custom($data) {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        die();
    }

    public static function print_sql_query($query) {
        // Get the generated SQL query
        $sqlQuery = $query->toSql();

        // Get the parameter bindings
        $bindings = $query->getBindings();

        // Replace the question marks with the actual parameter values in the SQL query
        foreach ($bindings as $binding) {
            $sqlQuery = preg_replace('/\?/', "'$binding'", $sqlQuery, 1);
        }

        // Create a JSON response containing the SQL query
        $responseData = [
            'sql' => $sqlQuery,
            // Add any other data you want to include in the response
        ];

        // Return the JSON response
        return response()->json($responseData);
    }

    public static function logSystemActivity($logs='', $module='', $platform='') {
        $user = Auth::user();

        $userId = $user->id;

        $log = [];
    	$log['logs'] = $logs;
    	$log['module'] = $module;
        $log['platform'] = $platform;
    	$log['user_id'] = $userId;
        $log['created_at'] = date('Y-m-d H:i:s', time());

        SystemLogs::create($log);
    }

    public static function letsReceiverLog($lets_id='', $action='') {
        $user = Auth::user();

        $userId = $user->id;

        $user_details = User::where('id', $userId)->get()->toArray();

        $receiverLog = [];
    	$receiverLog['user_id'] = $userId;
    	$receiverLog['lets_id'] = $lets_id;
        $receiverLog['user_longitude'] = $user_details['longitude'];
    	$receiverLog['user_latitude'] = $user_details['latitude'];
    	$receiverLog['action'] = $action;
        $receiverLog['created_at'] = date('Y-m-d H:i:s', time());

        SystemLogs::create($receiverLog);
    }

    public static function distance($lat1, $lon1, $lat2, $lon2, $unit, $radius) {
      /*
          lat1, lon1 = Latitude and Longitude of point 1 in decimal degrees
          lat2, lon2 = Latitude and Longitude of point 2 in decimal degrees
          unit = the unit you desire for results ('M' for meters, 'SM' for statute miles, 'K' for kilometers, 'N' for nautical miles)
          radius = optional radius to filter distances, set to null if not used
      */
  
      if (($lat1 == $lat2) && ($lon1 == $lon2)) {
          return 0;
      } else {
          $theta = $lon1 - $lon2;
          $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
          $dist = acos($dist);
          $dist = rad2deg($dist);
          $miles = $dist * 60 * 1.1515;
          $unit = strtoupper($unit);
  
          if ($unit == "K") {
              $distance = $miles * 1.609344; // Convert to kilometers
          } else if ($unit == "N") {
              $distance = $miles * 0.8684; // Convert to nautical miles
          } else if ($unit == "M") {
            $distance = $miles * 1609.344; // Keep the distance in meters
          } else {
              $distance = $miles; // Default to statute miles
          }

          return $distance;
  
          // Apply radius filter if provided
          if ($radius !== null && $distance <= $radius) {
              return $distance;
          } else {
              return null; // Distance is greater than the specified radius
          }
      }
    }

    public static function distance_meters($lat1, $lon1, $lat2, $lon2, $radius) {
        /*
            lat1, lon1 = Latitude and Longitude of point 1 in decimal degrees
            lat2, lon2 = Latitude and Longitude of point 2 in decimal degrees
            radius = optional radius in meters to filter distances, set to null if not used
        */
    
        $earthRadius = 6371000; // Earth's radius in meters
    
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        } else {
            $lat1Rad = deg2rad($lat1);
            $lon1Rad = deg2rad($lon1);
            $lat2Rad = deg2rad($lat2);
            $lon2Rad = deg2rad($lon2);
    
            $deltaLat = $lat2Rad - $lat1Rad;
            $deltaLon = $lon2Rad - $lon1Rad;
    
            $a = sin($deltaLat / 2) * sin($deltaLat / 2) + cos($lat1Rad) * cos($lat2Rad) * sin($deltaLon / 2) * sin($deltaLon / 2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
            $distance = $earthRadius * $c;
    
            // Apply radius filter if provided
            if ($radius !== null && $distance <= $radius) {
                return $distance;
            } else {
                return null; // Distance is greater than the specified radius
            }
        }
    }
    
  
}


?>