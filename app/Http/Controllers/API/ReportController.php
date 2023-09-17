<?php

namespace App\Http\Controllers\API;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Models\ReportModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (is_null($user)) {
            return response()->json(['message' => 'User not found'], 404);
        }

        try {
            $data = $request->validate([
                'user_id' => 'required',
                'reason' => 'required|string'
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 400);
        }

        $reports = ReportModel::create([
            'user_id' => $data['user_id'],
            'reason' => $data['reason'],
            'reported_by' => $user->id,
        ]);

        return response()->json(['message' => 'Report Submitted Successfully', 'data' => $reports], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $user = Auth::user();

        if (is_null($user)) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $report_list = ReportModel::where('user_id', $user->id)->get();

        return response()->json(['message' => 'Report List', 'list' => $report_list], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
