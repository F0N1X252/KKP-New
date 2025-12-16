import 'bootstrap';

/**
 * Load jQuery secara global agar DataTables bisa membacanya
 */
import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;

/**
 * Load Axios untuk request AJAX
 */
import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';