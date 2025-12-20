@vite(['resources/css/navbar.css', 'resources/js/navbar.js'])
<nav>
    <div class="left">
        <a href="/">RanIt</a>
        <div class="search-container">
            <button class="search-button" id="searchButton">
                <img 
                    src="{{ asset('images/icons/ui/search.png') }}"
                    class="nav-search-icon"
                    alt="Search">
            </button>
            <form method="GET" action="{{ route('user.index') }}">
                <input 
                    type="text" 
                    class="search-input" 
                    id="searchInput"
                    name="searchInput"
                    placeholder="Search athletes...">
            </form>
        </div>
        <a href="/dashboard">Dashboard</a>
        <a href="/training">Training</a>
        <a href="/maps">Maps</a>
    </div>

    <div class="right">
        <a href=""></a>
        <a href=""></a>
        
        <div class="profile-dropdown-container">
            <button id="dropdown-button">
                <image 
                src={{ Storage::disk('public')->exists('storage' . Auth::user()->profile_picture_path) ? asset('storage/' . Auth::user()->profile_picture_path ) : asset('images/' . Auth::user()->profile_picture_path ) }}
                class="nav-profile-icon"
                alt="{{ Auth::user()->name }}">
            </button>

            <div class="dropdown-content" id="dropdown-content">
                <a href="{{ route('profile', Auth::user()) }}">Profile</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="logout-btn" type="submit">Log out</button>
                </form>

            </div>
        </div>
            
        <a href="/upload">Upload</a>
    </div>
</nav>