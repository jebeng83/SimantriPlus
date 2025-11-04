<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Antri Poli (React)</title>
  @if(app()->environment('local', 'development'))
    @viteReactRefresh
  @endif
  @vite('resources/js/app.jsx')
</head>
<body class="bg-slate-900">
  <div id="antri-poli-root"></div>
  <script src="https://code.responsivevoice.org/responsivevoice.js?key=lTrSHda9"></script>
</body>
</html>