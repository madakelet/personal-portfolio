<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/site.js'])
</head>
<body>
    <div class="wrap"><h1>{{ config('app.name') }}</h1></div>
</body>
</html>
