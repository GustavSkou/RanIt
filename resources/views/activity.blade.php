@vite(['resources/css/activity.css'])
<x-body>
    <x-nav></x-nav>

    <div class="activity-container">
        
        <h2 class="user-name">{{ $activity->user->name}}</h2>
        
        <div class="top-container">
            <div class="left">
                <div class="top-right-panel">
                    <div class="icon-container">
                        <img class="profile-icon" src="{{ $activity->user->profile_picture_path }}">
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
        

    </div>
<!--
    <div>
        <header>
            <h2>
                <span>{{$activity->user->name}}</span> - <span>{{$activity->type}}</span>
            </h2>
        </header>

        <div class="summary">
            <div>
                <div class="image-container">
                    <img src="{{ $activity->user->profile_picture_path }}">
                </div>
            
                <h1>{{ $activity->name }}</h1>
                <p>description</p>
            </div>
            <div>
                <ul>
                    <l1>
                        <span>{{ $activity->getFormattedDistance() }}</span><span> km</span>
                        <br>
                        <label>Distance</label>
                    </l1>
                    
                    <l1>
                        <div>{{ $activity->GetFormattedDuration() }}</div>
                        <label>Moving Time</label>
                    </l1>
                        
                    <l1>
                        <div>{{ $activity->GetFormattedAverageSpeed() }}</div>
                        <label>Pace</label>
                    </l1>
                        
                    <l1>
                        <div>{{ $activity->GetFormattedAverageHeartRate() }}</div>
                        <label>HeartRate</label>
                    </l1>
                </ul>
            </div>
            
        
        </div>


    </div>
-->

</x-body>