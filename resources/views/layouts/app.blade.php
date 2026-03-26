<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema Bodega</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100 text-gray-800">

    <nav class="bg-gray-800 text-white px-6 py-3 flex gap-6">
        <a href="{{ route('ventas') }}"    class="hover:text-yellow-400">Ventas</a>
        <a href="{{ route('productos') }}" class="hover:text-yellow-400">Productos</a>
        <a href="{{ route('historial') }}" class="hover:text-yellow-400">Historial</a>
    </nav>

    <main class="p-6">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>