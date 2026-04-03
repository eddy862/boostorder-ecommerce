import axios from 'axios';
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Pusher = Pusher;

const broadcaster = import.meta.env.VITE_BROADCAST_DRIVER;
const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;
const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER;

if (broadcaster === 'pusher' && pusherKey) {
    console.log('Initializing Laravel Echo with Pusher broadcaster.');
    window.Echo = new Echo({
        broadcaster,
        key: pusherKey,
        cluster: pusherCluster,
        forceTLS: true,
    });
} else {
    // Do not crash the SPA when broadcast env vars are absent in a built image.
    console.warn('Broadcast driver is not set to "pusher" or Pusher key is missing. Broadcasting will be disabled.');
    window.Echo = new Echo({ broadcaster: 'null' });
}
