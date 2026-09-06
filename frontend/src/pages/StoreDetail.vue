<script setup>
import { ref, onMounted, computed, watch } from "vue";
import { useRoute } from "vue-router";
import { ExternalLink, Tag, Scissors, Share2, Heart, ArrowLeft, Menu } from "@lucide/vue";
import { fetchStoreById } from "@/lib/api";
import { useI18n } from "@/lib/i18n";
import { toast } from "@/lib/toast";
import DealCard from "@/components/shared/DealCard.vue";
import CouponCard from "@/components/shared/CouponCard.vue";
import SkeletonLoader from "@/components/shared/SkeletonLoader.vue";
import { usePageTitle } from "@/composables/usePageTitle";
import { pageTitles } from "@/lib/pageTitles";
usePageTitle(pageTitles.StoreDetail);
const route = useRoute();
const { locale, t } = useI18n();

const store = ref(null);
const isLoading = ref(false);
const activeTab = ref("all");
const isFavorited = ref(false);

const storeId = computed(() => {
  return route.params.id ? Number(route.params.id) : 0;
});

async function loadStoreDetail() {
  if (!storeId.value) return;
  try {
    isLoading.value = true;
    store.value = await fetchStoreById(storeId.value);
  } catch (err) {
    console.error("Failed to load store detail page:", err);
  } finally {
    isLoading.value = false;
  }
}

onMounted(() => {
  loadStoreDetail();
});

watch(() => route.params.id, () => {
  loadStoreDetail();
});

function handleShare() {
  navigator.clipboard.writeText(window.location.href);
  toast({
    title: locale.value === 'bn' ? "লিংক কপি করা হয়েছে!" : "Link Copied!",
    description: locale.value === 'bn' ? "স্টোরের লিংকটি ক্লিপবোর্ডে কপি করা হয়েছে।" : "Store details link copied to clipboard.",
  });
}

function handleFavorite() {
  isFavorited.value = !isFavorited.value;
  toast({
    title: locale.value === 'bn' ? (isFavorited.value ? "পছন্দ তালিকায় যুক্ত!" : "মুছে ফেলা হয়েছে!") : (isFavorited.value ? "Favorited!" : "Removed!"),
    description: locale.value === 'bn' 
      ? (isFavorited.value ? `আপনি এখন থেকে ${store.value.name}-এর নতুন অফারের আপডেট পাবেন।` : `পছন্দ তালিকা থেকে ${store.value.name} সরানো হয়েছে।`)
      : (isFavorited.value ? `You will now receive notifications for ${store.value.name} offers.` : `Removed ${store.value.name} from favorites.`),
  });
}
</script>

