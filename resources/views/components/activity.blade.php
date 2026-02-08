@props(['activity'])

<div class="activity-container" id="activity-{{ $activity->id }}">
    <div class="top-panel">
        <div class="icon-container">
            <a href="{{ route('profile', $activity->user) }}">
                <img
                    src="{{ Storage::disk('public')->exists($activity->user->profile_picture_path) ? asset('storage/' . $activity->user->profile_picture_path ) : asset('images/' . $activity->user->profile_picture_path ) }}"
                    class="profile-icon"
                    alt="User Profile">
            </a>
        </div>
        <div>
            <a href="{{ route('profile', $activity->user) }}" class="user-name">{{ $activity->user->name ?? 'NO NAME' }}</a>

            @if ($activity->start_time != null)
            <span>
                @if ($activity->start_time > now()) 
                    <time>Today : {{ $activity->start_time->toTimeString() }}</time>
                @elseif($activity->start_time > now()->subDay(1)) 
                    <time>Yesterday : {{ $activity->start_time->toTimeString() }}</time>
                @else
                    <time>{{ $activity->start_time->toFormattedDateString() }} : {{ $activity->start_time->toTimeString() }}</time>
                @endif
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
                    <div>{{ $activity->GetFormattedDistance() }}<span class="unit">{{ $activity->getDistanceType() }}</span></div>
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
        <img 
            src="{{ asset('storage/' . $activity->map_image_path) }}"
            class="map-image" 
            alt="{{ $activity->name }}">
        @endif
    </div>

    <footer class="activity-footer">
        <div class="kudos-display">
            <button>{{ $activity->kudos_count() }} kudos</button>
        </div>

        <form class="kudos">
            @csrf
            <input name="activity_id" value="{{ $activity->id }}" hidden>
            <button type="submit" id="kudos-button">
                <img 
                    src={{ $activity->kudosByAuth() ? asset("images/icons/social/liked.png") : asset("images/icons/social/like.png") }}
                    alt="Kudos">
            </button>
        </form>
    </footer>

</div>