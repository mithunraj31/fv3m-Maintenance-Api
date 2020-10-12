<?php

namespace App\Http\Controllers;

use App\Http\Resources\MaintenanceResource;
use App\Http\Resources\MaintenanceResources;
use App\Http\Resources\MemoResources;
use App\Models\Maintenance;
use App\Models\Memo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->query('perPage') ? (int)$request->query('perPage') : 15;
        return new MaintenanceResources(Maintenance::with('user', 'images')->paginate($perPage));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validating
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable|max:255',
            'lat' => 'nullable|max:30',
            'lng' => 'nullable|max:30',
            'device_id' => 'required|exists:App\Models\Device,id',
            'imageUrls.*' => 'url'
        ]);

        $maintenance = new Maintenance($validatedData);
        $maintenance->user_id = Auth::user()->id;
        $maintenance->save();

        $urls = [];
        foreach ($request->url as $url) {
            $urls[] = ['url' => $url];
        }
        $maintenance->images()->createMany($urls);

        return response($maintenance->load('images'), 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Maintenance  $maintenance
     * @return \Illuminate\Http\Response
     */
    public function show(Maintenance $maintenance)
    {
        $maintenance = new MaintenanceResource($maintenance->load(['user', 'device', 'images']));

        return $maintenance;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Maintenance  $maintenance
     * @return \Illuminate\Http\Response
     */
    public function edit(Maintenance $maintenance, Request $request)
    {
        //validating
        $validatedData = $request->validate([
            'name' => 'max:255',
            'description' => 'nullable|max:255',
            'lat' => 'nullable|max:30',
            'lng' => 'nullable|max:30',
            'device_id' => 'exists:App\Models\Device,id',
            'imageUrls.*' => 'url'
        ]);

        $maintenance->update($request->all());

        // Update image list
        if ($request->url) {
            $maintenance->images()->delete();

            $urls = [];
            foreach ($request->url as $url) {
                $urls[] = ['url' => $url];
            }
            $maintenance->images()->createMany($urls);
        }

        return response($maintenance);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Maintenance  $maintenance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();
        return response(['message' => 'Success!'], 200);
    }

    /**
     * Get memos of a Maintenance
     */

     public function getMemos($maintenance, Request $request)
     {
        $perPage = $request->query('perPage') ? (int)$request->query('perPage') : 15;
        return new MemoResources(Memo::where('maintenance_id',$maintenance)->with(['user','images'])->paginate($perPage));
     }
}
