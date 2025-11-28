@vite(['resources\css\activities.css'])
<x-body>
    <x-nav></x-nav>
    <main>
        @foreach ($activities as $activity)
        <div class="activity-container" id="activity-{{ $activity->id }}">

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

        </div>

        <image class="map-container" id="{{ $activity->id }}">>
        </image>
        </div>
        @endforeach
    </main>
</x-body>