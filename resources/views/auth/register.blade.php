<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta — Gestión de Tiendas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

    <nav class="bg-white border-b px-6 py-4 flex justify-between items-center shadow-sm">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                </svg>
            </div>
            <span class="font-bold text-gray-800">Gestión de Tiendas</span>
        </div>
        <a href="{{ route('owner.login') }}" class="text-sm text-indigo-600 hover:underline">
            Ya tengo cuenta →
        </a>
    </nav>

    <div class="max-w-5xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-start">

            {{-- Info izquierda --}}
            <div class="hidden md:block">
                <h1 class="text-4xl font-bold text-gray-900 mb-4 leading-tight">
                    Controla tu tienda<br>
                    <span class="text-indigo-600">desde cualquier lugar</span>
                </h1>
                <p class="text-gray-500 mb-8">
                    Sistema completo para tiendas de barrio. Inventario, clientes, ventas, deudas y reportes en un solo lugar.
                </p>

                <div class="space-y-4 mb-8">
                    @foreach([
                        ['icon' => '📦', 'title' => 'Control de inventario',    'desc' => 'Alertas de stock bajo y entradas de proveedor'],
                        ['icon' => '👥', 'title' => 'Gestión de clientes',      'desc' => 'Control de deudas y abonos en tiempo real'],
                        ['icon' => '🛒', 'title' => 'Ventas y fiados',          'desc' => 'Registra ventas de contado y a crédito'],
                        ['icon' => '📊', 'title' => 'Reportes y estadísticas',  'desc' => 'Gráficas de ventas y productos más vendidos'],
                        ['icon' => '📱', 'title' => 'Funciona sin internet',    'desc' => 'PWA instalable en tu celular Android'],
                    ] as $feature)
                        <div class="flex items-start gap-3">
                            <span class="text-2xl">{{ $feature['icon'] }}</span>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $feature['title'] }}</p>
                                <p class="text-sm text-gray-500">{{ $feature['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                    <p class="text-indigo-700 font-semibold">💳 ¿Cómo funciona el pago?</p>
                    <ol class="text-indigo-600 text-sm mt-2 space-y-1 list-decimal list-inside">
                        <li>Elige tu plan y regístrate</li>
                        <li>Te contactamos por WhatsApp con los datos de pago</li>
                        <li>Pagas por Nequi o transferencia</li>
                        <li>Activamos tu tienda en minutos</li>
                    </ol>
                </div>
            </div>

            {{-- Formulario derecha --}}
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h2 class="text-xl font-bold mb-6 text-gray-800">Crear cuenta</h2>

                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 p-3 rounded-lg mb-4 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg mb-4 text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- Datos personales --}}
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Tus datos</p>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Nombre completo *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            placeholder="Juan Pérez"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Correo electrónico *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            placeholder="juan@ejemplo.com"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">WhatsApp</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                            placeholder="300 000 0000"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    {{-- Datos de la tienda --}}
                    <div class="border-t pt-4 mb-4">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Tu primera tienda</p>

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Nombre de la tienda *</label>
                            <input type="text" name="store_name" value="{{ old('store_name') }}" required
                                placeholder="Tienda Don Juan"
                                class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Dirección</label>
                            <input type="text" name="store_address" value="{{ old('store_address') }}"
                                placeholder="Calle 10 # 5-20"
                                class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    {{-- Planes --}}
                    <div class="border-t pt-4 mb-4">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Elige tu plan</p>

                        <div class="space-y-3">
                            @foreach($plans as $key => $plan)
                                <label class="flex items-center justify-between border-2 rounded-xl p-4 cursor-pointer transition-all"
                                       id="label_{{ $key }}">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="plan" value="{{ $key }}"
                                               {{ old('plan', $selectedPlan) === $key ? 'checked' : '' }}
                                               class="text-indigo-600"
                                               onchange="selectPlan('{{ $key }}')">
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ $plan['label'] }}</p>
                                            <p class="text-xs text-gray-400">
                                                @if($key === '3_months') Ahorra $15000
                                                @elseif($key === '6_months') Ahorra $30000
                                                @elseif($key === '1_year') ⭐ Ahorra $60000
                                                @else Precio estándar
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-indigo-600">${{ number_format($plan['price'], 0, ',', '.') }}</p>
                                        <p class="text-xs text-gray-400">COP</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mt-4 text-sm text-blue-700">
                            <p class="font-semibold mb-1">💳 ¿Cómo pagar?</p>
                            <p>Después de registrarte te contactamos por WhatsApp con los datos de pago. Tu tienda se activa en minutos.</p>
                        </div>
                    </div>

                    {{-- Contraseña --}}
                    <div class="border-t pt-4 mb-6">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Contraseña</p>

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Contraseña *</label>
                            <input type="password" name="password" required
                                placeholder="Mínimo 6 caracteres"
                                class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Confirmar contraseña *</label>
                            <input type="password" name="password_confirmation" required
                                class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 font-semibold text-sm">
                        Crear cuenta
                    </button>

                    <p class="text-center text-xs text-gray-400 mt-4">
                        Al registrarte aceptas los términos de uso del servicio.
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Marcar el plan seleccionado por defecto
        document.addEventListener('DOMContentLoaded', () => {
            const checked = document.querySelector('input[name="plan"]:checked');
            if (checked) selectPlan(checked.value);
        });

        function selectPlan(key) {
            document.querySelectorAll('[id^="label_"]').forEach(el => {
                el.classList.remove('border-indigo-500', 'bg-indigo-50');
                el.classList.add('border-gray-200');
            });
            const selected = document.getElementById('label_' + key);
            if (selected) {
                selected.classList.remove('border-gray-200');
                selected.classList.add('border-indigo-500', 'bg-indigo-50');
            }
        }
    </script>
</body>
</html>