<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'News & Stories: Drama Stories, Lifestyle Tales & Trending Topics')</title>
    @yield('seo_meta')
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('logo_sharp.png') }}" type="image/png">
    @if(!request()->is('admin*') && !request()->is('login'))
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4370452252708446"
     crossorigin="anonymous"></script>
     
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-J7Y5ZEQ8HB"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-J7Y5ZEQ8HB');
    </script>
    @endif
    <!-- Google Fonts for Vietnamese -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    
    <!-- Vite / Local Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col antialiased bg-white text-black">
    <header class="border-b bg-white relative top-0 z-50">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-center relative">
            <a href="{{ route('home') }}" class="flex items-center">
                <img src="{{ asset('logo_sharp.png') }}" alt="Rabedo Logo" class="h-10 w-auto">
            </a>

        </div>
    </header>

    <main class="flex-grow">
        @yield('content')
    <footer class="bg-gray-50 border-t border-gray-200 py-12 mt-12">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-6 md:mb-0 text-center md:text-left">
                    <a href="{{ route('home') }}" class="inline-block">
                        <img src="{{ asset('logo_sharp.png') }}" alt="Logo" class="h-10 w-auto mx-auto md:mx-0 grayscale opacity-80 hover:grayscale-0 hover:opacity-100 transition duration-300">
                    </a>
                    <p class="mt-4 text-sm text-gray-500 max-w-sm">
                        Explore drama stories, lifestyle moments, and trending topics shaping everyday life.
                    </p>
                </div>
            </div>
            <div class="mt-8 border-t border-gray-200 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-gray-500">&copy; {{ date('Y') }} Rabedo</p>
                <div class="mt-4 md:mt-0 flex flex-wrap justify-center gap-x-6 gap-y-2 text-sm text-gray-500">
                    <a href="{{ route('page.about') }}" class="hover:text-gray-900 transition">About Us</a>
                    <a href="{{ route('page.contact') }}" class="hover:text-gray-900 transition">Contact</a>
                    <a href="{{ route('page.disclaimer') }}" class="hover:text-gray-900 transition">Disclaimer</a>
                    <a href="{{ route('page.privacy') }}" class="hover:text-gray-900 transition">Privacy Policy</a>
                </div>
            </div>
        </div>
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
            alerts.forEach(function(alert) {
                if(alert.querySelector('p')) {
                    setTimeout(function() {
                        alert.style.transition = 'opacity 0.5s ease-out';
                        alert.style.opacity = '0';
                        setTimeout(function() {
                            alert.style.display = 'none';
                        }, 500);
                    }, 4000);
                }
            });
        });
    </script>
</body>
</html>
