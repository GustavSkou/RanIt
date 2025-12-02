@vite(['resources/css/file-upload.css'])
<x-body>
    <x-nav></x-nav>
    <div class="container">
        <h1>Upload your Activities</h1>
        <form method="POST" action="{{ route('upload') }}" enctype="multipart/form-data">
            @csrf
            <input
                id="file"
                name="file"
                type="file"
                accept=".gpx"
                required>

            <button type="submit">submit</button>
        </form>
    </div>


</x-body>