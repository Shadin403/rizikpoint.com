<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from "vue";
import { useRouter } from "vue-router";
import { useCart } from "@/store/cart";
import { useI18n } from "@/lib/i18n";
import { useAuth } from "@/store/auth";
import { searchProducts } from "@/lib/api";
import { useBusinessSettings } from "@/composables/useBusinessSettings";
import { toast } from "@/lib/toast";
import { Heart, Loader2, Menu, Search, ShoppingCart, User, X } from "@lucide/vue";

const router = useRouter();
const { totalItems, openCart, subtotal } = useCart();
const { locale, toggleLocale } = useI18n();
const { user, isAuthenticated, openAuth, logout } = useAuth();
const { settings, get } = useBusinessSettings();

const headerLogo = ref("");
const logoError = ref(false);
const appName = ref("");
const logoLoading = ref(true);
const menuOpen = ref(false);
const searchOpen = ref(false);
const searchQuery = ref("");
const suggestions = ref([]);
const suggestionsOpen = ref(false);
const isSearching = ref(false);
const searchInputRef = ref(null);
const mobileSearchInputRef = ref(null);
let searchTimer = null;
let searchToken = 0;
let logoLoadingTimer = null;

function syncSettings() {
  headerLogo.value = get("header_logo");
  appName.value = get("app_name") || "Rizik Point";
}

onMounted(() => {
  syncSettings();
  // Never leave the navigation skeleton on screen when the settings API is
  // delayed or blocked. A real logo still replaces the fallback immediately.
  logoLoadingTimer = window.setTimeout(() => { logoLoading.value = false; }, 1500);
  document.addEventListener("click", closeSearchOnOutsideClick);
});
watch(settings, (value) => {
  syncSettings();
  if (Array.isArray(value) && value.length) logoLoading.value = false;
});
onBeforeUnmount(() => {
  document.removeEventListener("click", closeSearchOnOutsideClick);
  if (searchTimer) clearTimeout(searchTimer);
  if (logoLoadingTimer) clearTimeout(logoLoadingTimer);
});

function closeSearchOnOutsideClick(event) {
  const target = event.target;
  if (!(target instanceof Element) || !target.closest(".rp-search-wrap")) suggestionsOpen.value = false;
}

function toggleSearch() {
  searchOpen.value = !searchOpen.value;
  if (searchOpen.value) {
    requestAnimationFrame(() => {
      const input = window.innerWidth < 1101 ? mobileSearchInputRef.value : searchInputRef.value;
      input?.focus();
    });
  }
}

function onSearchInput() {
  if (searchTimer) clearTimeout(searchTimer);
  const term = searchQuery.value.trim();
  if (!term) {
    suggestions.value = [];
    suggestionsOpen.value = false;
    return;
  }
  searchTimer = setTimeout(runSearch, 250);
}

async function runSearch() {
  const term = searchQuery.value.trim();
  if (!term) return;
  const token = ++searchToken;
  isSearching.value = true;
  suggestionsOpen.value = true;
  try {
    const result = await searchProducts({ search: term, limit: 6 });
    if (token === searchToken) suggestions.value = result;
  } catch {
    if (token === searchToken) suggestions.value = [];
  } finally {
    if (token === searchToken) isSearching.value = false;
  }
}

function submitSearch() {
  const term = searchQuery.value.trim();
  if (!term) return;
  suggestionsOpen.value = false;
  router.push({ path: "/deals", query: { search: term } });
}

function pickSuggestion(product) {
  suggestionsOpen.value = false;
  searchQuery.value = "";
  router.push(`/deals/${product.slug || product.id}`);
}

function openAccount() {
  if (isAuthenticated.value) router.push("/dashboard");
  else openAuth("login");
}

function showWishlistNotice() {
  toast({ title: locale.value === "bn" ? "উইশলিস্ট শীঘ্রই আসছে" : "Wishlist coming soon" });
}

const vFocus = { mounted: (el) => el.focus() };
</script>

