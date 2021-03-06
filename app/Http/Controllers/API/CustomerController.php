<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\CustomerResources;
use App\Http\Resources\DeviceResources;
use App\Models\Customer;
use App\Models\Device;
use App\QueryBuilders\CustomerQueryBuilder;
use App\QueryBuilders\DeviceQueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *     path="/customers",
     *     tags={"Customers"},
     *     summary="Get list of customers",
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          name="perPage",
     *          required=false,
     *          in="query",
     *      ),
     *  @OA\Parameter(
     *          name="page",
     *          required=false,
     *          in="query",
     *      ),
     *     @OA\Response(response="200",
     *      description="returns list of customers with pagination .",
     *      @OA\JsonContent( type="array",
     *         @OA\Items(ref=""))),
     *      @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="403", description="Access denied!.")
     * )
     */
    public function index(Request $request): CustomerResources
    {
        $builder = Customer::with('user');
        $pager = CustomerQueryBuilder::applyWithPaginator($request, $builder);
        return new CustomerResources($pager);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Post(
     *      path="/customers",
     *      tags={"Customers"},
     *      summary="Store new customer",
     *     security={ {"bearer": {} }},
     *      description="Returns customer data",
     *      @OA\RequestBody(
     *       required=true,
     *       description="Enter customer data",
     *       @OA\JsonContent(
     *       required={"name"},
     *       @OA\Property(property="name", type="string", example="mithun"),
     *       @OA\Property(property="description", type="string",  example="new customer"),
     *    ),
     * ),
     *      @OA\Response(
     *          response=201,
     *          description="returns stored customer data",
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
            'description' => 'nullable|max:255',
            'furigana' => 'nullable',
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
    /**
     * @OA\Get(
     *      path="/customers/{customerId}",
     *      tags={"Customers"},
     *      summary="Get customer By Id",
     *      security={ {"bearer": {} }},
     *      description="Get Individual customer data according to customer-id",
     *
     *   @OA\Parameter(
     *          name="customerId",
     *          required=true,
     *          in="path",
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="returns customer data",
     *       @OA\JsonContent(ref="")),
     *       ),
     *     @OA\Response(response="401", description="Unauthenticated"),
     *       OA\Response(
     *          response=403,
     *          description="Access denied!"
     *      )
     *
     * )
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
    /**
     * @OA\Put(
     *      path="/customers/{customerId}",
     *      tags={"Customers"},
     *      summary="Update customer",
     *      security={ {"bearer": {} }},
     *      description="updates customer data",
     *
     *   @OA\Parameter(
     *          name="customerId",
     *          required=true,
     *          in="path",
     *      ),
     *      @OA\RequestBody(
     *       required=true,
     *       description="Enter Required customer data",
     *       @OA\JsonContent(
     *       required={"name"},
     *       @OA\Property(property="name", type="string", example="mithun"),
     *       @OA\Property(property="description", type="string",  example="new customer"),
     *    ),
     * ),
     *      @OA\Response(
     *          response=200,
     *          description="returns updated customer data",
     *        @OA\JsonContent(ref="")
     *       ),
     *       @OA\Response(response="401", description="Unauthenticated"),
     *  @OA\Response(response="403", description="Access denied!.")
     *
     * )
     */
    public function update(Request $request, Customer $customer)
    {
        //validating
        $request->validate([
            'name' => 'max:255',
            'description' => 'nullable|max:255',
            'furigana' => 'nullable',
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
    /**
     * @OA\Delete(
     *      path="/customers/{customerId}",
     *      tags={"Customers"},
     *      summary="Delete customer",
     *     security={ {"bearer": {} }},
     *      description="delete customer data",
     *
     *   @OA\Parameter(
     *          name="customerId",
     *          required=true,
     *          in="path",
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *       ),
     *   @OA\Response(response="401", description="Unauthenticated"),
     *  @OA\Response(response="403", description="Access denied!.")
     *
     *
     * )
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
    /**
     * @OA\Get(
     *      path="/customers/{customerId}/devices",
     *      tags={"Customers"},
     *      summary="Get devices based on customer",
     *     security={ {"bearer": {} }},
     *      description="Returns devices data based on customer",
     *     @OA\Parameter(
     *          name="customerId",
     *          required=true,
     *          in="path",
     *      ),
     *   @OA\Parameter(
     *          name="perPage",
     *          required=false,
     *          in="query",
     *      ),
     *  @OA\Parameter(
     *          name="page",
     *          required=false,
     *          in="query",
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="returns devices based on customers",
     *        @OA\JsonContent( type="array",
     *         @OA\Items(ref=""))
     *       ),
     *       @OA\Response(response="401", description="Unauthenticated"),
     *  @OA\Response(response="403", description="Access denied!.")
     *
     * )
     */
    public function getDevices($customer, Request $request)
    {
        $builder = Device::with(['user', 'images'])->where('customer_id', $customer);
        $pager = DeviceQueryBuilder::applyWithPaginator($request, $builder);
        return new DeviceResources($pager);
    }
}
