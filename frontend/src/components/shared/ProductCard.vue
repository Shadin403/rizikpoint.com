<script setup>
import { ref, computed } from "vue";
import { useCart } from "@/store/cart";
import { useI18n } from "@/lib/i18n";
import { ShoppingBasket, ShoppingBag, Star, StarHalf, Check } from "@lucide/vue";
import { animateFlyToCart } from "@/lib/cart-fly";

const props = defineProps({
  deal: { type: Object, required: true }
});

// Resolve image — supports full URL or relative uploads path from backend
// Laravel serves at: http://127.0.0.1:8000/uploads/all/xxx.webp (no /storage/ prefix)
const resolvedImage = computed(() => {
  const url = props.deal?.imageUrl;
  if (!url) return null;
  if (url.startsWith('http://') || url.startsWith('https://')) return url;
  const origin = import.meta.env.VITE_BACKEND_ORIGIN || 'http://127.0.0.1:8000';
  const clean  = url.startsWith('/') ? url.slice(1) : url;
  return `${origin}/${clean}`;
});

// ── Review / rating ──────────────────────────────────────────────────────────
// We only trust the API. If `rating` is 0 / null / undefined, there are no real
// reviews — we render NOTHING (no stars, no count) instead of faking data.
// `numOfSale` / `usedCount` are SALES counts, not review counts, so we ignore
// them for the rating display. A separate `reviews_count` (or aliases) from
// the backend is the only signal we use to display the (N) badge.
const rawRating = computed(() => {
  const r = parseFloat(props.deal?.rating);
  return Number.isFinite(r) && r > 0 ? r : null;
});
const hasRating = computed(() => rawRating.value !== null);
const fullStars  = computed(() => hasRating.value ? Math.floor(rawRating.value) : 0);
const hasHalf    = computed(() => hasRating.value && rawRating.value - fullStars.value >= 0.5);
const emptyStars = computed(() => hasRating.value ? 5 - fullStars.value - (hasHalf.value ? 1 : 0) : 0);

// Real review count from the backend. Many APIs expose it as `reviews_count`,
// `review_count`, or `ratings_count`. We accept any of these and require the
// rating itself to be > 0 before showing a number.
const reviewCount = computed(() => {
  const n = parseInt(
    props.deal?.reviews_count ??
    props.deal?.review_count ??
    props.deal?.ratings_count ??
    0
  );
  return Number.isFinite(n) && n > 0 ? n : 0;
});
const hasReviews = computed(() => hasRating.value && reviewCount.value > 0);

const { addItem, openCart } = useCart();
const { t, locale } = useI18n();
const isAdded = ref(false);

const savings = computed(() => {
  if (props.deal.originalPrice && props.deal.discountedPrice) {
    return Math.round(props.deal.originalPrice - props.deal.discountedPrice);
  }
  return null;
});

function handleAddToCart(event) {
  addItem({
    id: props.deal.id,
    title: props.deal.title,
    imageUrl: props.deal.imageUrl ?? null,
    price: props.deal.discountedPrice ?? props.deal.originalPrice ?? 0
  });
  animateFlyToCart(event, resolvedImage.value);

  isAdded.value = true;
  setTimeout(() => {
    isAdded.value = false;
  }, 1800);
}

function handleOrder() {
  addItem({
    id: props.deal.id,
    title: props.deal.title,
    imageUrl: props.deal.imageUrl ?? null,
    price: props.deal.discountedPrice ?? props.deal.originalPrice ?? 0
  });
  openCart();
}
</script>

