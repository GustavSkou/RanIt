@vite(['resources\css\profile.css'])
<x-body>
    <x-nav></x-nav>
    <div class="container">
        <div class="profile-top">

            <div>
                <image 
                    src={{ Storage::disk('public')->exists('storage' . $user->profile_picture_path) ? asset('storage/' . $user->profile_picture_path ) : asset('images/' . $user->profile_picture_path ) }}
                    class="profile-icon">
            </div>

            <div>
                <div>
                    <h1>
                        {{$user->name}}
                    </h1>

                    @if (Auth::user() == $user && 1 != 1)
                        <a href{{ route('edit-profile', $user) }}>edit</a>
                    @endif
                </div>

                <p>location</p>
            </div>
        </div>
    
    </div>

</x-body>