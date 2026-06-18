<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiTiendaDigital — Gestión para tiendas de barrio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        * { font-family: 'Inter', sans-serif; }
        .gradient-text {
            background: linear-gradient(135deg, #6366f1, #8b5cf6, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .card-hover { transition: transform 0.2s, box-shadow 0.2s; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(99,102,241,0.15); }
        .glow { box-shadow: 0 0 40px rgba(99,102,241,0.3); }
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-gray-950 text-gray-100">

    {{-- NAV --}}
    <nav class="fixed top-0 w-full z-50 bg-gray-950/80 backdrop-blur-xl border-b border-gray-800">
        <div class="max-w-6xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                    </svg>
                </div>
                <span class="font-bold text-white text-lg">MiTiendaDigital</span>
            </div>
            <div class="hidden md:flex items-center gap-8 text-sm text-gray-400">
                <a href="#features" class="hover:text-white transition-colors">Funciones</a>
                <a href="#pricing" class="hover:text-white transition-colors">Precios</a>
                <a href="#faq" class="hover:text-white transition-colors">Preguntas</a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('owner.login') }}"
                   class="text-sm text-gray-400 hover:text-white transition-colors">
                    Iniciar sesión
                </a>
                <a href="{{ route('register') }}"
                   class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm px-4 py-2 rounded-lg font-medium transition-colors">
                    Empezar gratis
                </a>
            </div>
        </div>
    </nav>

    {{-- HERO --}}
    <section class="pt-32 pb-20 px-6">
        <div class="max-w-4xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 bg-indigo-950 border border-indigo-800 text-indigo-300 text-xs px-4 py-2 rounded-full mb-8">
                <span class="w-2 h-2 bg-indigo-400 rounded-full animate-pulse"></span>
                Sistema 100% en la nube · Funciona sin internet
            </div>
            <h1 class="text-5xl md:text-7xl font-black text-white mb-6 leading-tight">
                Tu tienda,<br>
                <span class="gradient-text">digitalizada</span>
            </h1>
            <p class="text-xl text-gray-400 mb-10 max-w-2xl mx-auto leading-relaxed">
                Control total de inventario, clientes, ventas y deudas para tu tienda de barrio.
                Desde el celular, sin internet, sin complicaciones.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}"
                   class="bg-indigo-600 hover:bg-indigo-500 text-white px-8 py-4 rounded-xl font-semibold text-lg transition-all glow">
                    Comenzar ahora — $3.500/día
                </a>
                <a href="#features"
                   class="border border-gray-700 hover:border-gray-500 text-gray-300 hover:text-white px-8 py-4 rounded-xl font-semibold text-lg transition-all">
                    Ver funciones ↓
                </a>
            </div>
            <p class="text-gray-600 text-sm mt-6">Sin tarjeta de crédito · Activación en minutos · Soporte por WhatsApp</p>
        </div>
    </section>

    {{-- STATS --}}
    <section class="py-12 px-6 border-y border-gray-800 bg-gray-900/50">
        <div class="max-w-4xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            @foreach([
                ['number' => '100%', 'label' => 'En la nube'],
                ['number' => 'PWA',  'label' => 'Instala en tu celular'],
                ['number' => '24/7', 'label' => 'Disponible siempre'],
                ['number' => '∞',    'label' => 'Productos y clientes'],
            ] as $stat)
                <div>
                    <p class="text-3xl font-black text-indigo-400">{{ $stat['number'] }}</p>
                    <p class="text-gray-500 text-sm mt-1">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- FEATURES --}}
    <section id="features" class="py-24 px-6">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-black text-white mb-4">Todo lo que necesitas</h2>
                <p class="text-gray-400 text-lg">Un sistema completo pensado para tiendas de barrio colombianas</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach([
                    [
                        'icon'  => '📦',
                        'title' => 'Inventario inteligente',
                        'desc'  => 'Controla tu stock en tiempo real. Alertas automáticas cuando un producto se está acabando. Registro de entradas de proveedor.',
                        'color' => 'indigo',
                    ],
                    [
                        'icon'  => '👥',
                        'title' => 'Clientes y fiados',
                        'desc'  => 'Registra tus clientes y lleva el control de quién te debe y cuánto. Historial completo de abonos y pagos.',
                        'color' => 'violet',
                    ],
                    [
                        'icon'  => '🛒',
                        'title' => 'Ventas rápidas',
                        'desc'  => 'Registra ventas de contado o fiado en segundos. Calcula el cambio automáticamente. Compatible con lector de barras.',
                        'color' => 'purple',
                    ],
                    [
                        'icon'  => '📊',
                        'title' => 'Reportes y gráficas',
                        'desc'  => 'Ve cuánto vendiste hoy, esta semana o este mes. Conoce tus productos más vendidos y clientes con más deuda.',
                        'color' => 'indigo',
                    ],
                    [
                        'icon'  => '👨‍💼',
                        'title' => 'Múltiples empleados',
                        'desc'  => 'Crea usuarios para tus empleados con roles distintos: cajero, inventario o administrador. Cada uno ve solo lo que necesita.',
                        'color' => 'violet',
                    ],
                    [
                        'icon'  => '📱',
                        'title' => 'Funciona sin internet',
                        'desc'  => 'Instala la app en tu celular Android. Si se cae el internet, sigue vendiendo. Los datos se sincronizan solos cuando vuelve.',
                        'color' => 'purple',
                    ],
                ] as $feature)
                    <div class="card-hover bg-gray-900 border border-gray-800 rounded-2xl p-6">
                        <span class="text-4xl mb-4 block">{{ $feature['icon'] }}</span>
                        <h3 class="text-white font-bold text-lg mb-2">{{ $feature['title'] }}</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">{{ $feature['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- HOW IT WORKS --}}
    <section class="py-24 px-6 bg-gray-900/50">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-black text-white mb-4">¿Cómo funciona?</h2>
                <p class="text-gray-400">En menos de 10 minutos tu tienda está lista</p>
            </div>

            <div class="space-y-6">
                @foreach([
                    ['step' => '01', 'title' => 'Regístrate',         'desc' => 'Crea tu cuenta con el nombre de tu tienda y elige el plan que más te convenga.'],
                    ['step' => '02', 'title' => 'Paga y activa',      'desc' => 'Paga por Nequi o transferencia. Te activamos la tienda en minutos por WhatsApp.'],
                    ['step' => '03', 'title' => 'Carga tu inventario','desc' => 'Agrega tus productos con precios y stock inicial. Puedes escanear códigos de barras.'],
                    ['step' => '04', 'title' => 'Empieza a vender',   'desc' => 'Registra tus ventas, controla fiados y mira cómo crece tu negocio en tiempo real.'],
                ] as $step)
                    <div class="flex gap-6 items-start">
                        <div class="w-12 h-12 rounded-xl bg-indigo-600/20 border border-indigo-600/30 flex items-center justify-center flex-shrink-0">
                            <span class="text-indigo-400 font-black text-sm">{{ $step['step'] }}</span>
                        </div>
                        <div class="pt-2">
                            <h3 class="text-white font-bold text-lg mb-1">{{ $step['title'] }}</h3>
                            <p class="text-gray-400">{{ $step['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- PRICING --}}
    <section id="pricing" class="py-24 px-6">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-black text-white mb-4">Precios transparentes</h2>
                <p class="text-gray-400 text-lg">$3.500 por día · Sin costos ocultos · Cancela cuando quieras</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @foreach([
                    ['plan' => '1_month',  'label' => '1 mes',   'price' => 105000,  'daily' => 3500,  'saving' => null,  'popular' => false],
                    ['plan' => '3_months', 'label' => '3 meses', 'price' => 294000,  'daily' => 3267,  'saving' => '7%',  'popular' => false],
                    ['plan' => '6_months', 'label' => '6 meses', 'price' => 567000,  'daily' => 3150,  'saving' => '10%', 'popular' => true],
                    ['plan' => '1_year',   'label' => '1 año',   'price' => 1050000, 'daily' => 2877,  'saving' => '17%', 'popular' => false],
                ] as $p)
                    <div class="relative card-hover rounded-2xl p-6 {{ $p['popular'] ? 'bg-indigo-600 border-2 border-indigo-400' : 'bg-gray-900 border border-gray-800' }}">
                        @if($p['popular'])
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-indigo-400 text-indigo-950 text-xs font-bold px-3 py-1 rounded-full">
                                MÁS POPULAR
                            </div>
                        @endif
                        @if($p['saving'])
                            <div class="text-xs font-semibold {{ $p['popular'] ? 'text-indigo-200' : 'text-indigo-400' }} mb-2">
                                Ahorra {{ $p['saving'] }}
                            </div>
                        @endif
                        <p class="font-bold text-lg {{ $p['popular'] ? 'text-white' : 'text-white' }} mb-1">{{ $p['label'] }}</p>
                        <p class="text-3xl font-black {{ $p['popular'] ? 'text-white' : 'text-white' }}">
                            ${{ number_format($p['price'], 0, ',', '.') }}
                        </p>
                        <p class="text-sm {{ $p['popular'] ? 'text-indigo-200' : 'text-gray-500' }} mt-1 mb-6">
                            ${{ number_format($p['daily'], 0, ',', '.') }}/día
                        </p>
                        <a href="{{ route('register') }}?plan={{ $p['plan'] }}"
                           class="block text-center py-2.5 rounded-xl font-semibold text-sm transition-all
                                  {{ $p['popular'] ? 'bg-white text-indigo-600 hover:bg-indigo-50' : 'bg-indigo-600 hover:bg-indigo-500 text-white' }}">
                            Elegir plan
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 bg-gray-900 border border-gray-800 rounded-2xl p-6 text-center">
                <p class="text-gray-400 text-sm">
                    💳 Pagos por <strong class="text-white">Nequi, Daviplata o transferencia bancaria</strong> ·
                    Activación en minutos · Soporte por WhatsApp
                </p>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section id="faq" class="py-24 px-6 bg-gray-900/50">
        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-black text-white mb-4">Preguntas frecuentes</h2>
            </div>

            <div class="space-y-4" x-data="{ open: null }">
                @foreach([
                    ['q' => '¿Necesito internet para usar el sistema?',
                     'a' => 'No. Puedes instalar la app en tu celular y funciona sin internet. Cuando vuelve la conexión, los datos se sincronizan automáticamente.'],
                    ['q' => '¿Cuántas tiendas puedo tener?',
                     'a' => 'Puedes tener varias tiendas con una sola cuenta. Cada tienda tiene su propia suscripción mensual.'],
                    ['q' => '¿Cómo pago el servicio?',
                     'a' => 'Aceptamos Nequi, Daviplata y transferencia bancaria. Después de registrarte te enviamos los datos de pago por WhatsApp.'],
                    ['q' => '¿Puedo tener empleados usando el sistema?',
                     'a' => 'Sí. Puedes crear usuarios para tus empleados con roles diferentes: cajero, inventario o administrador.'],
                    ['q' => '¿Qué pasa si no renuevo a tiempo?',
                     'a' => 'El sistema te avisa con 7 días de anticipación. Si se vence, tu acceso se pausa pero tus datos quedan guardados. Al renovar todo vuelve a funcionar.'],
                    ['q' => '¿Hay contrato o permanencia?',
                     'a' => 'No. Pagas mes a mes y cancelas cuando quieras. Sin contratos ni letras pequeñas.'],
                ] as $i => $faq)
                    <details class="bg-gray-900 border border-gray-800 rounded-xl group">
                        <summary class="px-6 py-4 cursor-pointer flex justify-between items-center text-white font-medium list-none">
                            {{ $faq['q'] }}
                            <svg class="w-5 h-5 text-gray-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </summary>
                        <p class="px-6 pb-4 text-gray-400 text-sm leading-relaxed">{{ $faq['a'] }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA FINAL --}}
    <section class="py-24 px-6">
        <div class="max-w-3xl mx-auto text-center">
            <div class="bg-gradient-to-br from-indigo-950 to-gray-900 border border-indigo-800 rounded-3xl p-12">
                <h2 class="text-4xl font-black text-white mb-4">
                    ¿Listo para digitalizar<br>tu tienda?
                </h2>
                <p class="text-gray-400 mb-8">
                    Únete a los dueños de tienda que ya controlan su negocio desde el celular.
                </p>
                <a href="{{ route('register') }}"
                   class="inline-block bg-indigo-600 hover:bg-indigo-500 text-white px-10 py-4 rounded-xl font-bold text-lg transition-all glow">
                    Registrarme ahora
                </a>
                <p class="text-gray-600 text-sm mt-4">Activación en minutos · Soporte por WhatsApp</p>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="border-t border-gray-800 py-8 px-6">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded bg-indigo-600 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                    </svg>
                </div>
                <span class="text-gray-400 text-sm font-medium">MiTiendaDigital</span>
            </div>
            <p class="text-gray-600 text-sm">© {{ date('Y') }} MiTiendaDigital · Hecho en Colombia 🇨🇴</p>
            <div class="flex gap-6 text-sm text-gray-500">
                <a href="{{ route('owner.login') }}" class="hover:text-gray-300">Iniciar sesión</a>
                <a href="{{ route('register') }}" class="hover:text-gray-300">Registrarse</a>
            </div>
        </div>
    </footer>

</body>
</html>