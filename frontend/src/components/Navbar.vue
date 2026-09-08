<script setup>
import { ref, onMounted, onBeforeUnmount, watch, computed } from "vue";
import { useRouter } from "vue-router";
import { useCart } from "@/store/cart";
import { useI18n } from "@/lib/i18n";
import { useAuth } from "@/store/auth";
import { toast } from "@/lib/toast";
import {
  Search,
  ShoppingCart,
  Menu,
  X,
  Home,
  Package,
  Tag,
  Phone,
  ChevronDown,
  ChevronRight,
  Sparkles,
  User,
  Loader2,
  Info,
  FileText,
  Shield,
  HelpCircle,
  RotateCcw,
  LayoutDashboard,
} from "@lucide/vue";

import { fetchSubCategories, searchProducts } from "@/lib/api";
import { useBusinessSettings, useCategories } from "@/composables/useBusinessSettings";
import CategoryIcon from "@/components/shared/CategoryIcon.vue";

const router = useRouter();
const menuOpen = ref(false);
const searchOpen = ref(false);
const searchQuery = ref("");
const megaMenuOpen = ref(false);
const accountMenuOpen = ref(false);

const { totalItems, openCart, subtotal } = useCart();
const { locale, t, toggleLocale } = useI18n();
const { user, isAuthenticated, openAuth, logout } = useAuth();

// Dynamic Settings
const headerLogo = ref("");
const logoError = ref(false);
const appName = ref("");
const helplineNumber = ref("");
const enableStickyHeader = ref(false);
const categories = ref([]);
const categoriesExpanded = ref(false);
const subCategories = ref([]);
const loadingSubCategories = ref(false);
let subCategoriesLoaded = false;

async function toggleCategories() {
  categoriesExpanded.value = !categoriesExpanded.value;
  if (categoriesExpanded.value && !subCategoriesLoaded) {
    await loadSubCategories();
  }
}

async function loadSubCategories() {
  // If categories not yet loaded, fetch them first
  if (categories.value.length === 0) {
    try {
      categories.value = await fetchCategories();
    } catch (err) {
      console.error("Failed to load categories for sub-menu", err);
      return;
    }
  }
  // Load sub-categories for the first parent
  loadingSubCategories.value = true;
  try {
    const parent = categories.value[0];
    if (parent) {
      subCategories.value = await fetchSubCategories(parent.id);
    }
    subCategoriesLoaded = true;
  } catch (err) {
    console.error("Failed to load sub-categories", err);
  } finally {
    loadingSubCategories.value = false;
  }
}

// Live search state
const selectedCategory = ref(null);     // null = All categories
const categoryDropdownOpen = ref(false);
const suggestionsOpen = ref(false);
const suggestions = ref([]);
const isSearching = ref(false);
const searchInputRef = ref(null);
const searchContainerRef = ref(null);
let searchDebounceTimer = null;
let activeSearchToken = 0;

const selectedCategoryName = computed(() => {
  if (selectedCategory.value === null) {
    return locale.value === "bn" ? "সব ক্যাটাগরি" : "All Categories";
  }
  const cat = categories.value.find((c) => c.id === selectedCategory.value);
  return cat ? cat.name : (locale.value === "bn" ? "ক্যাটাগরি" : "Category");
});

const { settings, get } = useBusinessSettings();
const { categories: injectedCategories } = useCategories();

function syncSettings() {
  headerLogo.value = get("header_logo");
  appName.value = get("app_name") || "";
  helplineNumber.value = get("helpline_number") || "01635585340";
  enableStickyHeader.value = get("header_stikcy") === "on";
}

onMounted(() => {
  syncSettings();
  document.addEventListener("click", handleDocumentClick);
});

watch(settings, syncSettings, { immediate: false });

watch(injectedCategories, (val) => {
  if (val.length > 0) categories.value = val
})

onBeforeUnmount(() => {
  document.removeEventListener("click", handleDocumentClick);
  if (searchDebounceTimer) clearTimeout(searchDebounceTimer);
});

function handleDocumentClick(e) {
  if (searchContainerRef.value && !searchContainerRef.value.contains(e.target)) {
    suggestionsOpen.value = false;
    categoryDropdownOpen.value = false;
  }
}

function selectCategory(catId) {
  selectedCategory.value = catId;
  categoryDropdownOpen.value = false;
  if (searchQuery.value.trim()) {
    runLiveSearch();
  }
  searchInputRef.value?.focus();
}

function onSearchInput() {
  if (searchDebounceTimer) clearTimeout(searchDebounceTimer);
  const term = searchQuery.value.trim();
  if (!term) {
    suggestions.value = [];
    suggestionsOpen.value = false;
    return;
  }
  // 250ms debounce - feels live without spamming the API
  searchDebounceTimer = setTimeout(runLiveSearch, 250);
}

