<script setup>
import { ref, onMounted, computed, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import { SlidersHorizontal, ChevronDown, RotateCcw, Search, Loader2, LayoutGrid, List } from "@lucide/vue";
import { fetchDealsPaged, fetchFilterCategories, fetchFilterBrands } from "@/lib/api";
import { useI18n } from "@/lib/i18n";
import { useBusinessSettings } from "@/composables/useBusinessSettings";
import { dummyImagesEnabled, dummyProductImage } from "@/lib/dummyProductImages";
import ProductCard from "@/components/shared/ProductCard.vue";
import SkeletonLoader from "@/components/shared/SkeletonLoader.vue";
import { usePageTitle } from "@/composables/usePageTitle";
import { pageTitles } from "@/lib/pageTitles";
usePageTitle(pageTitles.ProductsList);
const route  = useRoute();
const router = useRouter();
const { locale, t } = useI18n();
const { get: getBusinessSetting } = useBusinessSettings();
const useDummyImages = computed(() => dummyImagesEnabled(getBusinessSetting));

function listImageFor(product) {
  return product.imageUrl || (useDummyImages.value ? dummyProductImage(product) : null);
}

// ── Filter state ───────────────────────────────────────────────────────────────
const sortBy             = ref("latest");
const viewMode = ref("grid"); // "grid" | "list"
const filterOpen         = ref(false);
const selectedCategory   = ref(null);
const filterPriceMax     = ref(50000);
const filterPriceCurrent = ref(50000);
const selectedBrands     = ref([]);
const freeDeliveryOnly   = ref(false);
const searchQuery        = ref("");

// ── Data ───────────────────────────────────────────────────────────────────────
const allProducts  = ref([]);

// ── Pagination state ───────────────────────────────────────────────────────────
const currentPage = ref(1);
const lastPage    = ref(1);
const perPage     = ref(10);
const totalProducts = ref(0);
const hasPrev = computed(() => currentPage.value > 1);
const hasNext = computed(() => currentPage.value < lastPage.value);
const pageNumbers = computed(() => {
  const total = lastPage.value;
  const cur   = currentPage.value;
  if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);
  const pages = new Set([1, total, cur, cur - 1, cur + 1]);
  const sorted = [...pages].filter(p => p >= 1 && p <= total).sort((a, b) => a - b);
  const out = [];
  for (let i = 0; i < sorted.length; i++) {
    if (i > 0 && sorted[i] - sorted[i - 1] > 1) out.push('...');
    out.push(sorted[i]);
  }
  return out;
});
function goToPage(p) {
  if (typeof p !== 'number' || p < 1 || p > lastPage.value || p === currentPage.value) return;
  currentPage.value = p;
  loadProducts();
  if (typeof window !== 'undefined') window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ── Refetch when per-page changes ───────────────────────────────────────
watch(perPage, () => {
  currentPage.value = 1;
  loadProducts();
});

const categories   = ref([]);
const brands       = ref([]);
const isLoading    = ref(false);
const isLoadingFilters = ref(false);

// ── Sidebar collapse states ────────────────────────────────────────────────────
const categoriesExpanded = ref(true);
const brandsExpanded     = ref(true);
const filtersExpanded    = ref(true);

// ── Init from URL query ────────────────────────────────────────────────────────
watch(
  () => route.query,
  (q) => {
    if (q.category) selectedCategory.value = Number(q.category);
    if (q.search)   searchQuery.value = q.search;
    if (q.free_delivery === "true") freeDeliveryOnly.value = true;
    if (q.brand)    selectedBrands.value = [q.brand];
  },
  { immediate: true }
);

// ── Load filter options ────────────────────────────────────────────────────────
async function loadFilters() {
  isLoadingFilters.value = true;
  try {
    const [cats, brs] = await Promise.all([fetchFilterCategories(), fetchFilterBrands()]);
    categories.value = cats;
    brands.value     = brs;
  } catch (err) {
    console.error("Failed to load filters:", err);
  } finally {
    isLoadingFilters.value = false;
  }
}

// ── Load products ──────────────────────────────────────────────────────────────
async function loadProducts() {
  isLoading.value = true;
  try {
    const params = {};
    if (selectedCategory.value) params.categoryId = selectedCategory.value;
    if (searchQuery.value.trim()) params.search = searchQuery.value.trim();
    params.page = currentPage.value;
    params.limit = perPage.value;  // page size; pagination meta drives total pages
    const resp = await fetchDealsPaged(params);
    allProducts.value = resp.deals ?? [];
    if (resp.meta) {
      currentPage.value  = resp.meta.current_page ?? 1;
      lastPage.value     = resp.meta.last_page ?? 1;
      perPage.value      = resp.meta.per_page ?? 10;
      totalProducts.value = resp.meta.total ?? 0;
    }
    // Compute max price from returned products
    const prices = allProducts.value.map(p => p.discountedPrice ?? p.originalPrice ?? 0);
    if (prices.length > 0) {
      const max = Math.ceil(Math.max(...prices) / 500) * 500;
      filterPriceMax.value     = max;
      filterPriceCurrent.value = max;
    }
  } catch (err) {
    console.error("Failed to load products:", err);
  } finally {
    isLoading.value = false;
  }
}

onMounted(() => {
  loadFilters();
  loadProducts();
});

// ── Clear all filters ──────────────────────────────────────────────────────────
function clearFilters() {
  selectedCategory.value   = null;
  filterPriceCurrent.value = filterPriceMax.value;
  selectedBrands.value     = [];
  freeDeliveryOnly.value   = false;
  sortBy.value             = "latest";
  currentPage.value = 1;
  searchQuery.value        = "";
  router.push({ query: {} });
  loadProducts();
}

// ── Apply category filter ──────────────────────────────────────────────────────
function selectCategory(catId) {
  selectedCategory.value = selectedCategory.value === catId ? null : catId;
  const q = { ...route.query };
  if (selectedCategory.value) q.category = selectedCategory.value;
  else delete q.category;
  router.push({ query: q });
  currentPage.value = 1;
  loadProducts();
}

// ── Category product counts ────────────────────────────────────────────────────
const categoryCounts = computed(() => {
  const counts = {};
  allProducts.value.forEach(p => {
    if (p.categoryId) counts[p.categoryId] = (counts[p.categoryId] || 0) + 1;
  });
  return counts;
});

// ── Derived brand list from products ──────────────────────────────────────────
const availableBrands = computed(() => {
  if (brands.value.length > 0) return brands.value;
  const seen = new Set();
  return allProducts.value
    .filter(p => p.storeName && !seen.has(p.storeName) && seen.add(p.storeName))
    .map(p => ({ id: p.storeId, name: p.storeName }));
});

// ── Filter + Sort pipeline ─────────────────────────────────────────────────────
const filteredProducts = computed(() => {
  let list = [...allProducts.value];

  // 1. Search
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase();
    list = list.filter(p => p.title.toLowerCase().includes(q));
  }

  // 2. Price
  list = list.filter(p => {
    const price = p.discountedPrice ?? p.originalPrice ?? 0;
    return price <= filterPriceCurrent.value;
  });

  // 3. Brands
  if (selectedBrands.value.length > 0) {
    list = list.filter(p => selectedBrands.value.includes(p.storeName));
  }

  // 4. Free delivery (simulated: big discount products)
  if (freeDeliveryOnly.value) {
    list = list.filter(p => p.discountPercent >= 10);
  }

  // 5. Sort
  switch (sortBy.value) {
    case "price_asc":  list.sort((a, b) => (a.discountedPrice ?? a.originalPrice ?? 0) - (b.discountedPrice ?? b.originalPrice ?? 0)); break;
    case "price_desc": list.sort((a, b) => (b.discountedPrice ?? b.originalPrice ?? 0) - (a.discountedPrice ?? a.originalPrice ?? 0)); break;
    case "discount":   list.sort((a, b) => (b.discountPercent ?? 0) - (a.discountPercent ?? 0)); break;
    case "popular":    list.sort((a, b) => (b.usedCount ?? 0) - (a.usedCount ?? 0)); break;
    default:           list.sort((a, b) => b.id - a.id); break;
  }

  return list;
});

