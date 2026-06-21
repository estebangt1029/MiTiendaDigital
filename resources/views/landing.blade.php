<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MiTiendaDigital | Sistema POS para Tiendas de Barrio</title>

    <meta name="description"
          content="Controla inventario, ventas, clientes y fiados desde cualquier lugar. Sistema POS moderno para tiendas de barrio.">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

        *{
            font-family:'Inter',sans-serif;
        }

        html{
            scroll-behavior:smooth;
        }

        body{
            background:#020617;
            color:white;
            overflow-x:hidden;
        }

        :root{
            --primary:#6366f1;
            --primary-light:#818cf8;
            --card:#0f172a;
            --border:rgba(255,255,255,.08);
        }

        .gradient-text{
            background:linear-gradient(
                135deg,
                #6366f1,
                #8b5cf6,
                #a78bfa
            );

            -webkit-background-clip:text;
            -webkit-text-fill-color:transparent;
        }

        .glass{
            backdrop-filter:blur(16px);
            background:rgba(15,23,42,.65);
            border:1px solid rgba(255,255,255,.08);
        }

        .card-hover{
            transition:.3s ease;
        }

        .card-hover:hover{
            transform:translateY(-6px);
            border-color:rgba(99,102,241,.4);
            box-shadow:
            0 20px 50px rgba(99,102,241,.15);
        }

        .glow{
            box-shadow:
            0 0 30px rgba(99,102,241,.25);
        }

        .hero-grid{
            background-image:
            linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);

            background-size:40px 40px;
        }

        .pricing-popular{
            background:
            linear-gradient(
            180deg,
            rgba(99,102,241,.18),
            rgba(15,23,42,.95)
            );
        }

        .section-divider{
            height:1px;

            background:
            linear-gradient(
            90deg,
            transparent,
            rgba(255,255,255,.12),
            transparent
            );
        }

        .mobile-menu{
            transition:.3s ease;
        }

        .floating-blur{
            position:absolute;
            border-radius:999px;
            filter:blur(120px);
            opacity:.25;
            pointer-events:none;
        }

        .btn-primary{
            background:linear-gradient(
            135deg,
            #6366f1,
            #8b5cf6
            );

            transition:.25s ease;
        }

        .btn-primary:hover{
            transform:translateY(-2px);
            box-shadow:
            0 10px 30px rgba(99,102,241,.35);
        }

        .btn-secondary{
            border:1px solid rgba(255,255,255,.1);
            background:rgba(255,255,255,.03);
            transition:.25s ease;
        }

        .btn-secondary:hover{
            background:rgba(255,255,255,.06);
        }

        @media(max-width:768px){

            .hero-title{
                font-size:2.8rem !important;
                line-height:1.05;
            }

            .hero-subtitle{
                font-size:1rem;
            }
        }
    </style>
</head>

