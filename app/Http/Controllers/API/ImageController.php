<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
     *      @OA\Response(response=200,description="Image Url received",
     *          @OA\MediaType(mediaType="application/json")
     *      ),
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

        return response(['imageUrl' => $path, 'uri' => env('AWS_S3_URL') . $path, 'prefix' => env('AWS_S3_URL')], 201);
    }
}
