<?php

namespace App\Repositories\Tenant;

use App\Interfaces\RepositoryInterfaces\Tenant\ProjectAreaRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\ProjectArea;
use App\Models\Project;

class ProjectAreaRepository implements ProjectAreaRepositoryInterface

{
    protected $model;

    public function __construct(ProjectArea $projectArea)
    {
        $this->model = $projectArea;
    }

    public function getProjectAreasAndMeasurementUnit($projectUuid)
    {
        $data['projectAreas'] = $this->model::whereProjectUuid($projectUuid)->get();
        $data['measurementUnit'] = Project::findOrFailByUuid($projectUuid)?->measurement_unit;
        return $data;
    }

    public function createOrUpdateProjectAreas($projectUuid, array $data)
    {

        DB::beginTransaction();
        try
        {
            $project = Project::findOrFailByUuid($projectUuid);
                // Loop through each area and either create or update
            foreach ($data['areas'] as $row) {
                $this->model::updateOrCreate(
                    ['uuid' => $row['uuid']], // Find by UUID
                    [
                        'project_id' => $project->id,
                        'project_uuid' => $project->uuid,
                        'area_name' => $row['area_name'],
                        'area_dimention' => $row['area_dimention'],
                        'area_code' => $row['area_code']
                    ]
                );
            }

        }
        catch( Exception $e )
        {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ],500);
        }
        DB::commit();
        return response()->json([
            'message' => 'Project Area is created successfully.',
            'alert-type' => 'success',
        ],200);

    }

    public function deleteProjectAreas($projectAreaUuid)
    {
        DB::beginTransaction();
        try
        {
            $this->model::where('uuid',$projectAreaUuid)->delete();

        }
        catch( Exception $e )
        {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ],500);
        }
        DB::commit();
        return response()->json([
            'message' => 'Project Area is deleted successfully.',
            'alert-type' => 'success',
        ],200);

    }

}