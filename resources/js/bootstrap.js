import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const shouldInitRealtime =
    !!import.meta.env.VITE_REVERB_APP_KEY &&
    !!(import.meta.env.VITE_REVERB_HOST || window.location.hostname);

if (shouldInitRealtime) {
    Promise.all([
        import('laravel-echo'),
        import('pusher-js'),
    ])
        .then(([echoModule, pusherModule]) => {
            const Echo = echoModule.default;
            const Pusher = pusherModule.default;

            window.Pusher = Pusher;
            window.Echo = new Echo({
                broadcaster: 'reverb',
                key: import.meta.env.VITE_REVERB_APP_KEY,
                wsHost: import.meta.env.VITE_REVERB_HOST ?? window.location.hostname,
                wsPort: Number(import.meta.env.VITE_REVERB_PORT ?? 8080),
                wssPort: Number(import.meta.env.VITE_REVERB_PORT ?? 8080),
                forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
                enabledTransports: ['ws', 'wss'],
            });
        })
        .catch((error) => {
            console.warn('Realtime chat disabled: missing Echo/Pusher dependencies.', error);
        });
}