<template>
  <div class="bg-white rounded-xl shadow-sm overflow-hidden flex flex-col group border border-transparent hover:border-primary/20 hover:shadow-md transition-all duration-200">
    <router-link :to="`/products/${deal.slug || deal.id}`" class="block">
      <div class="relative aspect-square bg-gray-100 overflow-hidden">
        <img
          v-if="resolvedImage"
          :src="resolvedImage"
          :alt="deal.title"
          class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
          loading="lazy"
        />
        <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
          <ShoppingBag class="w-10 h-10 text-gray-300" />
        </div>

        <!-- Discount Badge -->
        <div v-if="deal.discountPercent > 0" class="absolute top-0 right-0 z-10">
          <div class="bg-red-600 text-white font-extrabold px-2.5 py-1.5 rounded-bl-lg text-[10px] shadow-sm flex items-center justify-center gap-1 select-none">
            <template v-if="savings">
              <span>{{ savings }}৳</span>
            </template>
            <template v-else>
              <span>{{ deal.discountPercent }}%</span>
            </template>
            <span>{{ t('discount') }}</span>
          </div>
        </div>
      </div>
    </router-link>

    <div class="p-2 flex flex-col gap-1.5 flex-1">
      <router-link :to="`/products/${deal.slug || deal.id}`">
        <p class="text-sm font-semibold text-gray-800 line-clamp-2 leading-snug">
          {{ deal.title }}
        </p>
      </router-link>

      <!-- Star Rating: rendered only when the backend has a real rating.
           The (N) badge is rendered only when the backend also reports
           a positive review count, so we never show a fake "(4)" etc. -->
      <div v-if="hasRating" class="flex items-center gap-0.5" :title="`${rawRating.toFixed(1)} / 5`">
        <Star
          v-for="i in fullStars"
          :key="'f' + i"
          class="w-3.5 h-3.5 fill-yellow-400 text-yellow-400"
        />
        <StarHalf
          v-if="hasHalf"
          class="w-3.5 h-3.5 fill-yellow-400 text-yellow-400"
        />
        <Star
          v-for="i in emptyStars"
          :key="'e' + i"
          class="w-3.5 h-3.5 fill-gray-200 text-gray-200"
        />
        <span v-if="hasReviews" class="text-xs text-gray-500 ml-1">({{ reviewCount }})</span>
      </div>

      <!-- Price -->
      <div class="flex items-center gap-2">
        <span class="text-base font-bold text-orange-500">
          {{ deal.discountedPrice ?? deal.originalPrice ?? 0 }} {{ t('taka') }}
        </span>
        <span
          v-if="deal.originalPrice && deal.discountedPrice"
          class="text-xs text-gray-400 line-through"
        >
          {{ deal.originalPrice }} {{ t('taka') }}
        </span>
      </div>

      <!-- Actions -->
      <button
        @click="handleOrder"
        class="w-full flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 text-white text-sm font-semibold py-2 rounded-md transition-colors cursor-pointer"
      >
        <ShoppingBasket class="w-4 h-4" />
        {{ t('order_now') }}
      </button>

      <button
        @click="handleAddToCart"
        class="w-full flex items-center justify-center gap-2 border text-sm font-semibold py-2 rounded-md transition-all duration-300 cursor-pointer relative overflow-hidden"
        :class="isAdded 
          ? 'bg-primary border-primary text-white scale-[0.98]' 
          : 'border-gray-300 hover:border-primary hover:text-primary text-gray-700 bg-white hover:scale-[1.02] active:scale-[0.97]'
        "
        :disabled="isAdded"
      >
        <transition name="btn-text-slide" mode="out-in">
          <div v-if="isAdded" class="flex items-center justify-center gap-1.5" key="added">
            <Check class="w-4 h-4 text-white animate-bounce-short" />
            <span>{{ locale === 'bn' ? 'যোগ করা হয়েছে!' : 'Added!' }}</span>
          </div>
          <div v-else class="flex items-center justify-center gap-1.5" key="add">
            <ShoppingBag class="w-4 h-4" />
            <span>{{ t('add_to_cart') }}</span>
          </div>
        </transition>
      </button>
    </div>
  </div>
</template>

<style scoped>
/* Button text slide transition */
.btn-text-slide-enter-active,
.btn-text-slide-leave-active {
  transition: all 0.25s cubic-bezier(0.25, 0.8, 0.25, 1);
}
.btn-text-slide-enter-from {
  opacity: 0;
  transform: translateY(8px);
}
.btn-text-slide-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

@keyframes bounce-short {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-3px); }
}
.animate-bounce-short {
  animation: bounce-short 0.5s ease-out 1;
}
</style>
