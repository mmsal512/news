@props([
    'title',
    'message',
    'image' => null,
    'category' => null,
    'actionLabel' => null,
    'actionUrl' => null,
    'icon' => null
])

<div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-3xl p-12 lg:p-16 shadow-xl">
    <div class="flex flex-col lg:flex-row items-center gap-8 max-w-4xl mx-auto">
        <!-- Image -->
        <div class="flex-shrink-0">
            @if($image)
                <img src="{{ $image }}" 
                     alt="{{ $title }}"
                     class="w-64 h-64 lg:w-80 lg:h-80 object-cover rounded-2xl shadow-lg">
            @else
                <div class="w-64 h-64 lg:w-80 lg:h-80 rounded-2xl shadow-lg bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center relative overflow-hidden">
                    <!-- Pattern overlay -->
                    <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"1\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
                    
                    @if($icon)
                        <div class="relative z-10 text-white">
                            {!! $icon !!}
                        </div>
                    @else
                        <svg class="w-24 h-24 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                        </svg>
                    @endif
                </div>
            @endif
        </div>
        
        <!-- Content -->
        <div class="flex-1 text-center lg:text-left">
            <div class="inline-flex items-center justify-center lg:justify-start w-20 h-20 rounded-full bg-indigo-100 dark:bg-indigo-900 mb-4">
                @if($icon)
                    <div class="text-indigo-600 dark:text-indigo-400">
                        {!! $icon !!}
                    </div>
                @else
                    <svg class="w-10 h-10 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                @endif
            </div>
            <h3 class="text-3xl lg:text-4xl font-serif font-bold text-gray-900 dark:text-gray-100 mb-3">
                {{ $title }}
            </h3>
            <p class="text-lg text-gray-600 dark:text-gray-400 mb-6">
                {{ $message }}
            </p>
            @if($actionLabel && $actionUrl)
                <div class="flex flex-wrap gap-4 justify-center lg:justify-start">
                    <a href="{{ $actionUrl }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 font-medium shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                        {{ $actionLabel }}
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

