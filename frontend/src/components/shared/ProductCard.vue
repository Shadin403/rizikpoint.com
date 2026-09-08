<script setup>
import { ref, computed } from "vue";
import { useCart } from "@/store/cart";
import { useI18n } from "@/lib/i18n";
import { ShoppingBag, Star, StarHalf, Plus, Minus } from "@lucide/vue";
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
  <div class="organic-product bg-white rounded-md border border-[#e5e7eb] hover:border-[#146c30] hover:shadow-xs transition-all duration-150 flex flex-col justify-between overflow-hidden p-2.5 group relative">
    <!-- Top Content Area -->
    <div class="flex flex-col flex-1">
      <!-- Product Image Container -->
      <router-link :to="`/products/${deal.slug || deal.id}`" class="block rounded-sm focus-visible:outline-2 focus-visible:outline-[#168039]">
        <div class="organic-product-image relative aspect-square w-full overflow-hidden flex items-center justify-center p-1.5 mb-2 bg-white rounded-sm border border-stone-100">
          <img
            v-if="resolvedImage && !imageFailed"
            :src="resolvedImage"
            :alt="deal.title"
            class="w-full h-full object-contain transition-transform duration-200 group-hover:scale-[1.02]"
            @error="imageFailed = true"
            loading="lazy"
          />
          <div v-else class="w-full h-full flex items-center justify-center bg-gray-50 rounded-sm">
            <ShoppingBag class="w-10 h-10 text-gray-300" />
          </div>

          <!-- Discount Badge -->
          <div v-if="deal.discountPercent > 0" class="absolute top-1.5 right-1.5 z-10">
            <div class="bg-[#168039] text-white font-semibold px-2 py-0.5 rounded-[4px] text-[11px] leading-tight shadow-xs select-none">
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
        <h3 class="text-[14px] leading-[21px] font-normal text-[#111827] line-clamp-2 min-h-[2.6rem] hover:text-[#146c30] transition-colors">
          {{ deal.title }}
        </h3>
      </router-link>

      <!-- Unit / Weight / Quantity info -->
      <div class="text-[12px] font-medium text-[#6b7280] uppercase tracking-wide my-1">
        {{ unitText }}
      </div>

      <!-- Rating (optional) -->
      <div v-if="hasRating" class="flex items-center gap-0.5 mb-1" :title="`${rawRating.toFixed(1)} / 5`">
        <Star v-for="i in fullStars" :key="'f' + i" class="w-3.5 h-3.5 fill-amber-400 text-amber-400" />
        <StarHalf v-if="hasHalf" class="w-3.5 h-3.5 fill-amber-400 text-amber-400" />
        <Star v-for="i in emptyStars" :key="'e' + i" class="w-3.5 h-3.5 fill-gray-200 text-gray-200" />
        <span v-if="hasReviews" class="text-[12px] text-[#6b7280] ml-1">({{ reviewCount }})</span>
      </div>

      <!-- Price -->
      <div class="flex items-baseline gap-2 mt-auto pt-1 mb-2.5">
        <span class="text-[16px] font-bold text-[#111827] tracking-tight">
          {{ formattedPrice }}
        </span>
        <span
          v-if="deal.originalPrice > deal.discountedPrice && deal.discountedPrice != null"
          class="text-[12px] text-[#6b7280] line-through font-normal"
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
        class="w-full bg-[#168039] hover:bg-[#146c30] active:bg-[#146c30] text-white font-medium rounded-[4px] flex items-center justify-between h-9 px-1 shadow-2xs transition-colors select-none border border-[#146c30]"
      >
        <button
          @click.stop="handleDecrease"
          aria-label="Decrease quantity"
          class="w-7 h-7 flex items-center justify-center rounded-[4px] hover:bg-black/15 active:bg-black/25 transition-colors cursor-pointer focus-visible:ring-2 focus-visible:ring-white"
        >
          <Minus class="w-3.5 h-3.5 stroke-[2.5]" />
        </button>

        <span class="flex-1 text-center font-semibold text-[14px] leading-none">
          {{ cartQty }}
        </span>

        <button
          @click.stop="handleIncrease"
          aria-label="Increase quantity"
          class="w-7 h-7 flex items-center justify-center rounded-[4px] hover:bg-black/15 active:bg-black/25 transition-colors cursor-pointer focus-visible:ring-2 focus-visible:ring-white"
        >
          <Plus class="w-3.5 h-3.5 stroke-[2.5]" />
        </button>
      </div>

      <!-- Initial Add to Cart Button (When item is NOT in cart) -->
      <button
        v-else
        @click.stop="handleAddToCart"
        :aria-label="`${deal.title}: ${t('add_to_cart')}`"
        class="w-full h-9 border border-[#168039] text-[#168039] hover:bg-[#168039] hover:text-white bg-white rounded-[4px] text-[13px] font-medium flex items-center justify-center gap-1.5 transition-all shadow-2xs active:bg-[#146c30] cursor-pointer focus-visible:outline-2 focus-visible:outline-[#146c30]"
      >
        <ShoppingBag class="w-4 h-4" />
        <span>{{ t('add_to_cart') }}</span>
      </button>
    </div>
  </div>
</template>
