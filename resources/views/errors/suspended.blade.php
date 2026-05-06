<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Título de la página -->
    <title>Acceso Suspendido | Cepre Vallejo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-6">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-gray-100 p-8 text-center">
        <!-- Icono de Advertencia -->
        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-100 mb-6">
            <svg class="h-10 w-10 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
            </svg>
        </div>

        <!-- Título Principal -->
        <h1 class="text-2xl font-bold text-gray-900 mb-2">
            Acceso Restringido
        </h1>
        
        <!-- Mensaje -->
        <p class="text-gray-600 mb-8">
            Lo sentimos, tu cuenta en <span class="font-semibold text-gray-800">Cepre Vallejo</span> ha sido desactivada temporalmente. Por favor, contacta con el área administrativa para resolver tu situación.
        </p>

        <!-- Acciones -->
        <div class="space-y-3">
            <a href="/" 
               class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-xl transition duration-200 shadow-lg shadow-indigo-200">
                Ir al Inicio
            </a>
        </div>

        <!-- Footer -->
        <div class="mt-8 pt-6 border-t border-gray-100">
            <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">
                Plataforma Tukipu v3.0
            </p>
        </div>
    </div>

</body>
</html>