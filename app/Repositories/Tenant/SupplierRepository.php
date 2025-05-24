<?php

namespace App\Repositories\Tenant;

use App\Interfaces\RepositoryInterfaces\Tenant\SupplierRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Traits\FileHandler;
use App\Models\SupplierLibrary;
use App\Models\Categories;

class SupplierRepository implements SupplierRepositoryInterface

{
    use FileHandler;
    protected $model;

    public function __construct(SupplierLibrary $supplier)
    {
        $this->model = $supplier;
    }

    public function getSuppliers()
    {
        $userCompany = auth()?->user()?->company;
        return $this->model::where('company_id', $userCompany->id)->with('categories')->get();
    }
    public function getSupplier($uuid = ''){
        if(!$uuid) $uuid = '';
        return $this->model::findOrFailByUuid($uuid);
    }

    public function storeSupplier($data)
    {
        $response = [
            "error" => true,
            "message" => "Something went wrong",
        ];
        try {
            $this->model->fill($data);

            $uuid = $this->model->uuid;
            if (request()->hasFile('profile_file')) {
                $file = request()->file('profile_file');
                $path = $this->storeMediaByHashName($file, "/supplier/attachments" . $uuid );
                $data['profile_picture'] = $path; // Save the file path in the data array
            }
            $this->model->fill($data);
            if ($this->model->save()) {
                foreach ($data['itemSold'] as $item) {
                    if (!isset($item['created_at'])) {
                        $category = Categories::create([
                            'name' => $item['name'],
                            'company_id' => $item['company_id'],
                        ]);

                        $this->model->categories()->attach($category->id);
                    } else {
                        $this->model->categories()->attach($item['id']);
                    }
                }

            $response = [
                "error" => false,
                "message" => "Supplier created successfully",
            ];

            session()->flash('message', $response['message']);
            }

        } catch (\Exception $e) {
            // Catch any exceptions and set the error message
            $response['message'] = $e->getMessage();
            return response()->json($response, 500);  // Return error response if there's an exception
        }

        return response()->json($response, 200);
    }
    public function updateSupplier($supplierUuid, $data)
    {
        $supplier = $this->getSupplier($supplierUuid);
        $response = [
            "error" => true,
            "message" => "Something went wrong",
        ];

        try {
            if (request()->hasFile('profile_file')) {
                $file = request()->file('profile_file');
                $path = $this->storeMediaByHashName($file, "/supplier/attachments/" . $supplierUuid);
                $data['profile_picture'] = $path; // Save the file path in the data array
            }

            $supplier->update($data);

            if (isset($data['itemSold'])) {
                $supplier->categories()->detach();

                foreach ($data['itemSold'] as $item) {
                    if (!isset($item['created_at'])) {
                        $category = Categories::create([
                            'name' => $item['name'],
                            'company_id' => $item['company_id'],
                        ]);
                        $supplier->categories()->attach($category->id);
                    } else {
                        $supplier->categories()->attach($item['id']);
                    }
                }
            }

            $response = [
                "error" => false,
                "message" => "Supplier updated successfully",
            ];
            session()->flash('message', $response['message']);

        } catch (\Exception $e) {
            // Catch any exceptions and set the error message
            $response['message'] = $e->getMessage();
            return response()->json($response, 500);  // Return error response if there's an exception
        }

        return response()->json($response, 200);
    }

    public function deleteSupplierEntry($request)
    {
        $uuid = $request->uuid;
        $supplierEntry = $this->model::findOrFailByUuid($uuid);
        return $supplierEntry->delete();
    }



}
