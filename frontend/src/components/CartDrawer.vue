<script setup>
import { ref, onMounted, computed, defineAsyncComponent } from "vue";
import { X, Minus, Plus, Trash2, ShoppingCart, ShoppingBag } from "@lucide/vue";
import { useCart } from "@/store/cart";
import { fetchDeals } from "@/lib/api";
import { toast } from "@/lib/toast";
import { useI18n } from "@/lib/i18n";
import "swiper/css";
const Swiper = defineAsyncComponent(() => import("swiper/vue").then(m => m.Swiper));
const SwiperSlide = defineAsyncComponent(() => import("swiper/vue").then(m => m.SwiperSlide));

const { items, isOpen, closeCart, removeItem, updateQuantity, subtotal } = useCart();
const { t, locale } = useI18n();

const deals = ref([]);
const isLoadingDeals = ref(false);

onMounted(async () => {
  try {
    isLoadingDeals.value = true;
    deals.value = await fetchDeals();
  } catch (err) {
    console.error("Failed to load recommended deals in cart drawer:", err);
  } finally {
    isLoadingDeals.value = false;
  }
});

// Simple Fisher-Yates shuffle helper
function shuffle(array) {
  const copy = [...array];
  for (let i = copy.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [copy[i], copy[j]] = [copy[j], copy[i]];
  }
  return copy;
}

// Recommended items: based on categories of cart items, showing 10-12 random products
const recommended = computed(() => {
  // 1. Get all deals not currently in the cart
  const candidates = deals.value.filter(
    (deal) => !items.value.find((item) => item.id === deal.id)
  );

  if (candidates.length === 0) return [];

  // 2. Identify the category IDs of items currently in the cart
  const cartCategoryIds = items.value
    .map((item) => {
      const found = deals.value.find((d) => d.id === item.id);
      return found ? found.categoryId : null;
    })
    .filter(Boolean);

  // 3. Separate candidates into matching category and non-matching category
  let matchingDeals = [];
  let otherDeals = [];

  if (cartCategoryIds.length > 0) {
    matchingDeals = candidates.filter((deal) =>
      cartCategoryIds.includes(deal.categoryId)
    );
    otherDeals = candidates.filter(
      (deal) => !cartCategoryIds.includes(deal.categoryId)
    );
  } else {
    // If cart is empty, all candidates are others
    otherDeals = candidates;
  }

  // 4. Shuffle both arrays to provide random variety
  const shuffledMatching = shuffle(matchingDeals);
  const shuffledOther = shuffle(otherDeals);

  // 5. Combine them: prioritizing matching categories first
  const combined = [...shuffledMatching, ...shuffledOther];

  // 6. Return 12 products (approx 10-12)
  return combined.slice(0, 12);
});

function handleCheckout(page) {
  closeCart();
  toast({
    title: page === 'View Cart' ? t('view_cart') : t('checkout'),
    description: t('checkout_coming_soon'),
  });
}
</script>

