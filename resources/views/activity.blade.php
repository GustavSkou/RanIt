@vite(['resources/css/activity.css', 'resources/js/activityMap.js'])
<x-body>
    <x-nav></x-nav>

    <script>
        window.points = @json($points);
    </script>

    <div class="activity-container">

        <h2 class="user-name">{{ $activity->user->name}}</h2>

        <div class="top-container">
            <div class="left">
                <div class="top-right-panel">
                    <div class="icon-container">
                        <img class="profile-icon" src="{{ asset($activity->user->profile_picture_path) }}">
                    </div>
                    <div>
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

                        <a class="user-name">{{ $activity->name }}</a>
                        <p>description</p>
                    </div>
                </div>
            </div>

            <div class="right">
                <ul class="stat-ul">
                    @if ($activity->distance != null)
                    <li>
                        <div>{{ $activity->GetFormattedDistance() }}</div>
                        <label>Distance</label>
                    </li>
                    @endif
                    @if ($activity->average_speed != null)
                    <li>
                        <div>{{ $activity->GetFormattedAverageSpeed() }}</div>
                        @switch($activity->type)
                            @case('running')
                            <label>Pace</label>
                            @break
                            @case('cycling')
                            <label>Speed</label>
                            @break
                            @default
                            <label>Speed</label>
                            @break
                        @endswitch

                    </li>
                    @endif
                    @if ($activity->duration != null)
                    <li>
                        <div>
                            {{ $activity->GetFormattedDuration() }}
                        </div>
                        <label>Time</label>
                    </li>
                    @endif

                    @if ($activity->average_heart_rate != null)
                    <li>
                        <div>
                            {{ $activity->GetFormattedAverageHeartRate() }}
                        </div>
                        <label>Heart Rate</label>
                    </li>
                    @endif

                </ul>
            </div>
        </div>

    </div>

    <div id="map"></div>

</x-body>