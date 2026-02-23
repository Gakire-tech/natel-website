/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#e6ebf2',
          100: '#cdd6e5',
          200: '#9aaad9',
          300: '#677fcd',
          400: '#425fb8',
          500: '#1F3A5F', // Main primary color
          600: '#192f4d',
          700: '#13243b',
          800: '#0d1929',
          900: '#070e17',
        },
        accent: {
          50: '#fdf2ee',
          100: '#fde5dc',
          200: '#fcccb8',
          300: '#fbb294',
          400: '#fa9970',
          500: '#E07A5F', // Main accent color
          600: '#c86852',
          700: '#b05645',
          800: '#984438',
          900: '#80322b',
        },
        light: '#F4F6F8',
        white: '#FFFFFF',
        dark: '#2E2E2E',
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
      },
    },
  },
  plugins: [],
}