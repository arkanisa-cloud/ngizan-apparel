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
                    DEFAULT: '#ffffff',
                    card: '#f5f5f5',
                },
                'soft-cloud': '#f5f5f5',
                ink: {
                    DEFAULT: '#111111',
                    muted: '#707072',
                },
                charcoal: {
                    DEFAULT: '#39393b',
                    dark: '#111111',
                },
                ash: '#4b4b4d',
                mute: '#707072',
                stone: '#9e9ea0',
                hairline: '#cacacb',
                'hairline-soft': '#e5e5e5',
                sale: {
                    DEFAULT: '#d30005',
                    deep: '#780700',
                },
                'sale-deep': '#780700',
                success: {
                    DEFAULT: '#007d48',
                    bright: '#1eaa52',
                },
                'success-bright': '#1eaa52',
                info: {
                    DEFAULT: '#1151ff',
                    deep: '#0034e3',
                },
                'info-deep': '#0034e3',
                'premium-gold': '#F59E0B',
                'premium-gold-deep': '#D97706',
                'jnt-red': '#ED1C24',
            },
            fontFamily: {
                sans: ['"Inter"', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', ...defaultTheme.fontFamily.sans],
                display: ['"Bebas Neue"', 'Anton', 'sans-serif'],
                jersey: ['"Bebas Neue"', 'sans-serif'],
                archivo: ['"Inter"', 'sans-serif'],
            },
            borderRadius: {
                'pill': '30px',
                'pill-md': '24px',
                'pill-sm': '18px',
            },
            spacing: {
                'section': '48px',
            },
        },
    },
    plugins: [forms],
};
