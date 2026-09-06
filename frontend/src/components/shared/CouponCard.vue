<script setup>
import { ref } from "vue";
import { CheckCircle2, Copy, Scissors, ShieldCheck } from "@lucide/vue";
import { toast } from "@/lib/toast";
import { revealCoupon } from "@/lib/api";
import { useI18n } from "@/lib/i18n";

const props = defineProps({
  coupon: { type: Object, required: true }
});

const { locale, t } = useI18n();

const revealed = ref(false);
const code = ref(props.coupon.code);
const isPending = ref(false);

async function handleReveal() {
  if (revealed.value) {
    handleCopy(code.value);
    return;
  }

  isPending.value = true;
  try {
    const data = await revealCoupon(props.coupon.id);
    code.value = data.code;
    revealed.value = true;
    handleCopy(data.code);
  } catch (err) {
    toast({
      title: locale.value === 'bn' ? "কোড দেখাতে ব্যর্থ হয়েছে" : "Failed to reveal",
      description: locale.value === 'bn' ? "বর্তমানে কুপন কোডটি দেখানো যাচ্ছে না।" : "Could not reveal the coupon code right now.",
      variant: "destructive"
    });
  } finally {
    isPending.value = false;
  }
}

function handleCopy(text) {
  navigator.clipboard.writeText(text);
  toast({
    title: locale.value === 'bn' ? "কপি করা হয়েছে!" : "Copied to clipboard!",
    description: locale.value === 'bn' ? `কুপন কোড ${text} ব্যবহারের জন্য প্রস্তুত।` : `Code ${text} is ready to use.`,
  });
}
</script>

<template>
  <div class="h-full border border-gray-200 rounded-lg overflow-hidden flex flex-col sm:flex-row bg-white hover:shadow-md transition-shadow relative">
    <!-- Circular side notches for ticket aesthetic -->
    <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-[#f7f7f7] rounded-full border-r border-gray-200 hidden sm:block z-10" />
    <div class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-[#f7f7f7] rounded-full border-l border-gray-200 hidden sm:block z-10" />
    
    <!-- Store Info Column -->
    <div class="sm:w-1/3 p-6 bg-gray-50 flex flex-col items-center justify-center border-b sm:border-b-0 sm:border-r border-dashed border-gray-200 relative shrink-0">
      <img
        v-if="coupon.storeLogoUrl"
        :src="coupon.storeLogoUrl"
        :alt="coupon.storeName"
        class="w-16 h-16 object-contain mb-3 rounded-md bg-white p-1 shadow-sm"
      />
      <div v-else class="w-16 h-16 rounded-md bg-white shadow-sm flex items-center justify-center mb-3">
        <span class="font-display font-bold text-2xl text-primary">{{ coupon.storeName ? coupon.storeName.charAt(0) : 'S' }}</span>
      </div>
      <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">{{ coupon.storeName }}</span>
      
      <div class="mt-4 text-center">
        <div class="font-display font-bold text-3xl text-primary">
          {{ coupon.discountType === 'percentage' ? `${coupon.discountValue}%` : `৳${coupon.discountValue}` }}
        </div>
        <div class="text-xs font-medium uppercase tracking-wider text-gray-400 mt-1">{{ t('discount') }}</div>
      </div>
    </div>

    <!-- Coupon details -->
    <div class="p-6 flex-1 flex flex-col">
      <div class="flex items-start justify-between gap-4 mb-2">
        <h3 class="font-display font-semibold text-lg leading-tight text-gray-800">{{ coupon.title }}</h3>
        <div v-if="coupon.verified" class="flex items-center gap-1 text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full whitespace-nowrap shrink-0">
          <ShieldCheck class="w-3.5 h-3.5" />
          {{ t('verified') }}
        </div>
      </div>
      
      <p v-if="coupon.description" class="text-sm text-gray-500 line-clamp-2 mb-4">{{ coupon.description }}</p>

      <div class="mt-auto pt-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-1.5 text-xs text-gray-400">
          <CheckCircle2 class="w-4 h-4 text-emerald-500" />
          <span>{{ coupon.usedCount ?? 150 }} {{ t('uses_today') }}</span>
        </div>

        <div class="relative group w-full sm:w-auto">
          <button 
            @click="handleReveal"
            :disabled="isPending"
            class="w-full sm:w-[200px] h-11 border-2 border-primary/20 bg-primary/5 hover:bg-primary/10 text-primary transition-all rounded-lg overflow-hidden group font-mono text-base tracking-widest relative flex items-center justify-center cursor-pointer"
          >
            <transition name="fade" mode="out-in">
              <div v-if="!revealed" class="absolute inset-0 flex items-center justify-center w-full h-full bg-primary text-white font-sans font-semibold rounded-md tracking-normal">
                <Scissors class="w-4 h-4 mr-2" />
                {{ t('show_code') }}
              </div>
              <div v-else class="flex items-center gap-2">
                {{ code }}
                <Copy class="w-4 h-4 opacity-50 group-hover:opacity-100 transition-opacity" />
              </div>
            </transition>
            
            <span v-if="!revealed" class="blur-xs select-none opacity-50 font-mono">XXXXXX</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(5px);
}
</style>
