@vite(['resources\css\users.css'])

<x-body>
    <x-nav></x-nav>

    <div class="list">
        @foreach ($users as $user)
            <div class="user-container">
                <image 
                    src={{ Storage::disk('public')->exists('storage' . $user->profile_picture_path) ? asset('storage/' . $user->profile_picture_path ) : asset('images/' . $user->profile_picture_path ) }}
                    class="profile-icon"
                    alt="{{ Auth::user()->name }}">

                <div>
                    <h1>{{ $user->name }}</h1>
                    <p>location</p>
                </div>

                <form action="" method="post">
                    <button>
                        Follow
                    </button>
                </form>
            </div>
        @endforeach
    </div>  

</x-body>