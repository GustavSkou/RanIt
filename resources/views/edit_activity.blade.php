@vite(['resources/css/edit-activity.css'])
<x-body>
    <x-nav></x-nav>
    
    <form>
        <div class="top-panel">
            <div class="img">
                <p>pic</p>
            </div>

            <div>
                
                <a class="user-name">{{ $activity->user()->name ?? 'NO NAME' }}</a>

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
                    <input class="activity-name" type="text" placeholder="{{$activity->name}}">
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
                        <p>Pace</p>
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
    </form>
    
    <div class="map-container" id="{{ $activity->id }}"></div>

</x-body>