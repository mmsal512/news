<x-app-layout>
    <x-breaking-news-banner />
    
    <!-- Modern Header -->
    <section class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl lg:text-5xl font-serif font-bold mb-4">
                {{ __('Latest News') }}
            </h1>
            <p class="text-xl text-white/90">
                {{ __('Discover the latest stories from around the world') }}
            </p>
        </div>
    </section>

    <div class="bg-gray-50 dark:bg-gray-900 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Modern Category Filter Tabs -->
            <div class="mb-8 bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 sticky top-20 z-40">
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('articles.index') }}" 
                       class="px-6 py-3 rounded-xl font-medium transition-all {{ empty(request('category')) ? 'bg-indigo-600 text-white shadow-lg' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        {{ __('All') }}
                    </a>
                    @foreach($categories as $category)
                        <a href="{{ route('articles.index') }}?category={{ urlencode($category->slug) }}" 
                           class="px-6 py-3 rounded-xl font-medium transition-all {{ request('category') == $category->slug ? 'text-white shadow-lg' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}"
                           style="{{ request('category') == $category->slug ? 'background-color: ' . ($category->color ?? '#6366f1') . ';' : '' }}">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Modern Search Bar -->
            <div class="mb-12">
                <form action="{{ route('articles.index') }}" method="GET" class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1 relative">
                            <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input 
                                type="text" 
                                name="q" 
                                value="{{ request('q') }}"
                                placeholder="{{ __('Search articles by title, content, or category...') }}"
                                class="w-full pl-12 pr-4 py-4 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all dark:bg-gray-700 dark:text-white"
                            >
                        </div>
                        <select name="language" class="px-4 py-4 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:bg-gray-700 dark:text-white">
                            <option value="">{{ __('All Languages') }}</option>
                            <option value="en" {{ request('language') === 'en' ? 'selected' : '' }}>English</option>
                            <option value="ar" {{ request('language') === 'ar' ? 'selected' : '' }}>العربية</option>
                        </select>
                        <button type="submit" class="px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 font-semibold shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                            {{ __('Search') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Modern Articles Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($articles as $article)
                    <article class="article-card-modern group animate-fade-in-up">
                        <a href="{{ route('articles.show', $article->id) }}" class="block">
                            <div class="relative overflow-hidden">
                                <x-article-image :article="$article" class="article-card-modern-image" />
                                @if($article->category)
                                    <div class="absolute top-4 left-4">
                                        <span class="modern-badge text-white" style="background-color: {{ $article->category->color ?? '#6366f1' }};">
                                            {{ $article->category->name }}
                                        </span>
                                    </div>
                                @endif
                                @if($article->is_breaking)
                                    <div class="absolute top-4 right-4">
                                        <span class="modern-badge bg-red-600 text-white animate-pulse">
                                            🔴 Breaking
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </a>
                        <div class="article-card-modern-content">
                            <h3 class="text-2xl font-serif font-bold text-gray-900 dark:text-gray-100 mb-3 line-clamp-2">
                                <a href="{{ route('articles.show', $article->id) }}" 
                                   class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                    {{ $article->title }}
                                </a>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-3 flex-grow">
                                {{ Str::limit($article->summary ?? $article->content, 150) }}
                            </p>
                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-3">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $article->published_at?->format('M d, Y') }}
                                    </span>
                                    <span>•</span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $article->reading_time_formatted }}
                                    </span>
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
                @empty
                    <div class="col-span-full">
                        <x-empty-state 
                            :title="__('No articles found')"
                            :message="__('We couldn\'t find any articles matching your search. Try adjusting your filters or browse all articles.')"
                            :image="\App\Helpers\ImageHelper::getEmptyStateImage()"
                            :actionLabel="__('View all articles')"
                            :actionUrl="route('articles.index')"
                        />
                    </div>
                @endforelse
            </div>

            <!-- Modern Pagination -->
            @if($articles->hasPages())
                <div class="mt-12 flex justify-center">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-4">
                        {{ $articles->links() }}
                    </div>
                </div>
            @endif

            <!-- Newsletter Section -->
            <div class="mt-20">
                <x-newsletter-form />
            </div>
        </div>
    </div>
</x-app-layout>