const hasFilters = computed(() =>
  selectedCategory.value !== null ||
  filterPriceCurrent.value < filterPriceMax.value ||
  selectedBrands.value.length > 0 ||
  freeDeliveryOnly.value ||
  sortBy.value !== "latest" ||
  searchQuery.value.trim() !== ""
);

// Format price
function formatPrice(val) {
  return Number(val).toLocaleString("en-BD");
}

const paginationText = computed(() => {
  if (locale.value === 'bn') {
    return 'পৃষ্ঠা ' + currentPage.value + ' / ' + lastPage.value + ' — মোট ' + totalProducts.value + ' টি পণ্য'
  }
  return 'Page ' + currentPage.value + ' of ' + lastPage.value + ' — ' + totalProducts.value + ' products'
})

const productsCountText = computed(() => {
  if (locale.value === 'bn') {
    return 'মোট ' + filteredProducts.value.length + ' টি পণ্য পাওয়া গেছে'
  }
  return filteredProducts.value.length + ' products found'
})

const dealLink = (product) => {
  return '/deal/' + product.id
}
</script>

<template>
  <div class="pb-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 items-start">

      <!-- ── Left Sidebar: Desktop Filters ─────────────────────────────────── -->
      <aside class="md:col-span-1 hidden md:block sticky top-24 self-start">
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs space-y-5">

          <!-- Header -->
          <div class="flex justify-between items-center">
            <h3 class="font-display font-bold text-gray-800 text-sm flex items-center gap-1.5">
              <SlidersHorizontal class="w-4 h-4 text-primary" />
              {{ locale === 'bn' ? 'ফিল্টার' : 'Filters' }}
            </h3>
            <button
              v-if="hasFilters"
              @click="clearFilters"
              class="text-xs font-semibold text-red-600 hover:text-red-700 flex items-center gap-1 cursor-pointer transition-colors"
            >
              <RotateCcw class="w-3.5 h-3.5" />
              {{ locale === 'bn' ? 'রিসেট' : 'Reset' }}
            </button>
          </div>

          <!-- Search -->
          <div class="relative">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" />
            <input
              v-model="searchQuery"
              @input="loadProducts"
              type="search"
              :placeholder="locale === 'bn' ? 'পণ্য খুঁজুন...' : 'Search products...'"
              class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:border-primary bg-gray-50"
            />
          </div>

          <!-- Price Range -->
          <div class="space-y-2">
            <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">
              {{ locale === 'bn' ? 'মূল্য সীমা' : 'Price Range' }}
            </h4>
            <input
              type="range"
              min="0"
              :max="filterPriceMax"
              step="100"
              v-model.number="filterPriceCurrent"
              class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-primary"
            />
            <div class="flex items-center justify-between text-xs font-bold text-primary font-mono">
              <span>৳ 0</span>
              <span>৳ {{ formatPrice(filterPriceCurrent) }}</span>
            </div>
          </div>

          <!-- Categories -->
          <div class="space-y-2">
            <div
              class="flex justify-between items-center cursor-pointer pb-2 border-b-2 border-primary/30 select-none"
              @click="categoriesExpanded = !categoriesExpanded"
            >
              <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">
                {{ locale === 'bn' ? 'ক্যাটাগরি' : 'Categories' }}
              </h4>
              <span class="text-sm font-bold text-gray-400 font-mono">{{ categoriesExpanded ? '−' : '+' }}</span>
            </div>
            <transition name="slide-down">
              <div v-show="categoriesExpanded" class="pt-1 max-h-56 overflow-y-auto space-y-1 pr-1 hide-scrollbar">
                <div v-if="isLoadingFilters" class="flex items-center gap-2 py-2">
                  <Loader2 class="w-3.5 h-3.5 text-primary animate-spin" />
                  <span class="text-xs text-gray-400">{{ locale === 'bn' ? 'লোড হচ্ছে...' : 'Loading...' }}</span>
                </div>
                <template v-else>
                  <button
                    @click="selectCategory(null)"
                    class="w-full text-left py-1.5 px-2 text-xs font-semibold rounded-lg transition-colors flex justify-between items-center cursor-pointer"
                    :class="!selectedCategory ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:text-primary hover:bg-gray-50'"
                  >
                    <span>{{ locale === 'bn' ? 'সকল ক্যাটাগরি' : 'All Categories' }}</span>
                    <span class="font-bold text-gray-400 text-[10px]">({{ allProducts.length }})</span>
                  </button>
                  <button
                    v-for="cat in categories"
                    :key="cat.id"
                    @click="selectCategory(cat.id)"
                    class="w-full text-left py-1.5 px-2 text-xs font-semibold rounded-lg transition-colors flex justify-between items-center cursor-pointer"
                    :class="selectedCategory === cat.id ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:text-primary hover:bg-gray-50'"
                  >
                    <span>{{ cat.name }}</span>
                    <span class="font-bold text-gray-400 text-[10px]">({{ cat.dealCount || 0 }})</span>
                  </button>
                </template>
              </div>
            </transition>
          </div>

          <!-- Brands -->
          <div class="space-y-2">
            <div
              class="flex justify-between items-center cursor-pointer pb-2 border-b-2 border-primary/30 select-none"
              @click="brandsExpanded = !brandsExpanded"
            >
              <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">
                {{ locale === 'bn' ? 'ব্র্যান্ড / স্টোর' : 'Brands / Stores' }}
              </h4>
              <span class="text-sm font-bold text-gray-400 font-mono">{{ brandsExpanded ? '−' : '+' }}</span>
            </div>
            <transition name="slide-down">
              <div v-show="brandsExpanded" class="pt-1 max-h-52 overflow-y-auto space-y-1 pr-1 hide-scrollbar">
                <div v-if="availableBrands.length === 0" class="text-xs text-gray-400 py-2">
                  {{ locale === 'bn' ? 'ব্র্যান্ড পাওয়া যায়নি' : 'No brands found' }}
                </div>
                <div v-for="brand in availableBrands" :key="brand.id ?? brand.name" class="flex items-center gap-2.5 py-1 text-xs">
                  <input
                    type="checkbox"
                    :id="'brand-' + (brand.id ?? brand.name)"
                    :value="brand.name"
                    v-model="selectedBrands"
                    class="rounded border-gray-300 text-primary focus:ring-primary cursor-pointer h-3.5 w-3.5"
                  />
                  <label :for="'brand-' + (brand.id ?? brand.name)" class="text-gray-600 hover:text-primary cursor-pointer select-none font-semibold flex-1">
                    {{ brand.name }}
                  </label>
                </div>
              </div>
            </transition>
          </div>

          <!-- Additional filters -->
          <div class="space-y-2">
            <div
              class="flex justify-between items-center cursor-pointer pb-2 border-b-2 border-primary/30 select-none"
              @click="filtersExpanded = !filtersExpanded"
            >
              <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">
                {{ locale === 'bn' ? 'অন্যান্য ফিল্টার' : 'More Filters' }}
              </h4>
              <span class="text-sm font-bold text-gray-400 font-mono">{{ filtersExpanded ? '−' : '+' }}</span>
            </div>
            <transition name="slide-down">
              <div v-show="filtersExpanded" class="pt-1 space-y-2">
                <div class="flex items-center gap-2.5 py-1 text-xs">
                  <input
                    type="checkbox"
                    id="filter-free-delivery"
                    v-model="freeDeliveryOnly"
                    class="rounded border-gray-300 text-primary focus:ring-primary cursor-pointer h-3.5 w-3.5"
                  />
                  <label for="filter-free-delivery" class="text-gray-600 hover:text-primary cursor-pointer select-none font-semibold flex-1">
                    {{ locale === 'bn' ? 'ফ্রি ডেলিভারি' : 'Free Delivery' }}
                  </label>
                </div>
              </div>
            </transition>
          </div>
        </div>
      </aside>

      <!-- ── Right Column: Sort + Products ─────────────────────────────────── -->
      <section class="md:col-span-3">

        <!-- Mobile filter bar -->
        <div class="sticky top-14 z-30 bg-white border-b border-gray-200 shadow-sm md:hidden mb-4 rounded-xl overflow-hidden">
          <div class="flex items-center px-3 py-2 gap-2">
            <div class="relative flex-1">
              <select
                v-model="sortBy"
                class="w-full appearance-none border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 bg-white focus:outline-none focus:border-primary pr-8 cursor-pointer"
              >
                <option value="latest">{{ locale === 'bn' ? 'সর্বশেষ পণ্য' : 'Latest' }}</option>
                <option value="price_asc">{{ locale === 'bn' ? 'মূল্য: কম → বেশি' : 'Price: Low → High' }}</option>
                <option value="price_desc">{{ locale === 'bn' ? 'মূল্য: বেশি → কম' : 'Price: High → Low' }}</option>
                <option value="discount">{{ locale === 'bn' ? 'সর্বোচ্চ ছাড়' : 'Best Discount' }}</option>
                <option value="popular">{{ locale === 'bn' ? 'জনপ্রিয়' : 'Most Popular' }}</option>
              </select>
              <ChevronDown class="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
            </div>
            <button
              @click="filterOpen = !filterOpen"
              class="flex items-center gap-2 border rounded-lg px-4 py-2 text-sm font-semibold transition-colors cursor-pointer"
              :class="hasFilters ? 'bg-primary text-white border-primary' : 'border-gray-300 text-gray-700 bg-white'"
            >
              <SlidersHorizontal class="w-4 h-4" />
              {{ t('filter') }}
            </button>
          </div>

          <!-- Mobile expandable drawer -->
          <transition name="slide-down">
            <div v-show="filterOpen" class="px-4 pb-4 border-t border-gray-100 bg-white max-h-[65vh] overflow-y-auto space-y-4 pt-3 hide-scrollbar">

              <!-- Reset button -->
              <div class="flex justify-between items-center border-b pb-2">
                <span class="text-xs font-bold text-gray-700">{{ locale === 'bn' ? 'ফিল্টারসমূহ' : 'Filters' }}</span>
                <button v-if="hasFilters" @click="clearFilters" class="text-xs font-bold text-red-600 flex items-center gap-1 cursor-pointer">
                  <RotateCcw class="w-3.5 h-3.5" /> {{ locale === 'bn' ? 'রিসেট' : 'Reset All' }}
                </button>
              </div>

              <!-- Search -->
              <div class="relative">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" />
                <input
                  v-model="searchQuery"
                  @input="loadProducts"
                  type="search"
                  :placeholder="locale === 'bn' ? 'পণ্য খুঁজুন...' : 'Search products...'"
                  class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:border-primary bg-gray-50"
                />
              </div>

              <!-- Price -->
              <div class="space-y-2">
                <h4 class="text-xs font-bold text-gray-700 uppercase">{{ locale === 'bn' ? 'মূল্য সীমা' : 'Price Range' }}</h4>
                <input type="range" min="0" :max="filterPriceMax" step="100" v-model.number="filterPriceCurrent"
                  class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-primary" />
                <div class="flex justify-between text-xs font-bold text-primary font-mono">
                  <span>৳ 0</span><span>৳ {{ formatPrice(filterPriceCurrent) }}</span>
                </div>
              </div>

              <!-- Categories pills -->
              <div class="space-y-2">
                <h4 class="text-xs font-bold text-gray-700 uppercase border-b pb-1">{{ locale === 'bn' ? 'ক্যাটাগরি' : 'Categories' }}</h4>
                <div class="flex flex-wrap gap-2 pt-1">
                  <button @click="selectCategory(null)"
                    class="px-3 py-1.5 rounded-full text-xs font-semibold border transition-colors cursor-pointer"
                    :class="!selectedCategory ? 'bg-primary text-white border-primary' : 'border-gray-300 text-gray-600 bg-white'">
                    {{ locale === 'bn' ? 'সব' : 'All' }}
                  </button>
                  <button v-for="cat in categories" :key="cat.id"
                    @click="selectCategory(cat.id)"
                    class="px-3 py-1.5 rounded-full text-xs font-semibold border transition-colors cursor-pointer"
                    :class="selectedCategory === cat.id ? 'bg-primary text-white border-primary' : 'border-gray-300 text-gray-600 bg-white'">
                    {{ cat.name }} ({{ cat.dealCount || 0 }})
                  </button>
                </div>
              </div>

              <!-- Brands checkboxes -->
              <div class="space-y-2">
                <h4 class="text-xs font-bold text-gray-700 uppercase border-b pb-1">{{ locale === 'bn' ? 'ব্র্যান্ড' : 'Brands' }}</h4>
                <div class="grid grid-cols-2 gap-2 pt-1">
                  <div v-for="brand in availableBrands" :key="brand.id ?? brand.name" class="flex items-center gap-2 text-xs">
                    <input type="checkbox" :id="'mob-brand-' + (brand.id ?? brand.name)" :value="brand.name" v-model="selectedBrands"
                      class="rounded border-gray-300 text-primary focus:ring-primary cursor-pointer h-3.5 w-3.5" />
                    <label :for="'mob-brand-' + (brand.id ?? brand.name)" class="text-gray-600 cursor-pointer select-none font-semibold">{{ brand.name }}</label>
                  </div>
                </div>
              </div>

              <!-- Others -->
              <div class="space-y-2">
                <h4 class="text-xs font-bold text-gray-700 uppercase border-b pb-1">{{ locale === 'bn' ? 'অন্যান্য' : 'Others' }}</h4>
                <div class="flex items-center gap-2 text-xs pt-1">
                  <input type="checkbox" id="mob-free-delivery" v-model="freeDeliveryOnly"
                    class="rounded border-gray-300 text-primary focus:ring-primary cursor-pointer h-3.5 w-3.5" />
                  <label for="mob-free-delivery" class="text-gray-600 cursor-pointer select-none font-semibold">
                    {{ locale === 'bn' ? 'ফ্রি ডেলিভারি' : 'Free Delivery' }}
                  </label>
                </div>
              </div>

            </div>
          </transition>
        </div>

        <!-- Desktop sort bar -->
        <div class="hidden md:flex flex-wrap items-center justify-between gap-3 mb-5 bg-white border border-gray-200 rounded-2xl p-4 shadow-xs">
          <span class="text-xs text-gray-500 font-bold whitespace-nowrap">
            {{ productsCountText }}
          </span>

          <div class="flex items-center gap-3 flex-wrap">
            <div class="hidden sm:flex items-center gap-1 bg-gray-50 border border-gray-200 rounded-lg p-0.5" role="group" aria-label="View mode">
              <button
                type="button"
                @click="viewMode = 'grid'"
                :class="viewMode === 'grid' ? 'bg-white text-primary shadow-xs' : 'text-gray-400 hover:text-gray-600'"
                class="inline-flex items-center justify-center w-8 h-8 rounded-md transition-colors cursor-pointer"
                :title="locale === 'bn' ? 'গ্রিড ভিু' : 'Grid view'"
                aria-label="Grid view"
                :aria-pressed="viewMode === 'grid'"
              >
                <LayoutGrid class="w-4 h-4" />
              </button>
              <button
                type="button"
                @click="viewMode = 'list'"
                :class="viewMode === 'list' ? 'bg-white text-primary shadow-xs' : 'text-gray-400 hover:text-gray-600'"
                class="inline-flex items-center justify-center w-8 h-8 rounded-md transition-colors cursor-pointer"
                :title="locale === 'bn' ? 'লিস্ট ভিু' : 'List view'"
                aria-label="List view"
                :aria-pressed="viewMode === 'list'"
              >
                <List class="w-4 h-4" />
              </button>
            </div>

            <div class="flex items-center gap-2">
              <span class="text-xs font-semibold text-gray-400 whitespace-nowrap">{{ locale === 'bn' ? 'প্রতি পৃষ্ঠা:' : 'Per Page:' }}</span>
              <div class="relative">
                <select
                  v-model.number="perPage"
                  class="appearance-none border border-gray-200 rounded-lg px-3 py-2 pr-8 text-xs text-gray-700 bg-white focus:outline-none focus:border-primary cursor-pointer"
                >
                  <option :value="10">10</option>
                  <option :value="20">20</option>
                  <option :value="50">50</option>
                  <option :value="100">100</option>
                </select>
                <ChevronDown class="absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
              </div>
            </div>

            <div class="flex items-center gap-2">
              <span class="text-xs font-semibold text-gray-400 whitespace-nowrap">{{ locale === 'bn' ? 'সর্ট করুন:' : 'Sort By:' }}</span>
              <div class="relative">
                <select v-model="sortBy"
                  class="appearance-none border border-gray-200 rounded-lg px-3 py-2 pr-8 text-xs text-gray-700 bg-white focus:outline-none focus:border-primary cursor-pointer">
                  <option value="latest">{{ locale === 'bn' ? 'সর্বশেষ পণ্য' : 'Latest' }}</option>
                  <option value="price_asc">{{ locale === 'bn' ? 'মূহ্য: কম → বেশি' : 'Price: Low → High' }}</option>
                  <option value="price_desc">{{ locale === 'bn' ? 'মূহ্য: বেশি → কম' : 'Price: High → Low' }}</option>
                  <option value="discount">{{ locale === 'bn' ? 'সর্বোচ্ছ ছাড' : 'Best Discount' }}</option>
                  <option value="popular">{{ locale === 'bn' ? 'জনপ্রিয়' : 'Most Popular' }}</option>
                </select>
                <ChevronDown class="absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
              </div>
            </div>
          </div>
        </div>
        <!-- Product grid -->
        <div>
          <div v-if="isLoading">
            <SkeletonLoader type="card" :count="12" />
          </div>

          <div
            v-else-if="filteredProducts.length > 0"
            :class="viewMode === 'list' ? 'flex flex-col gap-3' : 'grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4'"
          >
            <template v-if="viewMode === 'grid'">
              <ProductCard
                v-for="product in filteredProducts"
                :key="product.id"
                :deal="product"
              />
            </template>
            <template v-else>
              <div
                v-for="product in filteredProducts"
                :key="product.id"
                class="flex flex-col sm:flex-row items-stretch gap-4 bg-white border border-gray-200 rounded-2xl p-3 sm:p-4 shadow-xs hover:shadow-md transition-shadow"
              >
                <router-link
                      :to="dealLink(product)"
                  class="block w-full sm:w-48 flex-shrink-0 bg-gray-50 rounded-xl overflow-hidden"
                >
                  <img
                    v-if="listImageFor(product)"
                    :src="listImageFor(product)"
                    :alt="product.title"
                    class="w-full h-40 sm:h-full object-cover"
                  />
                  <div v-else class="w-full h-40 sm:h-full flex items-center justify-center text-gray-300">
                    <LayoutGrid class="w-8 h-8" />
                  </div>
                </router-link>
                <div class="flex-1 min-w-0 flex flex-col">
                  <router-link :to="dealLink(product)" class="block">
                    <h3 class="text-sm sm:text-base font-bold text-gray-800 line-clamp-1 hover:text-primary transition-colors">
                      {{ product.title }}
                    </h3>
                  </router-link>
                  <p v-if="product.description" class="mt-1 text-xs text-gray-500 line-clamp-2">
                    {{ product.description }}
                  </p>
                  <div class="mt-2 flex items-baseline gap-2 flex-wrap">
                    <span class="text-lg font-extrabold text-primary">
                      ৳{{ Number(product.discountedPrice ?? product.originalPrice ?? 0).toLocaleString("en-BD") }}
                    </span>
                    <span
                      v-if="product.discountPercent > 0"
                      class="text-xs text-gray-400 line-through"
                    >
                      ৳{{ Number(product.originalPrice ?? 0).toLocaleString("en-BD") }}
                    </span>
                    <span
                      v-if="product.discountPercent > 0"
                      class="text-[10px] font-bold text-white bg-red-500 px-1.5 py-0.5 rounded"
                    >
                      -{{ product.discountPercent }}%
                    </span>
                  </div>
                  <div class="mt-auto pt-3 flex items-center gap-2">
                    <router-link
                      :to="dealLink(product)"
                      class="inline-flex items-center gap-1 bg-primary hover:bg-primary/90 text-white text-xs font-bold px-4 py-2 rounded-lg transition-colors"
                    >
                      {{ locale === "bn" ? "অর্ডার করুন" : "Order Now" }}
                    </router-link>
                    <button
                      type="button"
                      class="inline-flex items-center justify-center text-xs font-bold text-gray-500 hover:text-primary border border-gray-200 hover:border-primary rounded-lg px-3 py-2 transition-colors"
                    >
                      {{ locale === "bn" ? "কার্টে যোগ করুন" : "Add to Cart" }}
                    </button>
                  </div>
                </div>
              </div>
            </template>
          </div>

          <div v-else class="flex flex-col items-center justify-center py-20 bg-white border border-dashed border-gray-200 rounded-2xl">
            <SlidersHorizontal class="w-12 h-12 text-gray-200 mb-3" />
            <p class="text-gray-400 text-sm font-bold mb-2">{{ t('no_deals_found') }}</p>
            <button v-if="hasFilters" @click="clearFilters" class="mt-2 text-xs text-primary font-semibold hover:underline cursor-pointer">
              {{ locale === 'bn' ? 'সব ফিল্টার মুছুন' : 'Clear all filters' }}
            </button>
          </div>
        </div>


        <!-- ── Pagination ───────────────────────────────────────────────── -->
        <nav
          v-if="!isLoading && filteredProducts.length > 0 && lastPage > 1"
          class="flex flex-col sm:flex-row items-center justify-between gap-3 mt-8"
          aria-label="Pagination"
        >
          <p class="text-xs text-gray-500 font-semibold">
            {{ paginationText }}
          </p>
          <ul class="flex items-center gap-1">
            <li>
              <button
                @click="goToPage(currentPage - 1)"
                :disabled="!hasPrev"
                class="px-3 py-1.5 text-xs font-bold rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
              >
                {{ locale === "bn" ? "পূর্ববর্তী" : "Prev" }}
              </button>
            </li>
            <li v-for="(p, idx) in pageNumbers" :key="'p-' + idx">
              <span
                v-if="p === '...'"
                class="px-2 text-gray-400 text-xs"
              >…</span>
              <button
                v-else
                @click="goToPage(p)"
                class="min-w-[32px] px-2.5 py-1.5 text-xs font-bold rounded-lg border cursor-pointer"
                :class="
                  p === currentPage
                    ? 'bg-primary border-primary text-white'
                    : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50'
                "
              >{{ p }}</button>
            </li>
            <li>
              <button
                @click="goToPage(currentPage + 1)"
                :disabled="!hasNext"
                class="px-3 py-1.5 text-xs font-bold rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
              >
                {{ locale === "bn" ? "পরবর্তী" : "Next" }}
              </button>
            </li>
          </ul>
        </nav>
      </section>
    </div>
  </div>
</template>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.25s ease-out;
}
.slide-down-enter-from,
.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>


