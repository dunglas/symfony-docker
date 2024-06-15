# Vite

To take advantage of the benefits of Vite, follow the instructions below.

1. If you haven't already, start the project `docker compose up --pull always -d --wait`
2. Check that the laravel home page appears correctly `https://localhost`
3. Run `docker compose exec php npm install @vitejs/plugin-basic-ssl --save-dev`
4. Update your vite.config.js :
```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
+ import basicSsl from '@vitejs/plugin-basic-ssl'

export default defineConfig({
+   server: {
+       https: true,
+       host: '0.0.0.0',
+       hmr: {
+           host: 'localhost',
+       }
+   },
    plugins: [
+       basicSsl({
+           /** name of certification */
+           name: 'WhatYouWant',
+           /** custom trust domains */
+           domains: ['localhost'],
+           /** custom certification directory (from the project root for example) */
+           certDir: './cert'
+       }),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```
5. Just before `</head>` balise on `resources/views/welcome.blade.php`, add this : `@vite(['resources/css/app.css', 'resources/js/app.js'])`
6. Run `docker compose exec php npm run dev`
7. Accept certificate `https://localhost:5173`
8. Enjoy, you have vite refresh : `https://localhost`

Of course, you can also use it with any starter kit.
