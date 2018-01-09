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
    <link href="{{ url('/css/font-awesome.min.css') }}" rel="stylesheet">
</head>
<body>
    <section class="section">
        <div class="container">
            <div class="columns">
                <div class="column is-half is-offset-one-quarter">
                    <div class="box">
                        <div class="column">
                            <form role="form" action="/api/native/pad/bundle/save" method="post" enctype="multipart/form-data">
                                <div class="column">
                                    <h2>上傳bundle檔</h2>
                                </div>
                                <div class="column">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="text" class="input" name="version" placeholder="請輸入版本號"/>
                                    <small>version: 200.000.00.01 [系統號].[主版號].[次版號].[修訂號]</small>
                                    <small>bundle檔系統號為200開始</small>
                                </div>
                                <div class="column"> 
                                    <input type="file" name="bundle"/>
                                </div>
                                <div class="column"> 
                                    <div class="form-group">
                                        <button type="submit" class="button is-primary">上傳</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
    