<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Article Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters and Actions -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('admin.articles.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="{{ __('Search articles...') }}"
                        class="rounded-md border-gray-300 shadow-sm"
                    >
                    <select name="status" class="rounded-md border-gray-300 shadow-sm">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>{{ __('Published') }}</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                    </select>
                    <select name="category" class="rounded-md border-gray-300 shadow-sm">
                        <option value="">{{ __('All Categories') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        {{ __('Filter') }}
                    </button>
                </form>

                <!-- Bulk Actions -->
                <form method="POST" action="{{ route('admin.articles.bulk-action') }}" id="bulk-action-form" class="mt-4">
                    @csrf
                    <div class="flex items-center gap-4">
                        <select name="action" class="rounded-md border-gray-300 shadow-sm" required>
                            <option value="">{{ __('Bulk Actions') }}</option>
                            <option value="publish">{{ __('Publish') }}</option>
                            <option value="unpublish">{{ __('Unpublish') }}</option>
                            <option value="delete">{{ __('Delete') }}</option>
                            <option value="categorize">{{ __('Auto-Categorize') }}</option>
                        </select>
                        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                            {{ __('Apply') }}
                        </button>
                        <a href="{{ route('admin.articles.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            {{ __('Add New Article') }}
                        </a>
                    </div>
                </form>
            </div>

            <!-- Articles Table -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" id="select-all" class="rounded">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Title') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Category') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Views') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Published') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($articles as $article)
                            <tr>
                                <td class="px-6 py-4">
                                    <input type="checkbox" name="article_ids[]" value="{{ $article->id }}" class="article-checkbox rounded">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ Str::limit($article->title, 50) }}
                                    </div>
                                    @if($article->is_ai_related)
                                        <span class="text-xs text-blue-600 dark:text-blue-400">AI Related</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $article->category->name ?? __('Uncategorized') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($article->is_published)
                                        <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            {{ __('Published') }}
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            {{ __('Pending') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $article->views ?? 0 }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $article->published_at?->format('M d, Y') ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.articles.edit', $article) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
                                            {{ __('Edit') }}
                                        </a>
                                        @if($article->is_published)
                                            <form method="POST" action="{{ route('admin.articles.unpublish', $article) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400">
                                                    {{ __('Unpublish') }}
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.articles.publish', $article) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900 dark:text-green-400">
                                                    {{ __('Publish') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    {{ __('No articles found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $articles->links() }}
            </div>
        </div>
    </div>

    <script>
        document.getElementById('select-all')?.addEventListener('change', function() {
            document.querySelectorAll('.article-checkbox').forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        document.getElementById('bulk-action-form')?.addEventListener('submit', function(e) {
            const checked = document.querySelectorAll('.article-checkbox:checked');
            if (checked.length === 0) {
                e.preventDefault();
                alert('{{ __('Please select at least one article.') }}');
                return false;
            }
            checked.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'article_ids[]';
                input.value = checkbox.value;
                this.appendChild(input);
            });
        });
    </script>
</x-app-layout>

