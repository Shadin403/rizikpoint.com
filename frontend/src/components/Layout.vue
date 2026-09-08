<script setup>
import { computed } from "vue";
import Navbar from "./Navbar.vue";
import Footer from "./Footer.vue";
import CartDrawer from "./CartDrawer.vue";
import FloatingCart from "./FloatingCart.vue";
import BottomNav from "./BottomNav.vue";
import AuthModal from "./AuthModal.vue";
import { useI18n } from "@/lib/i18n";
import { toastState } from "@/lib/toast";
import { Info, CheckCircle2, AlertTriangle, X } from "@lucide/vue";
import { useBusinessSettings } from "@/composables/useBusinessSettings";

const { locale, t } = useI18n();
const { settings, loading, get } = useBusinessSettings();

function closeToast() {
  toastState.visible = false;
}

const marqueeLines = computed(() => {
  if (loading.value) return [];
  const text = locale.value === "en" ? get("header_marquee_text_en") : get("header_marquee_text_bn");
  if (!text) return [];
  return text
    .split("\n")
    .map((l) => l.trim())
    .filter((l) => l.length > 0);
});
</script>

<template>
  <div class="min-h-screen flex flex-col bg-background">
    <a href="#main-content" class="store-skip-link">{{ locale === 'bn' ? 'মূল কনটেন্টে যান' : 'Skip to content' }}</a>
    <!-- Announcement Bar -->
    <div
      v-if="marqueeLines.length > 0"
      class="bg-black text-white py-1.5 overflow-hidden relative"
    >
      <div class="whitespace-nowrap flex animate-marquee">
        <span class="inline-flex gap-12 px-4 text-xs font-medium">
          <!-- Loop the items twice for infinite marquee scrolling effect -->
          <span v-for="(item, idx) in marqueeLines" :key="'first-' + idx">{{
            item
          }}</span>
          <span v-for="(item, idx) in marqueeLines" :key="'second-' + idx">{{
            item
          }}</span>
        </span>
      </div>
    </div>

    <!-- Header Navigation -->
    <Navbar />

    <!-- Main Content Container -->
    <main
      id="main-content" tabindex="-1"
      class="flex-1 w-full max-w-[1536px] mx-auto px-3 sm:px-6 lg:px-8 py-6 pb-20 md:pb-6 flex flex-col"
    >
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <!-- Footer Layout -->
    <Footer />

    <!-- Cart Sidebar Drawer -->
    <CartDrawer />

    <!-- Floating Sticky Cart Button -->
    <FloatingCart />

    <!-- Authentication Modal -->
    <AuthModal />

    <!-- Toast Notifications Overlay -->
    <transition name="toast-fade">
      <div
        v-if="toastState.visible"
        class="fixed bottom-[78px] md:bottom-4 left-1/2 -translate-x-1/2 md:translate-x-0 md:left-auto md:right-4 z-[60] p-4 rounded-xl shadow-lg border max-w-sm w-[90%] md:w-80 flex items-start gap-3 bg-white"
        :class="
          toastState.type === 'destructive'
            ? 'border-red-200 text-red-800'
            : 'border-primary/20 text-gray-800'
        "
      >
        <CheckCircle2
          v-if="toastState.type !== 'destructive'"
          class="w-5 h-5 text-primary shrink-0 mt-0.5"
        />
        <AlertTriangle v-else class="w-5 h-5 text-red-600 shrink-0 mt-0.5" />

        <div class="flex-1">
          <p class="text-xs font-bold text-gray-900 leading-tight">
            {{ toastState.title }}
          </p>
          <p
            v-if="toastState.description"
            class="text-[11px] text-gray-500 mt-1 leading-snug"
          >
            {{ toastState.description }}
          </p>
        </div>

        <button
          @click="closeToast"
          class="text-gray-400 hover:text-gray-600 p-0.5 cursor-pointer"
        >
          <X class="w-4 h-4" />
        </button>
      </div>
    </transition>
    <!-- Mobile bottom navigation (visible only on small screens via md:hidden) -->
    <BottomNav />
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition:
    opacity 0.15s ease,
    transform 0.15s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(5px);
}

.toast-fade-enter-active,
.toast-fade-leave-active {
  transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.toast-fade-enter-from {
  opacity: 0;
  transform: translate(-50%, 20px);
}
@media (min-width: 768px) {
  .toast-fade-enter-from {
    transform: translate(0, 20px);
  }
}
.toast-fade-leave-to {
  opacity: 0;
  transform: scale(0.9) translate(-50%, 0);
}
@media (min-width: 768px) {
  .toast-fade-leave-to {
    transform: scale(0.9) translate(0, 0);
  }
}
</style>
