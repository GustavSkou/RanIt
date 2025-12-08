@vite(['resources/css/activity.css', 'resources/js/activityMap.js'])
<x-body>
    <x-nav></x-nav>

    <script>
        window.points = {{Illuminate\Support\Js::from($activity->points)}};
    </script>
        <div class="activity-container">

            <div class="summary-container">

                <h2 class="user-name">{{ $activity->user->name}}</h2>

                <div class="top-container">
                    <div class="panel-left">
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

                            <a class="activity-name">{{ $activity->name }}</a>
                            <p>description</p>
                        </div> 
                    </div>

                    <div class="panel-right">
                        <ul class="stat-ul">
                            @if ($activity->distance != null)
                            <li>
                                <div>{{ $activity->getFormattedDistance() }}</div>
                                <label>Distance</label>
                            </li>
                            @endif
                            
                            @if ($activity->average_speed != null)
                            <li>
                                <div>{{ $activity->getFormattedAverageSpeed() }}</div>
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
                                    {{ $activity->getFormattedDuration() }}
                                </div>
                                <label>Moving time</label>
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

                        <ul class="stat-ul more-stat-ul">
                            @if ($activity->distance != null)
                            <li>
                                <div>{{$activity->getFormattedElevation()}}</div>
                                <label>Elevation</label>
                            </li>
                            @endif
                            
                            @if ($activity->average_speed != null)
                            <li>
                                <div>{{$activity->movingTime()}}</div>
                                <label>Total time</label>
                            </li>
                            @endif

                            @if ($activity->duration != null)
                            <li>
                                <div>123</div>
                                <label>Calories</label>
                            </li>
                            @endif

                            @if ($activity->average_heart_rate != null)
                            <li>
                                <div>123</div>
                                <label>Effort</label>
                            </li>
                            @endif
                        </ul>

                    </div>
                </div>
            </div>

            <div>
                <div id="map" class="map"></div>
            </div>
        </div>

        

</x-body>