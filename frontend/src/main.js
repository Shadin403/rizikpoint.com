import { createApp } from 'vue'
import './style.css'
import 'nprogress/nprogress.css'
import App from './App.vue'
import { prefetchHotData, bustApiCache } from './lib/api'
import router from './router'
import NProgress from 'nprogress'
import { preloadSharedData } from '@/composables/useBusinessSettings'

NProgress.configure({ showSpinner: false, trickleSpeed: 120 })

router.beforeEach((to, from, next) => {
  if (to.path !== from.path) NProgress.start()
  next()
})

router.afterEach(() => {
  NProgress.done()
})

// Always bust the business-settings localStorage cache on startup
// so the favicon and other settings are always fresh after admin changes.
bustApiCache('/business-settings')

prefetchHotData()
preloadSharedData()

createApp(App).use(router).mount('#app')
