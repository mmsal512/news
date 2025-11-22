<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-3xl font-serif font-bold text-gray-900 dark:text-gray-100">
            {{ __('Dashboard') }}
        </h2>
            <div class="flex gap-3">
                <a href="{{ route('admin.articles.create') }}" 
                   class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 font-semibold shadow-lg hover:shadow-xl transition-all">
                    {{ __('New Article') }}
                </a>
                <a href="{{ route('admin.categories.create') }}" 
                   class="px-6 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 font-semibold shadow-lg hover:shadow-xl transition-all">
                    {{ __('New Category') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @php
                    $stats = app(\App\Services\News\NewsAggregatorService::class)->getNewsStatistics();
                @endphp
                
                <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl p-6 text-white shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold uppercase tracking-wider opacity-90">Total Articles</h3>
                        <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                        </svg>
                    </div>
                    <p class="text-4xl font-bold">{{ number_format($stats['total_articles'] ?? 0) }}</p>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold uppercase tracking-wider opacity-90">Published</h3>
                        <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-4xl font-bold">{{ number_format($stats['published_articles'] ?? 0) }}</p>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold uppercase tracking-wider opacity-90">AI Articles</h3>
                        <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    <p class="text-4xl font-bold">{{ number_format($stats['ai_articles'] ?? 0) }}</p>
                </div>

                <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-2xl p-6 text-white shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold uppercase tracking-wider opacity-90">Recent (7 days)</h3>
                        <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-4xl font-bold">{{ number_format($stats['recent_articles'] ?? 0) }}</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <a href="{{ route('admin.articles.index') }}" 
                   class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all group border-2 border-transparent hover:border-indigo-500">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">Manage Articles</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">View, edit, and publish articles</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.categories.index') }}" 
                   class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all group border-2 border-transparent hover:border-purple-500">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">Manage Categories</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Organize content by categories</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('articles.index') }}" 
                   class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all group border-2 border-transparent hover:border-green-500">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">View Site</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">See how it looks to visitors</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Recent Articles -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-serif font-bold text-gray-900 dark:text-gray-100">Recent Articles</h3>
                    <a href="{{ route('admin.articles.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                        View All →
                    </a>
                </div>
                <div class="space-y-4">
                    @php
                        $recentArticles = \App\Models\Article::with(['category', 'source'])
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp
                    @forelse($recentArticles as $article)
                        <div class="flex items-center gap-4 p-4 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            @if($article->image_url)
                                <img src="{{ $article->image_url }}" alt="{{ $article->title }}" 
                                     class="w-20 h-20 object-cover rounded-xl">
                            @endif
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">
                                    <a href="{{ route('admin.articles.edit', $article) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                        {{ Str::limit($article->title, 60) }}
                                    </a>
                                </h4>
                                <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                                    <span>{{ $article->category->name ?? 'Uncategorized' }}</span>
                                    <span>•</span>
                                    <span>{{ $article->published_at?->format('M d, Y') ?? 'Not published' }}</span>
                                    @if($article->is_published)
                                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-xs font-semibold">
                                            Published
                                        </span>
                                    @else
                                        <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 rounded-full text-xs font-semibold">
                                            Pending
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">No articles yet. Create your first article!</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
