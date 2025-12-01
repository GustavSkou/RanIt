@vite(['resources\css\activities.css', 'resources\css\profile-side-bar.css'])
<x-body>
    <x-nav></x-nav>
    <main>
        <div class="left-side">
            <div class="profile-info">
                <h1>{{Auth::user()->name}}</h1>
                <ul>
                    <li>
                        <p>Following</p>
                        <div>123</div>
                    </li>
                    <li>
                        <p>Followers</p>
                        <div>321</div>
                    </li>
                    <li>
                        <p>Activities</p>
                        <div>123</div>
                    </li>
                </ul>
            </div>

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
        </div>

        <div class="feat">        
            @foreach ($activities as $activity)
            <div class="activity-container" id="activity-{{ $activity->id }}">
                <div class="top-panel">
                    <div class="img">
                        <p>pic</p>
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
                    <div class="img">
                        <p>run</p>
                    </div>
                    <div class="info-panel">
                        <h2>
                            <a class="activity-name">{{$activity->name}}</a>
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
        </div >

        <div class="right-side">
            
        </div>
    </main>
</x-body>