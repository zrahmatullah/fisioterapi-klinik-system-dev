import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
  plugins: [vue()],

  resolve: {
    alias: {
      // Alias umum proyek
      '@': path.resolve(__dirname, './src'),

      // Alias khusus PrimeVue
      'primevue': path.resolve(__dirname, 'node_modules/primevue'),
      'primeicons': path.resolve(__dirname, 'node_modules/primeicons'),
    }
  },

  // ✅ TAMBAHAN PENTING (HILANGKAN API SILANG)
  server: {
    proxy: {
      // untuk API Laravel (api.php)
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      },

      // untuk route web Laravel (invoice, cetak, dll)
      '/invoice': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      },

      '/cetak': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      },
    }
  }
})
