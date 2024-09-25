<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Document</title>
    @vite('resources/css/app.css')
</head>
<body>
    <h1>Upload Document (.docx)</h1>
    <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="document" required>
        <button type="submit">Upload</button>
    </form>
</body>
</html>
