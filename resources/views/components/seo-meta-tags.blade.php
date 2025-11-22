<!-- Primary Meta Tags -->
<title>{{ $title }}</title>
<meta name="title" content="{{ $title }}">
<meta name="description" content="{{ $description }}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $image }}">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="{{ $url }}">
<meta property="twitter:title" content="{{ $title }}">
<meta property="twitter:description" content="{{ $description }}">
<meta property="twitter:image" content="{{ $image }}">

@if($article)
<!-- Article specific meta tags -->
<meta property="article:published_time" content="{{ $article->published_at?->toIso8601String() }}">
<meta property="article:modified_time" content="{{ $article->updated_at->toIso8601String() }}">
@if($article->author)
<meta property="article:author" content="{{ $article->author }}">
@endif
@if($article->category)
<meta property="article:section" content="{{ $article->category->name }}">
@endif
@if($article->tags)
@foreach($article->tags as $tag)
<meta property="article:tag" content="{{ $tag }}">
@endforeach
@endif
@endif

<!-- Structured Data (JSON-LD) -->
<script type="application/ld+json">
@php
$structuredData = [
    "@context" => "https://schema.org",
    "@type" => $type === 'article' ? 'NewsArticle' : 'WebSite',
    "headline" => $title,
    "description" => $description,
    "image" => $image,
    "url" => $url
];

if ($article && $type === 'article') {
    if ($article->published_at) {
        $structuredData['datePublished'] = $article->published_at->toIso8601String();
    }
    $structuredData['dateModified'] = $article->updated_at->toIso8601String();
    
    if ($article->author) {
        $structuredData['author'] = [
            "@type" => "Person",
            "name" => $article->author
        ];
    }
    
    if ($article->source) {
        $structuredData['publisher'] = [
            "@type" => "Organization",
            "name" => $article->source->name,
            "url" => $article->source->url
        ];
    }
}
@endphp
{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>

