<script setup>
import { ref, computed, onMounted } from "vue";
import { Sparkles, Search, ChevronRight } from "@lucide/vue";
import { fetchBrands } from "@/lib/api";
import { useI18n } from "@/lib/i18n";
import SkeletonLoader from "@/components/shared/SkeletonLoader.vue";
import { usePageTitle } from "@/composables/usePageTitle";
import { pageTitles } from "@/lib/pageTitles";
usePageTitle(pageTitles.Brands);
const brands = ref([]);
const isLoading = ref(false);
const searchQuery = ref("");
const { locale, t } = useI18n();

onMounted(async () => {
  try {
    isLoading.value = true;
    brands.value = await fetchBrands();
  } catch (err) {
    console.error("Failed to load brands page:", err);
  } finally {
    isLoading.value = false;
  }
});

const filteredBrands = computed(() => {
  if (!searchQuery.value.trim()) return brands.value;
  const q = searchQuery.value.toLowerCase().trim();
  return brands.value.filter(b => b.name.toLowerCase().includes(q));
});
</script>

<template>
  <div class="px-4 py-8 bg-[#fdfdfd] flex-1">
    <!-- Header Hero Section -->
    <div class="text-center max-w-3xl mx-auto mb-10">
      <div class="inline-flex items-center justify-center p-3 bg-green-50 rounded-2xl mb-4 border border-green-100">
        <Sparkles class="w-8 h-8 text-green-600 animate-pulse" />
      </div>
      <h1 class="text-3xl font-display font-bold mb-2 text-gray-800">
        {{ locale === 'bn' ? 'সকল ব্র্যান্ড সমূহ' : 'All Brands' }}
      </h1>
      <p class="text-gray-500 text-sm leading-relaxed">
        {{ locale === 'bn' ? 'আপনার প্রিয় ব্র্যান্ডগুলোর সেরা ডিল ও ডিসকাউন্ট কুপন কোডগুলো এক জায়গায় খুঁজুন।' : 'Find the best discount offers, deals, and coupon codes from your favorite brands.' }}
      </p>
      
      <!-- Local Search Bar -->
      <div class="max-w-md mx-auto mt-6 relative">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
        <input
          type="text"
          v-model="searchQuery"
          :placeholder="locale === 'bn' ? 'ব্র্যান্ড খুঁজুন...' : 'Search brands...'"
          class="w-full h-11 pl-10 pr-4 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none text-sm bg-white shadow-xs transition-all"
        />
      </div>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="isLoading" class="mt-4">
      <SkeletonLoader type="categories" :count="8" />
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredBrands.length === 0" class="text-center py-20 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
      <Sparkles class="h-12 w-12 text-gray-300 mx-auto mb-4 opacity-50" />
      <h3 class="text-lg font-display font-semibold mb-1 text-gray-800">
        {{ locale === 'bn' ? 'কোনো ব্র্যান্ড পাওয়া যায়নি' : 'No brands found' }}
      </h3>
      <p class="text-gray-500 text-xs">
        {{ locale === 'bn' ? 'ভিন্ন কোনো নাম লিখে আবার চেষ্টা করুন।' : 'Try checking your spelling or search for another brand.' }}
      </p>
    </div>

    <!-- Brands Grid -->
    <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
      <router-link
        v-for="brand in filteredBrands"
        :key="brand.id"
        :to="`/products-list?brand=${encodeURIComponent(brand.name)}`"
        class="group border border-gray-100 rounded-2xl p-5 hover:border-green-300 hover:shadow-md transition-all duration-300 flex flex-col items-center bg-white text-center cursor-pointer relative overflow-hidden"
      >
        <!-- Brand Logo -->
        <div class="w-20 h-20 mb-4 flex items-center justify-center bg-gray-50/60 p-2.5 rounded-xl border border-gray-50 group-hover:bg-white transition-all duration-300">
          <img
            v-if="brand.logo"
            :src="brand.logo"
            :alt="brand.name"
            class="max-w-full max-h-full object-contain filter grayscale group-hover:grayscale-0 transition-all duration-300"
          />
          <div v-else class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center text-green-600 font-display font-bold text-2xl group-hover:bg-green-600 group-hover:text-white transition-all">
            {{ brand.name ? brand.name.charAt(0).toUpperCase() : 'B' }}
          </div>
        </div>

        <h3 class="font-semibold text-sm group-hover:text-green-600 transition-colors text-gray-800 line-clamp-1 mb-2">{{ brand.name }}</h3>
        
        <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-green-600 mt-auto bg-green-50 group-hover:bg-green-600 group-hover:text-white px-2.5 py-1 rounded-full transition-all">
          {{ locale === 'bn' ? 'পণ্য দেখুন' : 'View Products' }}
          <ChevronRight class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" />
        </span>
      </router-link>
    </div>
  </div>
</template>

