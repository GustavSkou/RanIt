<x-body>
    <x-nav></x-nav>
    <main>
        @foreach ($activities as $activity)
            <div id="activity-{{ $activity->id }}">
                <div>
                    <div>
                        <a>
                            {{ Auth::user()?->name ?? 'NO NAME' }} 
                        </a>

                        @if ($activity->startTime != null)
                            <span>
                                <time>{{ $activity->startTime }}</time> <!-- take start time from the first point -->
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
                    <h2>
                        <a>{{$activity->name}}</a>
                    </h2>
                    <ul>
                        @if ($activity->distance != null)
                             <li>
                                <p>Distance</p>
                                <div>{{ $activity->distance }}</div>
                            </li>                           
                        @endif
                        @if ($activity->pace != null)
                             <li>
                                <p>Pace</p>
                                <div>{{ $activity->pace }}</div>
                            </li>                           
                        @endif
                        @if ($activity->time != null)
                             <li>
                                <p>Time</p>
                                <div>{{ $activity->time }}</div>
                            </li>                           
                        @endif
                        
                    </ul>
                <div>

                </div>

                <div id="map-{{ $activity->id }}">MAP</div>
            </div>
        @endforeach
    </main>
</x-body>