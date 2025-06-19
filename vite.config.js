import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    server: {
        host: "0.0.0.0",
        port: 5175, // match the port you're using
        strictPort: true,
        cors: {
            origin: "*", // or specify 'http://10.10.0.6:5055'
        },
        hmr: {
            host: "10.10.0.6", // the IP your Laravel app uses
        },
    },
    plugins: [laravel(["resources/js/app.js", "resources/css/app.css"])],
});
