<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Document</title>
    @vite('resources/js/app.js')
</head>
<body>
    <h1>Edit Document</h1>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('update', $id) }}" method="POST">
        @csrf
        <textarea id="summernote" name="content">{!! $htmlContent !!}</textarea>
        <button type="submit">Save</button>
    </form>
    <a href="{{ route('download', $id) }}">Download as .docx</a>
    <a href="{{ route('download-pdf', $id) }}">Download as PDF</a>
</body>
</html>
