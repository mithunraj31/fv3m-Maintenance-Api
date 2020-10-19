<?php

namespace App\Http\Controllers;

use App\Http\Resources\MemoResource;
use App\Http\Resources\MemoResources;
use App\Models\Memo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *     path="/memos",
     *     tags={"Memos"},
     *     summary="Get list of memos",
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
     *      description="returns list of memos with pagination .",
     *      @OA\JsonContent( type="array",
     *         @OA\Items(ref=""))),
     *     @OA\Response(response="403", description="Access denied!.")
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->query('perPage') ? (int)$request->query('perPage') : 15;
        return new MemoResources(Memo::with(['user', 'images', 'maintenance'])->paginate($perPage));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Post(
     *      path="/memos",
     *      tags={"Memos"},
     *      summary="Store a new memo",
     *   security={ {"bearer": {} }},
     *      description="Returns memo data",
     *     @OA\RequestBody(
     *       required=true,
     *       description="Pass user credentials",
     *       @OA\JsonContent(
     *       required={"name","maintenance_id"},
     *       @OA\Property(property="description", type="string", example="camera repair"),
     *       @OA\Property(property="maintenance_id", type="int",  example="1"),
     *       @OA\Property(property="imageUrls", type="string",
     *       example= "https://5.imimg.com/data5/AL/CC/MY-19161367/counterbalanced-forklift-250x250.png"),
     *    ),
     * ),
     *      @OA\Response(
     *          response=201,
     *          description="returns stored memo data",
     *        @OA\JsonContent(ref="")
     *       ),
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
            'description' => 'nullable|max:255',
            'maintenance_id' => 'required|exists:App\Models\Maintenance,id',
            'imageUrls.*' => 'url'
        ]);

        $memo = new Memo($validatedData);
        $memo->user_id = Auth::user()->id;
        $memo->save();

        if ($request->imageUrls) {
            $urls = [];
            foreach ($request->imageUrls as $url) {
                $urls[] = ['url' => $url];
            }
            $memo->images()->createMany($urls);
        }
        return response($memo->load('images'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Memo  $memo
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *      path="/memos/{memoId}",
     *      tags={"Memos"},
     *      summary="Get memo By Id",
     *   security={ {"bearer": {} }},
     *      description="Get Individual memo data according to memo-id",
     *
     *   @OA\Parameter(
     *          name="memoId",
     *          required=true,
     *          in="path",
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="returns memo data",
     *       @OA\JsonContent(ref="")),
     *       )
     *
     * )
     */
    public function show(Memo $memo)
    {
        $maintenance = new MemoResource($memo->load(['user', 'images', 'maintenance']));

        return $maintenance;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Memo  $memo
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Put(
     *      path="/memos/{id}",
     *      tags={"Memos"},
     *      summary="Update memo",
     *   security={ {"bearer": {} }},
     *      description="updates memo data",
     *
     *   @OA\Parameter(
     *          name="id",
     *          required=true,
     *          in="path",
     *      ),
     *    @OA\RequestBody(
     *       required=true,
     *       description="Pass user credentials",
     *       @OA\JsonContent(
     *       required={"name","maintenance_id"},
     *       @OA\Property(property="name", type="string", example="maintenece"),
     *       @OA\Property(property="description", type="string", example="camera repair"),
     *       @OA\Property(property="maintenance_id", type="int",  example="1"),
     *       @OA\Property(property="imageUrls", type="string",
     *       example= "https://5.imimg.com/data5/AL/CC/MY-19161367/counterbalanced-forklift-250x250.png"),
     *    ),
     * ),
     *      @OA\Response(
     *          response=201,
     *          description="returns updated memos data",
     *        @OA\JsonContent(ref="")
     *       )
     *
     * )
     */
    public function update(Request $request, Memo $memo)
    {
        //validating
        $validatedData = $request->validate([
            'name' => 'max:255',
            'description' => 'nullable|max:255',
            'maintenance_id' => 'exists:App\Models\Maintenance,id',
            'imageUrls.*' => 'url'
        ]);

        $memo->update($request->all());

        // Update image list
        if ($request->url) {
            $memo->images()->delete();

            $urls = [];
            foreach ($request->imageUrls as $url) {
                $urls[] = ['url' => $url];
            }
            $memo->images()->createMany($urls);
        }

        return response($memo);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Memo  $memo
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Delete(
     *      path="/memos/{memoId}",
     *      tags={"Memos"},
     *      summary="Delete memo",
     *   security={ {"bearer": {} }},
     *      description="delete memo data",
     *
     *   @OA\Parameter(
     *          name="memoId",
     *          required=true,
     *          in="path",
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Success",
     *       )
     *
     * )
     */
    public function destroy(Memo $memo)
    {
        $memo->delete();
        return response(['message' => 'Success!'], 200);
    }
}
