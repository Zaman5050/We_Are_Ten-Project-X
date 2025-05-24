<?php

namespace App\Repositories\Tenant;

use App\Interfaces\RepositoryInterfaces\Tenant\ProcurementRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\ProductLibrary;
use App\Models\Procurement;
use App\Models\SupplierLibrary;
use App\Models\Categories;
use App\Models\ProcurementBudget;
use App\Models\Project;
use App\Models\ProjectArea;
use Illuminate\Support\Arr;

class ProcurementRepository implements ProcurementRepositoryInterface
{
    protected $model;

    public function __construct(Procurement $procurement)
    {
        $this->model = $procurement;
    }

    public function getProcurements(string $projectUuid)
    {
        return $this->model::where('company_id', auth()->user()->company_id)->where('project_id', Project::findOrFailByUuid($projectUuid)->id)->with(['product', 'ProjectArea', 'baseCurrency', 'exchangeCurrency'])->latest()->get()->groupBy(function ($procurement) {
            return $procurement?->projectArea?->area_name;
        });
    }

    public function storeProcurement(string $subdomain, string $projectUuid, array $data)
    {
        DB::beginTransaction();
        try {
            $data['company_id'] = auth()->user()->company_id;
            $project_id = Project::findOrFailByUuid($projectUuid)->id;
            if (is_null($data['supplier_library_id'])) {
                $supplier = SupplierLibrary::create([
                    ...$data['supplier'],
                    'company_id' => $data['company_id'],
                ]);

                if (!empty($data['supplier_category_id'])) {
                    $supplier->categories()->attach(collect($data['supplier_category_id'])->pluck('id'));
                }

                $data['supplier_library_id'] = $supplier->id;
            }

            if (is_null($data['project_area_id'])) {
                $area = ProjectArea::create([
                    ...$data['area'],
                    'company_id' => $data['company_id'],
                    'project_id' => $project_id,
                    'project_uuid' => $projectUuid,
                ]);
                $data['project_area_id'] = $area->id;
            }
            $data = [
                ...$data,
                ...$this->calculateFinancialData($data)
            ];
            // Saving Custom Material in Material Library and assigning it to project in Project Material Library
            $product = ProductLibrary::create($data);
            foreach ($data['custom_specifications'] as $customSpecification) {
                $product->specifications()->create($customSpecification);
            }
            // Saving Attachments in Material Library and assigning it to project in Project Material Library
            foreach ($data['attachments'] as $attachment) {
                $baseUrl = config('filesystems.disks.s3.url');
                $attachment['path'] = str_replace($baseUrl . '/', '', $attachment['url']);
                $product->attachments()->create($attachment);
            }
            foreach ($data['cover_image'] as $coverImage) {
                $baseUrl = config('filesystems.disks.s3.url');
                $coverImage['path'] = str_replace($baseUrl . '/', '', $coverImage['url']);
                $coverImage['label'] = 'product_image';
                $product->attachments()->create($coverImage);
            }

            $procurement = $this->model::create([
                ...$data,
                'product_library_id' => $product->id,
                'project_id' => $project_id,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ], 500);
        }
        DB::commit();
        return response()->json([
            'message' => 'Schedule is created successfully.',
            'alert-type' => 'success',
            'procurement' => $procurement->loadMissing('product'),
        ], 200);
    }

    public function getProducts($projectUuid)
    {
        $projectId = Project::where('uuid', $projectUuid)->value('id');
        $categories = ProcurementBudget::where('project_id', $projectId)
            ->pluck('category_id');
        return ProductLibrary::where('company_id', auth()->user()->company_id)
            ->whereNotIn('id', $this->model::where(function ($query) use ($projectId) {
                $query->whereNotNull('project_id')
                    ->where('project_id', $projectId);
            })->pluck('product_library_id'))
            ->whereIn('category_id', $categories)
            ->with('category', 'supplier', 'cover_image', 'attachments')->latest()->get()->groupBy(function ($product) {
                return $product->category->name;
            });
    }

