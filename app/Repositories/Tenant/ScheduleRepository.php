<?php

namespace App\Repositories\Tenant;

use App\Interfaces\RepositoryInterfaces\Tenant\ScheduleRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\MaterialLibrary;
use App\Models\ProjectMaterial;
use App\Models\SupplierLibrary;
use App\Models\Categories;
use App\Models\Project;

class ScheduleRepository implements ScheduleRepositoryInterface
{
    protected $model;

    public function __construct(ProjectMaterial $schedule)
    {
        $this->model = $schedule;
    }

    public function getSchedules(string $projectUuid)
    {
        return $this->model::where('company_id', auth()->user()->company_id)->where('project_id', Project::findOrFailByUuid($projectUuid)->id)->with('material', 'areas')->latest()->get()->groupBy(function ($projectMaterial) {
            return $projectMaterial->material->category->name;
        });
    }

    public function storeSchedule(string $subdomain, string $projectUuid, array $data)
    {
        DB::beginTransaction();
        try {
            $material = $this->saveMaterial($data);
            $schedule = $this->model::create([
                ...$data,
                'company_id' => $material->company_id,
                'material_library_id' => $material->id,
                'project_id' => Project::findOrFailByUuid($projectUuid)->id,
            ]);
            $schedule->areas()->sync($data['project_areas']);
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
            'schedule' => $schedule->load('material'),
        ], 200);
    }

    public function getMaterials(string $projectUuid)
    {
        // returning only materials from library which are not in schedule
        return MaterialLibrary::where('company_id', auth()->user()->company_id)
            ->whereNotIn('id', $this->model::where(function ($query) use ($projectUuid) {
                $query->whereNotNull('project_id')
                    ->Where('project_id', Project::where('uuid', $projectUuid)->value('id'));
            })->pluck('material_library_id'))
            ->with('category', 'supplier', 'cover_image')->latest()->get()->groupBy(function ($material) {
                return $material->category ? $material->category->name : '';
            });
    }

    public function addScheduleFromLibrary($projectUuid, $data)
    {
        DB::beginTransaction();
        try {
            $companyId = auth()->user()->company_id;
            $projectId = Project::findOrFailByUuid($projectUuid)->id;
            
                $schedule = $this->model::create(
                    ['company_id' => $companyId, 'project_id' => $projectId, 'material_library_id' => $data['material_library_id']]
                );
                $schedule->areas()->sync($data['project_areas']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ], 500);
        }
        DB::commit();
        return response()->json([
            'message' => 'Schedule is added successfully.',
            'alert-type' => 'success',
            'schedule' => $schedule->load('material'),
        ], 200);
    }

    public function updateSchedule(string $scheduleUuid, array $data)
    {
        DB::beginTransaction();
        try {
            if (is_null($data['category_id'])) {
                $data['category_id'] = $data['supplier']['category'];
            }
            if (is_null($data['supplier_library_id'])) {
                // Adding Supplier if it's new
                $supplier = SupplierLibrary::create([
                    ...$data['supplier'],
                    'company_id' => $data['company_id'],
                ]);
                $supplier->categories()->attach($supplier->pluck('id')); // assigning category to supplier
                $data['supplier_library_id'] = $supplier->id;
            }
            // Saving Custom Material in Material Library and assigning it to project in Project Material Library
            $material = MaterialLibrary::findByUuid($data['material_uuid']);
            $material->update($data);
            // dd($material);
            $material->specifications()->delete();
            foreach ($data['custom_specifications'] as $customSpecification) {
                $material->specifications()->create($customSpecification);
            }
            $material->attachments()->delete();
            // Saving Attachments in Material Library and assigning it to project in Project Material Library
            foreach ($data['attachments'] as $attachment) {
                $baseUrl = config('filesystems.disks.s3.url');
                $attachment['path'] = str_replace($baseUrl . '/', '', $attachment['url']);
                $material->attachments()->create($attachment);
            }
            $material->cover_image()->delete();
            foreach ($data['cover_image'] as $image) {
                $baseUrl = config('filesystems.disks.s3.url');
                $image['path'] = str_replace($baseUrl . '/', '', $image['url']);
                $image['label'] = 'product_image';
                $material->attachments()->create($image);
            }
            $schedule = $this->model::findByUuid($scheduleUuid);
            $schedule->update($data);
            $schedule->areas()->sync($data['project_areas']);
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
            'schedule' => $schedule->load('material'),
        ], 200);
    }

    public function getAllMaterials()
    {
        // returning only materials from library which are not in schedule
        return MaterialLibrary::where('company_id', auth()->user()->company_id)->with('category', 'supplier', 'specifications', 'attachments', 'cover_image')->latest()->get();
    }

    public function updateMaterial(string $materialUuid, array $data)
    {
        DB::beginTransaction();
        try {
            if (is_null($data['category_id'])) {
                // Adding Category if it's new
                $category = Categories::create([
                    'name' => $data['category'],
                    'company_id' => $data['company_id']
                ]);
                $data['category_id'] = $category->id;
            }
            if (is_null($data['supplier_library_id'])) {
                // Adding Supplier if it's new
                $supplier = SupplierLibrary::create([
                    ...$data['supplier'],
                    'company_id' => $data['company_id'],
                ]);
                $supplier->categories()->attach($supplier->pluck('id')); // assigning category to supplier
                $data['supplier_library_id'] = $supplier->id;
            }
            // Saving Custom Material in Material Library and assigning it to project in Project Material Library
            $material = MaterialLibrary::findByUuid($materialUuid);
            $material->update($data);
            $material->specifications()->delete();
            foreach ($data['custom_specifications'] as $customSpecification) {
                $material->specifications()->create($customSpecification);
            }
            $material->attachments()->delete();
            // Saving Attachments in Material Library and assigning it to project in Project Material Library
            foreach ($data['attachments'] as $attachment) {
                $baseUrl = config('filesystems.disks.s3.url');
                $attachment['path'] = str_replace($baseUrl . '/', '', $attachment['url']);
                $material->attachments()->create($attachment);
            }
            $material->cover_image()->delete();
            foreach ($data['cover_image'] as $image) {
                $baseUrl = config('filesystems.disks.s3.url');
                $image['path'] = str_replace($baseUrl . '/', '', $image['url']);
                $image['label'] = 'product_image';
                $material->attachments()->create($image);
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
            'schedule' => $material->load('category', 'supplier', 'specifications', 'attachments', 'cover_image'),
        ], 200);
    }

    public function deleteMaterial(string $materialUuid)
    {
        DB::beginTransaction();
        try {
            MaterialLibrary::findOrFailByUuid($materialUuid)->delete();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        DB::commit();
    }

    public function storeMaterial(array $data)
    {
        DB::beginTransaction();
        try {
            $material = $this->saveMaterial($data);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e);
        }
        DB::commit();
        return $material->with('category', 'supplier', 'specifications', 'attachments', 'cover_image');
    }

    private function saveMaterial($data)
    {
        try {
            $data['company_id'] = auth()->user()->company_id;
            if (is_null($data['category_id'])) {
                $data['category_id'] = $data['supplier']['category'];
            }
            if (is_null($data['supplier_library_id'])) {
                // Adding Supplier if it's new
                $supplier = SupplierLibrary::create([
                    ...$data['supplier'],
                    'company_id' => $data['company_id'],
                ]);
                $supplier->categories()->attach($data['category_id']); // assigning category to supplier
                $data['supplier_library_id'] = $supplier->id;
            }
            // Saving Custom Material in Material Library and assigning it to project in Project Material Library
            $material = MaterialLibrary::create($data);
            foreach ($data['custom_specifications'] as $customSpecification) {
                $material->specifications()->create($customSpecification);
            }
            // Saving Attachments in Material Library and assigning it to project in Project Material Library
            foreach ($data['attachments'] as $attachment) {
                $baseUrl = config('filesystems.disks.s3.url');
                $attachment['path'] = str_replace($baseUrl . '/', '', $attachment['url']);
                $material->attachments()->create($attachment);
            }
            foreach ($data['cover_image'] as $image) {
                $baseUrl = config('filesystems.disks.s3.url');
                $image['path'] = str_replace($baseUrl . '/', '', $image['url']);
                $image['label'] = 'product_image';
                $material->attachments()->create($image);
            }
            return $material;
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }
}
