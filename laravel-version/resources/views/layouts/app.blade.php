<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Tin Tức & Blog')</title>
    @yield('seo_meta')
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4370452252708446"
     crossorigin="anonymous"></script>
    <!-- Google Fonts for Vietnamese -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
              serif: ['Lora', 'ui-serif', 'Georgia', 'serif'],
            },
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
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
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
    <footer class="bg-gray-50 border-t border-gray-200 py-12 mt-12">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-6 md:mb-0 text-center md:text-left">
                    <a href="{{ route('home') }}" class="inline-block">
                        <img src="{{ asset('logo.png') }}" alt="Logo" class="h-10 w-auto mx-auto md:mx-0 grayscale opacity-80 hover:grayscale-0 hover:opacity-100 transition duration-300">
                    </a>
                    <p class="mt-4 text-sm text-gray-500 max-w-sm">
                        Discover inspiring travel stories, authentic experiences, and breathtaking destinations with us.
                    </p>
                </div>
            </div>
            <div class="mt-8 border-t border-gray-200 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-gray-500">&copy; {{ date('Y') }} Rabedo. Travel & News Blog.</p>
                <div class="mt-4 md:mt-0 flex flex-wrap justify-center gap-x-6 gap-y-2 text-sm text-gray-500">
                    <a href="{{ route('page.about') }}" class="hover:text-gray-900 transition">About Us</a>
                    <a href="{{ route('page.contact') }}" class="hover:text-gray-900 transition">Contact</a>
                    <a href="{{ route('page.disclaimer') }}" class="hover:text-gray-900 transition">Disclaimer</a>
                    <a href="{{ route('page.privacy') }}" class="hover:text-gray-900 transition">Privacy Policy</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
