@vite(['resources\css\dashboard.css', 'resources\css\profile-side-bar.css'])
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
                    <h1>{{ $authedUser->name ?? "" }}</h1>
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

            @if ($latestActivity)
            <div class="week-summary">
                <div class="latest-activity">
                    <p>Latest activity</p>
                    <span>{{ $latestActivity->name }}</span>
                    <span>-</span>
                    <span>{{ $latestActivity->date() }}</span>
                </div>

                <!--STREAK WIP--->
                @if(false)
                <div>
                    <p>Your Week</p>
                    <div class="activity-streak">
                        <div class="streakFlame-container">
                            <img src="{{ asset('images/icons/ui/fire.png') }}" class="streakFlame">
                            <div class="streakFlame-text">{{ now()->week() }}</div>
                        </div>
                        <div class="streak-day-list">
                            @for ($i = 0; $i < 7; $i++)
                            <div class="streak-day">
                                
                                
                                <p>{{ now()->startOfWeek()->addDays($i)->format('l')[0] }}</p>
                                <div>
                                    @php
                                        $authedUser->activities

                                    @endphp
                                    
                                    @if (true)
                                        <image src={{ asset("images/" . $activity->type->path) }}>
                                    @else
                                        {{ now()->startOfWeek()->addDays($i)->format('j') }}
                                    @endif
                                    
                                </div>
                            </div>
                            @endfor
                        </div>
                    </div>
                    @endif

                </div>
            </div>
            @endif
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
                        <a class="user-name">{{ $activity->user->name ?? 'NO NAME' }}</a>

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
                                <div>{{ $activity->GetFormattedDistance() }}</div>
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
                    <img class="map-image" src="{{ asset('storage/' . $activity->map_image_path) }}" alt="{{ $activity->name }}">
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="right-side">
        </div>
    </main>
</x-body>