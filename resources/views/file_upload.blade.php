<form method="POST" action='{{route('upload')}}' enctype="multipart/form-data">
    @csrf
    <label for="upload">Upload file</label>
    <input
        id="file"
        name="file"
        type="file"
        accept=".gpx"
        required>

    <button type="submit">submit</button>
</form