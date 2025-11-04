<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Antrian Loket (React)</title>
  @if(app()->environment('local', 'development'))
    @viteReactRefresh
  @endif
  @vite('resources/js/app.jsx')
</head>
<body class="bg-slate-900">
  <div id="loket-display-root"></div>
</body>
</html>