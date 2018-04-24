<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Standard web</title>
    <link href="{{ url('/css/app.css?x=') . rand() }}" rel="stylesheet">
    <link href="{{ url('/css/fontawesome-all.min.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app"></div>
    <script src="{{ url('/js/manifest.js?x=') . rand() }}"></script>
    <script src="{{ url('/js/vendor.js?x=') . rand() }}"></script>
    <script src="{{ url('/js/app.js?x=') . rand() }}"></script>
</body>
</html>