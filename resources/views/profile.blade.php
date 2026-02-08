@vite(['resources\css\profile.css', 'resources\css\activity-feat.css'])
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

                    @if (Auth::user() == $user)
                        <a href{{ route('edit-profile', $user) }}>edit</a>
                    @endif
                </div>                
            </div>
        </div>

        <div class="profile-bottom">

            <div class="left">
                <div class="feat">
                    @foreach ($activities as $activity)
                        <x-activity :activity="$activity"></x-activity>
                    @endforeach
                </div>
            </div>

            <div class="right">
                <h2>My Stats</h2>
                <div></div>

                <div class="4-week-avg">
                
                </div>

                <div class="best-efforts">
                </div>

                <div class="current-year">
                </div>

                <div class="all-time">
                </div>

            </div>
        
        </div>
    
    </div>

</x-body>