<template>
  <div class="rp-navbar">
    <div class="rp-topbar">
      <div class="rp-nav-container">
        <span><ShoppingCart aria-hidden="true" /> {{ locale === "bn" ? "TK 1000-এর বেশি অর্ডারে ফ্রি হোম ডেলিভারি" : "Free Home Delivery on Orders Over TK 1000" }} <i>•</i> {{ locale === "bn" ? "টাটকা ও মানসম্মত" : "Fresh Cut & Hygienic" }} <i>•</i> {{ locale === "bn" ? "সময় বাঁচান" : "Save Time" }}</span>
        <strong>{{ locale === "bn" ? "এখনই অর্ডার করুন" : "Order Now" }} <span aria-hidden="true">→</span></strong>
      </div>
    </div>

    <header class="rp-header">
      <div class="rp-nav-container rp-nav-row">
        <router-link to="/" class="rp-brand" aria-label="Rizik Point home">
          <template v-if="logoLoading">
            <span class="rp-brand-loading-mark" aria-hidden="true"></span>
            <span class="rp-brand-loading-copy" aria-hidden="true"><i></i><i></i></span>
          </template>
          <template v-else>
            <img v-if="headerLogo && !logoError" :src="headerLogo" alt="Rizik Point" @error="logoError = true" />
            <span v-else class="rp-brand-mark" aria-hidden="true"></span>
            <span><b>{{ appName }}</b><small>Ready to Cook</small></span>
          </template>
        </router-link>

        <nav class="rp-desktop-links" aria-label="Primary navigation">
          <router-link to="/" exact-active-class="is-active">{{ locale === "bn" ? "হোম" : "Home" }}</router-link>
          <router-link to="/products-list">{{ locale === "bn" ? "শপ" : "Shop" }}</router-link>
          <router-link to="/products-list">{{ locale === "bn" ? "পণ্য" : "Products" }}</router-link>
          <router-link to="/categories">{{ locale === "bn" ? "পেজ" : "Pages" }}</router-link>
          <router-link to="/deals">{{ locale === "bn" ? "অফার" : "Deals" }}</router-link>
          <router-link to="/contact">{{ locale === "bn" ? "যোগাযোগ" : "Contact" }}</router-link>
        </nav>

        <div class="rp-nav-actions">
          <button type="button" aria-label="Search" @click="toggleSearch"><Search /></button>
          <button type="button" aria-label="Account" @click="openAccount"><User /></button>
          <button type="button" aria-label="Wishlist" @click="showWishlistNotice"><Heart /></button>
          <button type="button" aria-label="Cart" class="rp-cart-action" @click="openCart"><ShoppingCart /><span v-if="totalItems" class="rp-cart-count">{{ totalItems > 9 ? "9+" : totalItems }}</span></button>
          <span class="rp-cart-total">৳{{ Number(subtotal || 0).toFixed(2) }} <span aria-hidden="true">⌄</span></span>
          <button type="button" class="rp-lang" @click="toggleLocale">{{ locale === "bn" ? "EN" : "বাং" }}</button>
        </div>
      </div>

      <div v-if="searchOpen" class="rp-search-row rp-search-wrap">
        <form class="rp-search-form" @submit.prevent="submitSearch">
          <Search aria-hidden="true" />
          <label for="desktop-search" class="sr-only">Search products</label>
          <input id="desktop-search" ref="searchInputRef" v-model="searchQuery" v-focus type="search" :placeholder="locale === 'bn' ? 'পণ্য খুঁজুন...' : 'Search products...'" autocomplete="off" @input="onSearchInput" @focus="searchQuery.trim() && (suggestionsOpen = true)" />
          <Loader2 v-if="isSearching" class="animate-spin" aria-hidden="true" />
        </form>
        <div v-if="suggestionsOpen" class="rp-search-results">
          <div v-if="isSearching && !suggestions.length" class="rp-search-empty">{{ locale === "bn" ? "খোঁজা হচ্ছে..." : "Searching..." }}</div>
          <div v-else-if="!suggestions.length" class="rp-search-empty">{{ locale === "bn" ? "কোনো পণ্য পাওয়া যায়নি" : "No products found" }}</div>
          <button v-for="product in suggestions" v-else :key="product.id" type="button" class="rp-search-result" @mousedown.prevent="pickSuggestion(product)"><span>{{ product.title }}</span><small>৳{{ product.discountedPrice }}</small></button>
        </div>
      </div>
    </header>

    <header class="rp-mobile-header">
      <button type="button" aria-label="Open menu" @click="menuOpen = true"><Menu /></button>
      <router-link to="/" class="rp-mobile-brand">
        <template v-if="logoLoading"><span class="rp-brand-loading-mark" aria-hidden="true"></span><span class="rp-brand-loading-copy" aria-hidden="true"><i></i><i></i></span></template>
        <template v-else><span class="rp-brand-mark" aria-hidden="true"></span><span><b>Rizik Point</b><small>Ready to Cook</small></span></template>
      </router-link>
      <div><button type="button" aria-label="Search" @click="toggleSearch"><Search /></button><button type="button" aria-label="Cart" class="rp-cart-action" @click="openCart"><ShoppingCart /><span v-if="totalItems" class="rp-cart-count">{{ totalItems }}</span></button></div>
      <div v-if="searchOpen" class="rp-mobile-search rp-search-wrap"><form class="rp-search-form" @submit.prevent="submitSearch"><Search aria-hidden="true" /><input ref="mobileSearchInputRef" v-focus v-model="searchQuery" type="search" :placeholder="locale === 'bn' ? 'পণ্য খুঁজুন...' : 'Search products...'" @input="onSearchInput" /></form></div>
    </header>

    <transition name="fade">
      <div v-if="menuOpen" class="rp-drawer-layer" @click.self="menuOpen = false">
        <aside class="rp-drawer">
          <div class="rp-drawer-head"><router-link to="/" class="rp-mobile-brand" @click="menuOpen = false"><span class="rp-brand-mark" aria-hidden="true"></span><span><b>Rizik Point</b><small>Ready to Cook</small></span></router-link><button type="button" aria-label="Close menu" @click="menuOpen = false"><X /></button></div>
          <nav class="rp-drawer-links">
            <router-link to="/" @click="menuOpen = false">{{ locale === "bn" ? "হোম" : "Home" }}</router-link>
            <router-link to="/categories" @click="menuOpen = false">{{ locale === "bn" ? "ক্যাটাগরি" : "Categories" }}</router-link>
            <router-link to="/products-list" @click="menuOpen = false">{{ locale === "bn" ? "সব পণ্য" : "All Products" }}</router-link>
            <router-link to="/deals" @click="menuOpen = false">{{ locale === "bn" ? "অফার" : "Deals" }}</router-link>
            <router-link to="/contact" @click="menuOpen = false">{{ locale === "bn" ? "যোগাযোগ" : "Contact" }}</router-link>
            <button v-if="isAuthenticated" type="button" @click="logout(); menuOpen = false"><User /> {{ locale === "bn" ? "লগআউট" : "Logout" }}</button>
            <button v-else type="button" @click="openAuth('login'); menuOpen = false"><User /> {{ locale === "bn" ? "লগইন" : "Login" }}</button>
          </nav>
        </aside>
      </div>
    </transition>
  </div>
