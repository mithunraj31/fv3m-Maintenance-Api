<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Http\Resources\CustomerResources;
use App\Http\Resources\Customers;
use App\Http\Resources\DeviceResources;
use App\Models\Customer;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request):CustomerResources
    {
        $perPage = $request->query('perPage')?(int)$request->query('perPage'):15;
        return new CustomerResources(Customer::with('user')->paginate($perPage));
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
        ]);
        $group = new Customer($validatedData);

        $group->user_id = Auth::user()->id;

        $group->save();

        return response($group, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        $customer = new CustomerResource($customer->load(['user']));
        return $customer;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        //validating
        $request->validate([
            'name' => 'max:255',
            'description' => 'nullable|max:255',
        ]);

        $request->user_id = Auth::user()->id;

        $customer->update($request->all());

        return $customer;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response(['message' => 'Success!'], 200);
    }

    /**
     * Display the Device Collection resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDevices($customer, Request $request)
    {   $perPage = $request->query('perPage')?(int)$request->query('perPage'):15;
        return new DeviceResources(Device::where('customer_id',$customer)->paginate($perPage));

    }
}
