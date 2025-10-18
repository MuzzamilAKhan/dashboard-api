<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reports\Reports;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Reports::latest()->get());
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
        $report = Reports::create($request->only(['title', 'description', 'status']));
        return response()->json(['message' => 'Report created', 'data' => $report], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return response()->json(Reports::findOrFail($id));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reports $report)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $report = Reports::findOrFail($id);
        $report->update($request->only(['title', 'description', 'status']));
        return response()->json(['message' => 'Report updated', 'data' => $report]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Reports::destroy($id);
        return response()->json(['message' => 'Report deleted']);
    }
}
