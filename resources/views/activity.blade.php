@vite([
    'resources/css/activity.css', 
    'resources/js/activityMap.js', 
    'resources/js/activityChart.js'
    ])
<x-body>
    <x-nav></x-nav>

    <script>
        window.points = {{Illuminate\Support\Js::from($activity->points)}};
        window.activity = {{Illuminate\Support\Js::from($activity)}};
    </script>
    
    <div class="activity-container">

        <div class="summary-container">

            <a href="{{ route('profile', $activity->user) }}">
                <h2 class="user-name">{{ $activity->user->name}}</h2>
            </a>

            <div class="top-container">
                <div class="panel-left">
                    
                    <div class="icon-container">
                        <a href="{{ route('profile', $activity->user) }}">
                            <img
                            src={{ Storage::disk('public')->exists( $activity->user->profile_picture_path) ? asset('storage/' .  $activity->user->profile_picture_path ) : asset('images/' .  $activity->user->profile_picture_path ) }} 
                            class="profile-icon" 
                            alt="{{ $activity->user->name }}">
                        </a>
                    </div>
                    
                    <div> <!---->
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
                            <div>{{ $activity->getFormattedDistance() }} <span class="unit">{{ $activity->getDistanceType() }}</span></div>
                            <label>Distance</label>
                        </li>
                        @endif
                        
                        @if ($activity->average_speed != null)
                        <li>
                            <div>{{ $activity->getFormattedAverageSpeed() }} <span class="unit"> {{$activity->getSpeedType()}}</span></div> 
                            
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
                            <div>{{$activity->totalTime()}}</div>
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

        <!--Activity Map-->
        <div class="map-container">
            <div id="map" class="map"></div>    
        </div>

        <!--Activity Chart-->
        <div class="chart-container">
            <canvas id="chart" class="chart" height="250"></canvas>
        </div>
        
    </div>
</x-body>