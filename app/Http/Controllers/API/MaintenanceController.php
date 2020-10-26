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
    /**
     * @OA\Get(
     *     path="/maintenances",
     *     tags={"Maintenances"},
     *     summary="Get list of maintenances",
     *   security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          name="perPage",
     *          required=false,
     *          in="query",
     *      ),
     *      @OA\Parameter(
     *          name="page",
     *          required=false,
     *          in="query",
     *      ),
     *     @OA\Response(response="200",
     *      description="returns list of maintenances with pagination .",
     *      @OA\JsonContent( type="array",
     *         @OA\Items(ref=""))),
     *  @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="403", description="Access denied!.")
     * )
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
    /**
     * @OA\Post(
     *      path="/maintenances",
     *      tags={"Maintenances"},
     *      summary="Store new maintenance",
     *   security={ {"bearer": {} }},
     *      description="Returns maintenance data",
     *      @OA\RequestBody(
     *       required=true,
     *       description="Pass maintenance data",
     *       @OA\JsonContent(
     *       required={"name","device_id"},
     *       @OA\Property(property="name", type="string", example="maintenece"),
     *       @OA\Property(property="description", type="string", example="camera repair"),
     *       @OA\Property(property="lat", type="string",  example="35.26"),
     *       @OA\Property(property="lng", type="string", example="176.2"),
     *       @OA\Property(property="device_id", type="int",  example=1),
     *       @OA\Property(property="imageUrls", type="array",
     *       @OA\Items(example= "https://5.imimg.com/data5/AL/CC/MY-19161367/counterbalanced-forklift-250x250.png")),
     *
     *    ),
     * ),
     *
     *      @OA\Response(
     *          response=201,
     *          description="returns stored maintenance data",
     *        @OA\JsonContent(ref="")
     *       ),
     *  @OA\Response(response="401", description="Unauthenticated"),
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
            'description' => 'nullable|max:255',
            'lat' => 'nullable|max:30',
            'lng' => 'nullable|max:30',
            'device_id' => 'required|exists:App\Models\Device,id',
            'imageUrls.*' => 'url'
        ]);

        $maintenance = new Maintenance($validatedData);
        $maintenance->user_id = Auth::user()->id;
        $maintenance->save();

        if ($request->imageUrls) {
            $urls = [];
            foreach ($request->imageUrls as $url) {
                $urls[] = ['url' => $url];
            }
            $maintenance->images()->createMany($urls);
        }

        return response($maintenance->load('images'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Maintenance  $maintenance
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *      path="/maintenances/{maintenanceId}",
     *      tags={"Maintenances"},
     *      summary="Get maintenance By Id",
     *   security={ {"bearer": {} }},
     *      description="Get Individual maintenance data according to maintenance-id",
     *
     *   @OA\Parameter(
     *          name="maintenanceId",
     *          required=true,
     *          in="path",
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="returns maintenance data",
     *       @OA\JsonContent(ref="")
     *       ),
     *   @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="403", description="Access denied!.")
     *       )
     *
     * )
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
    /**
     * @OA\Put(
     *      path="/maintenances/{maintenanceId}",
     *      tags={"Maintenances"},
     *      summary="Update maintenance",
     *   security={ {"bearer": {} }},
     *      description="updates maintenance data",
     *
     *   @OA\Parameter(
     *          name="maintenanceId",
     *          required=true,
     *          in="path",
     *      ),
     *       @OA\RequestBody(
     *       required=true,
     *       description="Pass maintenance data",
     *       @OA\JsonContent(
     *       required={"name","device_id"},
     *       @OA\Property(property="name", type="string", example="maintenece"),
     *       @OA\Property(property="description", type="string", example="camera repair"),
     *       @OA\Property(property="lat", type="string",  example="35.26"),
     *       @OA\Property(property="lng", type="string", example="176.2"),
     *       @OA\Property(property="device_id", type="int",  example=1),
     *       @OA\Property(property="imageUrls", type="array",
     *        @OA\Items(example= "https://5.imimg.com/data5/AL/CC/MY-19161367/counterbalanced-forklift-250x250.png")),
     *    ),
     * ),
     *      @OA\Response(
     *          response=200,
     *          description="returns updated maintenance data",
     *        @OA\JsonContent(ref="")
     *       ),
     *  @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="403", description="Access denied!.")
     *       )
     *
     * )
     */
    public function update(Maintenance $maintenance, Request $request)
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
        $maintenance->images()->delete();
        // Update image list
        if ($request->imageUrls) {


            $urls = [];
            foreach ($request->imageUrls as $url) {
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
    /**
     * @OA\Delete(
     *      path="/maintenances/{maintenanceId}",
     *      tags={"Maintenances"},
     *      summary="Delete maintenance",
     *   security={ {"bearer": {} }},
     *      description="delete maintenance data",
     *
     *   @OA\Parameter(
     *          name="maintenanceId",
     *          required=true,
     *          in="path",
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *       ),
     *  @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="403", description="Access denied!.")
     *       )
     *
     * )
     */
    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();
        return response(['message' => 'Success!'], 200);
    }

    /**
     * Get memos of a Maintenance
     */
    /**
     * @OA\Get(
     *      path="/maintenances/{maintenanceId}/memos",
     *      tags={"Maintenances"},
     *      summary="Get memos based on maintenance",
     *   security={ {"bearer": {} }},
     *      description="Returns memos data based on maintenance",
     *     @OA\Parameter(
     *          name="maintenanceId",
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
     *          description="returns memos data based on maintenance",
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

    public function getMemos($maintenance, Request $request)
    {
        $perPage = $request->query('perPage') ? (int)$request->query('perPage') : 15;
        $order = $request->query('order');
        $order = $order ? ($order == 'oldest' ? 'asc' : 'desc') : 'desc';

        return new MemoResources(Memo::where('maintenance_id', $maintenance)->orderBy('created_at', $order)
            ->with(['user', 'images'])->paginate($perPage));
    }
}
