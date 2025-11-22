<x-app-layout>
    <x-breaking-news-banner />
    
    @php
        $seoTitle = $article->title . ' - ' . config('app.name');
        $seoDescription = $article->meta_description ?? $article->summary ?? Str::limit(strip_tags($article->content), 160);
        $seoImage = $article->image_url ?? asset('images/og-default.jpg');
    @endphp

    <x-seo-meta-tags 
        :title="$seoTitle"
        :description="$seoDescription"
        :image="$seoImage"
        :url="route('articles.show', $article->id)"
        type="article"
        :article="$article"
    />

    <!-- Modern Article Header -->
    <article class="bg-white dark:bg-gray-900">
        <div class="relative h-[500px] lg:h-[600px] overflow-hidden">
            <x-article-image :article="$article" class="w-full h-full object-cover" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-8 lg:p-12 text-white">
                    <div class="max-w-4xl mx-auto">
                        @if($article->category)
                            <a href="{{ route('articles.index', ['category' => $article->category->slug]) }}">
                                <span class="inline-block px-4 py-2 text-sm font-bold uppercase tracking-wider rounded-full mb-4 backdrop-blur-sm bg-white/20 hover:bg-white/30 transition-colors">
                                    {{ $article->category->name }}
                                </span>
                            </a>
                        @endif
                        <h1 class="text-4xl lg:text-6xl font-serif font-bold mb-4 leading-tight" dir="{{ $article->language === 'ar' ? 'rtl' : 'ltr' }}">
                            {{ $article->title }}
                        </h1>
                        <div class="flex flex-wrap items-center gap-6 text-sm">
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ $article->published_at?->format('F d, Y') }}
                            </span>
                            @if($article->author)
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ $article->author }}
                                </span>
                            @endif
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $article->reading_time_formatted }}
                            </span>
                            @if($article->views)
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    {{ $article->formatted_views }} {{ __('views') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        <!-- Article Content -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Summary -->
            @if($article->summary)
                <div class="mb-8 p-6 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-2xl border-l-4 border-indigo-600">
                    <p class="text-lg lg:text-xl text-gray-800 dark:text-gray-200 leading-relaxed font-serif" dir="{{ $article->language === 'ar' ? 'rtl' : 'ltr' }}">
                        {{ $article->summary }}
                    </p>
                </div>
            @endif

            <!-- Image Gallery -->
            @if($article->gallery_images && count($article->gallery_images) > 0)
                <div class="mb-12">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($article->gallery_images as $image)
                            <div class="relative group overflow-hidden rounded-2xl shadow-lg">
                                <img src="{{ $image }}" alt="{{ $article->title }}" class="w-full h-80 object-cover group-hover:scale-110 transition-transform duration-700">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Main Content -->
            <div class="prose prose-lg dark:prose-invert max-w-none mb-12" dir="{{ $article->language === 'ar' ? 'rtl' : 'ltr' }}">
                <div class="text-gray-800 dark:text-gray-200 leading-relaxed text-lg font-serif article-content">
                    {!! nl2br(e($article->content)) !!}
                </div>
            </div>

            <!-- Tags -->
            @if($article->tags && count($article->tags) > 0)
                <div class="mb-12 p-6 bg-gray-50 dark:bg-gray-800 rounded-2xl">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-4 uppercase tracking-wider">{{ __('Tags') }}</h3>
                    <div class="flex flex-wrap gap-3">
                        @foreach($article->tags as $tag)
                            <a href="{{ route('articles.index', ['q' => $tag]) }}" 
                               class="px-4 py-2 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-sm font-medium hover:bg-indigo-600 hover:text-white transition-colors shadow-sm">
                                #{{ $tag }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Copyright Notice -->
            <x-copyright-notice :article="$article" />

            <!-- Share Buttons -->
            <div class="mt-12 p-6 bg-gray-50 dark:bg-gray-800 rounded-2xl">
                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-4 uppercase tracking-wider">{{ __('Share This Article') }}</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('articles.show', $article->id)) }}&text={{ urlencode($article->title) }}" 
                       target="_blank"
                       class="px-4 py-2 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path>
                        </svg>
                        Twitter
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('articles.show', $article->id)) }}" 
                       target="_blank"
                       class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"></path>
                        </svg>
                        Facebook
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('articles.show', $article->id)) }}" 
                       target="_blank"
                       class="px-4 py-2 bg-blue-700 text-white rounded-xl hover:bg-blue-800 transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"></path>
                        </svg>
                        LinkedIn
                    </a>
                </div>
            </div>
        </div>
    </article>

    <!-- Related Articles -->
    @if($relatedArticles->count() > 0)
        <section class="bg-gray-50 dark:bg-gray-800 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl lg:text-4xl font-serif font-bold text-gray-900 dark:text-gray-100 mb-8">
                    {{ __('Related Articles') }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedArticles as $related)
                        <article class="article-card-modern group">
                            <a href="{{ route('articles.show', $related->id) }}" class="block">
                                <div class="relative overflow-hidden">
                                    <x-article-image :article="$related" class="article-card-modern-image" />
                                </div>
                            </a>
                            <div class="article-card-modern-content">
                                <h3 class="text-lg font-serif font-bold text-gray-900 dark:text-gray-100 mb-2 line-clamp-2">
                                    <a href="{{ route('articles.show', $related->id) }}" 
                                       class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                        {{ $related->title }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">
                                    {{ Str::limit($related->summary ?? $related->content, 100) }}
                                </p>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $related->published_at?->format('M d, Y') }}
                                </span>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Newsletter Section -->
    <section class="py-16 bg-white dark:bg-gray-900">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-newsletter-form />
        </div>
    </section>
</x-app-layout>
