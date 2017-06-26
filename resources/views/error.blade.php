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
    <div class="container" style="margin-top: 40px;">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3">
                        <div class="panel panel-warning">
                            <div class="panel-heading">
                                <h4 class="panel-title">error page</h4>
                            </div>
                            <div class="panel-body">
                                <h4>{{ $message }}</h4>
                            </div>
                        </div>
                    </div>
                </div>    
            </div>
        </div>
    </div>
    <script src="{{ url('/js/app.js') }}"></script>
</body>
</html>