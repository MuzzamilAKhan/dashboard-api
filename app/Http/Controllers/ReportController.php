<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Report::latest()->get());
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
        $report = Report::create($request->only(['title', 'description', 'status']));
        return response()->json(['message' => 'Report created', 'data' => $report], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return response()->json(Report::findOrFail($id));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Report $report)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        $report->update($request->only(['title', 'description', 'status']));
        return response()->json(['message' => 'Report updated', 'data' => $report]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Report::destroy($id);
        return response()->json(['message' => 'Report deleted']);
    }
}
