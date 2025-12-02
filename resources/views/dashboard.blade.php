@vite(['resources\css\dashboard.css', 'resources\css\profile-side-bar.css'])
<x-body>
    <x-nav></x-nav>
    <main>
        <div class="left-side">

            <div class="profile-info">

                <?php
                $authedUser = Auth::user();
                ?>

                <div>
                    <img src="{{ $authedUser->profile_picture_path }}">
                    <h1>{{$authedUser->name ?? ""}}</h1>
                </div>

                <ul>
                    <li>
                        <p>Following</p>
                        <div>{{ count($authedUser->following) }}</div>
                    </li>
                    <li>
                        <p>Followers</p>
                        <div>{{ count($authedUser->followers) }}</div>
                    </li>
                    <li>
                        <p>Activities</p>
                        <div>{{ count($authedUser->activities) }}</div>
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
                <div class="activity-streak">
                    <p>Week</p>

                </div>
            </div>
            @endif
        </div>

        <div class="feat">
            @foreach ($activities as $activity)
            <div class="activity-container" id="activity-{{ $activity->id }}">
                <div class="top-panel">
                    <div class="icon-container">
                        <img class="profile-icon" src="{{ $activity->user->profile_picture_path }}">
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
                        <img class="type-icon" src="{{ $activity->icon->path }}">
                    </div>
                    <div class="info-panel">
                        <h2>
                            <a class="activity-name" href={{ route('show', $activity) }}>{{ $activity->name }}</a>
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
                                <div>
                                    {{ $activity->GetFormattedDuration() }}
                                </div>
                            </li>
                            @endif

                        </ul>
                    </div>
                </div>

                <div class="image-panel">
                    @if ($activity->map_image_path != null)
                    <image class="map-image" src="{{ $activity->map_image_path }}" alt={{ $activity->name }}></image>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="right-side">

        </div>
    </main>
</x-body>