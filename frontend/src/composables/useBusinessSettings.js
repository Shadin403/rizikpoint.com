import { ref, provide, inject } from 'vue'
import { fetchBusinessSettings, fetchCategories, bustApiCache } from '@/lib/api'

const BUSINESS_SETTINGS_KEY = Symbol('business-settings')
const CATEGORIES_KEY = Symbol('categories')

// Module-level store — loads once, serves all consumers
const globalSettings = ref([])
const globalSettingsLoaded = ref(false)
const globalSettingsError = ref(null)

/**
 * Dynamically update the browser's favicon (<link rel="icon">)
 * using the site_icon value from business settings.
 * Always runs — even if iconUrl is empty (clears to nothing).
 */
function applyDynamicFavicon(settings) {
  const iconEntry = settings.find(s => s.type === 'site_icon')
  const iconUrl = iconEntry?.value ?? null

  // Remove any existing favicon links first
  document.querySelectorAll('link[rel="icon"], link[rel="shortcut icon"]').forEach(el => el.remove())

  if (!iconUrl) {
    // No site_icon set — restore the default static favicon
    const fallback = document.createElement('link')
    fallback.rel = 'icon'
    fallback.type = 'image/svg+xml'
    fallback.href = '/favicon.svg'
    document.head.appendChild(fallback)
    return
  }

  // Determine MIME type from URL extension
  const ext = iconUrl.split('?')[0].split('.').pop().toLowerCase()
  const mimeMap = { svg: 'image/svg+xml', ico: 'image/x-icon', png: 'image/png', jpg: 'image/jpeg', jpeg: 'image/jpeg', gif: 'image/gif', webp: 'image/webp' }
  const type = mimeMap[ext] || 'image/png'

  const link = document.createElement('link')
  link.rel = 'icon'
  link.type = type
  // Cache-bust so browser doesn't use stale favicon
  link.href = iconUrl + (iconUrl.includes('?') ? '&' : '?') + '_fv=' + Date.now()
  document.head.appendChild(link)
}

let settingsPromise = null
const SETTINGS_LOAD_TIMEOUT_MS = 5000
function ensureSettingsLoaded(forceFresh = false) {
  if (forceFresh) {
    // Bust the JS-level in-memory + localStorage cache so the next
    // fetch truly goes to the network, not a stale 5-minute snapshot.
    bustApiCache('/business-settings')
    settingsPromise = null
    globalSettingsLoaded.value = false
  }
  if (!settingsPromise) {
    const request = fetchBusinessSettings()
    const timeout = new Promise(resolve => setTimeout(() => resolve([]), SETTINGS_LOAD_TIMEOUT_MS))
    settingsPromise = Promise.race([request, timeout])
      .then(data => {
        globalSettings.value = Array.isArray(data) ? data : []
        globalSettingsLoaded.value = true
        applyDynamicFavicon(globalSettings.value)
      })
      .catch(err => {
        globalSettingsError.value = err
        globalSettings.value = []
        globalSettingsLoaded.value = true
        applyDynamicFavicon([])
      })
  }
  return settingsPromise
}

// Eagerly start loading — always bust localStorage so the favicon
// and other settings are never served from a stale 5-minute snapshot.
bustApiCache('/business-settings')
ensureSettingsLoaded()

export function useBusinessSettingsProvider() {
  provide(BUSINESS_SETTINGS_KEY, {
    settings: globalSettings,
    loading: globalSettingsLoaded,
    error: globalSettingsError,
    get: (key, lang = null) => {
      if (!globalSettingsLoaded.value) return null
      let match
      if (lang) {
        match = globalSettings.value.find(s => s.type === key && s.lang === lang)
      }
      if (!match) {
        match = globalSettings.value.find(s => s.type === key && (!s.lang || s.lang === 'en'))
      }
      if (!match) {
        match = globalSettings.value.find(s => s.type === key)
      }

      const value = match?.value ?? null
      return value
    },
    reload: () => {
      return ensureSettingsLoaded(true)
    }
  })
}


export function useBusinessSettings() {
  const ctx = inject(BUSINESS_SETTINGS_KEY)
  if (ctx) return ctx
  return {
    settings: globalSettings,
    loading: globalSettingsLoaded,
    error: globalSettingsError,
    get: (key, lang = null) => {
      if (!globalSettingsLoaded.value) return null
      let match
      if (lang) {
        match = globalSettings.value.find(s => s.type === key && s.lang === lang)
      }
      if (!match) {
        match = globalSettings.value.find(s => s.type === key && (!s.lang || s.lang === 'en'))
      }
      if (!match) {
        match = globalSettings.value.find(s => s.type === key)
      }
      return match?.value ?? null
    }
  }
}

// ─── Categories ──────────────────────────────────────────────────────────────

const globalCategories = ref([])
const globalCategoriesLoaded = ref(false)
const globalCategoriesError = ref(null)

let categoriesPromise = null
function ensureCategoriesLoaded() {
  if (!categoriesPromise) {
    categoriesPromise = fetchCategories()
      .then(data => {
        globalCategories.value = data
        globalCategoriesLoaded.value = true
      })
      .catch(err => {
        globalCategoriesError.value = err
        globalCategoriesLoaded.value = true
      })
  }
  return categoriesPromise
}

ensureCategoriesLoaded()

export function useCategoriesProvider() {
  provide(CATEGORIES_KEY, {
    categories: globalCategories,
    loading: globalCategoriesLoaded,
    error: globalCategoriesError,
    reload: () => {
      categoriesPromise = null
      globalCategoriesLoaded.value = false
      return ensureCategoriesLoaded()
    }
  })
}

export function useCategories() {
  const ctx = inject(CATEGORIES_KEY)
  if (ctx) return ctx
  return {
    categories: globalCategories,
    loading: globalCategoriesLoaded,
    error: globalCategoriesError
  }
}

// Preload both simultaneously at import time (called by main.js)
export function preloadSharedData() {
  return Promise.all([ensureSettingsLoaded(), ensureCategoriesLoaded()])
}
