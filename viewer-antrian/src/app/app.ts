import {
  Component,
  OnInit,
  Inject,
  PLATFORM_ID,
  NgZone,
  ChangeDetectorRef
} from '@angular/core';
import { CommonModule, isPlatformBrowser } from '@angular/common';
import { createEcho } from './echo';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [CommonModule],
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
  private suaraIndo: SpeechSynthesisVoice | null = null;
  private voicesSiap = false;
  private audioElement: HTMLAudioElement | null = null;

  // =========================================
  // GOOGLE CLOUD TTS — DIPANGGIL LANGSUNG DARI BROWSER
  //
  // PERINGATAN KEAMANAN:
  // API key ini akan terlihat oleh siapa pun yang membuka
  // DevTools / "View Source" di browser. Siapa saja bisa
  // menyalin key ini dan memakainya sendiri, yang akan
  // terhitung sebagai biaya/kuota di akun Google Cloud Anda.
  //
  // Mitigasi minimal yang SANGAT disarankan walau tetap di
  // satu file ini:
  // 1. Di Google Cloud Console > Credentials > klik API key
  //    ini > "Application restrictions" > pilih "Websites"
  //    > masukkan domain tempat halaman ini akan diakses.
  //    Ini membuat key hanya bisa dipakai dari domain Anda,
  //    bukan dari domain siapa pun yang mencurinya.
  // 2. Di "API restrictions", batasi key ini HANYA untuk
  //    "Cloud Text-to-Speech API", supaya walau dicuri,
  //    tidak bisa dipakai untuk layanan Google lain.
  // 3. Set budget alert di Google Cloud Billing supaya Anda
  //    diberi tahu kalau pemakaian melonjak tidak normal.
  // =========================================
  private readonly GOOGLE_TTS_API_KEY = 'GANTI_DENGAN_API_KEY_ANDA';
  private readonly GOOGLE_TTS_VOICE = 'id-ID-Wavenet-D'; // A/B/C/D = karakter suara berbeda
  private readonly GOOGLE_TTS_URL = 'https://texttospeech.googleapis.com/v1/text:synthesize';

  // Cache audio di memori (per sesi halaman) supaya panggilan
  // dengan nomor/nama/ruangan yang sama tidak generate ulang
  // ke Google Cloud selama halaman belum di-refresh.
  private audioCache = new Map<string, string>();

  constructor(
    @Inject(PLATFORM_ID) private platformId: Object,
    private zone: NgZone,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit(): void {

    if (!isPlatformBrowser(this.platformId)) return;

    this.audioElement = new Audio();

    // =========================================
    // JAM REALTIME
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
          this.cdr.detectChanges();
        });

      }, 1000);
    });

    // Siapkan voice browser sebagai fallback jika Cloud TTS gagal/offline
    this.muatVoices();

    // =========================================
    // CONNECT ECHO
    // =========================================
    this.echo = createEcho();

    console.log('ECHO:', this.echo);

    this.echo?.channel('antrian_channel')
      .subscribed(() => console.log('SUBSCRIBED KE CHANNEL'))
      .error((err: any) => console.error('CHANNEL ERROR:', err))
      .listen('.AntrianDipanggil', (e: any) => {

        console.log('EVENT MASUK:', e);

        const data = e.data ?? e;

        this.zone.run(() => {

          this.nomor = data.no_antrian ?? '—';
          this.ruangan = data.ruangan ?? 'Menunggu...';
          this.nama = data.nama_anak ?? '';

          if (this.audioAktif) {
            this.panggilSuara();
          }

          this.isCalling = true;
          setTimeout(() => {
            this.isCalling = false;
            this.cdr.detectChanges();
          }, 5000);

          this.cdr.detectChanges();

        });

      });

  }

  // =========================================
  // MUAT DAN CACHE VOICE BROWSER (fallback saja)
  // =========================================
  private muatVoices(percobaan = 0) {

    const voices = window.speechSynthesis.getVoices();

    if (voices.length > 0) {
      this.suaraIndo = voices.find(v => v.lang.toLowerCase().includes('id')) ?? null;
      this.voicesSiap = true;
      return;
    }

    if (percobaan >= 5) {
      this.voicesSiap = true;
      return;
    }

    window.speechSynthesis.onvoiceschanged = () => this.muatVoices(percobaan + 1);
    setTimeout(() => {
      if (!this.voicesSiap) this.muatVoices(percobaan + 1);
    }, 300);
  }

  // =========================================
  // AKTIFKAN AUDIO (klik sekali, wajib karena
  // browser memblokir audio sebelum ada interaksi user)
  // =========================================
  aktifkanAudio() {

    this.audioAktif = true;

    const speech = new SpeechSynthesisUtterance('Suara aktif');
    speech.lang = 'id-ID';
    if (this.suaraIndo) speech.voice = this.suaraIndo;
    window.speechSynthesis.speak(speech);

    // "Buka" elemen <audio> dengan play singkat senyap, supaya
    // audio.play() berikutnya (dari Cloud TTS) tidak diblokir browser.
    if (this.audioElement) {
      this.audioElement.muted = true;
      this.audioElement.play().catch(() => {});
      this.audioElement.muted = false;
    }
  }

  // =========================================
  // PANGGIL SUARA — Google Cloud TTS langsung
  // dari browser (utama), fallback ke speechSynthesis
  // browser jika gagal.
  // =========================================
  async panggilSuara() {

    if (!this.audioAktif) return;

    const nomorText = this.nomor === '0' ? 'kosong' : this.nomor;
    const teks =
      `Nomor antrian ${nomorText}. ` +
      (this.nama ? `Atas nama ${this.nama}. ` : '') +
      `Silakan menuju ruangan ${this.ruangan}. ` +
      `Terima kasih.`;

    const cacheKey = teks;

    try {
      let audioBase64 = this.audioCache.get(cacheKey);

      if (!audioBase64) {
        audioBase64 = await this.generateAudioGoogleTts(teks);
        this.audioCache.set(cacheKey, audioBase64);
      }

      this.putarAudioBase64(audioBase64);

    } catch (err) {
      console.warn('Google Cloud TTS gagal, fallback ke suara browser:', err);
      this.panggilSuaraFallback(teks);
    }
  }

  // =========================================
  // PANGGIL GOOGLE CLOUD TTS API LANGSUNG
  // =========================================
  private async generateAudioGoogleTts(teks: string): Promise<string> {

    if (!this.GOOGLE_TTS_API_KEY || this.GOOGLE_TTS_API_KEY === 'GANTI_DENGAN_API_KEY_ANDA') {
      throw new Error('GOOGLE_TTS_API_KEY belum diisi.');
    }

    const response = await fetch(`${this.GOOGLE_TTS_URL}?key=${this.GOOGLE_TTS_API_KEY}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        input: { text: teks },
        voice: {
          languageCode: 'id-ID',
          name: this.GOOGLE_TTS_VOICE
        },
        audioConfig: {
          audioEncoding: 'MP3',
          speakingRate: 0.95,
          pitch: 0
        }
      })
    });

    if (!response.ok) {
      const errText = await response.text();
      throw new Error(`Google TTS API error (${response.status}): ${errText}`);
    }

    const data = await response.json();

    if (!data.audioContent) {
      throw new Error('Respons Google TTS tidak berisi audioContent.');
    }

    return data.audioContent; // base64 MP3
  }

  private putarAudioBase64(base64: string) {
    if (!this.audioElement) return;

    this.audioElement.src = `data:audio/mp3;base64,${base64}`;
    this.audioElement.play().catch(err => {
      console.warn('Gagal memutar audio Cloud TTS, fallback ke browser:', err);
      this.panggilSuaraFallback(
        `Nomor antrian ${this.nomor}. Atas nama ${this.nama}. Silakan menuju ruangan ${this.ruangan}. Terima kasih.`
      );
    });
  }

  // =========================================
  // FALLBACK: speechSynthesis browser, dipakai
  // hanya jika Cloud TTS tidak bisa diakses
  // (API key salah, kuota habis, internet putus).
  // =========================================
  private panggilSuaraFallback(teks: string) {

    const ucapkan = () => {
      const speech = new SpeechSynthesisUtterance(teks);
      speech.lang = 'id-ID';
      speech.rate = 1;
      speech.pitch = 1;
      speech.volume = 1;
      if (this.suaraIndo) speech.voice = this.suaraIndo;
      window.speechSynthesis.speak(speech);
    };

    window.speechSynthesis.cancel();
    setTimeout(ucapkan, 80);
  }

}