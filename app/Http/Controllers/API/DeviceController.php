<?php

namespace App\Http\Controllers;

use App\Http\Resources\DeviceResource;
use App\Http\Resources\MaintenanceResources;
use App\Models\Device;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->query('perPage') ? (int)$request->query('perPage') : 15;
        return new DeviceResource(Device::with('user')->paginate($perPage));
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
            'lat' => 'nullable|max:30',
            'lng' => 'nullable|max:30',
            'customer_id' => 'required|exists:App\Models\Customer,id',
            'status_id' => 'required|exists:App\Models\Status,id',
            'imageUrls.*' => 'url'
        ]);

        $device = new Device($validatedData);
        $device->user_id = Auth::user()->id;
        $device->save();

        $urls = [];
        foreach ($request->imageUrls as $url) {
            $urls[] = ['url' => $url];
        }
        $device->images()->createMany($urls);

        return response($device->load('images'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function show(Device $device)
    {
        $device = new DeviceResource($device->load(['user', 'status', 'customer', 'images']));

        return $device;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Device $device)
    {
        //validating
        $validatedData = $request->validate([
            'name' => 'max:255',
            'lat' => 'nullable|max:30',
            'lng' => 'nullable|max:30',
            'customer_id' => 'exists:App\Models\Customer,id',
            'status_id' => 'exists:App\Models\Status,id',
            'imageUrls.*' => 'url'
        ]);

        $device->update($request->all());

        // Update image list
        if ($request->imageUrls) {
            $device->images()->delete();

            $urls = [];
            foreach ($request->imageUrls as $url) {
                $urls[] = ['url' => $url];
            }
            $device->images()->createMany($urls);
        }

        return response($device);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function destroy(Device $device)
    {
        $device->delete();
        return response(['message' => 'Success!'], 200);
    }

    /**
     * Get Maintenance info of a Device
     *
     */
    public function getMaintenances($device, Request $request)
    {
        $perPage = $request->query('perPage') ? (int)$request->query('perPage') : 15;
        return new MaintenanceResources(Maintenance::where('device_id',$device)->with(['user','images'])->paginate($perPage));
    }
}