    public function addProcurementFromLibrary($projectUuid, $data)
    {
        DB::beginTransaction();
        try {
            $companyId = auth()->user()->company_id;
            $project = Project::findOrFailByUuid($projectUuid);
            if (is_null($data['project_area_id'])) {
                $area = ProjectArea::create([
                    ...$data['area'],
                    'company_id' => $companyId,
                    'project_id' => $project?->id,
                    'project_uuid' => $project?->uuid,
                ]);
                $data['project_area_id'] = $area->id;
            }
            $procurement = $this->model::create(
                [
                    ...$data,
                    ...$this->calculateFinancialData($data),
                    'company_id' => $companyId,
                    'project_id' => $project?->id,
                    'project_area_id' => $data['project_area_id'],
                ]
            );
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ], 500);
        }
        DB::commit();
        return response()->json([
            'message' => 'Procurement is added successfully.',
            'alert-type' => 'success',
            'proc$procurement' => $procurement->load('product'),
        ], 200);
    }

    public function updateProcurement(string $procurementUuid, array $data)
    {
        DB::beginTransaction();
        try {
            if (is_null($data['supplier_library_id'])) {
                // Adding Supplier if it's new
                $supplier = SupplierLibrary::create([
                    ...$data['supplier'],
                    'company_id' => auth()->user()->company_id,
                ]);
                if ($data['supplier_category_id']) $supplier->categories()->attach($data['supplier_category_id']);
                $data['supplier_library_id'] = $supplier->id;
            }

            $data = [
                ...$data,
                ...$this->calculateFinancialData($data)
            ];
            // Saving Custom Material in Material Library and assigning it to project in Project Material Library
            $product = ProductLibrary::findByUuid($data['product_uuid']);
            $product->update($data);
            $product->specifications()->delete();
            foreach ($data['custom_specifications'] as $customSpecification) {
                $product->specifications()->create($customSpecification);
            }

            $product->attachments()->delete();
            // Saving Attachments in Material Library and assigning it to project in Project Material Library
            foreach ($data['attachments'] as $attachment) {
                $baseUrl = config('filesystems.disks.s3.url');
                $attachment['path'] = str_replace($baseUrl . '/', '', $attachment['url']);
                $product->attachments()->create($attachment);
            }
            $product->cover_image()->delete();
            foreach ($data['cover_image'] as $coverImage) {
                $baseUrl = config('filesystems.disks.s3.url');
                $coverImage['path'] = str_replace($baseUrl . '/', '', $coverImage['url']);
                $coverImage['label'] = 'product_image';
                $product->attachments()->create($coverImage);
            }
            unset($data['project_id']);
            $procurement = $this->model::findByUuid($procurementUuid);
            if (is_null($data['project_area_id'])) {
                $area = ProjectArea::create([
                    ...$data['area'],
                    'company_id' => auth()->user()->company_id,
                    'project_id' => $procurement->project_id,
                    'project_uuid' => request()->project,
                ]);
                $data['project_area_id'] = $area->id;
            }
            $procurement->update($data);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ], 500);
        }
        DB::commit();
        return response()->json([
            'message' => 'Schedule is created successfully.',
            'alert-type' => 'success',
            'procurement' => $procurement->loadMissing('product'),
        ], 200);
    }

    public function getAllProducts()
    {
        return ProductLibrary::where('company_id', auth()->user()->company_id)
            ->with('category', 'supplier', 'specifications', 'attachments', 'cover_image')->latest()->get();
    }


    public function updateProduct(string $productUuid, array $data)
    {
        DB::beginTransaction();
        try {
            if (is_null($data['category_id'])) {
                // Adding Category if it's new
                $category = Categories::create([
                    'name' => $data['category'],
                    'company_id' => auth()->user()->company_id,
                ]);
                $data['category_id'] = $category->id;
            }
            if (is_null($data['supplier_library_id'])) {
                // Adding Supplier if it's new
                $supplier = SupplierLibrary::create([
                    ...$data['supplier'],
                    'company_id' => auth()->user()->company_id,
                ]);
                $supplier->categories()->attach($supplier->pluck('id')); // assigning category to supplier
                $data['supplier_library_id'] = $supplier->id;
            }
            // Saving Custom Material in Material Library and assigning it to project in Project Material Library
            $product = ProductLibrary::findByUuid($productUuid);
            $product->update($data);
            $product->specifications()->delete();
            foreach ($data['custom_specifications'] as $customSpecification) {
                $product->specifications()->create($customSpecification);
            }

            $product->attachments()->delete();
            // Saving Attachments in Material Library and assigning it to project in Project Material Library
            foreach ($data['attachments'] as $attachment) {
                $baseUrl = config('filesystems.disks.s3.url');
                $attachment['path'] = str_replace($baseUrl . '/', '', $attachment['url']);
                $product->attachments()->create($attachment);
            }
            $product->cover_image()->delete();
            foreach ($data['cover_image'] as $coverImage) {
                $baseUrl = config('filesystems.disks.s3.url');
                $coverImage['path'] = str_replace($baseUrl . '/', '', $coverImage['url']);
                $coverImage['label'] = 'product_image';
                $product->attachments()->create($coverImage);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ], 500);
        }
        DB::commit();
        return response()->json([
            'message' => 'Schedule is created successfully.',
            'alert-type' => 'success',
            'procurement' => $product->loadMissing('category', 'supplier', 'specifications', 'attachments', 'cover_image'),
        ], 200);
    }

    public function deleteProduct(string $productUuid)
    {
        DB::beginTransaction();
        try {
            ProductLibrary::findOrFailByUuid($productUuid)->delete();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        DB::commit();
    }

    private function calculateFinancialData(array $data)
    {
        $response['trade_price'] = (floatval($data['retail_price']) - (floatval($data['retail_price']) * (floatval($data['trade_discount']) / 100))) ?? 0;
        $response['actual_price'] = ($response['trade_price'] + ($data['markup'] === "" ? 0 : $response['trade_price'] * ($data['markup'] / 100))) ?? 0;
        $response['total_exclusive_tax'] = ($response['actual_price'] * $data['quantity']) ?? 0;
        $response['total_inclusive_tax'] = ($response['total_exclusive_tax'] + $response['total_exclusive_tax'] * (5 / 100))  ?? 0;
        $response['profit'] =  (($response['actual_price'] - $response['trade_price']) * $data['quantity']) ?? 0;
        return $response;
    }
    public function deleteProcurement(string $procurementUuid)
    {
        DB::beginTransaction();
        try {
            Procurement::findOrFailByUuid($procurementUuid)->delete();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        DB::commit();
    }
    public function duplicateProcurement(string $procurementUuid)
    {
        DB::beginTransaction();
        try {

            $originalProcurement = Procurement::with('product', 'product.attachments', 'product.specifications')
                ->where('uuid', $procurementUuid)
                ->firstOrFail();
            $categoryRemaining = ProcurementBudget::where('project_id', $originalProcurement->project_id)->where('category_id', $originalProcurement->product->category_id)->first();
            if ($categoryRemaining->remainingQuantity <= 0) {
                return null;
            }
            $cloneProduct =     $originalProcurement->product;
            unset($cloneProduct['id'], $cloneProduct['uuid'], $cloneProduct['created_at'], $cloneProduct['updated_at']);

            unset(
                $originalProcurement['id'],
                $originalProcurement['uuid'],
                $originalProcurement['created_at'],
                $originalProcurement['updated_at'],
                $originalProcurement['product_library_id'],
                $originalProcurement['exchange_rate'],
                $originalProcurement['retail_price'],
                $originalProcurement['trade_discount'],
                $originalProcurement['trade_price'],
                $originalProcurement['actual_price'],
                $originalProcurement['trade_terms'],
                $originalProcurement['markup'],
                $originalProcurement['total_exclusive_tax'],
                $originalProcurement['total_inclusive_tax'],
                $originalProcurement['profit']
            );

            $originalProcurement['exchange_rate'] = 0;
            $originalProcurement['retail_price']  = 0;
            $originalProcurement['trade_discount'] = 0;
            $originalProcurement['trade_price'] = 0;
            $originalProcurement['actual_price'] = 0;
            $originalProcurement['trade_terms'] = 0;
            $originalProcurement['markup'] = 0;
            $originalProcurement['total_exclusive_tax'] = 0;
            $originalProcurement['total_inclusive_tax'] = 0;
            $originalProcurement['profit'] = 0;
            $cloneProduct['product_name'] = $cloneProduct['product_name'] . ' (Replica)';

            $product = $cloneProduct->replicate();
            $product->save();

            foreach ($cloneProduct->specifications as $specification) {
                $product->specifications()->create($specification->toArray());
            }

            foreach ($cloneProduct->attachments as $attachment) {
                $baseUrl = config('filesystems.disks.s3.url');
                $attachment['path'] = str_replace($baseUrl . '/', '', $attachment['url']);
                $product->attachments()->create($attachment->toArray());
            }


            $duplicatedProcurement = $originalProcurement->replicate();
            $duplicatedProcurement->product_library_id = $product->id;
            $duplicatedProcurement->save();

            DB::commit();
            return $duplicatedProcurement;
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ], 500);
        }
    }

    public function updateStatusProcurement(string $procurementUuid, string $status)
    {
        DB::beginTransaction();
        try {
            $procurement = $this->model::findByUuid($procurementUuid);

            $procurement->update([
                'status' => $status,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ], 500);
        }

        DB::commit();

        return response()->json([
            'message' => 'Procurement status updated successfully.',
            'alert-type' => 'error',
        ], 200);
    }

    public function updateProcurementMarkup(string $procurementUuid, array $data)
    {
        DB::beginTransaction();
        try {
            $procurement = $this->model::findByUuid($procurementUuid);
            $response = $this->calculateFinancialData([...$procurement->toArray(), 'markup' => $data['markup']]);
            $procurement->update([...$response, 'markup' => $data['markup']]);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e);
        }
        DB::commit();
        return $response;
    }

    public function storeProduct(array $data)
    {
        DB::beginTransaction();
        try {
            $data['company_id'] = auth()->user()->company_id;
            if (is_null($data['supplier_library_id'])) {
                // Adding Supplier if it's new
                $supplier = SupplierLibrary::create([
                    ...$data['supplier'],
                    'company_id' => $data['company_id'],
                ]);
                if ($data['supplier_category_id']) $supplier->categories()->attach($data['supplier_category_id']); // assigning category to supplier
                $data['supplier_library_id'] = $supplier->id;
            }

            $data = [
                ...$data,
                ...$this->calculateFinancialData($data)
            ];
            // Saving Custom Material in Material Library and assigning it to project in Project Material Library
            $product = ProductLibrary::create($data);
            foreach ($data['custom_specifications'] as $customSpecification) {
                $product->specifications()->create($customSpecification);
            }
            // Saving Attachments in Material Library and assigning it to project in Project Material Library
            foreach ($data['attachments'] as $attachment) {
                $baseUrl = config('filesystems.disks.s3.url');
                $attachment['path'] = str_replace($baseUrl . '/', '', $attachment['url']);
                $product->attachments()->create($attachment);
            }
            foreach ($data['cover_image'] as $coverImage) {
                $baseUrl = config('filesystems.disks.s3.url');
                $coverImage['path'] = str_replace($baseUrl . '/', '', $coverImage['url']);
                $coverImage['label'] = 'product_image';
                $product->attachments()->create($coverImage);
            }
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e);
        }
        DB::commit();
        return $product->loadMissing('category', 'supplier', 'specifications', 'attachments', 'cover_image');
    }
}
