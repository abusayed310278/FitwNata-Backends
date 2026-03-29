<nav class="sidebar" id="sidebar">
    <div class="navbar-brand-wrapper">
        <a class="navbar-brand brand-logo" href="{{ url('/') }}">
            <img src="{{ asset('assets/logo.png') }}" alt="fitwnata logo" />
        </a>
    </div>

    <!-- THIS UL IS THE SCROLLABLE AREA -->
    <ul class="nav">
        @role('admin')
            <li class="nav-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fa-solid fa-gauge-high menu-icon"></i>
                    <span class="menu-title">Dashboard</span>
                </a>
            </li>

            <!-- Management Section -->
            <li class="nav-item {{ Request::routeIs('staff.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('staff.index') }}">
                    <i class="fa-solid fa-users menu-icon"></i>
                    <span class="menu-title">User Management</span>
                </a>
            </li>
            <li class="nav-item {{ Request::routeIs('coach.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('coach.index') }}">
                    <i class="fa-solid fa-person-running menu-icon"></i>
                    <span class="menu-title">Coach Management</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('nutritionist.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('nutritionist.index') }}">
                    <i class="fa-solid fa-user-doctor menu-icon"></i>
                    <span class="menu-title">Nutritionist Management</span>
                </a>
            </li>

            <!-- Shop Section -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#ecommerce"
                    aria-expanded="{{ Request::routeIs(['product.*', 'categories.*', 'order.*']) ? 'true' : 'false' }}"
                    aria-controls="ecommerce">
                    <i class="fa-solid fa-store menu-icon"></i>
                    <span class="menu-title">E-commerce</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse {{ Request::routeIs(['product.*', 'categories.*', 'order.*']) ? 'show' : '' }}"
                    id="ecommerce">
                    <ul class="">
                        
                        <li class="nav-item p-0 m-0"> <a class="nav-link {{ Request::routeIs('product.*') ? 'active' : '' }}"
                                href="{{ route('product.index') }}">Products</a></li>
                        <li class="nav-item"> <a class="nav-link {{ Request::routeIs('categories.*') ? 'active' : '' }}"
                                href="{{ route('categories.index') }}">Categories</a></li>
                        <li class="nav-item"> <a class="nav-link {{ Request::routeIs('order.*') ? 'active' : '' }}"
                                href="{{ route('order.index') }}">Orders</a></li>
                    </ul>
                </div>
            </li>

            <!-- Features Section -->
            {{-- <li class="nav-item {{ Request::routeIs('appointment.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('appointment.index') }}">
                    <i class="fa-solid fa-calendar-check menu-icon"></i>
                    <span class="menu-title">Appointments</span>
                </a>
            </li> --}}
            <li class="nav-item {{ Request::routeIs('subscription.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('subscription.index') }}">
                    <i class="fa-solid fa-crown menu-icon"></i>
                    <span class="menu-title">Subscriptions</span>
                </a>
            </li>
            <li class="nav-item {{ Request::routeIs('plan.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('plan.index') }}">
                    <i class="fa-solid fa-clipboard-list menu-icon"></i>
                    <span class="menu-title">Plans</span>
                </a>
            </li>
            <li class="nav-item {{ Request::routeIs('tags.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('tags.index') }}">
                    <i class="fa-solid fa-clipboard-list menu-icon"></i>
                    <span class="menu-title">Tags</span>
                </a>
            </li>

            <!-- Content Management -->
            <!-- <li class="nav-item {{ Request::routeIs('article.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('article.index') }}">
                    <i class="fa-solid fa-pen-to-square menu-icon"></i>
                    <span class="menu-title">Articles</span>
                </a>
            </li> -->



            <!-- Progress Tracking -->
            {{-- <li class="nav-item {{ Request::routeIs('progress.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('progress.index') }}">
                    <i class="fa-solid fa-book menu-icon"></i>
                    <span class="menu-title">Progress Journals</span>
                </a>
            </li>
            <li class="nav-item {{ Request::routeIs('measurements.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('measurements.index') }}">
                    <i class="fa-solid fa-weight-scale menu-icon"></i>
                    <span class="menu-title">Measurements</span>
                </a>
            </li> --}}

            <!-- Reports & Analytics -->
            <!-- <li class="nav-item {{ Request::routeIs('report.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('report.index') }}">
                    <i class="fa-solid fa-chart-simple menu-icon"></i>
                    <span class="menu-title">Reports</span>
                </a>
            </li> -->
        @endrole
        @hasanyrole('coach|nutritionist')
            {{-- <li class="nav-item nav-category">Content</li>
            <li class="nav-item {{ Request::routeIs('article.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('article.index') }}">
                    <i class="fa-solid fa-pen-to-square menu-icon"></i>
                    <span class="menu-title">Tips/Blogs</span>
                </a>
            </li> --}}
        @endhasanyrole
        @hasanyrole('coach|nutritionist')
            <!-- Dashboard -->
            <li class="nav-item {{ Request::routeIs('coach.dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('coach.dashboard') }}">
                    <i class="fa-solid fa-gauge-high menu-icon"></i>
                    <span class="menu-title">Dashboard</span>
                </a>
            </li>

            <!-- Shared Features Section -->
            <li class="nav-item {{ Request::routeIs('coach.appointments.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('coach.appointments.index') }}">
                    <i class="fa-solid fa-calendar-check menu-icon"></i>
                    <span class="menu-title">Appointments</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('coach.availability.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('coach.availability.index') }}">
                    <i class="fa-solid fa-calendar-check menu-icon"></i>
                    <span class="menu-title">Availabilities</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('coach.clients.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('coach.clients.index') }}">
                    <i class="fa-solid fa-users menu-icon"></i>
                    <span class="menu-title">Clients</span>
                </a>
            </li>

            @role('nutritionist')
            <li class="nav-item {{ Request::routeIs(['nutritionist.meal_plans.*', 'nutritionist.recipes.*']) ? 'active' : '' }}">
                <a class="nav-link" data-bs-toggle="collapse" href="#nutritionPlans"
                    aria-expanded="{{ Request::routeIs(['nutritionist.meal_plans.*', 'nutritionist.recipes.*']) ? 'true' : 'false' }}"
                    aria-controls="nutritionPlans">
                    <i class="fa-solid fa-utensils menu-icon"></i>
                    <span class="menu-title">Meal Plans</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse {{ Request::routeIs(['nutritionist.mealplans.*', 'nutritionist.recipes.*']) ? 'show' : '' }}"
                    id="nutritionPlans">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('nutritionist.mealplans.*') ? 'active' : '' }}"
                            href="{{ route('nutritionist.mealplans.index') }}">
                            Meal Plans
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('nutritionist.recipes.*') ? 'active' : '' }}"
                            href="{{ route('nutritionist.recipes.index') }}">
                            Recipes
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endrole

            <!-- Fitness Management (Coach only) -->
            @role('coach')
            <li class="nav-item {{ Request::routeIs(['coach.exercise.*', 'coach.workout.*']) ? 'active' : '' }}">
                <a class="nav-link" data-bs-toggle="collapse" href="#fitness"
                    aria-expanded="{{ Request::routeIs(['coach.exercise.*', 'coach.workout.*']) ? 'true' : 'false' }}"
                    aria-controls="fitness">
                    <i class="fa-solid fa-dumbbell menu-icon"></i>
                    <span class="menu-title">Fitness</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse {{ Request::routeIs(['coach.exercise.*', 'coach.workout.*']) ? 'show' : '' }}"
                    id="fitness">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('coach.exercise.*') ? 'active' : '' }}"
                            href="{{ route('coach.exercise.index') }}">Exercises</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('coach.workout.*') ? 'active' : '' }}"
                            href="{{ route('coach.workout.index') }}">Workouts</a>
                        </li>
                    </ul>
                </div>
            </li>
            @endrole
        @endhasanyrole
    </ul>

    <!-- LOGOUT BUTTON IS SEPARATE AND AT THE BOTTOM -->
    <div class="logout-item mt-auto">
        <form method="POST" action="{{ route('logout') }}" style="width: 100%;">
            @csrf
            <a class="nav-link" href="{{ route('logout') }}"
                onclick="event.preventDefault(); this.closest('form').submit();">
                
                <span class="menu-title fw-bold" ><i class="fa-solid fa-arrow-right-from-bracket menu-icon"></i> Log out</span>
            </a>
        </form>
    </div>
</nav>
