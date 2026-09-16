import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                canvas: {
                    DEFAULT: '#EFEDE8',
                    card: '#E4E1DC',
                },
                ink: {
                    DEFAULT: '#101010',
                    muted: '#6B6862',
                },
                charcoal: {
                    DEFAULT: '#181818',
                    dark: '#0A0A0A',
                },
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'Outfit', ...defaultTheme.fontFamily.sans],
                display: ['Archivo', 'Outfit', 'sans-serif'],
                jersey: ['"Bebas Neue"', 'sans-serif'],
                archivo: ['Archivo', 'sans-serif'],
            },
        },
    },

    plugins: [forms],
};
