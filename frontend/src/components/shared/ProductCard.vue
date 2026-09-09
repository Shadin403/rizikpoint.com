<script setup>
import { ref, computed } from "vue";
import { useCart } from "@/store/cart";
import { useI18n } from "@/lib/i18n";
import { useBusinessSettings } from "@/composables/useBusinessSettings";
import { dummyImagesEnabled, dummyProductImage } from "@/lib/dummyProductImages";
import { Heart, ShoppingBag, Star, StarHalf, Plus, Minus } from "@lucide/vue";
import { animateFlyToCart } from "@/lib/cart-fly";

const props = defineProps({
  deal: { type: Object, required: true }
});

// Resolve image — supports full URL or relative uploads path from backend
const resolvedImage = computed(() => {
  const url = props.deal?.imageUrl;
  if (!url) return null;
  if (url.startsWith('http://') || url.startsWith('https://')) return url;
  const origin = import.meta.env.VITE_BACKEND_ORIGIN || 'http://127.0.0.1:8000';
  const clean  = url.startsWith('/') ? url.slice(1) : url;
  return `${origin}/${clean}`;
});

const { get: getBusinessSetting } = useBusinessSettings();
const useDummyImages = computed(() => dummyImagesEnabled(getBusinessSetting));

// --- Review / rating ---------------------------------------------------------
const rawRating = computed(() => {
  const r = parseFloat(props.deal?.rating);
  return Number.isFinite(r) && r > 0 ? r : null;
});
const hasRating = computed(() => rawRating.value !== null);
const fullStars  = computed(() => hasRating.value ? Math.floor(rawRating.value) : 0);
const hasHalf    = computed(() => hasRating.value && rawRating.value - fullStars.value >= 0.5);
const emptyStars = computed(() => hasRating.value ? 5 - fullStars.value - (hasHalf.value ? 1 : 0) : 0);

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

const { items, addItem, updateQuantity, removeItem } = useCart();
const { t } = useI18n();
const imageFailed = ref(false);
const wishlisted = ref(false);

const displayImage = computed(() => {
  if (resolvedImage.value && !imageFailed.value) return resolvedImage.value;
  return useDummyImages.value ? dummyProductImage(props.deal) : null;
});

// Find matching item in cart
const cartItem = computed(() => {
  return items.value.find(i => i.id === props.deal.id);
});

const cartQty = computed(() => {
  return cartItem.value ? cartItem.value.quantity : 0;
});

const savings = computed(() => {
  if (props.deal.originalPrice && props.deal.discountedPrice) {
    return Math.round(props.deal.originalPrice - props.deal.discountedPrice);
  }
  return null;
});

const displayCategoryName = computed(() => {
  if (props.deal?.categoryName) return props.deal.categoryName;
  if (props.deal?.category?.name) return props.deal.category.name;
  if (typeof props.deal?.category === 'string') return props.deal.category;
  return 'GROCERY';
});

// Unit / Weight Text (e.g. 500g, EACH, KG)
const unitText = computed(() => {
  if (props.deal?.unit) return props.deal.unit;
  if (props.deal?.capacity) return props.deal.capacity;
  if (props.deal?.weight) return props.deal.weight;
  
  // Try extracting weight/unit pattern from title
  const match = props.deal?.title?.match(/\b(\d+(?:\.\d+)?\s*(?:g|kg|pcs|pc|ml|l|ltr|gm|pack))\b/i);
  if (match) return match[1];

  return 'EACH';
});

const currentPriceNum = computed(() => {
  return Number(props.deal?.discountedPrice ?? props.deal?.originalPrice ?? 0);
});

const formattedPrice = computed(() => {
  const val = currentPriceNum.value;
  return `TK ${val.toFixed(2)}`;
});

function handleAddToCart(event) {
  addItem({
    id: props.deal.id,
    title: props.deal.title,
    imageUrl: props.deal.imageUrl ?? null,
    price: currentPriceNum.value,
    unit: unitText.value
  });
  if (event && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    animateFlyToCart(event, resolvedImage.value);
  }
}

function handleIncrease() {
  if (!cartItem.value) {
    handleAddToCart();
    return;
  }
  const targetId = cartItem.value.compositeId || cartItem.value.id;
  updateQuantity(targetId, cartQty.value + 1);
}

function handleDecrease() {
  if (!cartItem.value) return;
  const targetId = cartItem.value.compositeId || cartItem.value.id;
  const newQty = cartQty.value - 1;
  if (newQty <= 0) {
    removeItem(targetId);
  } else {
    updateQuantity(targetId, newQty);
  }
}
</script>

