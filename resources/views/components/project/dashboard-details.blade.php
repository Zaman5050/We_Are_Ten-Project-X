@props([
    'project' => null,
])
<div class="project-name-dashboard-mini-left d-flex flex-column justify-content-between">
    <div class="d-flex gap-2">
        <div style="width:70%">
            <div class="d-flex align-items-center gap-2 mb-4">
                @if ($project->owner?->profile_picture)
                    <img style="width: 24px; height: 24px; object-fit: cover; border-radius: 8px;"
                        src="{{ $project->owner->profile_picture }}">
                @else
                    <img style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px;"
                        src="{{ asset('assets/images/profile.png') }}">
                @endif
                <span>
                    <p style="font-size: 14px;margin-bottom: 4px;" class="text-decoration-underline darker-grotesque-bold">
                        Project Owner</p>
                    <p style="font-size: 16px;">{{ $project->owner?->full_name }}</p>
                </span>
            </div>
            <div class="project-name-mini-description">
                <p style="font-size: 14px;margin-bottom: 4px;" class="text-decoration-underline darker-grotesque-bold">
                    Description</p>
                <span style="font-size: 16px; color: #1C1C1C66;">{{ $project->description }} </span>
            </div>
        </div>
        <div style="width:30%;">
            <p style="font-size: 14px;margin-bottom: 10px;" class="text-decoration-underline darker-grotesque-bold">
            Team Members</p>
            <div class="member-img-icon-container d-flex ">
            @if (count($project->members) > 0)
                        <member-avatar-list :users="{{ $project->members }}" :size="{{ count($project->members) }}"
                            :uindex="{{ $project->id }}"></member-avatar-list>
            @endif
                    </div>
        </div>
    </div>
    <div class="d-flex justify-content-between gap-2">
        <div >
            <div class="w-100">
                <div class="d-flex dashboard-tags-container mb-2 align-items-center w-100">
                    <img class="me-2 tag-icon-img" src="{{ asset('assets/images/tag-icon.svg') }}">
                    <div class="d-flex flex-wrap gap-2 me-2">
                        @if (count($project->tags) > 0)
                            @foreach ($project->tags as $tag)
                                <span class="tags ">{{ $tag->tag_name }}</span>
                            @endforeach
        
                        @endif
                    </div>
                </div>
                <div class="d-flex dashboard-tags-container align-items-center w-100">
                    <p style="font-size: 14px;margin-bottom: 4px;" class="text-decoration-underline darker-grotesque-bold me-2">
                    Stages</p>
                    <div class="d-flex flex-wrap gap-2 me-2">
                        @if (count($project->stages) > 0)
                            <span class="tags dashboard-blue-tag">{{ $project->stages[0]->stage_name ?? 'Stage 12' }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="project-name-dashboard-mini-right">
    <div class="row">
        <div class="col-5 p-0">
            <div class="container mb-4"
                style="display: flex; align-items: center;justify-content: center; gap:10px;position: relative;">
                <div class="circle x-small" data-fill="{{ intval($project->project_logged_time['percentage']) }}"
                    style="--color: #252C32;font-size: 89px;">
                    <div class="bar"></div>
                </div>
                <span style="position: absolute;  top: 34px; font-size: 9.05px;text-align: center; width: 61px; ">
                    <span class="inter-Regular">{{ $project->project_logged_time['timeLogged'] }}</span>
                </span>
            </div>
            <div class="start-date text-center">
                <p style="font-size: 16px;color: #252C32;">Starts</p>
                <p style="font-size: 16px;color: #252C32;">{{ $project->start_date ? formatDate($project->start_date) : "--" }}</p>
                <span style="font-size: 16px;color: #ACACAC">{{ $project->start_date ? getYearFromDate($project->start_date) : ""  }}</span>
            </div>
        </div>
        <div class="col-2 p-0">
            <hr style="    margin-top: 137px;">
        </div>
        <div class="col-5 p-0">
            <div class="container mb-4"
                style="display: flex; align-items: center;justify-content:center; gap:10px;position: relative;">
                <div class="circle x-small" data-fill="{{ intval($project->project_completion_percentage) }}"
                    style="--color: #252C32;font-size: 89px;">
                    <div class="bar"></div>
                </div>
                <span style="position: absolute; font-size: 28px;top: 24px; width:70px; text-align:center">
                    <span style="font-size: 22px;"
                        class="inter-Regular">{{ intval($project->project_completion_percentage) }}%</span>
                </span>
            </div>
            <div class="end-date text-center">
                <p style="font-size: 16px;color: #252C32;">Ends</p>
                <p style="font-size: 16px;color: #252C32;">{{ $project->due_date ? formatDate($project->due_date) : "--" }}</p>
                <span style="font-size: 16px;color: #ACACAC">{{ $project->due_date ? getYearFromDate($project->due_date) : ""  }}</span>

            </div>
        </div>
    </div>

    @if (isFirstDateLessThanSecondDate(null, $project->due_date) && getDateDifference(null, $project->due_date) > 0)
    <div style="font-size: 16px; color: #F8624E;" class="text-center">
        Project is {{ getDateDifference(null, $project->due_date) }} days late!
    </div>
@endif

</div>
<div class="people-on-leave">
    <h5>People On Leave Today</h5>
    <ol>
        @if (count($project->membersOnLeave) > 0)
            @foreach ($project->membersOnLeave as $member)
                <li>{{ $member->full_name }}</li>
            @endforeach
        @else
            <li>N/A</li>
        @endif
    </ol>
</div>
