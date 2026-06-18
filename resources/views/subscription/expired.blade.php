<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suscripción vencida</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center">

            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-800 mb-2">Suscripción vencida</h1>
            <p class="text-gray-500 mb-6">
                La suscripción de
                <strong>{{ session('store_name', 'tu tienda') }}</strong>
                ha vencido. Para continuar usando el sistema contacta al administrador.
            </p>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 text-left">
                <p class="text-sm font-semibold text-amber-700 mb-2">¿Cómo renovar?</p>
                <ol class="text-sm text-amber-600 space-y-1 list-decimal list-inside">
                    <li>Realiza el pago por Nequi o transferencia</li>
                    <li>Envía el comprobante al administrador</li>
                    <li>Tu acceso se reactiva en minutos</li>
                </ol>
            </div>

            <div class="bg-gray-50 rounded-xl p-4 mb-6 text-sm text-gray-600">
                <p class="font-medium mb-1">Contacto administrador:</p>
                <p>📱 WhatsApp: <strong>{{ config('app.admin_phone', '300 000 0000') }}</strong></p>
                <p>📧 Email: <strong>{{ config('app.admin_email', 'admin@tuapp.com') }}</strong></p>
            </div>

            <div class="flex gap-3">
                @if(Auth::guard('owner')->check())
                    <a href="{{ route('owner.stores.index') }}"
                       class="flex-1 border border-gray-200 text-gray-600 py-2 rounded-lg text-sm hover:bg-gray-50">
                        ← Mis tiendas
                    </a>
                @endif
                <button onclick="window.location.reload()"
                    class="flex-1 bg-indigo-600 text-white py-2 rounded-lg text-sm hover:bg-indigo-700">
                    Verificar acceso
                </button>
            </div>
        </div>
    </div>
</body>
</html>