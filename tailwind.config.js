import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', ...defaultTheme.fontFamily.sans], // ✅ ganti ke Poppins
            },
            colors: {
                maroon: {
                    50:  '#fdf2f2',
                    100: '#fce8e8',
                    200: '#f9d0d0',
                    300: '#f3a8a8',
                    400: '#eb6a6a',
                    500: '#d73737',
                    600: '#b32424',
                    700: '#800000', // maroon utama
                    800: '#660000',
                    900: '#4d0000',
                },
                emerald: {
                    700: '#047857', // hijau emerald tua
                },
                gold: {
                    50:  '#fffdf2',
                    100: '#fffbe6',
                    200: '#fff6cc',
                    300: '#ffef99',
                    400: '#ffe566',
                    500: '#ffd700', // gold utama
                    600: '#e6c200',
                    700: '#b39b00',
                    800: '#807300',
                    900: '#4d4400',
                }
            }
        },
    },

    plugins: [forms],
}