<template>
  <article class="organic-product bg-white rounded-lg border border-gray-200 hover:border-gray-300 hover:shadow-md transition-all duration-200 flex flex-col justify-between group relative p-3 sm:p-4">
    <!-- Top Content Area -->
    <div class="flex flex-col flex-1">
      <!-- Product Image Container -->
      <router-link :to="`/products/${deal.slug || deal.id}`" class="block rounded-md focus-visible:outline-2 focus-visible:outline-[#168039]">
        <div class="organic-product-image relative aspect-square w-full overflow-hidden flex items-center justify-center p-2 mb-2 bg-white">
          <img
            v-if="displayImage"
            :src="displayImage"
            :alt="deal.title"
            class="w-full h-full object-contain transition-transform duration-200 group-hover:scale-[1.03]"
            @error="imageFailed = true"
            loading="lazy"
          />
          <div v-else class="w-full h-full flex items-center justify-center bg-gray-50 rounded-sm">
            <ShoppingBag class="w-10 h-10 text-gray-300" />
          </div>

          <!-- Discount Badge -->
          <div v-if="deal.discountPercent > 0" class="absolute top-1.5 right-1.5 z-10">
            <div class="bg-[#168039] text-white font-bold px-2 py-0.5 rounded-[4px] text-[11px] leading-tight shadow-xs select-none">
              <template v-if="savings">
                <span>-{{ savings }}৳</span>
              </template>
              <template v-else>
                <span>-{{ deal.discountPercent }}%</span>
              </template>
            </div>
          </div>
        </div>
      </router-link>

      <!-- Title -->
      <router-link :to="`/products/${deal.slug || deal.id}`" class="block focus-visible:outline-2 focus-visible:outline-[#168039]">
        <h3 class="text-[13px] sm:text-[14px] leading-[20px] font-normal text-gray-800 line-clamp-2 min-h-[2.5rem] hover:text-[#168039] transition-colors font-sans">
          {{ deal.title }}
        </h3>
      </router-link>

      <!-- Category Name info -->
      <div class="text-[11px] font-normal text-gray-500 uppercase tracking-wide mt-1.5 mb-1 line-clamp-1">
        {{ displayCategoryName }}
      </div>

      <!-- Rating (optional) -->
      <div v-if="hasRating" class="flex items-center gap-0.5 mb-1" :title="`${rawRating.toFixed(1)} / 5`">
        <Star v-for="i in fullStars" :key="'f' + i" class="w-3.5 h-3.5 fill-amber-400 text-amber-400" />
        <StarHalf v-if="hasHalf" class="w-3.5 h-3.5 fill-amber-400 text-amber-400" />
        <Star v-for="i in emptyStars" :key="'e' + i" class="w-3.5 h-3.5 fill-gray-200 text-gray-200" />
        <span v-if="hasReviews" class="text-[11px] text-gray-500 ml-1">({{ reviewCount }})</span>
      </div>

      <!-- Price -->
      <div class="flex items-baseline gap-2 mt-auto pt-1 mb-3">
        <span class="text-sm sm:text-base font-bold text-gray-900 tracking-tight font-sans">
          {{ formattedPrice }}
        </span>
        <span
          v-if="deal.originalPrice > deal.discountedPrice && deal.discountedPrice != null"
          class="text-xs text-gray-400 line-through font-normal"
        >
          TK {{ Number(deal.originalPrice).toFixed(2) }}
        </span>
      </div>
    </div>

    <!-- Bottom Action Button: Add to Cart OR Quantity Control -->
    <div class="mt-auto pt-1">
      <!-- Quantity Controller Bar (When item is in cart) -->
      <div
        v-if="cartQty > 0"
        class="w-full bg-[#168039] hover:bg-[#146c30] active:bg-[#146c30] text-white font-bold rounded-md flex items-center justify-between h-9 px-1 shadow-2xs transition-colors select-none border border-[#146c30]"
      >
        <button
          @click.stop="handleDecrease"
          aria-label="Decrease quantity"
          class="w-7 h-7 flex items-center justify-center rounded hover:bg-black/15 active:bg-black/25 transition-colors cursor-pointer text-white"
        >
          <Minus class="w-3.5 h-3.5 stroke-[2.5]" />
        </button>

        <span class="flex-1 text-center font-bold text-sm leading-none text-white select-none">
          {{ cartQty }}
        </span>

        <button
          @click.stop="handleIncrease"
          aria-label="Increase quantity"
          class="w-7 h-7 flex items-center justify-center rounded hover:bg-black/15 active:bg-black/25 transition-colors cursor-pointer text-white"
        >
          <Plus class="w-3.5 h-3.5 stroke-[2.5]" />
        </button>
      </div>

      <!-- Initial Add to Cart Button (When item is NOT in cart) -->
      <button
        v-else
        @click.stop="handleAddToCart"
        :aria-label="`${deal.title}: ${t('add_to_cart')}`"
        class="w-full h-9 border border-gray-300 hover:border-[#168039] text-gray-800 hover:text-[#168039] bg-white rounded-md text-xs font-semibold flex items-center justify-center gap-1.5 transition-all shadow-2xs cursor-pointer focus-visible:outline-2 focus-visible:outline-[#168039]"
      >
        <ShoppingBag class="w-3.5 h-3.5 text-gray-700 group-hover:text-[#168039]" />
        <span>{{ t('add_to_cart') }}</span>
      </button>
    </div>
  </article>
</template>