<template>
  <div class="bg-gray-50/50 min-h-screen pb-12 flex-1">
    <div v-if="isLoading" class="p-4">
      <SkeletonLoader type="list" :count="4" />
    </div>

    <!-- Not Found State -->
    <div v-else-if="!store" class="container mx-auto px-4 py-20 text-center">
      <h1 class="text-2xl font-display font-bold mb-4 text-gray-800">
        {{ locale === 'bn' ? 'স্টোরটি পাওয়া যায়নি' : 'Store Not Found' }}
      </h1>
      <p class="text-gray-500 mb-8">
        {{ locale === 'bn' ? 'আপনি যে স্টোরটি খুঁজছেন তা বর্তমানে নেই অথবা সরিয়ে ফেলা হয়েছে।' : 'The store you are looking for does not exist or has been removed.' }}
      </p>
      <router-link to="/stores" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors">
        {{ locale === 'bn' ? 'সকল স্টোর দেখুন' : 'Browse All Stores' }}
      </router-link>
    </div>

    <!-- Store Profile and Offers Feed -->
    <div v-else>
      <!-- Store Header -->
      <div class="bg-white border-b border-gray-100 p-4">
        <router-link to="/stores" class="inline-flex items-center gap-1.5 text-gray-500 text-xs mb-4 hover:text-green-700">
          <ArrowLeft class="w-3.5 h-3.5" /> {{ t('back_to_stores') }}
        </router-link>
        
        <div class="flex flex-col items-center text-center gap-4">
          <div class="w-24 h-24 bg-white rounded-2xl border shadow-2xs p-3 flex items-center justify-center shrink-0">
            <img v-if="store.logoUrl" :src="store.logoUrl" :alt="store.name" class="max-w-full max-h-full object-contain" />
            <span v-else class="font-display font-bold text-4xl text-green-600">{{ store.name.charAt(0) }}</span>
          </div>
          
          <div>
            <h1 class="text-xl font-display font-bold text-gray-800">
              {{ store.name }} {{ locale === 'bn' ? 'ডিল এবং প্রোমো কোড' : 'Deals & Promo Codes' }}
            </h1>
            <p v-if="store.description" class="text-xs text-gray-500 max-w-sm mt-2 leading-relaxed">{{ store.description }}</p>
            
            <div class="flex items-center justify-center gap-2 mt-4">
              <span class="flex items-center gap-1 bg-green-50 text-green-700 px-2.5 py-1 rounded-full text-[10px] font-semibold">
                <Tag class="w-3 h-3" /> {{ store.dealCount }} {{ t('deals_count') }}
              </span>
              <span class="flex items-center gap-1 bg-amber-50 text-amber-700 px-2.5 py-1 rounded-full text-[10px] font-semibold">
                <Scissors class="w-3 h-3" /> {{ store.couponCount }} {{ t('coupons_count') }}
              </span>
            </div>
          </div>
          
          <div class="flex gap-2 w-full mt-2">
            <button
              @click="handleShare"
              class="flex-1 flex items-center justify-center gap-1 border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs py-2 rounded-lg transition-colors cursor-pointer"
            >
              <Share2 class="w-3.5 h-3.5" /> {{ t('share') }}
            </button>
            <button
              @click="handleFavorite"
              class="flex-1 flex items-center justify-center gap-1 border border-gray-300 hover:bg-rose-50 hover:text-rose-600 text-xs py-2 rounded-lg transition-colors cursor-pointer"
              :class="isFavorited ? 'text-rose-600 border-rose-200 bg-rose-50/30' : 'text-gray-700'"
            >
              <Heart class="w-3.5 h-3.5" :class="{ 'fill-rose-500': isFavorited }" />
              {{ isFavorited ? t('favorited') : t('favorite') }}
            </button>
            <a
              v-if="store.website"
              :href="store.website"
              target="_blank"
              rel="noopener noreferrer"
              class="flex-1 flex items-center justify-center gap-1 bg-green-600 hover:bg-green-700 text-white text-xs py-2 rounded-lg font-semibold transition-colors cursor-pointer"
            >
              {{ t('website') }} <ExternalLink class="w-3.5 h-3.5" />
            </a>
          </div>
        </div>
      </div>

      <!-- Offers Tabs Layout -->
      <div class="p-4">
        <!-- Tabs Header -->
        <div class="flex border-b border-gray-200 mb-6 gap-2 bg-white p-1 rounded-xl shadow-2xs">
          <button
            @click="activeTab = 'all'"
            class="flex-1 text-center py-2 text-xs font-semibold rounded-lg transition-colors cursor-pointer"
            :class="activeTab === 'all' ? 'bg-green-600 text-white shadow-sm' : 'text-gray-500 hover:text-gray-800'"
          >
            {{ t('all_offers') }}
          </button>
          <button
            @click="activeTab = 'deals'"
            class="flex-1 text-center py-2 text-xs font-semibold rounded-lg transition-colors cursor-pointer"
            :class="activeTab === 'deals' ? 'bg-green-600 text-white shadow-sm' : 'text-gray-500 hover:text-gray-800'"
          >
            {{ t('deals_count') }} ({{ store.deals?.length ?? 0 }})
          </button>
          <button
            @click="activeTab = 'coupons'"
            class="flex-1 text-center py-2 text-xs font-semibold rounded-lg transition-colors cursor-pointer"
            :class="activeTab === 'coupons' ? 'bg-green-600 text-white shadow-sm' : 'text-gray-500 hover:text-gray-800'"
          >
            {{ t('coupons_count') }} ({{ store.coupons?.length ?? 0 }})
          </button>
        </div>

        <!-- Tabs Content -->
        <div class="space-y-6">
          <!-- ALL TAB -->
          <template v-if="activeTab === 'all'">
            <div v-if="store.coupons?.length > 0" class="space-y-4">
              <h3 class="text-base font-semibold text-gray-800 flex items-center gap-1.5 font-display">
                <Scissors class="w-4 h-4 text-green-600" /> {{ t('active_codes') }}
              </h3>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <CouponCard v-for="coupon in store.coupons" :key="coupon.id" :coupon="coupon" />
              </div>
            </div>
            
            <div v-if="store.deals?.length > 0" class="space-y-4 pt-4">
              <h3 class="text-base font-semibold text-gray-800 flex items-center gap-1.5 font-display">
                <Tag class="w-4 h-4 text-green-600" /> {{ locale === 'bn' ? 'সর্বশেষ ডিল সমূহ' : 'Latest Deals' }}
              </h3>
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <DealCard v-for="deal in store.deals" :key="deal.id" :deal="deal" />
              </div>
            </div>

            <div v-if="(!store.deals || store.deals.length === 0) && (!store.coupons || store.coupons.length === 0)" class="text-center py-16 bg-white rounded-xl border border-dashed">
              <p class="text-gray-400 text-sm">{{ t('no_offers') }}</p>
            </div>
          </template>

          <!-- DEALS TAB -->
          <template v-if="activeTab === 'deals'">
            <div v-if="store.deals?.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
              <DealCard v-for="deal in store.deals" :key="deal.id" :deal="deal" />
            </div>
            <div v-else class="text-center py-16 bg-white rounded-xl border border-dashed">
              <p class="text-gray-400 text-sm">{{ t('no_deals') }}</p>
            </div>
          </template>

          <!-- COUPONS TAB -->
          <template v-if="activeTab === 'coupons'">
            <div v-if="store.coupons?.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
              <CouponCard v-for="coupon in store.coupons" :key="coupon.id" :coupon="coupon" />
            </div>
            <div v-else class="text-center py-16 bg-white rounded-xl border border-dashed">
              <p class="text-gray-400 text-sm">{{ t('no_coupons') }}</p>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

