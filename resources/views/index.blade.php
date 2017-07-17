<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Stardant D2K Web Api</title>
    <link href="{{ url('/css/app.css') }}" rel="stylesheet">
</head>
<body>
    <script type="text/javascript">
        var serverName = "/{{ env('APP_VD', '') }}";
    </script> 
    <div id="app"></div>
    <script src="{{ url('/js/app.js') }}"></script>
</body>
</html>