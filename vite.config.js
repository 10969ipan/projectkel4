import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/backend/dashboard_charts.js',
                'resources/js/frontend/chat.js',
                'resources/js/frontend/chatbot_widget.js',
                'resources/js/backend/pharmacare_transactions.js'
            ],
            refresh: true,
        }),
    ],
});