<template>
  <transition name="fade">
    <div v-if="isOpen" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center pb-[62px] sm:pb-0">
      <!-- Backdrop -->
      <div class="absolute inset-0 bg-black/40" @click="closeCart" />

      <!-- Drawer Content -->
      <div class="relative bg-white w-full sm:w-[420px] sm:rounded-2xl flex flex-col overflow-hidden z-10 cart-drawer-panel">
        <div class="flex items-center justify-between px-4 py-3 border-b">
          <h2 class="font-bold text-gray-800 text-base">{{ t('cart_title') }}</h2>
          <button @click="closeCart" class="text-gray-500 hover:text-gray-700 p-1 cursor-pointer">
            <X class="w-5 h-5" />
          </button>
        </div>

        <div class="flex-1 overflow-y-auto">
          <!-- Cart Empty State -->
          <div v-if="items.length === 0" class="m-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
            <ShoppingCart class="w-8 h-8 text-yellow-400 mx-auto mb-2 animate-bounce" />
            <p class="text-gray-600 text-sm font-medium">{{ t('cart_empty') }}</p>
          </div>

          <!-- Cart Items List -->
          <div v-else class="p-4 flex flex-col gap-3">
            <div v-for="item in items" :key="item.compositeId || item.id" class="flex gap-3 bg-gray-50 rounded-xl p-3">
              <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-200 shrink-0">
                <img v-if="item.imageUrl" :src="item.imageUrl" :alt="item.title" class="w-full h-full object-cover" />
                <div v-else class="w-full h-full flex items-center justify-center bg-gray-200">
                  <ShoppingCart class="w-6 h-6 text-gray-400" />
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-800 line-clamp-2 mb-0.5">{{ item.title }}</p>
                
                <!-- Display selected variations -->
                <div v-if="item.color || item.variantStr" class="flex flex-wrap gap-1.5 mb-1.5 text-[10px] font-bold text-gray-500">
                  <span v-if="item.color" class="bg-gray-200 px-1.5 py-0.5 rounded flex items-center gap-1">
                    {{ locale === 'bn' ? 'রঙ: ' : 'Color: ' }}{{ item.color }}
                  </span>
                  <span v-if="item.variantStr" class="bg-gray-200 px-1.5 py-0.5 rounded">
                    {{ item.variantStr }}
                  </span>
                </div>

                <p class="text-sm font-bold text-orange-500">{{ item.price }} {{ t('taka') }}</p>
                
                <div class="flex items-center gap-2 mt-2">
                  <button
                    @click="updateQuantity(item.compositeId || item.id, item.quantity - 1)"
                    class="w-6 h-6 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100 cursor-pointer"
                  >
                    <Minus class="w-3 h-3" />
                  </button>
                  <span class="text-sm font-semibold w-5 text-center">{{ item.quantity }}</span>
                  <button
                    @click="updateQuantity(item.compositeId || item.id, item.quantity + 1)"
                    class="w-6 h-6 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100 cursor-pointer"
                  >
                    <Plus class="w-3 h-3" />
                  </button>
                  
                  <button
                    @click="removeItem(item.compositeId || item.id)"
                    class="ml-auto text-red-400 hover:text-red-600 p-1 cursor-pointer"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Recommended Slider -->
          <div v-if="recommended.length > 0" class="px-4 pb-4">
            <p class="text-sm font-bold text-gray-700 mb-1">{{ t('recommended') }}</p>
            <p class="text-xs text-gray-500 mb-3">{{ t('recommended_desc') }}</p>
            <swiper
              :slides-per-view="2.8"
              :space-between="10"
              class="w-full pb-2"
            >
              <swiper-slide
                v-for="deal in recommended"
                :key="deal.id"
              >
                <router-link
                  :to="`/products/${deal.slug || deal.id}`"
                  @click="closeCart"
                  class="bg-white border rounded-xl overflow-hidden shadow-2xs hover:shadow-xs transition-shadow flex flex-col group h-full border-gray-150 hover:border-primary/30"
                >
                  <div class="aspect-square bg-gray-50 overflow-hidden relative">
                    <img 
                      v-if="deal.imageUrl" 
                      :src="deal.imageUrl" 
                      :alt="deal.title" 
                      class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" 
                    />
                    <div v-else class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-300">
                      <ShoppingBag class="w-6 h-6" />
                    </div>
                  </div>
                  <div class="p-2 flex flex-col justify-between flex-1">
                    <p class="text-[10px] text-gray-700 font-semibold line-clamp-2 leading-tight group-hover:text-primary transition-colors mb-1.5">
                      {{ deal.title }}
                    </p>
                    <p class="text-[11px] font-extrabold text-orange-500 mt-auto">
                      {{ deal.discountedPrice ?? deal.originalPrice }} {{ t('taka') }}
                    </p>
                  </div>
                </router-link>
              </swiper-slide>
            </swiper>
          </div>
        </div>

        <!-- Subtotal and Checkouts -->
        <div class="border-t px-4 py-3 bg-white">
          <div class="flex items-center justify-between mb-3">
            <span class="font-semibold text-gray-800">{{ t('subtotal') }}</span>
            <span class="font-bold text-gray-800">{{ subtotal }} {{ t('taka') }}</span>
          </div>
          <router-link
            to="/cart"
            @click="closeCart"
            class="block w-full bg-primary hover:bg-primary/90 text-white text-center font-bold py-3 rounded-lg mb-2 transition-colors cursor-pointer"
          >
            {{ t('view_cart') }}
          </router-link>
          <router-link
            to="/checkout"
            @click="closeCart"
            class="block w-full bg-pink-500 hover:bg-pink-600 text-white text-center font-bold py-3 rounded-lg transition-colors cursor-pointer"
          >
            {{ t('checkout') }}
          </router-link>
        </div>
      </div>
    </div>
  </transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Mobile: sit above the 62px bottom nav */
.cart-drawer-panel {
  max-height: calc(90vh - 62px);
}

/* Desktop: normal centered modal */
@media (min-width: 640px) {
  .cart-drawer-panel {
    max-height: 90vh;
  }
}
</style>
