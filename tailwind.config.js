/** @type {import('tailwindcss').Config} */
module.exports = {
  // Toggle dark mode via <html class="dark">
  darkMode: 'class',
  content: [
    './*.php',
    './template-parts/**/*.php',
    './inc/**/*.php',
    './js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        // Brand color — customize these for your project
        brand: {
          DEFAULT: '#2563eb', // blue-600
          light:   '#3b82f6', // blue-500
          muted:   '#1d4ed8', // blue-700
        },
      },
    },
  },
  plugins: [],
}