<nav
x-data="{open:false}"
class="fixed top-0 left-0 right-0 z-50 border-b border-white/5 bg-slate-950/80 backdrop-blur-xl">

    <div class="max-w-7xl mx-auto px-6">

        <div class="h-20 flex items-center justify-between">

            <a href="#"
               class="flex items-center gap-3">

                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center">

                    <svg
                    class="w-5 h-5 text-white"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                        <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
                    </svg>

                </div>

                <div>
                    <p class="font-bold text-white">
                        MiTiendaDigital
                    </p>

                    <p class="text-xs text-slate-500">
                        Sistema POS
                    </p>
                </div>

            </a>

            <div class="hidden md:flex items-center gap-8 text-sm">

                <a href="#features" class="text-slate-400 hover:text-white">
                    Funciones
                </a>

                <a href="#pricing" class="text-slate-400 hover:text-white">
                    Precios
                </a>

                <a href="#faq" class="text-slate-400 hover:text-white">
                    FAQ
                </a>

            </div>

            <div class="hidden md:flex items-center gap-3">

                <a
                href="{{ route('owner.login') }}"
                class="text-slate-400 hover:text-white">

                    Ingresar
                </a>

                <a
                href="{{ route('register') }}"
                class="btn-primary px-5 py-2.5 rounded-xl text-white font-medium">

                    Empezar gratis
                </a>

            </div>

            <button
            @click="open=!open"
            class="md:hidden">

                <svg class="w-7 h-7"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">

                    <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"/>
                </svg>

            </button>

        </div>

        <div
        x-show="open"
        x-transition
        class="md:hidden pb-6 space-y-4">

            <a href="#features" class="block text-slate-300">
                Funciones
            </a>

            <a href="#pricing" class="block text-slate-300">
                Precios
            </a>

            <a href="#faq" class="block text-slate-300">
                Preguntas
            </a>

            <a href="{{ route('owner.login') }}"
               class="block text-slate-300">

                Iniciar sesión
            </a>

            <a href="{{ route('register') }}"
               class="block btn-primary text-center py-3 rounded-xl text-white">

                Empezar gratis
            </a>

        </div>

    </div>

</nav>

