<script setup>
import { ref, onMounted, computed, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import {
  Search, Tag, Store as StoreIcon, Grid, Zap, RotateCcw, Sparkles,
  ChevronLeft, ChevronRight,
} from "@lucide/vue";
import { fetchFlashDeals, fetchDealsPaged, fetchFilterCategories, fetchStores } from "@/lib/api";
import { useI18n } from "@/lib/i18n";
import DealCard from "@/components/shared/DealCard.vue";
import SkeletonLoader from "@/components/shared/SkeletonLoader.vue";
import Pagination from "@/components/shared/Pagination.vue";
import { usePageTitle } from "@/composables/usePageTitle";
import { pageTitles } from "@/lib/pageTitles";
usePageTitle(pageTitles.Deals);
const route  = useRoute();
const router = useRouter();
const { locale, t } = useI18n();

const searchInput      = ref(route.query.search || "");
const selectedCategory = ref(route.query.category ? Number(route.query.category) : null);
const selectedStore    = ref(route.query.store ? Number(route.query.store) : null);
const currentPage      = ref(route.query.page ? Number(route.query.page) : 1);
const pageSize         = ref(20);

const filterOpen       = ref(false);
const activeTab        = ref("all"); // "flash" | "all"

const flashDeals  = ref([]);
const allDeals    = ref([]);
const pagination  = ref({ meta: null, links: null });
const categories  = ref([]);
const stores      = ref([]);

const isLoadingFlash = ref(false);
const isLoadingAll   = ref(false);
const isLoadingMeta  = ref(false);

// ── Load flash deals ──────────────────────────────────────────────────────────
async function loadFlashDeals() {
  isLoadingFlash.value = true;
  try {
    flashDeals.value = await fetchFlashDeals();
  } catch (err) {
    console.error("Failed to load flash deals:", err);
  } finally {
    isLoadingFlash.value = false;
  }
}

// ── Load paged deals ─────────────────────────────────────────────────────────
async function loadDeals() {
  isLoadingAll.value = true;
  try {
    const params = {
      search:     route.query.search || undefined,
      categoryId: selectedCategory.value || undefined,
      storeId:    selectedStore.value    || undefined,
      page:       currentPage.value,
      limit:      pageSize.value,
    };
    const res = await fetchDealsPaged(params);
    allDeals.value = res.deals;
    pagination.value = { meta: res.meta, links: res.links };
  } catch (err) {
    console.error("Failed to load deals:", err);
  } finally {
    isLoadingAll.value = false;
  }
}

// ── Load filter options ──────────────────────────────────────────────────────
async function loadFilterOptions() {
  isLoadingMeta.value = true;
  try {
    const [cats, stos] = await Promise.all([fetchFilterCategories(), fetchStores()]);
    categories.value = cats;
    stores.value     = stos;
  } catch (err) {
    console.error("Failed to load filter options:", err);
  } finally {
    isLoadingMeta.value = false;
  }
}

function pushQuery() {
  const query = {};
  if (route.query.search)     query.search   = route.query.search;
  if (selectedCategory.value) query.category = selectedCategory.value;
  if (selectedStore.value)    query.store    = selectedStore.value;
  if (currentPage.value > 1)  query.page     = currentPage.value;
  router.push({ path: "/deals", query });
}

onMounted(() => {
  loadFlashDeals();
  loadDeals();
  loadFilterOptions();
});

watch(() => route.query.search, (val) => {
  searchInput.value = val || "";
  currentPage.value = 1;
  loadDeals();
});

watch([selectedCategory, selectedStore], () => {
  currentPage.value = 1;
  pushQuery();
  loadDeals();
});

watch(currentPage, () => {
  pushQuery();
  loadDeals();
  if (typeof window !== "undefined") {
    window.scrollTo({ top: 0, behavior: "smooth" });
  }
});

function handleSearchSubmit() {
  const term = searchInput.value.trim();
  currentPage.value = 1;
  const query = {};
  if (term)                       query.search   = term;
  if (selectedCategory.value)     query.category = selectedCategory.value;
  if (selectedStore.value)        query.store    = selectedStore.value;
  router.push({ path: "/deals", query });
  loadDeals();
}

function clearFilters() {
  searchInput.value      = "";
  selectedCategory.value = null;
  selectedStore.value    = null;
  currentPage.value      = 1;
  router.push({ path: "/deals" });
  loadDeals();
}

function onPageChange(p) {
  currentPage.value = p;
}

const hasFilters = computed(() =>
  searchInput.value !== "" || selectedCategory.value !== null || selectedStore.value !== null
);

const displayDeals = computed(() =>
  activeTab.value === "flash" ? flashDeals.value : allDeals.value
);

const isLoading = computed(() =>
  activeTab.value === "flash" ? isLoadingFlash.value : isLoadingAll.value
);

const lastPage = computed(() => pagination.value?.meta?.last_page ?? 1);
const total    = computed(() => pagination.value?.meta?.total ?? 0);
const resultLabel = computed(() => {
  if (isLoading.value) return locale.value === "bn" ? "লোড হচ্ছে..." : "Loading...";
  if (!displayDeals.value.length) return locale.value === "bn" ? "কোনো ফলাফল নেই" : "No results";
  return locale.value === "bn"
    ? `${total.value} টি পণ্য পাওয়া গেছে`
    : `${total.value} products found`;
});
</script>

<template>
  <div class="px-4 py-8 bg-[#fdfdfd] flex-1">

    <!-- Page Header -->
    <div class="max-w-7xl mx-auto mb-6 flex flex-col gap-1">
      <div class="flex items-center gap-2 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
        <Zap class="w-3.5 h-3.5 text-emerald-500" />
        {{ locale === 'bn' ? 'সকল ডিল' : 'All Deals' }}
      </div>
      <h1 class="text-2xl md:text-3xl font-display font-extrabold text-gray-900">
        {{ route.query.search
            ? (locale === 'bn' ? `“${route.query.search}” এর ফলাফল` : `Results for “${route.query.search}”`)
            : (locale === 'bn' ? 'সেরা ডিল ও অফার' : 'Best Deals & Offers') }}
      </h1>
      <p class="text-sm text-gray-500">
        {{ locale === 'bn'
            ? 'আপনার পছন্দের দোকান ও ক্যাটাগরি অনুযায়ী সেরা অফার খুঁজুন।'
            : 'Discover the best offers tailored by your favorite stores and categories.' }}
      </p>
    </div>

    <!-- Search + Quick Filters Bar -->
    <div class="max-w-7xl mx-auto bg-white border border-gray-200 rounded-2xl shadow-xs p-3 md:p-4 mb-6">
      <form @submit.prevent="handleSearchSubmit" class="flex items-center gap-2">
        <div class="relative flex-1">
          <Search class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
          <input
            type="search"
            v-model="searchInput"
            :placeholder="t('search_placeholder')"
            class="pl-10 pr-4 py-2.5 w-full border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-colors"
          />
        </div>
        <button
          type="submit"
          class="hidden sm:inline-flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-colors shadow-sm cursor-pointer"
        >
          <Search class="w-3.5 h-3.5" /> {{ locale === 'bn' ? 'খুঁজুন' : 'Search' }}
        </button>
        <button
          type="button"
          @click="filterOpen = !filterOpen"
          class="inline-flex items-center gap-1.5 border border-gray-200 bg-white hover:border-green-500 text-gray-700 text-xs font-bold px-3 py-2.5 rounded-xl transition-colors cursor-pointer"
        >
          <Grid class="w-3.5 h-3.5" />
          {{ locale === 'bn' ? 'ফিল্টার' : 'Filters' }}
        </button>
        <button
          v-if="hasFilters"
          type="button"
          @click="clearFilters"
          class="inline-flex items-center gap-1.5 text-gray-500 hover:text-red-600 text-xs font-bold px-2 py-2.5 rounded-xl transition-colors cursor-pointer"
        >
          <RotateCcw class="w-3.5 h-3.5" /> {{ locale === 'bn' ? 'রিসেট' : 'Reset' }}
        </button>
      </form>

      <transition name="slide-down">
        <div v-if="filterOpen" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 pt-4 border-t border-gray-100">
          <!-- Categories Filter Column -->
          <div class="space-y-2">
            <h4 class="font-bold flex items-center gap-1 text-[10px] uppercase tracking-wider text-gray-400">
              <Grid class="w-3.5 h-3.5" /> {{ t('categories') }}
            </h4>
            <div class="flex flex-wrap gap-1.5 max-h-32 overflow-y-auto pr-1">
              <button @click="selectedCategory = null"
                class="px-3 py-1.5 rounded-lg text-[10px] font-bold border transition-all cursor-pointer"
                :class="selectedCategory === null ? 'bg-green-600 text-white border-green-600 shadow-xs' : 'border-gray-200 text-gray-600 bg-white hover:border-green-300'">
                {{ t('all') }}
              </button>
              <button
                v-for="cat in categories" :key="cat.id"
                @click="selectedCategory = selectedCategory === cat.id ? null : cat.id"
                class="px-3 py-1.5 rounded-lg text-[10px] font-bold border transition-all cursor-pointer"
                :class="selectedCategory === cat.id ? 'bg-green-600 text-white border-green-600 shadow-xs' : 'border-gray-200 text-gray-600 bg-white hover:border-green-300'">
                {{ cat.name }}
              </button>
            </div>
          </div>

          <!-- Stores Filter Column -->
          <div class="space-y-2">
            <h4 class="font-bold flex items-center gap-1 text-[10px] uppercase tracking-wider text-gray-400">
              <StoreIcon class="w-3.5 h-3.5" /> {{ t('stores') }}
            </h4>
            <div class="flex flex-wrap gap-1.5 max-h-32 overflow-y-auto pr-1">
              <button @click="selectedStore = null"
                class="px-3 py-1.5 rounded-lg text-[10px] font-bold border transition-all cursor-pointer"
                :class="selectedStore === null ? 'bg-green-600 text-white border-green-600 shadow-xs' : 'border-gray-200 text-gray-600 bg-white hover:border-green-300'">
                {{ t('all') }}
              </button>
              <button
                v-for="store in stores" :key="store.id"
                @click="selectedStore = selectedStore === store.id ? null : store.id"
                class="px-3 py-1.5 rounded-lg text-[10px] font-bold border transition-all cursor-pointer"
                :class="selectedStore === store.id ? 'bg-green-600 text-white border-green-600 shadow-xs' : 'border-gray-200 text-gray-600 bg-white hover:border-green-300'">
                {{ store.name }}
              </button>
            </div>
          </div>
        </div>
      </transition>
    </div>

    <!-- Result meta row -->
    <div class="max-w-7xl mx-auto flex items-center justify-between mb-4">
      <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">
        {{ resultLabel }}
      </p>
      <div class="hidden sm:flex items-center gap-1 text-[11px] font-bold text-gray-400">
        {{ locale === 'bn' ? 'প্রতি পৃষ্ঠা' : 'Per page' }}: {{ pageSize }}
      </div>
    </div>

    <!-- Skeleton Loaders -->
    <div v-if="isLoading" class="max-w-7xl mx-auto">
      <SkeletonLoader type="list" :count="8" />
    </div>

    <!-- Deals Feed Grid: 2 cols on mobile, 3 on tablet, 4 on xl -->
    <div
      v-else-if="displayDeals.length > 0"
      class="max-w-7xl mx-auto grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-5"
    >
      <DealCard v-for="deal in displayDeals" :key="deal.id" :deal="deal" />
    </div>

    <!-- Empty State -->
    <div
      v-else
      class="max-w-xl mx-auto text-center py-16 bg-white rounded-3xl border border-dashed border-gray-200"
    >
      <Sparkles class="h-12 w-12 text-gray-300 mx-auto mb-4 opacity-50 animate-pulse" />
      <h3 class="text-xl font-display font-semibold mb-2 text-gray-800">{{ t('no_deals_found') }}</h3>
      <p class="text-gray-500 text-sm max-w-xs mx-auto mb-6">{{ t('no_deals_desc') }}</p>
      <button v-if="hasFilters" @click="clearFilters"
        class="bg-green-600 hover:bg-green-700 text-white text-xs font-extrabold px-5 py-2.5 rounded-xl transition-all shadow-md hover:shadow-lg cursor-pointer">
        {{ locale === 'bn' ? 'সব ফিল্টার মুছুন' : 'Clear All Filters' }}
      </button>
    </div>

    <!-- Pagination -->
    <div v-if="!isLoading && displayDeals.length > 0 && activeTab === 'all'" class="max-w-7xl mx-auto">
      <Pagination
        :current-page="currentPage"
        :last-page="lastPage"
        :total="total"
        :per-page="pageSize"
        @change="onPageChange"
      />
    </div>
  </div>
</template>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-down-enter-from,
.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-12px);
}
</style>

