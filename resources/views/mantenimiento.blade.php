<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema Bodega — Mantenimiento</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-turquesa: #27B86D;
            --color-verde:    #43AA72;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--color-turquesa) 0%, var(--color-verde) 100%);
            padding: 1.5rem;
        }
        .card {
            background: #fff;
            border-radius: 24px;
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 460px;
            text-align: center;
            box-shadow: 0 16px 48px rgba(15,45,30,.25);
        }
        .icon {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, var(--color-turquesa), var(--color-verde));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
            box-shadow: 0 0 0 12px rgba(39,184,109,.12);
        }
        h1 { font-size: 1.5rem; font-weight: 800; color: #0f2d1e; margin-bottom: .5rem; }
        .mensaje {
            font-size: .925rem; color: #4a7360; line-height: 1.6; margin-bottom: 1.75rem;
        }
        .badge {
            display: inline-block;
            background: #d4f5e5; color: #1d9458;
            font-size: .75rem; font-weight: 700;
            padding: .3rem .85rem; border-radius: 99px;
            margin-bottom: 1.75rem;
        }
        .footer { font-size: .78rem; color: #89b09e; }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 12px rgba(39,184,109,.12); }
            50%       { box-shadow: 0 0 0 20px rgba(39,184,109,.04); }
        }
        .icon { animation: pulse 2.5s ease-in-out infinite; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">&#9881;</div>

        <span class="badge">En mantenimiento</span>

        <h1>Sistema temporalmente no disponible</h1>

        <p class="mensaje">
            @php
                $msg = session('mensaje_bloqueo');
                if (!$msg) {
                    try {
                        $r = \Illuminate\Support\Facades\DB::select(
                            'EXEC bodega.sp_get_parametro @nombre=?', ['MENSAJE_MANTENIMIENTO']
                        );
                        $msg = !empty($r) ? e($r[0]->valor) : 'Estamos realizando mejoras. Vuelve pronto.';
                    } catch (\Throwable) {
                        $msg = 'Estamos realizando mejoras. Vuelve pronto.';
                    }
                }
                echo e($msg);
            @endphp
        </p>

        <p class="footer">
            Si eres administrador puedes
            <a href="{{ route('login') }}" style="color:var(--color-turquesa); font-weight:600;">iniciar sesion aqui</a>.
        </p>
    </div>
</body>
</html>