<section class="relative overflow-hidden pt-36 pb-24 hero-grid">

    <div class="floating-blur w-96 h-96 bg-indigo-500 top-0 left-0"></div>
    <div class="floating-blur w-96 h-96 bg-violet-500 bottom-0 right-0"></div>

    <div class="max-w-7xl mx-auto px-6">

        <div class="grid lg:grid-cols-2 gap-16 items-center">

            {{-- TEXTO --}}
            <div>

                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-indigo-500/20 bg-indigo-500/10 text-indigo-300 text-sm mb-8">

                    <span class="w-2 h-2 bg-indigo-400 rounded-full animate-pulse"></span>

                    Sistema POS moderno para tiendas de barrio

                </div>

                <h1 class="hero-title text-5xl lg:text-7xl font-black leading-tight mb-6">

                    Controla tu

                    <span class="gradient-text">
                        tienda
                    </span>

                    desde cualquier lugar.

                </h1>

                <p class="hero-subtitle text-xl text-slate-400 leading-relaxed mb-10 max-w-xl">

                    Inventario, ventas, clientes, fiados y reportes
                    en una sola plataforma.

                    Accede desde tu celular, tablet o computador.

                </p>

                <div class="flex flex-col sm:flex-row gap-4">

                    <a
                    href="{{ route('register') }}"
                    class="btn-primary px-8 py-4 rounded-2xl font-semibold text-center">

                        Empezar ahora

                    </a>

                    <a
                    href="#features"
                    class="btn-secondary px-8 py-4 rounded-2xl text-center">

                        Ver funciones

                    </a>

                </div>

                <div class="grid grid-cols-3 gap-6 mt-12">

                    <div>

                        <p class="text-3xl font-black text-white">
                            24/7
                        </p>

                        <p class="text-sm text-slate-500">
                            Disponible
                        </p>

                    </div>

                    <div>

                        <p class="text-3xl font-black text-white">
                            PWA
                        </p>

                        <p class="text-sm text-slate-500">
                            Instalable
                        </p>

                    </div>

                    <div>

                        <p class="text-3xl font-black text-white">
                            Cloud
                        </p>

                        <p class="text-sm text-slate-500">
                            En la nube
                        </p>

                    </div>

                </div>

            </div>

            {{-- MOCKUP --}}
            <div class="relative">

                <div class="glass rounded-3xl p-4 border border-white/10 shadow-2xl">

                    <div class="flex items-center gap-2 mb-4">

                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>

                    </div>

                    <div class="bg-slate-950 rounded-2xl overflow-hidden">

                        <div class="grid grid-cols-12 min-h-[500px]">

                            {{-- SIDEBAR --}}
                            <div class="col-span-4 border-r border-white/5 p-4">

                                <div class="bg-indigo-500/10 border border-indigo-500/20 rounded-2xl p-3 mb-4">

                                    <p class="text-xs text-slate-500">
                                        Tienda activa
                                    </p>

                                    <p class="font-bold text-white">
                                        Tienda Alegría
                                    </p>

                                </div>

                                <div class="space-y-3">

                                    <div class="bg-indigo-500/15 border border-indigo-500/20 rounded-xl px-3 py-2 text-indigo-300">
                                        Inventario
                                    </div>

                                    <div class="px-3 py-2 text-slate-400">
                                        Nueva venta
                                    </div>

                                    <div class="px-3 py-2 text-slate-400">
                                        Clientes
                                    </div>

                                    <div class="px-3 py-2 text-slate-400">
                                        Reportes
                                    </div>

                                </div>

                            </div>

                            {{-- CONTENIDO --}}
                            <div class="col-span-8 p-5">

                                <div class="flex justify-between items-center mb-5">

                                    <h3 class="font-bold">
                                        Inventario
                                    </h3>

                                    <button class="bg-indigo-600 px-3 py-1 rounded-lg text-sm">
                                        Nuevo
                                    </button>

                                </div>

                                <div class="grid grid-cols-2 gap-3 mb-5">

                                    <div class="bg-slate-900 rounded-xl p-3">

                                        <p class="text-slate-500 text-xs">
                                            Productos
                                        </p>

                                        <p class="font-bold text-xl">
                                            245
                                        </p>

                                    </div>

                                    <div class="bg-slate-900 rounded-xl p-3">

                                        <p class="text-slate-500 text-xs">
                                            Ventas Hoy
                                        </p>

                                        <p class="font-bold text-xl">
                                            $520k
                                        </p>

                                    </div>

                                </div>

                                <div class="space-y-3">

                                    @for($i=0;$i<5;$i++)

                                    <div class="bg-slate-900 rounded-xl p-3 flex justify-between">

                                        <div>

                                            <div class="h-3 w-24 bg-slate-700 rounded"></div>

                                            <div class="h-2 w-16 bg-slate-800 rounded mt-2"></div>

                                        </div>

                                        <div class="h-6 w-12 rounded-full bg-emerald-500/20"></div>

                                    </div>

                                    @endfor

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<section class="py-16">

    <div class="max-w-6xl mx-auto px-6">

        <div class="grid md:grid-cols-4 gap-6">

            <div class="glass rounded-2xl p-6">

                <h3 class="font-bold mb-2">
                    Inventario Inteligente
                </h3>

                <p class="text-slate-400 text-sm">
                    Control total del stock en tiempo real.
                </p>

            </div>

            <div class="glass rounded-2xl p-6">

                <h3 class="font-bold mb-2">
                    Clientes y Fiados
                </h3>

                <p class="text-slate-400 text-sm">
                    Lleva el control de cada deuda fácilmente.
                </p>

            </div>

            <div class="glass rounded-2xl p-6">

                <h3 class="font-bold mb-2">
                    Reportes
                </h3>

                <p class="text-slate-400 text-sm">
                    Conoce exactamente cuánto vende tu negocio.
                </p>

            </div>

            <div class="glass rounded-2xl p-6">

                <h3 class="font-bold mb-2">
                    Multiplataforma
                </h3>

                <p class="text-slate-400 text-sm">
                    Funciona en celular, tablet y computador.
                </p>

            </div>

        </div>

    </div>

</section>

