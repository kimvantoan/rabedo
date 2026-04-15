<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Tin Tức & Blog')</title>
    @yield('seo_meta')
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              blue: {
                600: '#2563eb',
              }
            }
          }
        }
      }
    </script>
</head>
<body class="min-h-full flex flex-col antialiased bg-white text-black">
    <header class="border-b bg-white relative top-0 z-50">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center">
                <img src="{{ asset('logo.png') }}" alt="Rabedo Logo" class="h-10 w-auto">
            </a>
            <nav class="hidden sm:flex space-x-6">
            </nav>
            <div class="flex items-center space-x-4">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-black">Đến Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800">Đăng xuất</button>
                    </form>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-grow">
        @yield('content')
    </main>
</body>
</html>
