@vite(['resources\css\dashboard.css', 'resources\css\profile-side-bar.css', 'resources\js\kudos.js'])
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
                    <p>Latest activity</p>
                    <a href={{route('show', $activities->first())}}>
                        <span>{{ $activities->first()->name}}</span>
                        <span>-</span>
                        <span>{{ $activities->first()->getFormattedDate() }}</span>
                    </a>
                </div>

                <!--STREAK WIP--->
                <div>
                    <p>Your Week</p>
                    <div class="activity-streak">
                        <div class="streakFlame-container">
                            <img src="{{ asset('images/icons/ui/fire.png') }}" class="streakFlame">
                            <div class="streakFlame-text">{{ $weeksInRow }}</div>
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
            <div class="activity-container" id="activity-{{ $activity->id }}">
                <div class="top-panel">
                    <div class="icon-container">
                        <a href="{{ route('profile', $activity->user) }}">
                            <img
                                src="{{ Storage::disk('public')->exists($authedUser->profile_picture_path) ? asset('storage/' . $authedUser->profile_picture_path ) : asset('images/' . $authedUser->profile_picture_path ) }}"
                                class="profile-icon"
                                alt="User Profile">
                        </a>
                    </div>
                    <div>
                        <a href="{{ route('profile', $activity->user) }}" class="user-name">{{ $activity->user->name ?? 'NO NAME' }}</a>

                        @if ($activity->start_time != null)
                        <span>
                            <time>{{ $activity->start_time }}</time>
                        </span>
                        @endif

                        @if ($activity->device != null)
                        <span>{{ $activity->device }}</span>
                        @endif

                        @if ($activity->location != null)
                        <span>{{ $activity->location }}</span>
                        @endif
                    </div>
                </div>

                <div class="middle-panel">
                    <div class="icon-container">
                        <img class="type-icon" src="{{ asset('images/' . $activity->icon->path) }}" alt="Activity Type">
                    </div>
                    <div class="info-panel">
                        <h2>
                            <a class="activity-name" href="{{ route('show', $activity) }}">{{ $activity->name }}</a>
                        </h2>
                        <ul class="stat-ul">
                            @if ($activity->distance != null)
                            <li>
                                <p>Distance</p>
                                <div>{{ $activity->GetFormattedDistance() }}<span class="unit">{{ $activity->getDistanceType() }}</span></div>
                            </li>
                            @endif

                            @if ($activity->average_speed != null)
                            <li>
                                @switch($activity->type)
                                @case('running')
                                <p>Pace</p>
                                @break
                                @case('cycling')
                                <p>Speed</p>
                                @break
                                @default
                                <p>Speed</p>
                                @break
                                @endswitch
                                <div>{{ $activity->GetFormattedAverageSpeed() }}</div>
                            </li>
                            @endif

                            @if ($activity->duration != null)
                            <li>
                                <p>Time</p>
                                <div>{{ $activity->GetFormattedDuration() }}</div>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <div class="image-panel">
                    @if ($activity->map_image_path != null)
                    <img 
                        src="{{ asset('storage/' . $activity->map_image_path) }}"
                        class="map-image" 
                        alt="{{ $activity->name }}">
                    @endif
                </div>

                <footer class="activity-footer">
                    <div class="kudos-display">
                        <button>{{ $activity->kudos_count() }} kudos</button>
                    </div>

                    <form class="kudos">
                        @csrf
                        <input name="activity_id" value="{{ $activity->id }}" hidden>
                        <button type="submit" id="kudos-button">
                            <img 
                                src={{ $activity->kudosByAuth() ? asset("images/icons/social/liked.png") : asset("images/icons/social/like.png") }}
                                alt="Kudos">
                        </button>
                    </form>
                </footer>

            </div>
            @endforeach
        </div>

        <div class="right-side">
        </div>
    </main>
</x-body>