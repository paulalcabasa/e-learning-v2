<header class="main-header">
    <!-- Logo -->
    <a href="http://ecommerce4/e-learning/admin" class="logo">
        <span class="logo-mini"><b>IPC</b></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><img src="{{ url('public/images/isuzu_logo_white.png') }}"></span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" data-toggle="push-menu">
            <i class="ion ion-android-menu" style="margin-top: 15px; margin-left: 15px; color: white; font-size: 24px;"></i>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li class="dropdown messages-menu" id="calendar_tab">
                    <a href="{{ url('/admin/calendar/get?filter=all') }}">
                        <i class="fa fa-calendar-alt"></i>&nbsp;
                        <span>Calendar</span>
                    </a>
                </li>

                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!-- The user image in the navbar-->
                        <img src="{{ load_pic() }}" class="user-image">
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                        <span class="hidden-xs">{{ title_case(session('full_name')) }}</span>
                    </a>
                    <ul class="dropdown-menu shadow">
                        <!-- The user image in the menu -->
                        <li class="user-header">
                            <img src="{{ load_pic() }}" class="img-circle">
                            
                            <p>
                                {{ title_case(session('full_name')) }}
                                <small>Administrator</small>
                            </p>
                        </li>

                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="{{ route('ipc_home') }}" 
                                class="btn btn-default btn-sm btn-flat"
                                >
                                <i class="fas fa-home"></i>
                                IPC Home
                                </a>
                            </div>
                            <div class="pull-right">
                                <a href="{{ url('/flush_session') }}" 
                                type="button" 
                                class="btn btn-default btn-sm btn-flat">
                                <i class="fas fa-sign-out-alt"></i>
                                Sign out</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>