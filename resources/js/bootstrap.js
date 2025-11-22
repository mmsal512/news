import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Import and initialize Alpine.js for dropdown menus and interactive components
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();
