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
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h4 class="panel-title">上傳檔案</h4>
                            </div>
                            <div class="panel-body">
                                <form role="form" action="/api/file/upload/old/code" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="file_id" value="{{ $file_id }}"/>
                                        <input type="hidden" name="user_id" value="{{ $user_id }}"/>
                                    <div class="form-group">
                                        <input type="file" name="file_data"/>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">
                                            <span class="glyphicon glyphicon-upload"></span>上傳
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
    