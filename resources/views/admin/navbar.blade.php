<!-- top navbar-->
<header class="topnavbar-wrapper">
    <!-- START Top Navbar-->
    <nav class="navbar topnavbar">
        <!-- START navbar header-->
        <div class="navbar-header">
            <a class="navbar-brand" href="javascript:void(0);">
                <div class="brand-logo">
                <img class="img-fluid" src="{{ asset('angle/img/logo-single.png') }}" alt="App Logo"></div>
                <div class="brand-logo-collapsed">
                <img class="img-fluid" src="{{ asset('angle/img/logo-single.png') }}" alt="App Logo"></div>
            </a>
        </div>
        <!-- END navbar header-->
        <!-- START Left navbar-->
        <ul class="navbar-nav mr-auto flex-row">
            <li class="nav-item">
                <!-- Button used to collapse the left sidebar. Only visible on tablet and desktops--><a class="nav-link d-none d-md-block d-lg-block d-xl-block" href="javascript:void(0);" data-trigger-resize="" data-toggle-state="aside-collapsed"><em class="fas fa-bars"></em></a><!-- Button to show/hide the sidebar on mobile. Visible on mobile only.--><a class="nav-link sidebar-toggle d-md-none" href="#" data-toggle-state="aside-toggled" data-no-persist="true"><em class="fas fa-bars"></em></a>
            </li>
            <!-- START User avatar toggle-->
            <li class="nav-item d-none d-md-block">
                <!-- Button used to collapse the left sidebar. Only visible on tablet and desktops--><a class="nav-link" id="user-block-toggle" href="#user-block" data-toggle="collapse"><em class="icon-user"></em></a>
            </li>
        </ul>
        <!-- END Left navbar-->
        <!-- START Right Navbar-->
        <ul class="navbar-nav flex-row">
            <!-- Search icon-->
			<!--
            <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-search-open=""><em class="icon-magnifier"></em></a></li>
			-->
            <!-- Fullscreen (only desktops)-->
			<!--
            <li class="nav-item d-none d-md-block"><a class="nav-link" href="javascript:void(0);" data-toggle-fullscreen=""><em class="fas fa-expand"></em></a></li>
            -->
			<!-- START Alert menu-->
			<!--
            <li class="nav-item dropdown dropdown-list">
                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:void(0);" data-toggle="dropdown"><em class="icon-bell"></em></a>
            </li>
			-->
			<!-- START Dropdown menu-->
            <!-- END Alert menu-->
			<li class="nav-item d-none d-md-block">               
				<a class="nav-link" href="{{ route('logout') }}" title="{{ __('Logout') }}"
				   onclick="event.preventDefault();
								 document.getElementById('logout-form').submit();">
					<em class="icon-logout"></em>
				</a>

				<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
					@csrf
				</form>
			</li><!-- END lock screen--> 			
        </ul>
        <!-- END Right Navbar-->
        <!-- START Search form-->
        <form class="navbar-form" role="search" action="search.html">
            <div class="form-group">
                <input class="form-control" type="text" placeholder="Type and hit enter ...">
                <div class="fas fa-times navbar-form-close" data-search-dismiss=""></div>
            </div>
            <button class="d-none" type="submit">Submit</button>
        </form>
        <!-- END Search form-->
    </nav>
    <!-- END Top Navbar-->
</header>