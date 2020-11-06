<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *     path="/statuses",
     *     tags={"Status"},
     *     summary="Get list of status",
     *  security={ {"bearer": {} }},
     *     @OA\Response(response="200",
     *      description="returns list of status",
     *      @OA\JsonContent( type="array",
     *         @OA\Items(ref=""))),
     *    @OA\Response(response="401", description="Unauthenticated"),
     * @OA\Response(
     *          response=403,
     *          description="Access denied!"
     *      )
     * )
     */
    public function index()
    {
        return Status::get();
    }
}
