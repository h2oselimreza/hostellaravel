<div class="sidebar-nav" id="sideNavBar">
    <ul class="nav flex-column">

        @foreach($moduleGroups as $group)

    @php
        //@dd(request()->path(), $m['module_url']);
        $isActiveGroup = collect($group['modules'])->contains(fn($m) => str_starts_with(request()->path(), ltrim($m['module_url'], '/')));
        //@dd($isActiveGroup);
    @endphp

            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center {{ $isActiveGroup ? '' : 'collapsed' }}"
                   data-bs-toggle="collapse"
                   href="#{{ $group['module_group_code'] }}"
                   role="button"
                   aria-expanded="{{ $isActiveGroup ? 'true' : 'false' }}"
                   aria-controls="{{ $group['module_group_code'] }}">

                    <span>
                        <i class="fa fa-fw fa-dashboard me-2"></i>
                        {{ $group['module_group_name'] }}
                    </span>

                    <i class="fa fa-chevron-down small"></i>
                </a>

                {{-- Modules --}}
                <div class="collapse {{ $isActiveGroup ? 'show' : '' }}"
                     id="{{ $group['module_group_code'] }}">

                    <ul class="nav flex-column ms-3">

                        @foreach($group['modules'] as $module)

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is($module['module_url'] . '*') ? 'active fw-bold text-primary' : '' }}"
                                   href="{{ url($module['module_url']) }}">

                                    <i class="fa fa-caret-right me-2"></i>
                                    {{ $module['modules_name'] }}
                                </a>
                            </li>

                        @endforeach

                    </ul>
                </div>
            </li>

        @endforeach

    </ul>
</div>