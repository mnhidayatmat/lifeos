import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Stats
                stat: {
                    discipline: '#f59e0b',
                    focus: '#6366f1',
                    knowledge: '#3b82f6',
                    strength: '#ef4444',
                    wealth: '#10b981',
                    creativity: '#ec4899',
                    influence: '#8b5cf6',
                    wisdom: '#14b8a6',
                },
                // Ranks
                rank: {
                    initiate: '#9ca3af',
                    apprentice: '#60a5fa',
                    specialist: '#a78bfa',
                    expert: '#fbbf24',
                    master: '#f97316',
                    legend: '#ef4444',
                },
            },
        },
    },

    plugins: [forms],
};
