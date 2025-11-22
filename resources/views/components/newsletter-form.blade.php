<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-200 dark:border-gray-700">
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
        </div>
        <h3 class="text-2xl font-serif font-bold text-gray-900 dark:text-gray-100 mb-2">
            {{ __('Stay Informed') }}
        </h3>
        <p class="text-gray-600 dark:text-gray-400">
            {{ __('Subscribe to our newsletter for the latest news and updates.') }}
        </p>
    </div>
    
    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="space-y-4" id="newsletter-form">
        @csrf
        
        @if(isset($showName) && $showName)
        <input 
            type="text" 
            name="name" 
            placeholder="{{ __('Your Name') }}"
            class="w-full px-5 py-4 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-all"
        >
        @endif
        
        <input 
            type="email" 
            name="email" 
            required
            placeholder="{{ __('Your Email Address') }}"
            class="w-full px-5 py-4 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-all"
        >
        
        <button 
            type="submit"
            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold py-4 px-6 rounded-xl transition-all transform hover:-translate-y-0.5 shadow-lg hover:shadow-xl"
        >
            {{ __('Subscribe Now') }}
        </button>
    </form>
    
    @if(session('success'))
        <div class="mt-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-sm text-green-700 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-sm text-red-700 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif
</div>

