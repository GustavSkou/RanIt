<x-body>
<x-nav></x-nav>

@foreach ($users as $user)
    <h1>{{ $user->name }}</h1>
@endforeach

</x-body>