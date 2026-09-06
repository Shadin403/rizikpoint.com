import { ref, watch, onMounted } from "vue";
import { useI18n } from "@/lib/i18n";
import { fetchBusinessSettings } from "@/lib/api";

/**
 * Lightweight composable that sets the document title and updates
 * common meta tags (description, og:title, og:description) per route.
 *
 * The site name shown after the page title is pulled from the
 * `app_name` business setting (Header Setting -> App Name in admin).
 * Fallbacks: "DealPabo" / "ডিলপাবো".
 *
 * Usage:
 *   usePageTitle({
 *     en: "All Brands",
 *     bn: "সকল ব্র্যান্ড সমূহ",
 *     description: {
 *       en: "Browse all brands on our site.",
 *       bn: "আমাদের সাইটে সকল ব্র্যান্ড দেখুন।",
 *     },
 *   });
 */
const FALLBACK_NAME_EN = "DealPabo";
const FALLBACK_NAME_BN = "ডিলপাবো";
const SEPARATOR = " | ";

// Module-level cache so we only fetch business settings once per page load.
const appNameCache = {
  en: FALLBACK_NAME_EN,
  bn: FALLBACK_NAME_BN,
  loaded: false,
  promise: null,
};

function pickLocalized(settings, type, lang) {
  if (!Array.isArray(settings) || settings.length === 0) return null;
  // Try exact language match first
  let match = settings.find((s) => s.type === type && s.lang === lang);
  if (match && match.value) return match.value;
  // Fallback to language-less or english
  match = settings.find(
    (s) => s.type === type && (!s.lang || s.lang === "en"),
  );
  if (match && match.value) return match.value;
  // Final fallback: first matching type
  match = settings.find((s) => s.type === type);
  return match && match.value ? match.value : null;
}

async function loadAppName() {
  if (appNameCache.loaded) return appNameCache;
  if (appNameCache.promise) return appNameCache.promise;
  appNameCache.promise = (async () => {
    try {
      const settings = await fetchBusinessSettings();
      const en = pickLocalized(settings, "app_name", "en") || FALLBACK_NAME_EN;
      const bn = pickLocalized(settings, "app_name", "bn") || FALLBACK_NAME_BN;
      appNameCache.en = en;
      appNameCache.bn = bn;
    } catch (err) {
      // Keep fallbacks on error
      console.warn("usePageTitle: failed to load app_name, using fallback", err);
    } finally {
      appNameCache.loaded = true;
    }
    return appNameCache;
  })();
  return appNameCache.promise;
}

function setMeta(selector, attr, value) {
  let el = document.head.querySelector(selector);
  if (!el) {
    el = document.createElement(selector.startsWith("link") ? "link" : "meta");
    if (selector.includes('property="og:')) {
      el.setAttribute("property", selector.match(/property="([^"]+)"/)[1]);
    } else if (selector.includes('name="')) {
      el.setAttribute("name", selector.match(/name="([^"]+)"/)[1]);
    }
    if (selector.startsWith("link")) {
      el.setAttribute("rel", "canonical");
    }
    document.head.appendChild(el);
  }
  el.setAttribute(attr, value);
}

export function usePageTitle(config) {
  const { locale } = useI18n();

  const buildTitle = (lang) => {
    const raw =
      typeof config === "string"
        ? config
        : (config?.[lang] ?? config?.en ?? config?.bn ?? "");
    const siteName = lang === "bn" ? appNameCache.bn : appNameCache.en;
    return raw ? `${raw}${SEPARATOR}${siteName}` : siteName;
  };

  const buildDescription = (lang) => {
    const desc =
      typeof config?.description === "object"
        ? (config.description[lang] ?? config.description.en ?? config.description.bn ?? "")
        : "";
    return desc;
  };

  const apply = () => {
    const lang = locale.value;
    document.title = buildTitle(lang);
    const desc = buildDescription(lang);
    if (desc) {
      setMeta('meta[name="description"]', "content", desc);
      setMeta('meta[property="og:title"]', "content", document.title);
      setMeta('meta[property="og:description"]', "content", desc);
    } else {
      setMeta('meta[property="og:title"]', "content", document.title);
    }
  };

  // Trigger load (idempotent) and apply once ready.
  onMounted(async () => {
    await loadAppName();
    apply();
  });

  // Re-apply on locale change so localized site name updates.
  watch(locale, () => apply());

  // If the app name arrives after the first apply (cache miss),
  // also re-apply when the cache resolves.
  if (!appNameCache.loaded) {
    loadAppName().then(apply);
  }

  return { apply };
}

/**
 * Set the title once (no watchers). Useful for non-component contexts.
 */
export function setPageTitle(title, description = "") {
  document.title = title;
  if (description) {
    setMeta('meta[name="description"]', "content", description);
  }
  setMeta('meta[property="og:title"]', "content", title);
}