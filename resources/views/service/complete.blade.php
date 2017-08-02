<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Stardant D2K Web Api</title>
    <link href="{{ url('/css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container" style={containerStyle}>
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2>{{ $title }}</h2>
                <h4>{{ $message}}</h4>
            </div>
        </div>
    </div>
</body>
</html>
    