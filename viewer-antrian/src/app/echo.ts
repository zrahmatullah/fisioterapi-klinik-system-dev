import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

export function createEcho() {

  if (typeof window === 'undefined') {
    return null; // SSR: jangan buat Echo
  }

  (window as any).Pusher = Pusher;

  return new Echo({
    broadcaster: 'reverb',
    key: 'app_key_123',
    wsHost: '127.0.0.1',
    wsPort: 6001,
    forceTLS: false,
    disableStats: true,
    enabledTransports: ['ws'],
  });
}
