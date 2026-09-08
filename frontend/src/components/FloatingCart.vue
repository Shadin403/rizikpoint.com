<script setup>
import { ref, watch } from "vue";
import { ShoppingCart } from "@lucide/vue";
import { useCart } from "@/store/cart";
import { useI18n } from "@/lib/i18n";

const { totalItems, subtotal, openCart } = useCart();
const { locale } = useI18n();

const isAnimating = ref(false);

watch(totalItems, (newVal, oldVal) => {
  if (newVal > oldVal) {
    isAnimating.value = true;
    // Reset animation state after the animation completes
    setTimeout(() => {
      isAnimating.value = false;
    }, 800);
  }
});
</script>

<template>
  <button
    @click="openCart"
    class="floating-cart-btn fixed right-0 top-1/2 z-40 bg-primary text-white flex flex-col items-center justify-center p-3.5 rounded-l-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] cursor-pointer select-none border-l border-y border-white/10"
    :class="{ 'animate-cart-shake': isAnimating }"
    :aria-label="locale === 'bn' ? 'কার্ট দেখুন' : 'View cart'"
  >
    <!-- Cart Icon with Quantity Badge -->
    <div class="relative flex items-center justify-center p-1">
      <ShoppingCart class="w-6 h-6 text-white stroke-[2.2]" />
      
      <!-- Quantity Badge -->
      <span
        class="absolute -top-2 -right-2 bg-amber-400 text-gray-900 text-[10px] font-extrabold w-5 h-5 rounded-full flex items-center justify-center border-2 border-white shadow-md transition-all duration-300"
        :class="{ 'animate-badge-pop': isAnimating }"
      >
        {{ totalItems }}
      </span>
    </div>

    <!-- Items Count Text -->
    <div class="text-[10px] font-extrabold mt-1.5 tracking-wide text-white uppercase leading-none select-none">
      {{ totalItems }} {{ locale === 'bn' ? 'টি পণ্য' : (totalItems === 1 ? 'Item' : 'Items') }}
    </div>

    <!-- Price Pill -->
    <div class="bg-black/15 px-2.5 py-1 rounded-full mt-2 flex items-center justify-center shadow-[inset_0_1px_2px_rgba(0,0,0,0.15)] border border-white/5 select-none">
      <span class="text-[9px] font-black tracking-wider text-white whitespace-nowrap">
        {{ subtotal }} {{ locale === 'bn' ? 'টাকা' : 'TK' }}
      </span>
    </div>
  </button>
</template>

<style scoped>
.floating-cart-btn {
  width: 76px;
  transform: translate(0, -50%);
  transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease, background-color 0.2s ease;
}

/* Mobile already has a header cart; keep the reading area unobstructed. */
@media (max-width: 767px) {
  .floating-cart-btn { display: none; }
}

.floating-cart-btn:hover {
  transform: translate(-6px, -50%) scale(1.04);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
  background-color: hsl(141, 72%, 32%); /* Slightly darker hover green */
}

.floating-cart-btn:active {
  transform: translate(-4px, -50%) scale(0.96);
}

/* Add-to-cart Wiggle/Shake Animation */
@keyframes cart-shake {
  0%, 100% {
    transform: translate(0, -50%) scale(1) rotate(0deg);
  }
  15%, 45%, 75% {
    transform: translate(0, -50%) scale(1.15) rotate(-6deg);
  }
  30%, 60%, 90% {
    transform: translate(0, -50%) scale(1.15) rotate(6deg);
  }
}

/* Badge scaling pop animation */
@keyframes badge-pop {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.4);
    background-color: #f59e0b; /* Highlight to warmer amber */
  }
}

.animate-cart-shake {
  animation: cart-shake 0.8s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
}

.animate-badge-pop {
  animation: badge-pop 0.5s ease-out both;
}
</style>