async function runLiveSearch() {
  const term = searchQuery.value.trim();
  if (!term) {
    suggestions.value = [];
    suggestionsOpen.value = false;
    return;
  }
  activeSearchToken += 1;
  const token = activeSearchToken;
  isSearching.value = true;
  suggestionsOpen.value = true;
  try {
    const results = await searchProducts({
      search: term,
      categoryId: selectedCategory.value || undefined,
      limit: 8,
    });
    if (token !== activeSearchToken) return; // stale response
    suggestions.value = results;
  } catch (err) {
    if (token !== activeSearchToken) return;
    suggestions.value = [];
  } finally {
    if (token === activeSearchToken) isSearching.value = false;
  }
}

function pickSuggestion(product) {
  suggestionsOpen.value = false;
  searchQuery.value = "";
  suggestions.value = [];
  router.push(`/deals/${product.slug || product.id}`);
}

function handleSearchSubmit() {
  const term = searchQuery.value.trim();
  if (!term) return;
  const query = { search: term };
  if (selectedCategory.value) query.category = selectedCategory.value;
  suggestionsOpen.value = false;
  suggestions.value = [];
  router.push({ path: "/deals", query });
  searchInputRef.value?.blur();
}

function handleLoginClick() {
  toast({
    title: t("checkout_feature"),
    description: t("checkout_coming_soon"),
  });
}

function handleContactClick() {
  router.push("/contact");
}
</script>

