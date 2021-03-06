<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeviceResource;
use App\Http\Resources\DeviceResources;
use App\Http\Resources\MaintenanceResources;
use App\Models\Device;
use App\Models\Maintenance;
use App\QueryBuilders\DeviceQueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *     path="/devices",
     *     tags={"Devices"},
     *     summary="Get list of devices",
     *   security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          name="perPage",
     *          required=false,
     *          in="query",
     *      ),
     *     @OA\Parameter(
     *          name="page",
     *          required=false,
     *          in="query",
     *      ),
     *     @OA\Response(response="200",
     *      description="returns list of devices with pagination .",
     *      @OA\JsonContent( type="array",
     *         @OA\Items(ref=""))),
     *   @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="403", description="Access denied!.")
     * )
     */
    public function index(Request $request)
    {
        $builder = Device::with(['user', 'images']);
        $pager = DeviceQueryBuilder::applyWithPaginator($request, $builder);
        return new DeviceResources($pager);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Post(
     *      path="/devices",
     *      tags={"Devices"},
     *      summary="Store new device",
     *   security={ {"bearer": {} }},
     *      description="Returns device data",
     *     @OA\RequestBody(
     *       required=true,
     *       description="Pass device data",
     *       @OA\JsonContent(
     *       required={"name"},
     *       @OA\Property(property="name", type="string", example="forklift"),
     *       @OA\Property(property="lat", type="string",  example="35.26"),
     *       @OA\Property(property="lng", type="string", example="176.2"),
     *       @OA\Property(property="customer_id", type="int",  example=1),
     *       @OA\Property(property="status_id", type="int", example=1),
     *       @OA\Property(property="serial_number", type="string", example="MAF0225"),
     *       @OA\Property(property="regist_date", type="date", example="2020-10-20"),
     *       @OA\Property(property="mutated_date", type="date", example="2020-10-25"),
     *       @OA\Property(property="os", type="int", example=1),
     *       @OA\Property(property="description", type="string", example="describe your device"),
     *       @OA\Property(property="imageUrls", type="array",
     *          @OA\Items(example= "https://5.imimg.com/data5/AL/CC/MY-19161367/counterbalanced-forklift-250x250.png")),
     *       ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="returns stored device data",
     *        @OA\JsonContent(ref="")
     *       ),
     *   @OA\Response(response="401", description="Unauthenticated"),
     *      @OA\Response(
     *          response=403,
     *          description="Access denied!"
     *      )
     * )
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
            'imageUrls.*' => 'string',
            'serial_number' => 'string|nullable',
            'regist_date' => 'date|nullable',
            'mutated_date' => 'date|nullable',
            'os' => 'integer|nullable',
            'description' => 'string|nullable'
        ]);

        $device = new Device($request->all());
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
    /**
     * @OA\Get(
     *      path="/devices/{deviceId}",
     *      tags={"Devices"},
     *      summary="Get device By Id",
     *   security={ {"bearer": {} }},
     *      description="Get Individual device data according to device-id",
     *
     *   @OA\Parameter(
     *          name="deviceId",
     *          required=true,
     *          in="path",
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="returns device data",
     *       @OA\JsonContent(ref="")
     *         ),
     * @OA\Response(response="401", description="Unauthenticated"),
     *      @OA\Response(
     *          response=403,
     *          description="Access denied!"
     *      )
     *
     *
     *
     * )
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
    /**
     * @OA\Put(
     *      path="/devices/{deviceId}",
     *      tags={"Devices"},
     *      summary="Update device",
     *   security={ {"bearer": {} }},
     *      description="updates device data",
     *
     *   @OA\Parameter(
     *          name="deviceId",
     *          required=true,
     *          in="path",
     *      ),
     *      @OA\RequestBody(
     *       required=true,
     *       description="Pass device data",
     *       @OA\JsonContent(
     *       required={"name"},
     *       @OA\Property(property="name", type="string", example="forklift"),
     *       @OA\Property(property="lat", type="string",  example="35.26"),
     *       @OA\Property(property="lng", type="string", example="176.2"),
     *       @OA\Property(property="customer_id", type="int",  example=1),
     *       @OA\Property(property="status_id", type="int", example=1),
     *       @OA\Property(property="serial_number", type="string", example="MAF0225"),
     *       @OA\Property(property="regist_date", type="date", example="2020-10-20"),
     *       @OA\Property(property="mutated_date", type="date", example="2020-10-25"),
     *       @OA\Property(property="os", type="int", example=1),
     *       @OA\Property(property="description", type="string", example="describe your device"),
     *       @OA\Property(property="imageUrls", type="array",  @OA\Items(
     *              example= "https://5.imimg.com/data5/AL/CC/MY-19161367/counterbalanced-forklift-250x250.png")),
     *          ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="returns updated devices data",
     *        @OA\JsonContent(ref="")
     *       ),
     *  @OA\Response(response="401", description="Unauthenticated"),
     *  @OA\Response(
     *          response=403,
     *          description="Access denied!"
     *      )
     *
     * )
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
            'imageUrls.*' => 'string',
            'serial_number' => 'string|nullable',
            'regist_date' => 'date|nullable',
            'mutated_date' => 'date|nullable',
            'os' => 'integer|nullable',
            'description' => 'string|nullable'
        ]);

        $device->update($request->all());

        $device->images()->delete();
        // Update image list
        if ($request->imageUrls) {


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
    /**
     * @OA\Delete(
     *      path="/devices/{deviceId}",
     *      tags={"Devices"},
     *      summary="Delete device",
     *   security={ {"bearer": {} }},
     *      description="delete device data",
     *
     *   @OA\Parameter(
     *          name="deviceId",
     *          required=true,
     *          in="path",
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *       ),
     * @OA\Response(response="401", description="Unauthenticated"),
     *  @OA\Response(
     *          response=403,
     *          description="Access denied!"
     *      )
     *
     * )
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
    /**
     * @OA\Get(
     *      path="/devices/{deviceId}/maintenances",
     *      tags={"Devices"},
     *      summary="Get maintenances based on device",
     *   security={ {"bearer": {} }},
     *      description="Returns maintenances data based on device",
     *     @OA\Parameter(
     *          name="deviceId",
     *          required=true,
     *          in="path",
     *      ),
     *    @OA\Parameter(
     *          name="perPage",
     *          required=false,
     *          in="query",
     *      ),
     *     @OA\Parameter(
     *          name="page",
     *          required=false,
     *          in="query",
     *      ),
     *     @OA\Parameter(
     *          name="order",
     *          required=false,
     *          in="query",
     *          description="order by latest or oldest"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="returns maintenances data based on device",
     *        @OA\JsonContent( type="array",
     *         @OA\Items(ref=""))
     *       ),
     *  @OA\Response(response="401", description="Unauthenticated"),
     *      @OA\Response(
     *          response=403,
     *          description="Access denied!"
     *      )
     * )
     */
    public function getMaintenances($device, Request $request)
    {
        $perPage = $request->query('perPage') ? (int)$request->query('perPage') : 15;
        $order = $request->query('order');
        $order = $order ? ($order == 'oldest' ? 'asc' : 'desc') : 'desc';

        return new MaintenanceResources(Maintenance::where('device_id', $device)->orderBy('created_at', $order)
            ->with(['user', 'images'])->paginate($perPage));
    }
}