<div class="max-w-7xl mx-auto">

    <div class="text-center mb-20">

        <span class="text-indigo-400 font-semibold">
            FUNCIONES
        </span>

        <h2 class="text-4xl md:text-5xl font-black mt-4 mb-6">
            Todo lo que necesita una tienda moderna
        </h2>

        <p class="text-slate-400 max-w-2xl mx-auto text-lg">
            Diseñado para que puedas controlar ventas,
            inventario, clientes y fiados desde cualquier lugar.
        </p>

    </div>

    <div class="grid lg:grid-cols-3 gap-6">

        <div class="glass rounded-3xl p-8 card-hover">

            <div class="w-14 h-14 rounded-2xl bg-indigo-500/15 flex items-center justify-center mb-6">

                📦

            </div>

            <h3 class="text-xl font-bold mb-3">
                Inventario Inteligente
            </h3>

            <p class="text-slate-400">
                Controla existencias, entradas,
                salidas y alertas de stock bajo.
            </p>

        </div>

        <div class="glass rounded-3xl p-8 card-hover">

            <div class="w-14 h-14 rounded-2xl bg-violet-500/15 flex items-center justify-center mb-6">

                💰

            </div>

            <h3 class="text-xl font-bold mb-3">
                Ventas Rápidas
            </h3>

            <p class="text-slate-400">
                Registra ventas en segundos y calcula
                cambios automáticamente.
            </p>

        </div>

        <div class="glass rounded-3xl p-8 card-hover">

            <div class="w-14 h-14 rounded-2xl bg-purple-500/15 flex items-center justify-center mb-6">

                👥

            </div>

            <h3 class="text-xl font-bold mb-3">
                Clientes y Fiados
            </h3>

            <p class="text-slate-400">
                Lleva control completo de deudas,
                pagos y movimientos.
            </p>

        </div>

        <div class="glass rounded-3xl p-8 card-hover">

            <div class="w-14 h-14 rounded-2xl bg-indigo-500/15 flex items-center justify-center mb-6">

                📊

            </div>

            <h3 class="text-xl font-bold mb-3">
                Reportes
            </h3>

            <p class="text-slate-400">
                Conoce tus ventas diarias,
                semanales y mensuales.
            </p>

        </div>

        <div class="glass rounded-3xl p-8 card-hover">

            <div class="w-14 h-14 rounded-2xl bg-violet-500/15 flex items-center justify-center mb-6">

                👨‍💼

            </div>

            <h3 class="text-xl font-bold mb-3">
                Empleados
            </h3>

            <p class="text-slate-400">
                Crea cuentas para cajeros,
                administradores y vendedores.
            </p>

        </div>

        <div class="glass rounded-3xl p-8 card-hover">

            <div class="w-14 h-14 rounded-2xl bg-purple-500/15 flex items-center justify-center mb-6">

                📱

            </div>

            <h3 class="text-xl font-bold mb-3">
                Funciona Sin Internet
            </h3>

            <p class="text-slate-400">
                Sigue vendiendo incluso cuando
                falla la conexión.
            </p>

        </div>

    </div>

</div>

