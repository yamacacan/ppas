import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

// Register Alpine plugins
Alpine.plugin(collapse);

// Initialize Alpine.js
window.Alpine = Alpine;
Alpine.start();

import Swal from 'sweetalert2';
window.Swal = Swal;

// Dark Mode Toggle
window.toggleDarkMode = function () {
    if (document.documentElement.classList.contains('dark')) {
        document.documentElement.classList.remove('dark');
        localStorage.theme = 'light';
    } else {
        document.documentElement.classList.add('dark');
        localStorage.theme = 'dark';
    }
};

// Initialize dark mode from localStorage
if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark');
} else {
    document.documentElement.classList.remove('dark');
}

// Sidebar Toggle
window.toggleSidebar = function () {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('-translate-x-full');
};
