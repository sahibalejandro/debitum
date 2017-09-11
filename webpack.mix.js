let mix = require('laravel-mix');

mix.js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css')
   .sass('resources/assets/sass/mail/mail.scss', 'public/css')
   .copy('public/css/mail.css', 'resources/views/vendor/mail/html/themes/default.css');