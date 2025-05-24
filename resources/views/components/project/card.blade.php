<div class="d-flex gap-3 flex-column mb-2">

    <h6 class="px-3 text-uppercase text-secondary">{{ $project->type }}</h6>

    <div style="height: 100%" class="project-listing-main-container">
        <p class="box-text mb-2">{{ $project->catagory }}</p>
        <div style="height: 100%" class="project-listing-box d-flex flex-column justify-content-between">
            <div class="">


                <div class="text-end mb-3">
                    <div class="dropdown three-dots-dropdown">
                        <a style="width: fit-content; margin-left:  auto;"
                            class="d-flex justify-content-end filter-btn border-0 dropdown-toggle gap-3" href="#"
                            role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ asset('assets/images/three-dots.svg') }}">
                        </a>
                        <ul style="width: 100%; min-width: 138px; padding: 15px 18px; box-shadow: 0px 1px 2px 0px #5B687152; box-shadow: 0px 0px 2px 0px #1A202452;"
                            class="dropdown-menu border-0" aria-labelledby="dropdownMenuLink">
                            <li><a class="dropdown-item mb-2"
                                    href="{{ tenant_route('projects.details', ['project' => $project->uuid]) }}">Edit</a>
                            </li>
                            <li>
                                <form action="{{ tenant_route('projects.clone', ['project' => $project->uuid]) }}"
                                    method="POST">
                                    @csrf
                                    <button class="dropdown-item mb-2" type="submit">Duplicate</button>
                                </form>
                            </li>
                            {{-- <li><a class="dropdown-item mb-2" href="#">Duplicate</a></li> --}}
                            <li>
                                <form action="{{ tenant_route('projects.destroy', ['project' => $project->uuid]) }}"
                                    method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button style="color: #F8624E;" class="dropdown-item mb-2"
                                        type="submit">Delete</button>
                                </form>
                            </li>
                        </ul>
                    </div>

                </div>


                <a href="{{ $project->status !== 'draft' ? tenant_route('projects.show', ['project' => $project->uuid]) : 'javascript:void(0)' }}" 
                            @if($project->status === 'draft') data-bs-toggle="modal" data-bs-target="#create-new-project" @endif>
                    @if ($project?->attachment)
                        <img class="project-list-img" src="{{ $project?->attachment?->media_url }}">
                    @else
                        <img class="project-list-img" src="{{ asset('assets/images/project-list-default.svg') }}">
                    @endif
                </a>
                <div class="d-flex align-items-start">
                    <img class="me-2" src="{{ asset('assets/images/tag-icon.svg') }}">
                    <div class="mb-3">
                        @foreach ($project->tags as $tag)
                            <span style="background: #BD94F2DE;" class="tags me-2">{{ $tag->tag_name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="">
                <div class="mb-3 d-flex justify-content-between gap-3 align-items-center">
                    <p style="font-size: 16px; color:#1A1B1F;">
                        {{ $project->name }}
                    </p>
                    <div></div>
                    @if (count($project->stages) > 0)
                        <span class="tags">{{ $project->stages[0]->stage_name }}</span>
                    @endif
                </div>
                <div class="mb-3 d-flex justify-content-between gap-3">
                    <div style="font-size: 16px;" class="d-flex ">
                        <p>Date Created:</p><span class="text-secondary"
                            style="color: #000">{{ \Carbon\Carbon::parse($project->created_at)->format('d/m/Y')  }}</span>
                    </div>
                    <div class="member-img-icon-container d-flex justify-content-end">
                        <member-avatar-list :users="{{ $project->members }}" :size="{{ count($project->members) }}"
                            :uindex="{{ $project->id }}"></member-avatar-list>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