<section id="pricing" class="py-32">

    <div class="max-w-7xl mx-auto px-6">

        <div class="text-center mb-20">

            <span class="text-indigo-400 font-semibold">
                PRECIOS
            </span>

            <h2 class="text-5xl font-black mt-4 mb-6">
                Planes simples
            </h2>

            <p class="text-slate-400 text-lg">
                Paga solo el tiempo que necesites.
            </p>

        </div>

        <div class="grid lg:grid-cols-4 gap-6">

            <!-- MES -->

            <div class="glass rounded-3xl p-8">

                <h3 class="font-bold text-xl mb-4">
                    1 Mes
                </h3>

                <div class="mb-8">

                    <span class="text-4xl font-black">
                        $105.000
                    </span>

                </div>

                <ul class="space-y-3 text-slate-300 mb-8">

                    <li>✓ Inventario</li>
                    <li>✓ Clientes</li>
                    <li>✓ Fiados</li>
                    <li>✓ Reportes</li>
                    <li>✓ Soporte</li>

                </ul>

                <a href="{{ route('register') }}"
                   class="block text-center btn-secondary py-3 rounded-xl">

                    Elegir

                </a>

            </div>

             <!-- 3 MESES -->

            <div class="glass rounded-3xl p-8">

                <h3 class="font-bold text-xl mb-4">
                    3 Meses
                </h3>

                <div class="mb-8">

                    <span class="text-4xl font-black">
                        $300.000
                    </span>

                </div>

                <ul class="space-y-3 text-slate-300 mb-8">

                    <li>✓ Inventario</li>
                    <li>✓ Clientes</li>
                    <li>✓ Fiados</li>
                    <li>✓ Reportes</li>
                    <li>✓ Ahorro $15.000</li>

                </ul>

                <a href="{{ route('register') }}"
                   class="block text-center btn-secondary py-3 rounded-xl">

                    Elegir

                </a>

            </div>

            <!-- POPULAR -->

            <div class="pricing-popular rounded-3xl p-8 border border-indigo-500 relative scale-105">

                <div class="absolute top-0 right-0 bg-indigo-500 text-white text-xs px-3 py-1 rounded-bl-xl rounded-tr-3xl">

                    MÁS POPULAR

                </div>

                <h3 class="font-bold text-xl mb-4">
                    6 Meses
                </h3>

                <div class="mb-8">

                    <span class="text-5xl font-black">
                        $600.000
                    </span>

                </div>

                <ul class="space-y-3 mb-8">

                    <li>✓ Inventario</li>
                    <li>✓ Clientes</li>
                    <li>✓ Fiados</li>
                    <li>✓ Reportes</li>
                    <li>✓ Soporte</li>
                    <li>✓ 10% descuento</li>

                </ul>

                <a href="{{ route('register') }}"
                   class="block text-center btn-primary py-3 rounded-xl">

                    Elegir Plan

                </a>

            </div>

           

            <!-- AÑO -->

            <div class="glass rounded-3xl p-8">

                <h3 class="font-bold text-xl mb-4">
                    1 Año
                </h3>

                <div class="mb-8">

                    <span class="text-4xl font-black">
                        $1.200.000
                    </span>

                </div>

                <ul class="space-y-3 text-slate-300 mb-8">

                    <li>✓ Inventario</li>
                    <li>✓ Clientes</li>
                    <li>✓ Fiados</li>
                    <li>✓ Reportes</li>
                    <li>✓ 17% descuento</li>

                </ul>

                <a href="{{ route('register') }}"
                   class="block text-center btn-secondary py-3 rounded-xl">

                    Elegir

                </a>

            </div>

        </div>

    </div>

</section>

<section id="faq" class="py-32">

    <div class="max-w-4xl mx-auto px-6">

        <div class="text-center mb-20">

            <span class="text-indigo-400 font-semibold">
                PREGUNTAS FRECUENTES
            </span>

            <h2 class="text-5xl font-black mt-4 mb-6">
                Resolvemos tus dudas
            </h2>

            <p class="text-slate-400">
                Todo lo que necesitas saber antes de empezar.
            </p>

        </div>

        <div class="space-y-4">

            <details class="glass rounded-2xl p-6 group">

                <summary class="cursor-pointer font-semibold flex justify-between items-center list-none">

                    ¿Necesito internet para usar el sistema?

                    <span class="group-open:rotate-45 transition">
                        +
                    </span>

                </summary>

                <p class="mt-4 text-slate-400">
                    No. Puedes instalar la aplicación y seguir trabajando.
                    Cuando vuelva la conexión los datos se sincronizan.
                </p>

            </details>

            <details class="glass rounded-2xl p-6 group">

                <summary class="cursor-pointer font-semibold flex justify-between items-center list-none">

                    ¿Puedo tener empleados?

                    <span class="group-open:rotate-45 transition">
                        +
                    </span>

                </summary>

                <p class="mt-4 text-slate-400">
                    Sí. Puedes crear usuarios con permisos diferentes.
                </p>

            </details>

            <details class="glass rounded-2xl p-6 group">

                <summary class="cursor-pointer font-semibold flex justify-between items-center list-none">

                    ¿Cómo realizo los pagos?

                    <span class="group-open:rotate-45 transition">
                        +
                    </span>

                </summary>

                <p class="mt-4 text-slate-400">
                    Nequi, Daviplata o transferencia bancaria.
                </p>

            </details>

            <details class="glass rounded-2xl p-6 group">

                <summary class="cursor-pointer font-semibold flex justify-between items-center list-none">

                    ¿Hay permanencia mínima?

                    <span class="group-open:rotate-45 transition">
                        +
                    </span>

                </summary>

                <p class="mt-4 text-slate-400">
                    No. Puedes cancelar cuando quieras.
                </p>

            </details>

        </div>

    </div>

