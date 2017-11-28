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
    <section class="section">
        <div class="container">
            <div class="columns">
                <div class="column has-text-centered">
                    <div id="pdf-container"></div>
                </div>
            </div>
        </div>
    </section>
    <script src="{{ url('/js/pdf.js') }}"></script>
    <script src="{{ url('/js/pdf.worker.js') }}"></script>
    <script>
        var BASE64_MARKER = ';base64,';
        var pdfAsDataUri = '{{$src}}'; // shortened
        var pdfAsArray = convertDataURIToBinary(pdfAsDataUri);

        PDFJS.getDocument(pdfAsArray).then(function(pdf) {
            for (var pageNum = 1; pageNum <= pdf.numPages; ++pageNum) {
                pdf.getPage(pageNum).then(function(page) {
                // you can now use *page* here
                
                    var viewport = page.getViewport(1);

                    var canvas = document.createElement('canvas');
                    var context = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    var renderContext = {
                        canvasContext: context,
                        viewport: viewport
                    };
                    page.render(renderContext);

                    document.getElementById('pdf-container').appendChild(canvas);
                });
            }
        })
        
        function convertDataURIToBinary(dataURI) {
            var base64Index = dataURI.indexOf(BASE64_MARKER) + BASE64_MARKER.length;
            var base64 = dataURI.substring(base64Index);
            var raw = window.atob(base64);
            var rawLength = raw.length;
            var array = new Uint8Array(new ArrayBuffer(rawLength));

            for(var i = 0; i < rawLength; i++) {
                array[i] = raw.charCodeAt(i);
            }
            return array;
        }
    </script>
</body>
</html>
    