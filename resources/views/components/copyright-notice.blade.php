@if($article && $article->requires_attribution)
<div class="mt-8 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border-l-4 border-blue-500">
    <div class="flex items-start">
        <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div class="flex-1">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1">
                {{ __('Copyright & Attribution') }}
            </h4>
            <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                {{ $article->copyright_notice ?? 'This article is sourced from external news providers. All rights reserved by the original publishers.' }}
            </p>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                <p><strong>{{ __('Source') }}:</strong> 
                    @if($article->source)
                        <a href="{{ $article->source->url }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 dark:text-blue-400 hover:underline">
                            {{ $article->source->name }}
                        </a>
                    @else
                        {{ $article->author ?? __('Unknown') }}
                    @endif
                </p>
                @if($article->url)
                <p class="mt-1">
                    <a href="{{ $article->url }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 dark:text-blue-400 hover:underline">
                        {{ __('View Original Article') }} →
                    </a>
                </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

