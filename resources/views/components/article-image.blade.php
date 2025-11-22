@if($article->image_url)
    <img src="{{ $imageUrl }}" 
         alt="{{ $alt }}" 
         class="{{ $class }}"
         loading="lazy">
@else
    @php
        // Determine gradient based on category
        $gradient = 'from-indigo-500 to-purple-600';
        if ($article->category) {
            $categorySlug = strtolower($article->category->slug);
            $gradients = [
                'politics' => 'from-indigo-500 to-purple-600',
                'technology' => 'from-cyan-500 to-blue-600',
                'sports' => 'from-green-500 to-emerald-600',
                'entertainment' => 'from-pink-500 to-rose-600',
                'business' => 'from-orange-500 to-amber-600',
            ];
            $gradient = $gradients[$categorySlug] ?? $gradient;
        }
    @endphp
    <div class="{{ $class }} bg-gradient-to-br {{ $gradient }} flex items-center justify-center relative overflow-hidden">
        <!-- Pattern overlay -->
        <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"1\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        
        <!-- Icon and text -->
        <div class="relative z-10 text-center p-8">
            <svg class="w-16 h-16 mx-auto mb-4 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
            </svg>
            @if($article->category)
                <p class="text-white/90 font-semibold uppercase tracking-wider text-sm">{{ $article->category->name }}</p>
            @endif
        </div>
    </div>
@endif
