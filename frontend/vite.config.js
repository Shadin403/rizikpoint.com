import { defineConfig, loadEnv } from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'
import path from 'path'

// https://vite.dev/config/
export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '')

  // Backend origin (full URL, e.g. http://127.0.0.1:8000)
  const backendOrigin = env.VITE_BACKEND_ORIGIN || 'http://127.0.0.1:8000'

  return {
    plugins: [
      vue(),
      tailwindcss()
    ],
    resolve: {
      alias: {
        '@': path.resolve(import.meta.dirname, 'src')
      }
    },
    server: {
      // Proxy /api/* → backend server (solves CORS in development)
      proxy: {
        '/api': {
          target: backendOrigin,
          changeOrigin: true,
          secure: false,
          configure: (proxy) => {
            proxy.on('error', (err) => {
              console.warn('[proxy] Backend unavailable:', err.message)
            })
          }
        }
      }
    }
  }
})
