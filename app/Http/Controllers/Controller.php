<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * @OA\Info(title="FV3M", version="0.1",
     * @OA\Contact(
     *          email="mbel002@mbel.co.jp"
     *      ),
     *      @OA\License(
     *          name="MBEL",
     *          url="http://www.mbel.co.jp/"
     *      ))
     *
     * @OA\Server(
     *      url=L5_SWAGGER_CONST_HOST,
     *      description="Open API Server"
     * )
     *
     *
     *  @OA\Tag(
     *     name="FV3M",
     *     description="API Endpoints of FV3M"
     * )
     *
     */
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
