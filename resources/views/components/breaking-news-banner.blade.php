@if($breakingArticles->isNotEmpty())
<div class="bg-red-600 text-white py-2 px-4 overflow-hidden relative">
    <div class="flex items-center gap-4 animate-scroll">
        <span class="font-bold text-sm uppercase tracking-wider whitespace-nowrap flex-shrink-0">
            🔴 Breaking News
        </span>
        <div class="flex gap-6 overflow-hidden">
            @foreach($breakingArticles as $article)
                <a href="{{ route('articles.show', $article->id) }}" 
                   class="text-sm hover:underline whitespace-nowrap flex-shrink-0">
                    {{ $article->title }}
                </a>
            @endforeach
        </div>
    </div>
</div>

<style>
@keyframes scroll {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
.animate-scroll {
    animation: scroll 30s linear infinite;
}
</style>
@endif

