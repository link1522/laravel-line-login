<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <a href="{{ route('goToLineLogin') }}">line login</a>

    {{ Auth::check() ? 'login' : 'not login' }}

    @session('lineInfo')
        <p>
            hello, {{ session('lineInfo')['displayName'] }}
        </p>
        {{ json_encode(session('lineInfo')) }}
    @endsession
</body>

</html>
