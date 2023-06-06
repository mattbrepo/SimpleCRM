<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="favicon.png">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        
    <title>Simple CRM</title>
  </head>
  <body class="antialiased">
    <div class="flex-center position-ref full-height" id="app">
      <app-component></app-component>
    </div>
    <script src="{{ mix('js/app.js') }}"></script>
  </body>
</html>
