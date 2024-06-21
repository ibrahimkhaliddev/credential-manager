// webpack.mix.js
const mix = require('laravel-mix');

mix.postCss('resources/css/styles.css', 'public/css', [
    require('tailwindcss'),
]);
