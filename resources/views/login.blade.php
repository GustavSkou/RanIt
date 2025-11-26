<x-body>
    @vite('resources/css/login.css')
    <div>
        <form method='POST' action='{{route('login')}}'>
            <input placeholder="Email..."
                type="text"
                id="email"
                name="email"
                required>

            <input placeholder="Password..."
                type="password"
                id="password"
                name="password"
                required>

            <button type="submit">Login</button>
        </form>
    </div>
</x-body>