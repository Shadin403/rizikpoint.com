<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { Clock, Eye, ArrowRight } from "@lucide/vue";
import { useI18n } from "@/lib/i18n";
import { useBusinessSettings } from "@/composables/useBusinessSettings";
import { dummyProductImage } from "@/lib/dummyProductImages";

const props = defineProps({
  deal: { type: Object, required: true },
  isFlash: { type: Boolean, default: false }
});

const { locale, t } = useI18n();
const { get: getBusinessSetting } = useBusinessSettings();
const imageFailed = ref(false);
const dummyImagesEnabled = computed(() => {
  const value = getBusinessSetting('dummy_product_images');
  return ['1', 'true', 'on', 'yes'].includes(String(value ?? '').toLowerCase());
});
const displayImage = computed(() => {
  if (props.deal.imageUrl && !imageFailed.value) return props.deal.imageUrl;
  return dummyImagesEnabled.value ? dummyProductImage(props.deal) : null;
});

const timeLeft = ref("");
let intervalId = null;

function updateCountdown() {
  if (!props.deal.expiresAt) return;
  const end = new Date(props.deal.expiresAt).getTime();
  const now = new Date().getTime();
  const distance = end - now;

  if (distance < 0) {
    timeLeft.value = t('expired');
    clearInterval(intervalId);
    return;
  }

  const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  const seconds = Math.floor((distance % (1000 * 60)) / 1000);

  timeLeft.value = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
}

onMounted(() => {
  if (props.isFlash && props.deal.expiresAt) {
    updateCountdown();
    intervalId = setInterval(updateCountdown, 1000);
  }
});

onUnmounted(() => {
  if (intervalId) clearInterval(intervalId);
});
</script>

<template>
  <div class="h-full border border-gray-200 rounded-lg overflow-hidden flex flex-col bg-white group hover:shadow-lg transition-all duration-300">
    <div class="relative aspect-[4/3] bg-gray-100 overflow-hidden">
      <img
        v-if="displayImage"
        :src="displayImage"
        :alt="deal.title"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
        @error="imageFailed = true"
      />
      <div v-else class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400">
        <span class="font-display font-bold text-xl opacity-50">{{ deal.storeName }}</span>
      </div>

      <!-- Badges -->
      <div class="absolute top-3 left-3 flex flex-col gap-2">
        <span
          v-if="deal.discountPercent > 0"
          class="bg-red-600 text-white text-xs font-semibold px-2 py-1 rounded shadow-sm"
        >
          {{ deal.discountPercent }}% {{ t('discount') }}
        </span>
        <span
          v-if="deal.featured"
          class="bg-primary text-white text-xs font-semibold px-2 py-1 rounded shadow-sm"
        >
          {{ t('featured') }}
        </span>
      </div>

      <!-- Countdown -->
      <div v-if="isFlash && deal.expiresAt" class="absolute bottom-3 left-3 right-3 flex justify-center">
        <div class="backdrop-blur-md bg-white/90 p-1.5 rounded-lg shadow-sm border border-white/20">
          <div class="flex items-center gap-1.5 text-xs font-semibold bg-red-50 text-red-600 px-2 py-1 rounded-md">
            <Clock class="h-3.5 w-3.5" />
            {{ timeLeft }}
          </div>
        </div>
      </div>
    </div>

    <div class="p-4 flex flex-col flex-1">
      <div class="flex items-center gap-2 mb-2">
        <img
          v-if="deal.storeLogoUrl"
          :src="deal.storeLogoUrl"
          :alt="deal.storeName"
          class="w-5 h-5 rounded-full object-contain border border-gray-200"
        />
        <div v-else class="w-5 h-5 rounded-full bg-primary/10 flex items-center justify-center text-[10px] font-bold text-primary">
          {{ deal.storeName ? deal.storeName.charAt(0) : 'S' }}
        </div>
        <span class="text-xs font-medium text-gray-500">{{ deal.storeName }}</span>
        <span class="text-xs text-gray-300">•</span>
        <span class="text-xs font-medium text-gray-500">{{ deal.categoryName }}</span>
      </div>

      <router-link :to="`/deals/${deal.slug || deal.id}`" class="block flex-1 hover:text-primary transition-colors">
        <h3 class="font-display font-semibold text-lg line-clamp-2 leading-tight mb-2 text-gray-800">
          {{ deal.title }}
        </h3>
      </router-link>

      <div class="flex items-end gap-2 mb-4 mt-auto">
        <span v-if="deal.discountedPrice != null" class="text-xl font-bold text-gray-900">৳{{ deal.discountedPrice.toLocaleString() }}</span>
        <span v-if="deal.originalPrice != null" class="text-sm font-medium text-gray-400 line-through mb-1">
          ৳{{ deal.originalPrice.toLocaleString() }}
        </span>
        <span v-if="deal.discountedPrice == null && deal.originalPrice == null" class="text-xl font-bold text-gray-900">Free</span>
      </div>

      <div class="flex items-center justify-between pt-4 border-t border-gray-100">
        <div class="flex items-center gap-1.5 text-xs text-gray-500">
          <Eye class="h-3.5 w-3.5 text-gray-400" />
          <span>{{ deal.usedCount }} {{ locale === 'bn' ? 'জন ভিউ করেছে' : 'views' }}</span>
        </div>
        <router-link
          :to="`/deals/${deal.slug || deal.id}`"
          class="flex items-center gap-1 bg-primary hover:bg-primary/90 text-white text-xs px-3 py-1.5 rounded-full font-semibold shadow-sm transition-colors cursor-pointer"
        >
          {{ locale === 'bn' ? 'পণ্য দেখুন' : 'View Product' }} <ArrowRight class="h-3.5 w-3.5 ml-1" />
        </router-link>
      </div>
    </div>
  </div>
</template>
