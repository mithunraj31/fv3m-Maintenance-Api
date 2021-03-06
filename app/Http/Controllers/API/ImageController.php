<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{

    /**
     * @OA\Post(
     *      path="/images",
     *      tags={"Image"},
     *      summary="Store new image",
     *   security={ {"bearer": {} }},
     *      description="Returns images data",
     *    @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="image",
     *                      description="Item image",
     *                      type="array",
     *                      @OA\Items(type="string", format="binary")
     *                   ),
     *               ),
     *           ),
     *       ),
     *
     *      @OA\Response(response=201,description="Image Url received",
     *          @OA\MediaType(mediaType="application/json")
     *      ),
     * @OA\Response(response="401", description="Unauthenticated"),
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
            'image' => 'required|image',
        ]);

        // Start uploading s3
        $image  =  $request->file('image');
        $path  =  Storage::disk('s3')->put('images',  $image,  'public');

        return response(['imageUrl' => $path, 'uri' => config('aws.s3.base_url') . $path, 'prefix' => config('aws.s3.base_url')], 201);
    }
    public function storeBase64(Request $request)
    {
        //validating
        $validatedData = $request->validate([
            'image' => 'required|string',
        ]);
        $base64_image  =  $request->get('image');

        if (preg_match('/^data:image\/(\w+);base64,/', $base64_image)) {
            $data = substr($base64_image, strpos($base64_image, ',') + 1);

            $data = base64_decode($data);
            $user = Auth::user()->name;
            $mytime = Carbon::now();
            $timesting = $mytime->toDateTimeString();
            // Start uploading s3
            $rand =(string) Str::uuid();
            $path  = 'images/'.$user.'_'.$timesting.'_'.$rand.'.jpeg';
            Storage::disk('s3')->put($path, $data,  'public');

            return response(['imageUrl' => $path, 'uri' => config('aws.s3.base_url') . $path, 'prefix' => config('aws.s3.base_url')], 201);
        }
    }
}