</template>

<style scoped>
.rp-navbar { position: relative; z-index: 40; background: #fff; font-family: var(--app-font-sans); }
.rp-nav-container { width: min(1180px, calc(100% - 44px)); margin-inline: auto; }
.rp-topbar { color: #fff; background: #075d32; font-size: 11px; }
.rp-topbar .rp-nav-container { min-height: 34px; display: flex; align-items: center; justify-content: space-between; gap: 20px; }
.rp-topbar span { display: inline-flex; align-items: center; gap: 7px; opacity: .96; }
.rp-topbar svg { width: 13px; height: 13px; }
.rp-topbar i { opacity: .55; font-style: normal; }
.rp-topbar strong { font-size: 11px; }
.rp-header { position: sticky; top: 0; background: #fff; box-shadow: 0 1px 0 rgba(0,0,0,.06); }
.rp-nav-row { min-height: 82px; display: grid; grid-template-columns: 220px 1fr auto; align-items: center; gap: 20px; }
.rp-brand, .rp-mobile-brand { display: inline-flex; align-items: center; gap: 9px; color: #075d32; white-space: nowrap; }
.rp-brand img { width: 150px; max-height: 58px; object-fit: contain; }
.rp-brand-loading-mark { width: 150px; height: 48px; display: block; border-radius: 6px; background: linear-gradient(90deg, #e7ece8 25%, #f5f7f5 50%, #e7ece8 75%); background-size: 200% 100%; animation: rp-navbar-shimmer 1.4s ease-in-out infinite; }
.rp-brand-loading-copy { display: flex; flex-direction: column; gap: 6px; }
.rp-brand-loading-copy i { display: block; width: 92px; height: 14px; border-radius: 4px; background: #e7ece8; animation: rp-navbar-pulse 1.4s ease-in-out infinite; }
.rp-brand-loading-copy i:last-child { width: 58px; height: 8px; }
.rp-brand > span:last-child, .rp-mobile-brand > span:last-child { display: flex; flex-direction: column; }
.rp-brand b, .rp-mobile-brand b { font-size: 20px; line-height: 1; font-weight: 800; letter-spacing: -.8px; }
.rp-brand small, .rp-mobile-brand small { color: #f56a1d; margin-top: 4px; font-size: 10px; line-height: 1; font-weight: 800; }
.rp-brand-mark { width: 33px; height: 33px; position: relative; display: inline-block; border: 3px solid #075d32; border-top: 0; border-right: 0; border-radius: 0 0 0 11px; transform: rotate(-8deg); }
.rp-brand-mark::before { content: ""; position: absolute; right: -4px; top: -8px; width: 16px; height: 9px; border-radius: 18px 18px 0 18px; background: #f56a1d; transform: rotate(-28deg); }
.rp-desktop-links { display: flex; align-items: center; justify-content: center; gap: clamp(16px, 2.4vw, 34px); }
.rp-desktop-links a { position: relative; padding: 31px 0; color: #242925; font-size: 12px; font-weight: 700; }
.rp-desktop-links a:hover, .rp-desktop-links a.is-active { color: #075d32; }
.rp-desktop-links a.is-active::after { content: ""; position: absolute; left: 50%; bottom: 21px; width: 27px; height: 2px; background: #111; transform: translateX(-50%); }
.rp-nav-actions { display: flex; align-items: center; justify-content: flex-end; gap: 12px; }
.rp-nav-actions > button:not(.rp-lang) { width: 34px; height: 34px; display: grid; place-items: center; border: 0; color: #171b18; background: transparent; cursor: pointer; }
.rp-nav-actions > button:hover { color: #075d32; }
.rp-nav-actions svg { width: 19px; height: 19px; stroke-width: 1.8; }
.rp-cart-action { position: relative; }
.rp-cart-count { position: absolute; right: -1px; top: -1px; min-width: 16px; height: 16px; padding-inline: 3px; display: grid; place-items: center; border-radius: 50%; color: #fff; background: #111; font-size: 9px; font-weight: 800; }
.rp-cart-total { color: #303632; font-size: 12px; font-weight: 700; white-space: nowrap; }
.rp-lang { padding: 5px 7px; border: 1px solid #dce4de; border-radius: 5px; color: #075d32; background: #f5faf5; font-size: 10px; font-weight: 800; cursor: pointer; }
.rp-search-row { position: absolute; left: 50%; top: calc(100% + 8px); width: min(500px, calc(100% - 40px)); transform: translateX(-50%); z-index: 50; }
.rp-search-form { min-height: 45px; display: flex; align-items: center; gap: 10px; padding: 0 14px; border: 1px solid #dfe6df; border-radius: 8px; background: #fff; box-shadow: 0 12px 35px rgba(0,0,0,.12); }
.rp-search-form svg { width: 17px; color: #69736d; flex: 0 0 auto; }
.rp-search-form input { min-width: 0; flex: 1; border: 0; outline: 0; background: transparent; color: #111; font-size: 13px; }
.rp-search-results { margin-top: 6px; overflow: hidden; border: 1px solid #e5ebe5; border-radius: 8px; background: #fff; box-shadow: 0 12px 35px rgba(0,0,0,.12); }
.rp-search-result { width: 100%; padding: 11px 14px; display: flex; justify-content: space-between; gap: 12px; border: 0; border-bottom: 1px solid #f0f3f0; background: #fff; color: #242925; text-align: left; font-size: 12px; cursor: pointer; }
.rp-search-result:hover { background: #f5faf5; color: #075d32; }
.rp-search-result small { color: #075d32; font-weight: 800; }
.rp-search-empty { padding: 18px; color: #69736d; text-align: center; font-size: 12px; }
.rp-mobile-header { display: none; }
.rp-drawer-layer { position: fixed; inset: 0; z-index: 70; background: rgba(0,0,0,.42); }
.rp-drawer { width: min(320px, 86vw); height: 100%; padding: 18px; background: #fff; box-shadow: 10px 0 30px rgba(0,0,0,.16); }
.rp-drawer-head { display: flex; align-items: center; justify-content: space-between; padding-bottom: 18px; border-bottom: 1px solid #edf1ed; }
.rp-drawer-head button { border: 0; background: transparent; cursor: pointer; }
.rp-drawer-links { display: flex; flex-direction: column; gap: 4px; padding-top: 18px; }
.rp-drawer-links a, .rp-drawer-links button { display: flex; align-items: center; gap: 10px; padding: 13px 10px; border: 0; border-radius: 6px; background: transparent; color: #26312b; font-size: 14px; font-weight: 700; text-align: left; cursor: pointer; }
.rp-drawer-links a:hover, .rp-drawer-links button:hover { color: #075d32; background: #edf7ee; }
.rp-drawer-links svg { width: 17px; }
@media (max-width: 1050px) { .rp-nav-row { grid-template-columns: 190px 1fr auto; } .rp-desktop-links { gap: 14px; } .rp-desktop-links a { font-size: 11px; } .rp-cart-total { display: none; } }
@media (max-width: 1100px) {
  .rp-topbar .rp-nav-container { min-height: 32px; justify-content: center; }
  .rp-topbar .rp-nav-container > strong { display: none; }
  .rp-topbar span { font-size: 10px; }
  .rp-topbar span i { display: none; }
  .rp-header { display: none; }
  .rp-mobile-header { width: 100%; min-width: 0; min-height: 62px; padding: 0 14px; position: sticky; top: 0; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #edf1ed; background: #fff; box-shadow: 0 1px 5px rgba(0,0,0,.04); box-sizing: border-box; }
  .rp-mobile-header > button, .rp-mobile-header > div > button { width: 38px; height: 38px; display: grid; place-items: center; border: 0; background: transparent; color: #26312b; cursor: pointer; }
  .rp-mobile-header > button svg, .rp-mobile-header > div > button svg { width: 20px; }
  .rp-mobile-header > div { display: flex; gap: 2px; }
  .rp-mobile-brand b { font-size: 16px; }
  .rp-mobile-brand small { font-size: 8px; }
  .rp-mobile-brand .rp-brand-mark { width: 28px; height: 28px; }
  .rp-mobile-search { position: absolute; left: 14px; right: 14px; top: calc(100% + 8px); z-index: 60; }
  .rp-mobile-search .rp-search-form { box-shadow: 0 8px 25px rgba(0,0,0,.14); }
}
@media (max-width: 420px) { .rp-topbar span { max-width: 100%; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; } }
@keyframes rp-navbar-shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
@keyframes rp-navbar-pulse { 50% { opacity: .55; } }
</style>
