<x-body>
    @vite('resources/css/authForm.css')
    <div>
        <form method="post" action="{{route('register')}}">
            @csrf
            <input placeholder="Name..."
                type="text"
                id='name'
                name='name'
                required>

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

            <input placeholder="Confirm password..."
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                required>

            <button type="submit">Register</button>

            <a href="/login">Have an account?</a>
        </form>
    </div>
</x-body>