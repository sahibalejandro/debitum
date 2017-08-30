<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="turbolinks-cache-control" content="no-cache">
        <title>Turbo Payments</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
        <script src="{{ mix('js/app.js') }}"></script>
    </head>
    <body>
        <div class="container">
            <div id="app">
                <{{ $component }}
                @foreach(isset($props) ? $props : [] as $prop => $value)
                    {{ $prop }}="{{ $value }}"
                @endforeach
                ></{{ $component }}>
            </div>
        </div>
    </body>
</html>
