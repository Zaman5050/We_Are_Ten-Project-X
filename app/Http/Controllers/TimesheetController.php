<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Timesheet\AddEntryTimeRequest;
use App\Interfaces\ServiceInterfaces\Tenant\TimesheetServiceInterface;

class TimesheetController extends Controller
{
    private $service; 

    public function __construct( TimesheetServiceInterface $TimesheetService)
    {
        $this->service = $TimesheetService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->service->listing($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(AddEntryTimeRequest $request)
    {
        return $this->service->addEntryTime($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->service->timeLogged($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return $this->service->updateTimeLogged($request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        return $this->service->deleteTimeEntry($request);
    }

    public function updateTimerStatus(Request $request)
    {
        return $this->service->updateTimerStatus($request);
    }
}
