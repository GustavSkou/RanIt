@vite(['resources\css\users.css'])

<x-body>
    <x-nav></x-nav>

    <div class="list">
        @foreach ($users as $user)
            <div class="user-container">
                <a href="{{ route('profile', $user) }}">
                    <image 
                        src={{ Storage::disk('public')->exists('storage' . $user->profile_picture_path) ? asset('storage/' . $user->profile_picture_path ) : asset('images/' . $user->profile_picture_path ) }}
                        class="profile-icon"
                        alt="{{ Auth::user()->name }}">
                </a>
                <div>
                    <a href="{{ route('profile', $user) }}">
                        <h1>{{ $user->name }}</h1>
                    </a>
                    <p>location</p>
                </div>

                @if (Auth::user() == $user)

                @elseif (Auth::user()->following()->where( 'follows_user_id', $user->id )->exists() )
                    <form action="{{ route('unFollow') }}" method="post">
                        @csrf
                        <input value="{{ $user->id }}" name="user" id="user" hidden>

                        <button class="button following">
                            Following
                        </button>
                    </form>

                @else
                    <form action="{{ route('follow') }}" method="post">
                        @csrf
                        <input value="{{ $user->id }}" name="user" id="user" hidden>

                        <button class="button following">
                            Follow
                        </button>
                    </form>
                @endif

            </div>
        @endforeach
    </div>  

</x-body>