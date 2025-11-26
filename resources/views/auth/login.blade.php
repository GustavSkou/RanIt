<x-body>
    @vite('resources/css/authForm.css')
    <div>
        <form method='POST' action='{{route('login')}}'>
            @csrf
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

            <a href="/Register">Need an account?</a>
        </form>
    </div>
</x-body>