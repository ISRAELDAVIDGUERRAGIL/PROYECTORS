<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error @yield('code')</title>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-4xl w-full bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
            <div class="flex flex-col md:flex-row items-center">

                {{-- Imagen del muñeco --}}
                <div class="w-full md:w-1/2 p-8 flex items-center justify-center bg-gray-50 dark:bg-gray-700">
                    <img src="{{ asset('images/kuromi-error.png') }}"
                         alt="Error"
                         class="max-w-full h-auto max-h-80 object-contain"
                         onerror="this.style.display='none'">
                </div>

                {{-- Texto del error --}}
                <div class="w-full md:w-1/2 p-8 text-center md:text-left">
                    <h1 class="text-8xl font-extrabold text-gray-900 dark:text-white mb-2">
                        @yield('code')
                    </h1>
                    <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200 mb-4">
                        @yield('title')
                    </h2>
                    <p class="text-gray-500 dark:text-gray-400 mb-8">
                        @yield('message')
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center md:justify-start">
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                            Volver al inicio
                        </a>
                        @auth
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    Cerrar sesión
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}"
                               class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                Iniciar sesión
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
