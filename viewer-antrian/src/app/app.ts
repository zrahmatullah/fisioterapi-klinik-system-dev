import {
  Component,
  OnInit,
  Inject,
  PLATFORM_ID,
  NgZone,
  ChangeDetectorRef
} from '@angular/core';
import { isPlatformBrowser } from '@angular/common';
import { createEcho } from './echo';

@Component({
  selector: 'app-root',
  standalone: true,
  templateUrl: './app.html',
  styleUrls: ['./app.css']
})
export class App implements OnInit {

  nomor = '—';
  ruangan = 'Menunggu...';
  nama = '';

  jam = '';
  tanggal = '';

  isCalling = false;
  audioAktif = false;

  private echo: any;

  constructor(
    @Inject(PLATFORM_ID) private platformId: Object,
    private zone: NgZone,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit(): void {

    if (!isPlatformBrowser(this.platformId)) return;

    // =========================================
    // ⏰ JAM REALTIME
    // =========================================
    this.zone.runOutsideAngular(() => {
      setInterval(() => {

        const now = new Date();

        const jam = now.toLocaleTimeString('id-ID');
        const tanggal = now.toLocaleDateString('id-ID', {
          weekday: 'long',
          day: 'numeric',
          month: 'long',
          year: 'numeric'
        });

        this.zone.run(() => {
          this.jam = jam;
          this.tanggal = tanggal;
          this.cdr.detectChanges(); // 🔥 fix hydration
        });

      }, 1000);
    });

    // =========================================
    // 🔔 CONNECT ECHO
    // =========================================
    this.echo = createEcho();

    console.log('ECHO:', this.echo);

    this.echo?.channel('antrian_channel')
      .subscribed(() => console.log('✅ SUBSCRIBED KE CHANNEL'))
      .error((err: any) => console.error('❌ CHANNEL ERROR:', err))
      .listen('.AntrianDipanggil', (e: any) => {

        console.log('🔥 EVENT MASUK:', e);

        const data = e.data ?? e;

        this.zone.run(() => {

          // =====================================
          // 📺 UPDATE UI
          // =====================================
          this.nomor = data.no_antrian ?? '—';
          this.ruangan = data.ruangan ?? 'Menunggu...';
          this.nama = data.nama_anak ?? '';

          // =====================================
          // 🔊 SUARA (jika sudah diaktifkan)
          // =====================================
          if (this.audioAktif) {
            this.panggilSuara();
          }

          // =====================================
          // ✨ EFEK KEDIP
          // =====================================
          this.isCalling = true;
          setTimeout(() => {
            this.isCalling = false;
            this.cdr.detectChanges();
          }, 5000);

          // 💣 WAJIB UNTUK HYDRATION
          this.cdr.detectChanges();

        });

      });

  }

  // =========================================
  // 🔊 AKTIFKAN AUDIO (klik sekali)
  // =========================================
  aktifkanAudio() {

    this.audioAktif = true;

    const speech = new SpeechSynthesisUtterance('Suara aktif');
    speech.lang = 'id-ID';
    window.speechSynthesis.speak(speech);
  }

  // =========================================
  // 🔊 PANGGIL SUARA
  // =========================================
  panggilSuara() {

    if (!this.audioAktif) return; // 🔥 cegah jika belum diaktifkan

    const nomorText =
      this.nomor === '0' ? 'kosong' : this.nomor;

    const text =
      `Nomor antrian ${nomorText}. ` +
      `Atas nama ${this.nama}. ` +
      `Silakan menuju ruangan ${this.ruangan}. ` +
      `Terima kasih.`;

    const speech = new SpeechSynthesisUtterance(text);

    speech.lang = 'id-ID';
    speech.rate = 0.85;
    speech.pitch = 1;
    speech.volume = 1;

    const speakNow = () => {
      const voices = speechSynthesis.getVoices();

      const indoVoice = voices.find(v =>
        v.lang.toLowerCase().includes('id')
      );

      if (indoVoice) speech.voice = indoVoice;

      window.speechSynthesis.cancel();
      window.speechSynthesis.speak(speech);
    };

    if (speechSynthesis.getVoices().length === 0) {
      speechSynthesis.onvoiceschanged = speakNow;
    } else {
      speakNow();
    }
  }

}