</section>

<section class="py-32">

    <div class="max-w-7xl mx-auto px-6">

        <div class="text-center mb-20">

            <span class="text-indigo-400 font-semibold">
                TESTIMONIOS
            </span>

            <h2 class="text-5xl font-black mt-4">
                Pensado para dueños de tienda
            </h2>

        </div>

        <div class="grid lg:grid-cols-3 gap-6">

            <div class="glass rounded-3xl p-8">

                <p class="text-slate-300 mb-6">
                    "Ahora sé exactamente cuánto vendo cada día."
                </p>

                <div>
                    <p class="font-semibold">
                        Próximamente
                    </p>

                    <p class="text-slate-500 text-sm">
                        Dueño de tienda
                    </p>
                </div>

            </div>

            <div class="glass rounded-3xl p-8">

                <p class="text-slate-300 mb-6">
                    "El control de fiados me ahorra mucho tiempo."
                </p>

                <div>
                    <p class="font-semibold">
                        Próximamente
                    </p>

                    <p class="text-slate-500 text-sm">
                        Comerciante
                    </p>
                </div>

            </div>

            <div class="glass rounded-3xl p-8">

                <p class="text-slate-300 mb-6">
                    "Puedo revisar el negocio desde mi celular."
                </p>

                <div>
                    <p class="font-semibold">
                        Próximamente
                    </p>

                    <p class="text-slate-500 text-sm">
                        Tendero
                    </p>
                </div>

            </div>

        </div>

    </div>

</section>

<section class="py-32">

    <div class="max-w-5xl mx-auto px-6">

        <div class="rounded-[32px] overflow-hidden relative">

            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-violet-600 to-purple-700"></div>

            <div class="relative p-12 md:p-20 text-center">

                <h2 class="text-4xl md:text-6xl font-black mb-6">

                    Empieza a controlar
                    tu negocio hoy mismo

                </h2>

                <p class="text-indigo-100 text-lg max-w-2xl mx-auto mb-10">

                    Ventas, inventario, clientes,
                    fiados y reportes desde una sola plataforma.

                </p>

                <a
                href="{{ route('register') }}"
                class="bg-white text-indigo-700 px-8 py-4 rounded-2xl font-bold inline-block hover:scale-105 transition">

                    Crear mi tienda gratis

                </a>

            </div>

        </div>

    </div>

</section>

<footer class="border-t border-white/5 py-12">

    <div class="max-w-7xl mx-auto px-6">

        <div class="flex flex-col lg:flex-row justify-between items-center gap-6">

            <div class="flex items-center gap-3">

                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center">

                    🏪

                </div>

                <div>

                    <p class="font-bold">
                        MiTiendaDigital
                    </p>

                    <p class="text-slate-500 text-sm">
                        Sistema POS para tiendas
                    </p>

                </div>

            </div>

            <div class="flex gap-6 text-slate-400 text-sm">

                <a href="#features">Funciones</a>

                <a href="#pricing">Precios</a>

                <a href="#faq">FAQ</a>

            </div>

            <p class="text-slate-500 text-sm">

                © {{ date('Y') }} MiTiendaDigital

            </p>

        </div>

    </div>

</footer>