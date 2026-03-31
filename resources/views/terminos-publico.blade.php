<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Términos y Condiciones</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f4fdf8; 
               display: flex; justify-content: center; padding: 2rem; }
        .card { background: #fff; border-radius: 16px; padding: 2rem; 
                max-width: 760px; width: 100%; box-shadow: 0 4px 16px rgba(39,184,109,.14); }
        h1 { color: #27B86D; font-size: 1.5rem; margin-bottom: .5rem; }
        .version { font-size: .8rem; color: #89b09e; margin-bottom: 1.5rem; }
        .content { line-height: 1.8; color: #4a7360; white-space: pre-wrap; }
        .back { display: inline-block; margin-top: 1.5rem; color: #27B86D; font-weight: 600; text-decoration: none; }
    </style>
</head>
<body>
    <div class="card">
        @if($termino)
            <h1>{{ $termino->titulo }}</h1>
            <p class="version">Versión {{ $termino->version }} · 
               {{ \Carbon\Carbon::parse($termino->fecha_creacion)->format('d/m/Y') }}</p>
            <div class="content">{{ $termino->contenido }}</div>
        @else
            <h1>Términos y Condiciones</h1>
            <p>No hay términos publicados aún.</p>
        @endif
        <a href="{{ route('login') }}" class="back">← Volver al inicio</a>
    </div>
</body>
</html>