<script setup>
import { ref, onMounted, computed, watch } from "vue";
import { Filter, Store as StoreIcon, Grid, Scissors } from "@lucide/vue";
import { fetchCoupons, fetchCategories, fetchStores } from "@/lib/api";
import { useI18n } from "@/lib/i18n";
import CouponCard from "@/components/shared/CouponCard.vue";
import SkeletonLoader from "@/components/shared/SkeletonLoader.vue";
import { usePageTitle } from "@/composables/usePageTitle";
import { pageTitles } from "@/lib/pageTitles";
usePageTitle(pageTitles.Coupons);
const selectedCategory = ref(null);
const selectedStore = ref(null);
const filterOpen = ref(false);

const coupons = ref([]);
const categories = ref([]);
const stores = ref([]);
const isLoading = ref(false);
const { locale, t } = useI18n();

async function loadCoupons() {
  try {
    isLoading.value = true;
    const params = {};
    if (selectedCategory.value) params.categoryId = selectedCategory.value;
    if (selectedStore.value) params.storeId = selectedStore.value;
    coupons.value = await fetchCoupons(params);
  } catch (err) {
    console.error("Failed to load coupons list:", err);
  } finally {
    isLoading.value = false;
  }
}

async function loadFilterOptions() {
  try {
    categories.value = await fetchCategories();
    stores.value = await fetchStores();
  } catch (err) {
    console.error("Failed to load coupon filters:", err);
  }
}

onMounted(() => {
  loadCoupons();
  loadFilterOptions();
});

watch([selectedCategory, selectedStore], () => {
  loadCoupons();
});

function clearFilters() {
  selectedCategory.value = null;
  selectedStore.value = null;
  filterOpen.value = false;
}

const hasFilters = computed(() => {
  return selectedCategory.value !== null || selectedStore.value !== null;
});
</script>

<template>
  <div class="bg-gray-50/50 min-h-screen pb-12 flex-1">
    <!-- Header Banner -->
    <div class="bg-gray-900 text-white py-8 px-4 text-center">
      <Scissors class="w-10 h-10 text-green-500 mx-auto mb-2" />
      <h1 class="text-2xl font-display font-bold mb-1 text-white">
        {{ locale === 'bn' ? 'প্রোমো কোড এবং কুপন' : 'Promo Codes & Coupons' }}
      </h1>
      <p class="text-gray-400 text-[11px] max-w-xs mx-auto leading-relaxed">
        {{ locale === 'bn' ? 'বাংলাদেশের সেরা ব্র্যান্ডগুলোর ভেরিফাইড ডিসকাউন্ট কোড দিয়ে পেমেন্টের সময় বাড়তি সেভ করুন।' : 'Save extra on checkout with verified discount codes from top brands in Bangladesh.' }}
      </p>
    </div>

    <!-- Filters Bar -->
    <div class="sticky top-14 z-30 bg-white border-b border-gray-200 shadow-2xs">
      <div class="flex items-center justify-between px-4 py-2 gap-4">
        <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider">
          {{ coupons.length }} {{ locale === 'bn' ? 'টি ভেরিফাইড কোড' : 'verified codes' }}
        </span>
        
        <button
          @click="filterOpen = !filterOpen"
          class="flex items-center gap-1.5 border rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors cursor-pointer"
          :class="filterOpen || hasFilters
            ? 'bg-green-600 text-white border-green-600'
            : 'border-gray-300 text-gray-700 bg-white'"
        >
          <Filter class="h-3.5 w-3.5" /> {{ t('filter') }}
        </button>
      </div>

      <!-- Filters Drawer Expandable -->
      <transition name="slide-down">
        <div v-if="filterOpen" class="px-4 pb-4 border-t border-gray-100 bg-white space-y-4 max-h-[70vh] overflow-y-auto">
          <!-- Clear Filters -->
          <div class="flex items-center justify-between pt-2">
            <span class="text-xs font-bold text-gray-700">
              {{ locale === 'bn' ? 'কুপন ফিল্টার করুন' : 'Filter Coupons' }}
            </span>
            <button
              v-if="hasFilters"
              @click="clearFilters"
              class="text-xs text-red-500 font-semibold hover:underline cursor-pointer"
            >
              {{ t('clear_all') }}
            </button>
          </div>

          <!-- Categories Filter -->
          <div>
            <h4 class="font-semibold mb-2 flex items-center gap-1 text-[10px] uppercase tracking-wider text-gray-400">
              <Grid class="w-3.5 h-3.5" /> {{ t('categories') }}
            </h4>
            <div class="flex flex-wrap gap-1.5">
              <button
                @click="selectedCategory = null"
                class="px-2.5 py-1 rounded-full text-[10px] font-medium border transition-colors cursor-pointer"
                :class="selectedCategory === null
                  ? 'bg-green-600 text-white border-green-600'
                  : 'border-gray-200 text-gray-600 bg-white'"
              >
                {{ t('all') }}
              </button>
              <button
                v-for="category in categories"
                :key="category.id"
                @click="selectedCategory = selectedCategory === category.id ? null : category.id"
                class="px-2.5 py-1 rounded-full text-[10px] font-medium border transition-colors cursor-pointer"
                :class="selectedCategory === category.id
                  ? 'bg-green-600 text-white border-green-600'
                  : 'border-gray-200 text-gray-600 bg-white'"
              >
                {{ category.name }}
              </button>
            </div>
          </div>

          <!-- Stores Filter -->
          <div class="border-t pt-3">
            <h4 class="font-semibold mb-2 flex items-center gap-1 text-[10px] uppercase tracking-wider text-gray-400">
              <StoreIcon class="w-3.5 h-3.5" /> {{ t('stores') }}
            </h4>
            <div class="flex flex-wrap gap-1.5">
              <button
                @click="selectedStore = null"
                class="px-2.5 py-1 rounded-full text-[10px] font-medium border transition-colors cursor-pointer"
                :class="selectedStore === null
                  ? 'bg-green-600 text-white border-green-600'
                  : 'border-gray-200 text-gray-600 bg-white'"
              >
                {{ t('all') }}
              </button>
              <button
                v-for="store in stores"
                :key="store.id"
                @click="selectedStore = selectedStore === store.id ? null : store.id"
                class="px-2.5 py-1 rounded-full text-[10px] font-medium border transition-colors cursor-pointer"
                :class="selectedStore === store.id
                  ? 'bg-green-600 text-white border-green-600'
                  : 'border-gray-200 text-gray-600 bg-white'"
              >
                {{ store.name }}
              </button>
            </div>
          </div>
        </div>
      </transition>
    </div>

    <!-- Coupons Feed -->
    <div class="p-4">
      <div v-if="isLoading" class="mt-2">
        <SkeletonLoader type="list" :count="6" />
      </div>

      <div v-else-if="coupons.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <CouponCard v-for="coupon in coupons" :key="coupon.id" :coupon="coupon" />
      </div>

      <div v-else class="text-center py-16 bg-white rounded-xl border border-dashed border-gray-200">
        <Scissors class="h-10 w-10 text-gray-400 mx-auto mb-3 opacity-50" />
        <h3 class="text-base font-semibold mb-1 text-gray-800">{{ t('no_coupons_found') }}</h3>
        <p class="text-xs text-gray-500 mb-4">{{ t('no_coupons_desc') }}</p>
        <button v-if="hasFilters" @click="clearFilters" class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors cursor-pointer">
          {{ locale === 'bn' ? 'ফিল্টার মুছুন' : 'Clear Filters' }}
        </button>
      </div>
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
  transform: translateY(-10px);
}
</style>

