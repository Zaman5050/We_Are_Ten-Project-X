<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SupplierRequest;
use App\Interfaces\ServiceInterfaces\Tenant\SupplierServiceInterface;

class SupplierController extends Controller
{
    private $service; 

    public function __construct( SupplierServiceInterface $SupplierService)
    {
        $this->service = $SupplierService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data =  $this->service->listing($request);
        $suppliers = $data['suppliers'];
        $categories = $data['categories'];
        $timezones = $data['timezones'];
        $currentCompany = $data['currentCompany'];
        return view('pages.supplier.index', compact('suppliers','timezones','categories','currentCompany'));

    }

     /**
     * Store a newly created resource in storage.
     */

    public function store(SupplierRequest $request)
    {
        
        return $this->service->storeSupplier($request->validated());
    }

     /**
     * Update the specified resource in storage.
     */
    public function update(SupplierRequest $request)
    {
        return $this->service->updateSupplier($request);
    }


     /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        return $this->service->deleteSupplier($request);
    }


}
