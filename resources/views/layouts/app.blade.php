<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CashFlow') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    </head>
    <body class="font-sans text-slate-800 antialiased bg-slate-50">
        <div class="flex h-screen overflow-hidden" 
             x-data="{ 
                sidebarOpen: false, 
                sidebarPreference: localStorage.getItem('sidebarMinimized') === 'true',
                windowWidth: window.innerWidth,
                get sidebarMinimized() { return this.windowWidth >= 1024 ? this.sidebarPreference : false; }
             }" 
             @resize.window="windowWidth = window.innerWidth"
             x-init="$watch('sidebarPreference', val => localStorage.setItem('sidebarMinimized', val))">
            
            @include('layouts.navigation')

            <!-- Main content -->
            <main :class="sidebarMinimized ? 'lg:ml-20' : 'lg:ml-64'" class="flex-1 overflow-y-auto transition-all duration-300">
                <!-- Top bar -->
                <div class="sticky top-0 z-10 bg-white/80 backdrop-blur border-b border-slate-100 px-4 sm:px-6 min-h-[4rem] py-3 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-slate-500 hover:text-slate-700 p-1 -ml-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <div>
                            @isset($header)
                                {{ $header }}
                            @endisset
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 sm:gap-4 shrink-0">
                        <!-- Real-time Clock -->
                        <div class="hidden sm:flex items-center gap-1 sm:gap-2 text-[10px] sm:text-sm font-medium text-slate-500 bg-white sm:bg-slate-100/50 px-2 sm:px-3 py-1.5 rounded-lg border border-slate-200/50" 
                             x-data="{ 
                                time: '', 
                                date: '',
                                updateTime() {
                                    const now = new Date();
                                    this.time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                                    this.date = now.toLocaleDateString('id-ID', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });
                                }
                             }" 
                             x-init="updateTime(); setInterval(() => updateTime(), 1000)">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 text-brand-500 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-text="date" class="hidden sm:inline"></span>
                            <span class="text-slate-300 mx-0.5 hidden sm:inline">•</span>
                            <span x-text="time" class="font-mono text-slate-700 font-bold"></span>
                        </div>

                        @isset($headerActions)
                            {{ $headerActions }}
                        @endisset
                    </div>
                </div>

                <div class="p-4 sm:p-6 pb-24 sm:pb-12 space-y-4 sm:space-y-6">
                    {{ $slot }}
                </div>
            </main>
            
            @include('components.chatbot')
        </div>
    </body>
</html>
