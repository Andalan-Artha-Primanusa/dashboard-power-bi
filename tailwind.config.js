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
                brand: '#BD9B75',
                maroon: {
                    50:  '#BD9B75',
                    100: '#BD9B75',
                    200: '#BD9B75',
                    300: '#BD9B75',
                    400: '#BD9B75',
                    500: '#BD9B75',
                    600: '#BD9B75',
                    700: '#BD9B75',
                    800: '#BD9B75',
                    900: '#BD9B75',
                },
                emerald: {
                    700: '#BD9B75',
                },
                gold: {
                    50:  '#BD9B75',
                    100: '#BD9B75',
                    200: '#BD9B75',
                    300: '#BD9B75',
                    400: '#BD9B75',
                    500: '#BD9B75',
                    600: '#BD9B75',
                    700: '#BD9B75',
                    800: '#BD9B75',
                    900: '#BD9B75',
                }
            }
        },
    },

    plugins: [forms],
}
