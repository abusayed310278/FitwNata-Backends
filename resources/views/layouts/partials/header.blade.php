<div class="dashboard-header">
    {{-- Search Bar --}}
    <!-- <div class="nav-search d-none d-lg-block">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Search" aria-label="search">
            <div class="input-group-prepend hover-cursor">
                <span class="input-group-text">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
            </div>
        </div>
    </div> -->

    <div class="nav-search d-none d-lg-block position-relative">
        <div class="input-group">
            <input type="text" 
                id="dashboard-search" 
                class="form-control" 
                placeholder="Search modules..." 
                aria-label="search">

            <div class="input-group-prepend hover-cursor">
                <span class="input-group-text">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
            </div>
        </div>

        <!-- Results dropdown -->
        <ul id="search-results" 
            class="list-group position-absolute w-100 shadow-sm mt-1"
            style="top: 100%; z-index: 1050; display: none; max-height: 250px; overflow-y: auto;">
        </ul>
    </div>

    {{-- Right-aligned User Profile --}}
    <div class="user-profile" x-data="{ open: false }">
        <div class="flex items-center space-x-3" @click="open = !open">
            <span class="d-none d-sm-inline">Hello, <strong>{{ Auth::user()->name ?? 'User' }}</strong></span>
            {{-- Replace with dynamic user image --}}
            <img src="{{ Auth::user()->profile_photo_url ?? asset('assets/images/faces/face28.jpg') }}"
                 alt="Profile"
                 class="w-10 h-10 rounded-full object-cover border-2 border-white shadow">
        </div>

        {{-- Dropdown Menu --}}
        <div x-show="open"
             @click.away="open = false"
             class="dropdown-menu show">
            <a href="{{ route('profile.edit') }}" class="dropdown-item">
                <i class="fa-solid fa-user mr-2"></i>Profile
            </a>
            <div class="dropdown-divider"></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item">
                    <i class="fa-solid fa-right-from-bracket mr-2"></i>Logout
                </button>
            </form>
        </div>
    </div>
</div>
