import { Injectable } from '@angular/core';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

@Injectable({
  providedIn: 'root'
})
export class RealtimeService {

  private echo: any;

  constructor() {
    (window as any).Pusher = Pusher;

    this.echo = new Echo({
      broadcaster: 'reverb',
      key: 'app_key_123',     // REVERB_APP_KEY dari .env Laravel
      wsHost: '127.0.0.1',    // host backend
      wsPort: 6001,           // port Reverb
      forceTLS: false,
      enabledTransports: ['ws'],
    });
  }

  listenAntrian(callback: (data: any) => void) {
    this.echo
      .channel('antrian_channel')
      .listen('.AntrianDipanggil', (event: any) => {
        callback(event.data);
      });
  }
}
