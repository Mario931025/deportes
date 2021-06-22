<!-- sidebar-->
<aside class="aside-container">
    <!-- START Sidebar (left)-->
    <div class="aside-inner">
        <nav class="sidebar" data-sidebar-anyclick-close="">
            <!-- START sidebar nav-->
            <ul class="sidebar-nav">
                <!-- START user info-->
                <li class="has-user-block">
                    <div class="collapse" id="user-block">
                        <div class="item user-block">
                            <!-- User picture-->
                            <div class="user-block-picture">
                                <div class="user-block-status">
                                    <img class="img-thumbnail rounded-circle" src="{{ Auth::user()->profile_photo ? url('storage/'. Auth::user()->profile_photo) : asset('angle/img/default-user-profile.png') }}" alt="Avatar" width="60" height="60">
                                    <div class="circle bg-success circle-lg"></div>
                                </div>
                            </div>
                            <!-- Name and Job-->
                            <div class="user-block-info"><span class="user-block-name">{{ Auth::user()->name }}</span><span class="user-block-role"></span></div>
                        </div>
                    </div>
                </li>
                <!-- END user info-->
                <!-- Iterates over all sidebar items-->
                <li class="nav-heading"><span>{{-- __('Main Navigation') --}}</span></li>
                
                <li class="{{ request()->routeIs('admin.home') ? 'active' : '' }}">
                    <a href="{{ route('admin.home') }}" title="{{ __('Main') }}">
                        <em class="fa fa-columns"></em><span>{{ __('Main') }}</span>
                    </a>
                </li>
                
                @if (auth()->user()->hasAnyRole(['instructor', 'country-manager','latam-manager','admin']))
                    <li class="{{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.students.index') }}" title="{{ __('Students') }}">
                            <em class="fa fa-user-graduate"></em><span>{{ __('Students') }}</span>
                        </a>
                    </li>
                @endif
                
                <li class="{{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.promotions.index') }}" title="{{ __('Promotions') }}">
                        <em class="fas fa-arrow-up"></em><span>{{ __('Promotions') }}</span>
                    </a>
                </li>
                
                @if (auth()->user()->hasAnyRole(['country-manager','latam-manager','admin']))
                    <li class="{{ request()->routeIs('admin.instructors.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.instructors.index') }}" title="{{ __('Instructors') }}">
                            <em class="fas fa-chalkboard-teacher"></em><span>{{ __('Instructors') }}</span>
                        </a>
                    </li> 
                @endif
                
                @if (auth()->user()->hasAnyRole(['instructor', 'country-manager','latam-manager','admin']))
                    <li class="{{ request()->routeIs('admin.notifications.*') || request()->routeIs('admin.motivations.*') ? 'active' : '' }}">
                        <a href="#notifications" title="{{ __('Notifications') }}" cl data-toggle="collapse">
                            <em class="fas fa-paper-plane"></em><span>{{ __('Notifications') }}</span>
                        </a>
                        <ul class="sidebar-nav sidebar-subnav collapse {{ request()->routeIs('admin.notifications.*') || request()->routeIs('admin.motivations.*') ? 'show' : '' }}" id="notifications">
                            <li class="sidebar-subnav-header">{{ __('Notifications') }}</li>
                            <li class="{{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}"><a href="{{ route('admin.notifications.create') }}" title="{{ __('Send Notifications') }}"><span>{{ __('Send Notifications') }}</span></a></li>
                            @if (auth()->user()->hasAnyRole(['country-manager','latam-manager','admin']))
                                <li class="{{ request()->routeIs('admin.motivations.*') ? 'active' : '' }}"><a href="{{ route('admin.motivations.index') }}" title="{{ __('Motivations') }}"><span>{{ __('Motivations') }}</span></a></li>
                            @endif
                        </ul>
                    </li>
                @endif        

                @if (auth()->user()->hasAnyRole(['country-manager','latam-manager','admin']))                    
                    <li class="{{ request()->routeIs('admin.academies.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.academies.index') }}" title="{{ __('Academies') }}">
                            <em class="fa fa-school"></em><span>{{ __('Academies') }}</span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('admin.cities.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.cities.index') }}" title="{{ __('Cities') }}">
                            <em class="fa fa-map-pin"></em><span>{{ __('Cities') }}</span>
                        </a>
                    </li>
                @endif
                
                @if (auth()->user()->hasAnyRole(['latam-manager','admin']))
                    <li class="{{ request()->routeIs('admin.countries.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.countries.index') }}" title="{{ __('Countries') }}">
                            <em class="fa fa-map-marker-alt"></em><span>{{ __('Countries') }}</span>
                        </a>
                    </li>                
                @endif
                
                @if (auth()->user()->hasRole('admin'))
                    <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}" title="{{ __('Users') }}">
                            <em class="fa fa-users"></em><span>{{ __('Users') }}</span>
                        </a>                    
                    </li>
                @endif
                
                <li class="{{ request()->routeIs('admin.assistances.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.assistances.index') }}" title="{{ __('Assistances') }}">
                        <em class="fas fa-book"></em><span>{{ __('Assistances') }}</span>
                    </a>
                </li>                
                
                <li class="{{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.profile.index') }}" title="{{ __('My Profile') }}">
                        <em class="fa fa-user-edit"></em><span>{{ __('My Profile') }}</span>
                    </a>
                </li>

            </ul>
            <!-- END sidebar nav-->
        </nav>
    </div>
    <!-- END Sidebar (left)-->
</aside>