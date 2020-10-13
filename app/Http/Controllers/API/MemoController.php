<?php

namespace App\Http\Controllers;

use App\Http\Resources\MemoResource;
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
    public function index(Request $request)
    {
        $perPage = $request->query('perPage') ? (int)$request->query('perPage') : 15;
        return new MemoResource(Memo::with(['user','images','maintenance'])->paginate($perPage));
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
            'maintenance_id' => 'required|exists:App\Models\Maintenance,id',
            'imageUrls.*' => 'url'
        ]);

        $memo = new Memo($validatedData);
        $memo->user_id = Auth::user()->id;
        $memo->save();

        $urls = [];
        foreach ($request->imageUrls as $url) {
            $urls[] = ['url' => $url];
        }
        $memo->images()->createMany($urls);

        return response($memo->load('images'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Memo  $memo
     * @return \Illuminate\Http\Response
     */
    public function show(Memo $memo)
    {
        $maintenance = new MemoResource($memo->load(['user', 'images','maintenance']));

        return $maintenance;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Memo  $memo
     * @return \Illuminate\Http\Response
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
            foreach ($request->url as $url) {
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
    public function destroy(Memo $memo)
    {
        $memo->delete();
        return response(['message' => 'Success!'], 200);
    }
}
