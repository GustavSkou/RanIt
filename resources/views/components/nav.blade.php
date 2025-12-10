@vite('resources/css/navbar.css')
<nav>
    <div class="left">
        <a href="/">RanIt</a>
        <a href="/dashboard">Dashboard</a>
        <a href="/training">Training</a>
        <a href="/maps">Maps</a>
    </div>

    <div class="right">
        <a href=""></a>
        <a href=""></a>
        <a href="{{ route('profile', Auth::user()) }}">
            <image 
            src={{ Storage::disk('public')->exists('storage' . Auth::user()->profile_picture_path) ? asset('storage/' . Auth::user()->profile_picture_path ) : asset('images/' . Auth::user()->profile_picture_path ) }}
            class="nav-profile-icon"
            alt="{{ Auth::user()->name }}">
        </a>
        <a href="/upload">Upload</a>
    </div>
</nav>