<template>
  <div>
    <!-- Responsive Desktop Header Layout -->
    <header
    :class="[
      'bg-white border-b border-gray-200 hidden md:block transition-all duration-300',
      enableStickyHeader ? 'sticky top-0 z-40 shadow-md' : '',
    ]"
  >
    <!-- Top Row: Logo, Search, Actions -->
    <div
      class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between"
    >
      <!-- Logo -->
      <router-link to="/" class="flex items-center gap-3">
        <img
          v-if="headerLogo && !logoError"
          :src="headerLogo"
          alt="Logo"
          class="w-auto object-contain" style="height: 90px;"
          @error="logoError = true"
        />
        <span v-else class="text-xl font-bold text-primary font-display">{{ appName || 'RizikPoint' }}</span>
        <!-- <div
          v-else
          class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center font-bold text-white text-lg font-display"
        >
          {{ (appName || "S").charAt(0).toUpperCase() }}
        </div> -->
        <!-- <div class="flex flex-col">
          <span
            class="font-bold text-primary text-xl font-display leading-none"
          >
            {{ appName }}
          </span>
          <span
            class="text-gray-400 text-[9px] tracking-widest leading-none mt-1 font-bold uppercase"
          >
            Best Deals Daily
          </span>
        </div> -->
      </router-link>

      <!-- Center Search Bar with Category Filter + Live Suggestions -->
      <div ref="searchContainerRef" class="flex-1 max-w-2xl mx-8 relative">
        <form
          @submit.prevent="handleSearchSubmit"
          class="flex items-center bg-white border border-gray-200 rounded-full overflow-visible shadow-sm hover:shadow-md hover:border-primary/40 transition-all duration-200"
        >
          <!-- Category selector -->
          <div class="relative shrink-0">
            <button
              type="button"
              @click.stop="categoryDropdownOpen = !categoryDropdownOpen"
              class="flex items-center gap-1.5 pl-4 pr-3 h-11 text-xs font-bold text-gray-700 hover:text-primary transition-colors cursor-pointer whitespace-nowrap max-w-[160px]"
            >
              <Tag class="w-3.5 h-3.5 text-primary" />
              <span class="truncate">{{ selectedCategoryName }}</span>
              <ChevronDown
                class="w-3.5 h-3.5 transition-transform"
                :class="categoryDropdownOpen ? 'rotate-180' : ''"
              />
            </button>
            <!-- Vertical divider -->
            <span class="absolute right-0 top-1/2 -translate-y-1/2 h-5 w-px bg-gray-200"></span>

            <!-- Category dropdown -->
            <transition name="fade">
              <div
                v-show="categoryDropdownOpen"
                class="absolute left-0 top-full mt-2 w-56 bg-white border border-gray-100 rounded-2xl shadow-xl py-2 z-50 max-h-72 overflow-y-auto"
              >
                <button
                  type="button"
                  @click="selectCategory(null)"
                  class="w-full text-left px-4 py-2 text-xs font-semibold hover:bg-primary/5 hover:text-primary flex items-center gap-2 cursor-pointer"
                  :class="selectedCategory === null ? 'text-primary bg-primary/5' : 'text-gray-700'"
                >
                  <span class="w-2 h-2 rounded-full" :class="selectedCategory === null ? 'bg-primary' : 'bg-gray-300'"></span>
                  {{ locale === "bn" ? "সব ক্যাটাগরি" : "All Categories" }}
                </button>
                <div class="h-px bg-gray-100 my-1"></div>
                <button
                  v-for="cat in categories"
                  :key="cat.id"
                  type="button"
                  @click="selectCategory(cat.id)"
                  class="w-full text-left px-4 py-2 text-xs font-semibold hover:bg-primary/5 hover:text-primary flex items-center gap-2 cursor-pointer"
                  :class="selectedCategory === cat.id ? 'text-primary bg-primary/5' : 'text-gray-700'"
                >
                  <span class="w-2 h-2 rounded-full" :class="selectedCategory === cat.id ? 'bg-primary' : 'bg-gray-300'"></span>
                  <span class="truncate">{{ cat.name }}</span>
                </button>
              </div>
            </transition>
          </div>

          <!-- Search input -->
          <div class="relative flex-1 flex items-center">
            <input
              ref="searchInputRef"
              type="text"
              v-model="searchQuery"
              @input="onSearchInput"
              @focus="searchQuery.trim() && (suggestionsOpen = true)"
              :placeholder="t('search_placeholder')"
              class="flex-1 px-3 h-11 bg-transparent outline-none text-sm text-gray-800 placeholder:text-gray-400"
              autocomplete="off"
            />
            <Loader2
              v-if="isSearching"
              class="w-4 h-4 text-primary animate-spin mr-2"
            />
          </div>

          <!-- Search button (primary) -->
          <button
            type="submit"
            class="bg-primary hover:bg-primary/90 h-11 w-12 text-white flex items-center justify-center transition-colors cursor-pointer rounded-r-full"
          >
            <Search class="w-4 h-4" />
          </button>
        </form>

        <!-- Live suggestions dropdown -->
        <transition name="fade">
          <div
            v-if="suggestionsOpen && (suggestions.length > 0 || isSearching || searchQuery.trim())"
            class="absolute left-0 right-0 top-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-2xl z-50 overflow-hidden"
          >
            <div v-if="isSearching && suggestions.length === 0" class="px-4 py-6 text-center text-xs text-gray-500">
              <Loader2 class="w-4 h-4 animate-spin inline mr-2" />
              {{ locale === "bn" ? "খোঁজা হচ্ছে..." : "Searching..." }}
            </div>
            <div
              v-else-if="suggestions.length === 0 && searchQuery.trim()"
              class="px-4 py-6 text-center text-xs text-gray-500"
            >
              {{ locale === "bn" ? "কোনো পণ্য পাওয়া যায়নি" : "No products found" }}
            </div>
            <ul v-else class="max-h-96 overflow-y-auto py-1">
              <li
                v-for="p in suggestions"
                :key="p.id"
                @mousedown.prevent="pickSuggestion(p)"
                class="flex items-center gap-3 px-3 py-2 mx-1 rounded-xl hover:bg-primary/5 cursor-pointer transition-colors"
              >
                <img
                  v-if="p.imageUrl"
                  :src="p.imageUrl"
                  :alt="p.title"
                  class="w-10 h-10 rounded-lg object-cover bg-gray-100 shrink-0"
                />
                <div
                  v-else
                  class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center shrink-0"
                >
                  <Package class="w-4 h-4 text-primary" />
                </div>
                <div class="flex-1 min-w-0">
                  <div class="text-xs font-bold text-gray-800 line-clamp-2 break-words">{{ p.title }}</div>
                  <div class="text-[10px] text-gray-500 truncate" v-if="p.categoryName">
                    {{ p.categoryName }}
                  </div>
                </div>
                <div v-if="p.discountedPrice" class="text-xs font-extrabold text-primary shrink-0">
                  ৳{{ p.discountedPrice }}
                </div>
              </li>
            </ul>
            <div
              v-if="suggestions.length > 0"
              class="border-t border-gray-100 px-4 py-2.5 text-center bg-gray-50/60"
            >
              <button
                type="button"
                @mousedown.prevent="handleSearchSubmit"
                class="text-[11px] font-extrabold text-primary hover:underline cursor-pointer"
              >
                {{ locale === "bn"
                    ? `“${searchQuery}” এর জন্য সব ফলাফল দেখুন`
                    : `See all results for “${searchQuery}”`
                }} →
              </button>
            </div>
          </div>
        </transition>
      </div>
      <!-- Right actions: Login & Cart -->
      <div class="flex items-center gap-4">
        <template v-if="isAuthenticated">
          <div class="flex items-center gap-3">
            <span class="text-sm font-semibold text-gray-700">
              {{ user?.name }}
            </span>
            <button
              @click="logout"
              class="bg-gray-100 hover:bg-gray-250 text-gray-700 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors cursor-pointer border border-gray-200"
            >
              {{ locale === "bn" ? "লগআউট" : "Logout" }}
            </button>
          </div>
        </template>
        <template v-else>
          <button
            @click="openAuth('login')"
            class="bg-primary hover:bg-primary/90 text-white px-5 py-2 rounded-lg text-sm font-semibold transition-colors cursor-pointer"
          >
            {{ locale === "bn" ? "লগইন / সাইন আপ" : "Login / Sign Up" }}
          </button>
        </template>

        <button
          @click="openCart"
          class="flex items-center gap-2 text-gray-700 hover:text-primary transition-colors cursor-pointer"
        >
          <span class="relative">
            <ShoppingCart class="w-6 h-6 text-gray-600" />
            <span
              v-if="totalItems > 0"
              class="absolute -top-1.5 -right-1.5 w-4.5 h-4.5 bg-primary text-white text-[10px] font-bold rounded-full flex items-center justify-center"
            >
              {{ totalItems }}
            </span>
          </span>
          <span class="text-sm font-medium font-display text-gray-600"
            >cart</span
          >
        </button>
      </div>
    </div>

    <!-- Bottom Row: Navigation Bar in Theme Primary Green -->
    <div class="bg-primary text-white relative">
      <div
        class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 h-12 flex items-center justify-between"
      >
        <!-- Nav Links -->
        <nav class="flex items-center gap-6 h-full">
          <router-link
            to="/"
            class="hover:bg-white/10 px-3 h-full flex items-center text-sm font-semibold transition-colors"
          >
            {{ locale === "bn" ? "হোম" : "Home" }}
          </router-link>

          <!-- Category Trigger with Mega Menu -->
          <div
            class="relative h-full flex items-center group cursor-pointer"
            @mouseenter="megaMenuOpen = true"
            @mouseleave="megaMenuOpen = false"
          >
            <span
              class="hover:bg-white/10 px-3 h-full flex items-center text-sm font-semibold transition-colors gap-1"
            >
              {{ locale === "bn" ? "ক্যাটাগরি" : "Categories" }}
              <ChevronDown class="w-3.5 h-3.5" />
            </span>

            <!-- Mega Menu Dropdown Card -->
            <transition name="fade">
              <div
                v-show="megaMenuOpen"
                class="absolute left-0 mt-0 top-full bg-white border border-gray-200 shadow-2xl rounded-b-2xl p-6 z-50 text-gray-800 border-t-4 border-primary w-[640px]"
              >
                <div
                  class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4 border-b pb-2"
                >
                  {{ locale === "bn" ? "ক্যাটাগরি সমূহ" : "Categories" }}
                </div>

                <!-- Category Grid -->
                <div
                  v-if="categories && categories.length > 0"
                  class="grid grid-cols-2 gap-3 max-h-[400px] overflow-y-auto pr-1"
                >
                  <router-link
                    v-for="category in categories"
                    :key="category.id"
                    :to="`/products-list?category=${category.id}`"
                    class="flex items-center gap-3 p-2 rounded-xl hover:bg-primary/5 hover:text-primary transition-all group border border-gray-50 hover:border-primary/10"
                    @click="megaMenuOpen = false"
                  >
                    <div
                      class="w-8 h-8 rounded-full bg-primary/5 flex items-center justify-center shrink-0 group-hover:bg-primary group-hover:text-white transition-all"
                    >
                      <CategoryIcon
                        :name="category.name"
                        :icon="category.icon"
                        class="w-4 h-4 text-primary group-hover:text-white transition-colors"
                      />
                    </div>
                    <div class="flex-1 min-w-0">
                      <h4
                        class="text-xs font-semibold truncate text-gray-700 group-hover:text-primary transition-colors"
                      >
                        {{ category.name }}
                      </h4>
                      <p class="text-[10px] text-gray-400 mt-0.5 truncate">
                        {{
                          (category.dealCount || 0) +
                          (category.couponCount || 0)
                        }}
                        {{ t("total_offers") }}
                      </p>
                    </div>
                  </router-link>
                </div>

                <div v-else class="py-6 text-center text-xs text-gray-400">
                  {{
                    locale === "bn"
                      ? "কোনো ক্যাটাগরি পাওয়া যায়নি"
                      : "No categories found"
                  }}
                </div>

                <!-- Footer Action -->
                <div class="mt-4 pt-3 border-t text-center">
                  <router-link
                    to="/categories"
                    class="text-xs font-bold text-primary hover:underline inline-flex items-center gap-1"
                    @click="megaMenuOpen = false"
                  >
                    {{
                      locale === "bn"
                        ? "সব ক্যাটাগরি দেখুন"
                        : "View All Categories"
                    }}
                    →
                  </router-link>
                </div>
              </div>
            </transition>
          </div>

          <router-link
            to="/products-list"
            class="hover:bg-white/10 px-3 h-full flex items-center text-sm font-semibold transition-colors"
          >
            {{ locale === "bn" ? "সকল পণ্য" : "All Products" }}
          </router-link>
          <router-link
            to="/deals"
            class="hover:bg-white/10 px-3 h-full flex items-center text-sm font-semibold transition-colors"
          >
            {{ locale === "bn" ? "সকল অফার" : "All Offers" }}
          </router-link>
          <router-link
            to="/products-list?free_delivery=true"
            class="hover:bg-white/10 px-3 h-full flex items-center text-sm font-semibold transition-colors"
          >
            {{ locale === "bn" ? "ফ্রি ডেলিভারি" : "Free Delivery" }}
          </router-link>
          <router-link
            to="/brands"
            class="hover:bg-white/10 px-3 h-full flex items-center text-sm font-semibold transition-colors"
          >
            {{ locale === "bn" ? "সকল ব্র্যান্ড" : "All Brands" }}
          </router-link>
          <router-link
            to="/contact"
            class="hover:bg-white/10 px-3 h-full flex items-center text-sm font-semibold transition-colors"
          >
            {{ t("contact") }}
          </router-link>

          <!-- Account Dropdown -->
          <div
            class="relative h-full flex items-center group cursor-pointer"
            @mouseenter="accountMenuOpen = true"
            @mouseleave="accountMenuOpen = false"
          >
            <span
              class="hover:bg-white/10 px-3 h-full flex items-center text-sm font-semibold transition-colors gap-1"
            >
              {{ isAuthenticated ? (user?.name ?? "অ্যাকাউন্ট") : (locale === "bn" ? "অ্যাকাউন্ট" : "Account") }}
              <ChevronDown class="w-3.5 h-3.5" />
            </span>
            <transition name="fade">
              <div
                v-show="accountMenuOpen"
                class="absolute left-0 mt-0 top-full bg-white border border-gray-200 shadow-lg rounded-b-xl py-2 w-40 z-50 text-gray-800 text-left"
              >
                <template v-if="isAuthenticated">
                  <router-link
                    to="/dashboard"
                    class="block w-full text-left px-4 py-2 text-xs font-semibold hover:bg-gray-100 hover:text-primary transition-colors cursor-pointer"
                  >
                    {{ locale === "bn" ? "ড্যাশবোর্ড" : "Dashboard" }}
                  </router-link>
                  <button
                    @click="logout"
                    class="w-full text-left px-4 py-2 text-xs font-semibold hover:bg-gray-100 hover:text-primary transition-colors cursor-pointer border-t border-gray-100"
                  >
                    {{ locale === "bn" ? "লগআউট" : "Logout" }}
                  </button>
                </template>
                <template v-else>
                  <button
                    @click="openAuth('login')"
                    class="w-full text-left px-4 py-2 text-xs font-semibold hover:bg-gray-100 hover:text-primary transition-colors cursor-pointer"
                  >
                    {{ locale === "bn" ? "লগইন" : "Login" }}
                  </button>
                  <button
                    @click="openAuth('register')"
                    class="w-full text-left px-4 py-2 text-xs font-semibold hover:bg-gray-100 hover:text-primary transition-colors cursor-pointer"
                  >
                    {{ locale === "bn" ? "সাইন আপ" : "Sign Up" }}
                  </button>
                </template>
              </div>
            </transition>
          </div>

          <button
            @click="handleContactClick"
            class="hover:bg-white/10 px-3 h-full flex items-center text-sm font-semibold transition-colors cursor-pointer"
          >
            {{ locale === "bn" ? "যোগাযোগ" : "Contact" }}
          </button>
        </nav>

        <!-- Language Switcher button -->
        <button
          @click="toggleLocale"
          class="px-3 py-1 text-xs font-bold bg-white/20 text-white hover:bg-white/35 border border-white/25 rounded-md transition-colors cursor-pointer"
        >
          {{ locale === "bn" ? "English" : "বাংলা" }}
        </button>
      </div>
    </div>
  </header>

  <!-- Responsive Mobile Header Layout -->
  <header
    class="sticky top-0 z-40 bg-white border-b border-gray-200 shadow-sm md:hidden"
  >
    <div
      class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between"
    >
      <!-- Left: Menu & Logo/Name -->
      <div class="flex items-center gap-3">
        <!-- Menu Toggle -->
        <button @click="menuOpen = true" :aria-label="locale === 'bn' ? 'মেনু খুলুন' : 'Open menu'" class="min-w-11 min-h-11 text-gray-600 cursor-pointer flex items-center justify-center">
          <Menu class="w-6 h-6" />
        </button>

        <!-- Logo -->
        <router-link
          to="/"
          class="flex items-center gap-2"
        >
          <img
            v-if="headerLogo && !logoError"
            :src="headerLogo"
            alt="Logo"
            class="h-9 w-auto object-contain"
            @error="logoError = true"
          />
          <span v-else class="text-base font-bold text-primary">{{ appName || 'RizikPoint' }}</span>
         
        </router-link>
      </div>

      <!-- Right: Cart and Search Actions -->
      <div class="flex items-center gap-2 shrink-0">
        <!-- Language Switcher Button -->
        <button
          @click="toggleLocale"
          class="px-2 py-1 text-xs font-bold bg-primary/10 text-primary hover:bg-primary/20 border border-primary/20 rounded-md transition-colors cursor-pointer"
        >
          {{ locale === "bn" ? "EN" : "বাং" }}
        </button>

        <button
          @click="searchOpen = !searchOpen"
          class="p-1.5 text-gray-600 hover:text-primary cursor-pointer"
        >
          <Search class="w-5 h-5" />
        </button>
        <button
          @click="openCart"
          class="relative p-1.5 text-gray-600 hover:text-primary cursor-pointer"
        >
          <ShoppingCart class="w-5 h-5" />
          <span
            v-if="totalItems > 0"
            class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-primary text-white text-[10px] font-bold rounded-full flex items-center justify-center animate-pulse"
          >
            {{ totalItems > 9 ? "9+" : totalItems }}
          </span>
        </button>
      </div>
    </div>

    <!-- Mobile Search Input Bar (with live suggestions) -->
    <transition name="slide-down">
      <div
        v-if="searchOpen"
        class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 pb-3 relative"
        ref="searchContainerRef"
      >
        <form @submit.prevent="handleSearchSubmit" class="relative">
          <Search
            class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 z-10"
          />
          <input
            type="search"
            v-model="searchQuery"
            v-focus
            @input="onSearchInput"
            @focus="searchQuery.trim() && (suggestionsOpen = true)"
            :placeholder="t('search_placeholder')"
            class="w-full h-10 pl-9 pr-4 rounded-full border border-gray-300 focus:border-primary focus:ring-1 focus:ring-primary outline-none text-sm bg-white"
            autocomplete="off"
          />
          <Loader2
            v-if="isSearching"
            class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-primary animate-spin"
          />
        </form>

        <!-- Mobile suggestions -->
        <transition name="fade">
          <div
            v-if="suggestionsOpen && (suggestions.length > 0 || isSearching || searchQuery.trim())"
            class="absolute left-4 right-4 top-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-2xl z-50 overflow-hidden"
          >
            <div v-if="isSearching && suggestions.length === 0" class="px-4 py-6 text-center text-xs text-gray-500">
              <Loader2 class="w-4 h-4 animate-spin inline mr-2" />
              {{ locale === "bn" ? "খোঁজা হচ্ছে..." : "Searching..." }}
            </div>
            <div
              v-else-if="suggestions.length === 0 && searchQuery.trim()"
              class="px-4 py-6 text-center text-xs text-gray-500"
            >
              {{ locale === "bn" ? "কোনো পণ্য পাওয়া যায়নি" : "No products found" }}
            </div>
            <ul v-else class="max-h-80 overflow-y-auto py-1">
              <li
                v-for="p in suggestions"
                :key="p.id"
                @mousedown.prevent="pickSuggestion(p)"
                class="flex items-center gap-3 px-3 py-2 mx-1 rounded-xl hover:bg-primary/5 cursor-pointer transition-colors"
              >
                <img
                  v-if="p.imageUrl"
                  :src="p.imageUrl"
                  :alt="p.title"
                  class="w-9 h-9 rounded-lg object-cover bg-gray-100 shrink-0"
                />
                <div
                  v-else
                  class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center shrink-0"
                >
                  <Package class="w-4 h-4 text-primary" />
                </div>
                <div class="flex-1 min-w-0">
                  <div class="text-xs font-bold text-gray-800 line-clamp-2 break-words">{{ p.title }}</div>
                </div>
                <div v-if="p.discountedPrice" class="text-xs font-extrabold text-primary shrink-0">
                  ৳{{ p.discountedPrice }}
                </div>
              </li>
            </ul>
            <div
              v-if="suggestions.length > 0"
              class="border-t border-gray-100 px-4 py-2.5 text-center bg-gray-50/60"
            >
              <button
                type="button"
                @mousedown.prevent="handleSearchSubmit"
                class="text-[11px] font-extrabold text-primary hover:underline cursor-pointer"
              >
                {{ locale === "bn"
                    ? `“${searchQuery}” এর জন্য সব ফলাফল দেখুন`
                    : `See all results for “${searchQuery}”`
                }} →
              </button>
            </div>
          </div>
        </transition>
      </div>
    </transition>
  </header>

  <!-- Mobile Sidebar Menu Drawer -->
  <transition name="fade">
    <div v-if="menuOpen" class="fixed inset-0 z-50 flex">
      <!-- Backdrop -->
      <div class="absolute inset-0 bg-black/40" @click="menuOpen = false" />

      <!-- Drawer Content -->
      <div
        class="relative w-64 bg-white h-full shadow-xl flex flex-col z-10 transition-transform duration-300 overflow-hidden"
      >
        <div class="flex items-center justify-between p-4 border-b">
          <div class="flex items-center gap-2">
            <img
              v-if="headerLogo && !logoError"
              :src="headerLogo"
              alt="Logo"
              class="h-9 w-auto object-contain"
              @error="logoError = true"
            />
            
          </div>
          <button @click="menuOpen = false" class="p-1 cursor-pointer">
            <X class="w-5 h-5 text-gray-600" />
          </button>
        </div>

        <nav class="flex flex-col p-4 gap-1 overflow-y-auto flex-1 min-h-0">
