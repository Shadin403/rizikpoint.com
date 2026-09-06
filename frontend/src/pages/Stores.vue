<script setup>
import { ref, onMounted } from "vue";
import { Store as StoreIcon, Tag, Scissors } from "@lucide/vue";
import { fetchStores } from "@/lib/api";
import { useI18n } from "@/lib/i18n";
import SkeletonLoader from "@/components/shared/SkeletonLoader.vue";
import { usePageTitle } from "@/composables/usePageTitle";
import { pageTitles } from "@/lib/pageTitles";
usePageTitle(pageTitles.Stores);
const stores = ref([]);
const isLoading = ref(false);
const { locale, t } = useI18n();

onMounted(async () => {
  try {
    isLoading.value = true;
    stores.value = await fetchStores();
  } catch (err) {
    console.error("Failed to load stores page:", err);
  } finally {
    isLoading.value = false;
  }
});
</script>

<template>
  <div class="px-4 py-8 bg-[#fdfdfd] flex-1">
    <div class="text-center max-w-3xl mx-auto mb-8">
      <div class="inline-flex items-center justify-center p-3 bg-green-50 rounded-2xl mb-4 border border-green-100">
        <StoreIcon class="w-8 h-8 text-green-600" />
      </div>
      <h1 class="text-3xl font-display font-bold mb-2 text-gray-800">
        {{ locale === 'bn' ? 'শীর্ষ স্টোর সমূহ' : 'Browse Top Stores' }}
      </h1>
      <p class="text-gray-500 text-sm leading-relaxed">
        {{ locale === 'bn' ? 'বাংলাদেশের আপনার প্রিয় অনলাইন শপগুলোর জন্য সেরা ডিল এবং কুপন কোডগুলো খুঁজে নিন।' : 'Find the best deals and working promo codes for your favorite online shops in Bangladesh.' }}
      </p>
    </div>

    <div v-if="isLoading" class="mt-4">
      <SkeletonLoader type="categories" :count="6" />
    </div>

    <!-- Empty State -->
    <div v-else-if="!stores || stores.length === 0" class="text-center py-20 bg-gray-50 rounded-xl border border-dashed border-gray-200">
      <StoreIcon class="h-12 w-12 text-gray-400 mx-auto mb-4 opacity-50" />
      <h3 class="text-xl font-display font-semibold mb-2 text-gray-800">
        {{ locale === 'bn' ? 'কোনো স্টোর পাওয়া যায়নি' : 'No stores available' }}
      </h3>
      <p class="text-gray-500 text-sm">
        {{ locale === 'bn' ? 'নতুন যুক্ত হওয়া স্টোর দেখতে পরে আবার চেক করুন।' : 'Check back later for newly added stores.' }}
      </p>
    </div>

    <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
      <router-link
        v-for="store in stores"
        :key="store.id"
        :to="`/stores/${store.id}`"
        class="h-full border border-gray-100 rounded-xl hover:border-green-300 hover:shadow-sm transition-all duration-300 group flex flex-col items-center p-4 bg-white text-center cursor-pointer"
      >
        <div class="w-20 h-20 mb-4 flex items-center justify-center bg-gray-50/50 p-2 rounded-lg">
          <img
            v-if="store.logoUrl"
            :src="store.logoUrl"
            :alt="store.name"
            class="max-w-full max-h-full object-contain filter grayscale group-hover:grayscale-0 transition-all duration-300"
          />
          <div v-else class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center text-green-600 font-display font-bold text-2xl">
            {{ store.name.charAt(0) }}
          </div>
        </div>
        <h3 class="font-semibold text-sm mb-3 group-hover:text-green-600 transition-colors text-gray-800">{{ store.name }}</h3>
        
        <div class="flex flex-col gap-1.5 w-full mt-auto">
          <div class="flex items-center justify-between text-[10px] text-gray-500 bg-gray-50 rounded-md px-2.5 py-1">
            <span class="flex items-center gap-1"><Tag class="w-3 h-3 text-green-600" /> {{ t('deals_count') }}</span>
            <span class="font-semibold text-gray-800">{{ store.dealCount }}</span>
          </div>
          <div class="flex items-center justify-between text-[10px] text-gray-500 bg-gray-50 rounded-md px-2.5 py-1">
            <span class="flex items-center gap-1"><Scissors class="w-3 h-3 text-green-600" /> {{ t('coupons_count') }}</span>
            <span class="font-semibold text-gray-800">{{ store.couponCount }}</span>
          </div>
        </div>
      </router-link>
    </div>
  </div>
</template>

