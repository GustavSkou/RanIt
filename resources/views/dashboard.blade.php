@vite([
    'resources\css\dashboard.css'
    'resources\css\activity-feat.css', 
    'resources\css\profile-side-bar.css', 

    'resources\js\kudos.js', 
    ])

<x-body>
    <x-nav></x-nav>
    <main>
        <div class="left-side">
            <div class="profile-info">
                @php
                $authedUser = Auth::user();
                $profilePicPath = Storage::disk('public')->exists($authedUser->profile_picture_path)
                @endphp

                <div>
                    <!--check if there is a user generated image in storage-->
                    <a href="{{ route('profile', $authedUser) }}">
                        <img
                            src={{ Storage::disk('public')->exists($authedUser->profile_picture_path) ? asset('storage/' . $authedUser->profile_picture_path ) : asset('images/' . $authedUser->profile_picture_path ) }}
                            alt="Profile Picture"
                            class="profile-picture">
                    </a>
                    <a href="{{ route('profile', $authedUser) }}">
                        <h1>{{ $authedUser->name ?? "" }}</h1>
                    </a>
                </div>

                <ul>
                    <li>
                        <p>Following</p>
                        <div>{{ $authedUser->following()->count() }}</div>
                    </li>
                    <li>
                        <p>Followers</p>
                        <div>{{ $authedUser->followers()->count() }}</div>
                    </li>
                    <li>
                        <p>Activities</p>
                        <div>{{ $authedUser->activities()->count() }}</div>
                    </li>
                </ul>
            </div>

            <div class="week-summary">
                <div class="latest-activity">
                @if ($activities->isEmpty())
                    <p>No activities yet</p>
                @else
                    <p>Latest activity</p>
                    <a href={{route('show', $activities->first())}}>
                        <span>{{ $activities->first()->name}}</span>
                        <span>-</span>
                        <span>{{ $activities->first()->start_time->toFormattedDateString() }}</span>
                    </a>
                @endif
                </div>

                <!--STREAK WIP--->
                <div class="activity-streak-container">
                    <p>Your Streak</p>
                    <div class="activity-streak">
                        <div class="streakFlame-container">
                            <img src="{{ asset('images/icons/ui/fire.png') }}" class="streakFlame">
                            <div class="streakFlame-text">{{ $weeksInRow }}</div>
                            <p>weeks</p>
                        </div>

                        <div class="streak-day-list">
                            @php
                                $thisWeekActivities = $authedUser->activities->where('start_time', '>=', now()->startOfWeek()->toDateString());
                                
                            @endphp

                            @for ($i = 0; $i < 7; $i++)
                                @php
                                    // Get activities between the day's start and its end
                                    $activitesOnDay = $thisWeekActivities->whereBetween('start_time', [
                                        now()->startOfWeek()->addDays($i)->toDateString() . ' 00:00:00', 
                                        now()->startOfWeek()->addDays($i)->toDateString() . ' 23:59:59'
                                        ]
                                    );
                                    $activity = $activitesOnDay->first();
                                @endphp
                                <div class="streak-day">
                                    <p>{{ now()->startOfWeek()->addDays($i)->format('l')[0] }}</p>
                                    @if ($activitesOnDay->count() > 0)
                                        <a href="{{ route('show', $activity) }}">
                                            <div class="streak-day-icon">        
                                                <img src={{ asset("images/" . $activity->icon->path) }}>
                                            </div>
                                        </a>
                                    @else 
                                        <div class="streak-day-text">{{ now()->startOfWeek()->addDays($i)->format('j') }}</div>
                                    @endif
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="feat">
            @foreach ($activities as $activity)
                <x-activity :activity="$activity"></x-activity>
            @endforeach
        </div>

        <div class="right-side">
        </div>
    </main>
</x-body>