<!-- Expandable Categories -->
          <div>
            <button
              @click="toggleCategories"
              class="flex w-full items-center gap-3 px-3 py-3 rounded-lg hover:bg-primary/10 hover:text-primary text-gray-700 font-medium cursor-pointer text-left text-sm"
            >
              <Menu class="w-5 h-5" />
              <span class="flex-1">{{ t("categories") }}</span>
              <Loader2 v-if="loadingSubCategories" class="w-4 h-4 animate-spin text-primary" />
              <ChevronDown v-else class="w-4 h-4 transition-transform" :class="categoriesExpanded ? 'rotate-180' : ''" />
            </button>
            <transition name="drawer-sub">
              <div v-if="categoriesExpanded" class="pl-9 pr-2 pb-2 space-y-0.5 drawer-sub-list">
                <router-link
                  to="/categories"
                  @click="menuOpen = false"
                  class="flex items-center justify-between gap-2 px-3 py-2 rounded-md text-xs font-semibold text-primary hover:bg-primary/10 transition-colors"
                >
                  <span>{{ t("all_categories") }}</span>
                  <ChevronRight class="w-3.5 h-3.5" />
                </router-link>
                <div v-if="loadingSubCategories && subCategories.length === 0" class="px-3 py-3 text-xs text-gray-400 text-center">
                  <Loader2 class="w-4 h-4 animate-spin mx-auto" />
                </div>
                <router-link
                  v-for="cat in subCategories"
                  :key="cat.id"
                  :to="`/products-list?category=${cat.id}`"
                  @click="menuOpen = false"
                  class="flex items-center justify-between gap-2 px-3 py-2 rounded-md text-xs text-gray-600 hover:bg-primary/5 hover:text-primary transition-colors"
                >
                  <span class="truncate">{{ cat.name }}</span>
                  <ChevronRight class="w-3 h-3 opacity-50" />
                </router-link>
                <p
                  v-if="!loadingSubCategories && subCategories.length === 0"
                  class="px-3 py-2 text-xs text-gray-400"
                >
                  {{ locale === "bn" ? "কোনো সাব-ক্যাটাগরি নেই" : "No sub-categories" }}
                </p>
              </div>
            </transition>
          </div>
          <router-link
            to="/brands"
            @click="menuOpen = false"
            class="flex items-center gap-3 px-3 py-3 rounded-lg hover:bg-primary/10 hover:text-primary text-gray-700 font-medium"
          >
            <Sparkles class="w-5 h-5" />
            {{ locale === "bn" ? "ব্র্যান্ড সমূহ" : "Brands" }}
          </router-link>
          
          <router-link
            to="/coupons"
            @click="menuOpen = false"
            class="flex items-center gap-3 px-3 py-3 rounded-lg hover:bg-primary/10 hover:text-primary text-gray-700 font-medium"
          >
            <Tag class="w-5 h-5" /> {{ t("coupons") }}
          </router-link>
          <router-link
            to="/contact"
            @click="menuOpen = false"
            class="flex items-center gap-3 px-3 py-3 rounded-lg hover:bg-primary/10 hover:text-primary text-gray-700 font-medium"
          >
            <Phone class="w-5 h-5" /> {{ t("contact") }}
          </router-link>
          <!-- Account options for mobile -->
          <hr class="my-2 border-gray-100" />
          <template v-if="isAuthenticated">
            <div class="px-3 py-2 text-xs font-semibold text-gray-400">
              {{ locale === "bn" ? "অ্যাকাউন্ট: " : "Account: " }}{{ user?.name }}
            </div>
            <router-link
              to="/dashboard"
              @click="menuOpen = false"
              class="flex w-full items-center gap-3 px-3 py-2.5 rounded-lg bg-gradient-to-r from-primary/10 to-emerald-50 hover:from-primary/20 hover:to-emerald-100 text-primary font-semibold cursor-pointer text-left text-sm border border-primary/20 transition-colors"
            >
              <LayoutDashboard class="w-5 h-5" />
              <span class="flex-1">{{ locale === "bn" ? "ড্যাশবোর্ড" : "Dashboard" }}</span>
              <ChevronRight class="w-4 h-4 opacity-60" />
            </router-link>
            <button
              @click="logout(); menuOpen = false"
              class="flex w-full items-center gap-3 px-3 py-3 rounded-lg hover:bg-red-50 hover:text-red-650 text-gray-700 font-medium cursor-pointer text-left text-sm"
            >
              <User class="w-5 h-5" /> {{ locale === "bn" ? "লগআউট" : "Logout" }}
            </button>
          </template>
          <template v-else>
            <button
              @click="openAuth('login'); menuOpen = false"
              class="flex w-full items-center gap-3 px-3 py-3 rounded-lg hover:bg-primary/10 hover:text-primary text-gray-700 font-medium cursor-pointer text-left text-sm"
            >
              <User class="w-5 h-5" /> {{ locale === "bn" ? "লগইন" : "Login" }}
            </button>
            <button
              @click="openAuth('register'); menuOpen = false"
              class="flex w-full items-center gap-3 px-3 py-3 rounded-lg hover:bg-primary/10 hover:text-primary text-gray-700 font-medium cursor-pointer text-left text-sm"
            >
              <Sparkles class="w-5 h-5" /> {{ locale === "bn" ? "নিবন্ধন" : "Register" }}
            </button>
          </template>
        </nav>

        <div class="mt-auto p-4 border-t bg-gray-50">
          <div class="flex items-center gap-2 text-sm text-gray-600">
            <Phone class="w-4 h-4 text-primary" />
            <span>{{ helplineNumber }}</span>
          </div>
        </div>
      </div>
    </div>
  </transition>

  <!-- Floating Green Cart Badge matching user mockup -->
  <div
    @click="openCart"
    class="fixed right-0 top-1/2 -translate-y-1/2 bg-primary hover:bg-primary/90 text-white p-3 rounded-l-2xl shadow-xl z-40 flex flex-col items-center gap-1.5 cursor-pointer transition-all duration-300 border-l border-y border-white/20 select-none group"
  >
    <div class="relative">
      <ShoppingCart
        class="w-5 h-5 group-hover:scale-110 transition-transform"
      />
      <span
        class="absolute -top-2 -right-2 bg-yellow-400 text-gray-900 text-[9px] font-extrabold w-4 h-4 rounded-full flex items-center justify-center shadow-xs"
      >
        {{ totalItems }}
      </span>
    </div>
    <span
      class="text-[9px] font-bold tracking-tight text-center whitespace-nowrap leading-none"
    >
      {{ totalItems }} Items
    </span>
    <span
      class="text-[8px] font-extrabold bg-white/20 px-1 py-0.5 rounded leading-none font-mono"
    >
      {{ subtotal }} TK
    </span>
  </div>
  </div>
</template>

<script>
// Custom directive to focus input on mount
const vFocus = {
  mounted: (el) => el.focus(),
};
export default {
  directives: {
    focus: vFocus,
  },
};
</script>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.25s ease-out;
}
.slide-down-enter-from,
.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

.drawer-sub-enter-active,
.drawer-sub-leave-active {
  transition: opacity 0.2s ease;
}
.drawer-sub-enter-from,
.drawer-sub-leave-to {
  opacity: 0;
}
.drawer-sub-list {
  max-height: 50vh;
  overflow-y: auto;
  overscroll-behavior: contain;
}
.drawer-sub-list::-webkit-scrollbar {
  width: 4px;
}
.drawer-sub-list::-webkit-scrollbar-thumb {
  background: rgba(16, 185, 129, 0.4);
  border-radius: 2px;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>

