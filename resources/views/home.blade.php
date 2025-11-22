<x-app-layout>
    <x-breaking-news-banner />
    
    <!-- Modern Hero Section with Featured Articles -->
    <section class="relative bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 overflow-hidden">
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        
        @if($featuredArticles->isNotEmpty())
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
        @else
            <!-- Empty State for Featured Articles -->
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
                <div class="text-center text-white">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-white/20 backdrop-blur-sm mb-6">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                        </svg>
                    </div>
                    <h1 class="text-4xl lg:text-6xl font-serif font-bold mb-4">
                        {{ __('Welcome to Our News Platform') }}
                    </h1>
                    <p class="text-xl lg:text-2xl text-white/90 mb-8 max-w-2xl mx-auto">
                        {{ __('We\'re currently preparing amazing content for you. Check back soon for the latest news and stories!') }}
                    </p>
                    <a href="{{ route('articles.index') }}" 
                       class="inline-flex items-center gap-2 px-8 py-4 bg-white text-indigo-600 rounded-xl hover:bg-gray-100 font-semibold shadow-xl hover:shadow-2xl transition-all transform hover:-translate-y-1">
                        {{ __('Browse Articles') }}
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>
            </div>
        @endif
        
        @if($featuredArticles->isNotEmpty())
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Featured Article -->
                    <div class="lg:col-span-2">
                        @php $mainArticle = $featuredArticles->first(); @endphp
                        <article class="group relative h-[600px] rounded-2xl overflow-hidden shadow-2xl">
                            @if($mainArticle->image_url)
                                <img src="{{ $mainArticle->image_url }}" 
                                     alt="{{ $mainArticle->title }}"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            @else
                                <x-article-image :article="$mainArticle" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-8 lg:p-12 text-white">
                                @if($mainArticle->category)
                                    <a href="{{ route('articles.index', ['category' => $mainArticle->category->slug]) }}">
                                        <span class="inline-block px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-full mb-4 backdrop-blur-sm bg-white/20 hover:bg-white/30 transition-colors">
                                            {{ $mainArticle->category->name }}
                                        </span>
                                    </a>
                                @endif
                                <h1 class="text-4xl lg:text-6xl font-serif font-bold mb-4 leading-tight">
                                    <a href="{{ route('articles.show', $mainArticle->id) }}" 
                                       class="hover:text-indigo-300 transition-colors">
                                        {{ $mainArticle->title }}
                                    </a>
                                </h1>
                                <p class="text-lg lg:text-xl mb-6 line-clamp-2 text-gray-200">
                                    {{ Str::limit($mainArticle->summary ?? $mainArticle->content, 200) }}
                                </p>
                                <div class="flex items-center gap-6 text-sm">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $mainArticle->published_at?->format('F d, Y') }}
                                    </span>
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $mainArticle->reading_time_formatted }}
                                    </span>
                                    @if($mainArticle->views)
                                        <span class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            {{ $mainArticle->formatted_views }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </article>
                    </div>
                    
                    <!-- Side Featured Articles -->
                    <div class="space-y-6">
                        @foreach($featuredArticles->skip(1)->take(2) as $article)
                            <article class="group relative h-[290px] rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300">
                                <x-article-image :article="$article" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
                                <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                                    @if($article->category)
                                        <span class="inline-block px-3 py-1 text-xs font-bold uppercase rounded-full mb-2 backdrop-blur-sm bg-white/20">
                                            {{ $article->category->name }}
                                        </span>
                                    @endif
                                    <h2 class="text-xl font-serif font-bold mb-2 line-clamp-2">
                                        <a href="{{ route('articles.show', $article->id) }}" 
                                           class="hover:text-indigo-300 transition-colors">
                                            {{ $article->title }}
                                        </a>
                                    </h2>
                                    <span class="text-xs text-gray-300">{{ $article->published_at?->format('M d') }}</span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </section>

    <!-- Modern Category Sections -->
    <section class="py-16 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @foreach($categories as $category)
                <div class="mb-16">
                    @if(isset($articlesByCategory[$category->slug]) && $articlesByCategory[$category->slug]->isNotEmpty())
                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center gap-4">
                                <div class="w-1 h-12 rounded-full" style="background: linear-gradient(180deg, {{ $category->color ?? '#6366f1' }}, {{ $category->color ?? '#6366f1' }}80);"></div>
                                <div>
                                    <h2 class="text-3xl lg:text-4xl font-serif font-bold text-gray-900 dark:text-gray-100">
                                        {{ $category->name }}
                                    </h2>
                                    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $category->description }}</p>
                                </div>
                            </div>
                            <a href="{{ route('articles.index', ['category' => $category->slug]) }}" 
                               class="hidden md:flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors font-medium shadow-lg hover:shadow-xl">
                                View All
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </a>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            @foreach($articlesByCategory[$category->slug]->take(6) as $article)
                                <article class="article-card-modern group">
                                    <a href="{{ route('articles.show', $article->id) }}" class="block">
                                        <div class="relative overflow-hidden">
                                            <x-article-image :article="$article" class="article-card-modern-image" />
                                            <div class="absolute top-4 left-4">
                                                <span class="modern-badge text-white" style="background-color: {{ $category->color ?? '#6366f1' }};">
                                                    {{ $category->name }}
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="article-card-modern-content">
                                        <h3 class="text-xl font-serif font-bold text-gray-900 dark:text-gray-100 mb-3 line-clamp-2">
                                            <a href="{{ route('articles.show', $article->id) }}" 
                                               class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                                {{ $article->title }}
                                            </a>
                                        </h3>
                                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-3 flex-grow">
                                            {{ Str::limit($article->summary ?? $article->content, 120) }}
                                        </p>
                                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 pt-4 border-t border-gray-200 dark:border-gray-700">
                                            <div class="flex items-center gap-3">
                                                <span>{{ $article->published_at?->format('M d, Y') }}</span>
                                                <span>•</span>
                                                <span>{{ $article->reading_time_formatted }}</span>
                                            </div>
                                            @if($article->views)
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    {{ $article->formatted_views }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                        
                        <div class="mt-8 text-center md:hidden">
                            <a href="{{ route('articles.index', ['category' => $category->slug]) }}" 
                               class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors font-medium">
                                View All {{ $category->name }}
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    @else
                        <!-- Empty State for Category -->
                        <x-empty-state 
                            :title="__('No articles in :category yet', ['category' => $category->name])"
                            :message="__('We\'re working on bringing you the latest news in this category. Check back soon for updates!')"
                            :image="\App\Helpers\ImageHelper::getEmptyStateImage($category)"
                            :category="$category"
                            :actionLabel="__('Browse All Articles')"
                            :actionUrl="route('articles.index')"
                        />
                    @endif
                </div>
            @endforeach
        </div>
    </section>

    <!-- Newsletter Section with Modern Design -->
    <section class="py-20 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 relative overflow-hidden">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="bg-white/10 backdrop-blur-lg rounded-3xl p-8 lg:p-12 border border-white/20 shadow-2xl">
                <h2 class="text-3xl lg:text-4xl font-serif font-bold text-white mb-4">
                    {{ __('Stay Ahead of the News') }}
                </h2>
                <p class="text-xl text-white/90 mb-8">
                    {{ __('Get the latest stories delivered to your inbox. No spam, unsubscribe anytime.') }}
                </p>
                <x-newsletter-form :showName="true" />
            </div>
        </div>
    </section>
</x-app-layout>
