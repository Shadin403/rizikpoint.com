<script setup>
import { ref, computed } from "vue";
import { useRouter } from "vue-router";
import {
  Trash2,
  Minus,
  Plus,
  ShoppingCart,
  MapPin,
  Tag,
  ArrowLeft,
  ChevronRight,
  PhoneCall,
  ShoppingBag
} from "@lucide/vue";
import { useCart } from "@/store/cart";
import { useI18n } from "@/lib/i18n";
import { toast } from "@/lib/toast";
import { usePageTitle } from "@/composables/usePageTitle";
import { pageTitles } from "@/lib/pageTitles";
usePageTitle(pageTitles.Cart);
const router = useRouter();
const { items, removeItem, updateQuantity, subtotal } = useCart();
const { t, locale } = useI18n();

// Grand total computation
const grandTotal = computed(() => subtotal.value);

// Coupon state
const couponCode = ref("");
const isCouponApplied = ref(false);
const couponDiscount = ref(0);

function applyCoupon() {
  if (!couponCode.value.trim()) {
    toast({
      title: locale.value === "bn" ? "ভুল কুপন!" : "Invalid Coupon!",
      description: locale.value === "bn" ? "দয়া করে একটি সঠিক কুপন কোড লিখুন।" : "Please enter a valid coupon code.",
      type: "error",
    });
    return;
  }
  
  // Simulated discount for demo
  isCouponApplied.value = true;
  couponDiscount.value = Math.round(subtotal.value * 0.1); // 10% discount
  
  toast({
    title: locale.value === "bn" ? "কুপন কোড সফল!" : "Coupon Applied!",
    description: locale.value === "bn" 
      ? `অভিনন্দন! ১০% ছাড় হিসেবে ${couponDiscount.value} টাকা হ্রাস পেয়েছে।` 
      : `Congratulations! 10% discount of ${couponDiscount.value} TK has been applied.`,
  });
}

function removeCoupon() {
  isCouponApplied.value = false;
  couponDiscount.value = 0;
  couponCode.value = "";
  toast({
    title: locale.value === "bn" ? "কুপন সরানো হয়েছে" : "Coupon Removed",
  });
}

const finalTotal = computed(() => Math.max(0, grandTotal.value - couponDiscount.value));

function handlePlaceOrder() {
  router.push("/checkout");
}
</script>

<template>
  <div class="flex-1 w-full max-w-7xl mx-auto py-2 flex flex-col gap-6">
    <!-- Breadcrumb navigation -->
    <div class="flex items-center gap-2 text-xs font-semibold text-gray-500">
      <router-link to="/" class="hover:text-primary transition-colors">{{ t('home') }}</router-link>
      <ChevronRight class="w-3.5 h-3.5" />
      <span class="text-gray-800">{{ locale === 'bn' ? 'শপিং কার্ট' : 'Shopping Cart' }}</span>
    </div>

    <!-- Page Title -->
    <div class="flex items-center justify-between border-b pb-4 border-gray-200/80">
      <div>
        <h1 class="text-xl sm:text-2xl font-black text-gray-800 tracking-tight flex items-center gap-2">
          <ShoppingCart class="w-6 h-6 text-primary shrink-0" />
          {{ t('cart_title') }}
        </h1>
        <p class="text-xs text-gray-500 mt-1">
          {{ locale === 'bn' ? `আপনার কার্টে মোট ${items.length} টি প্রোডাক্ট রয়েছে` : `You have ${items.length} product(s) in your cart` }}
        </p>
      </div>
      
      <router-link
        to="/products-list"
        class="inline-flex items-center gap-1 text-xs font-bold text-primary hover:underline"
      >
        <ArrowLeft class="w-3.5 h-3.5" />
        {{ locale === 'bn' ? 'শপিং চালিয়ে যান' : 'Continue Shopping' }}
      </router-link>
    </div>

    <!-- Cart Empty State -->
    <div v-if="items.length === 0" class="bg-white border rounded-2xl p-8 sm:p-12 text-center shadow-xs max-w-lg mx-auto w-full my-6">
      <div class="w-16 h-16 bg-yellow-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-yellow-100">
        <ShoppingBag class="w-8 h-8 text-yellow-500 animate-bounce" />
      </div>
      <h2 class="text-lg font-bold text-gray-800">{{ t('cart_empty') }}</h2>
      <p class="text-xs text-gray-500 mt-2 max-w-xs mx-auto leading-relaxed">
        {{ locale === 'bn' ? 'কার্টে কোনো প্রোডাক্ট যোগ করা হয়নি। কেনাকাটা শুরু করতে নিচে ক্লিক করুন।' : 'There are no items in your cart. Start shopping to add items.' }}
      </p>
      <router-link
        to="/products-list"
        class="inline-flex items-center justify-center bg-primary hover:bg-primary/90 text-white font-bold px-6 py-3 rounded-xl mt-6 transition-colors shadow-sm text-sm"
      >
        {{ locale === 'bn' ? 'কেনাকাটা শুরু করুন' : 'Start Shopping' }}
      </router-link>
    </div>

    <!-- Active Cart View -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
      <!-- Cart Items List (Left Column) -->
      <div class="lg:col-span-2 flex flex-col gap-4">
        <div class="bg-white border border-gray-200/80 rounded-2xl overflow-hidden shadow-xs">
          <!-- Desktop Header -->
          <div class="hidden sm:grid grid-cols-12 gap-3 px-6 py-3 bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-500 uppercase tracking-wider">
            <div class="col-span-6">{{ locale === 'bn' ? 'প্রোডাক্ট' : 'Product' }}</div>
            <div class="col-span-2 text-center">{{ locale === 'bn' ? 'মূল্য' : 'Price' }}</div>
            <div class="col-span-2 text-center">{{ locale === 'bn' ? 'পরিমাণ' : 'Quantity' }}</div>
            <div class="col-span-2 text-right">{{ locale === 'bn' ? 'মোট' : 'Total' }}</div>
          </div>

          <!-- Items Row -->
          <div class="divide-y divide-gray-150">
            <div
              v-for="item in items"
              :key="item.compositeId || item.id"
              class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center p-4 sm:p-6"
            >
              <!-- Thumbnail & Info -->
              <div class="col-span-1 sm:col-span-6 flex gap-4 items-center">
                <div class="w-16 h-16 rounded-xl bg-gray-50 border border-gray-200 overflow-hidden shrink-0 shadow-2xs">
                  <img v-if="item.imageUrl" :src="item.imageUrl" :alt="item.title" class="w-full h-full object-cover" />
                  <div v-else class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-300">
                    <ShoppingCart class="w-6 h-6" />
                  </div>
                </div>
                <div class="min-w-0">
                  <h3 class="text-sm font-extrabold text-gray-800 line-clamp-2 leading-snug">
                    {{ item.title }}
                  </h3>
                  <!-- Variations badges -->
                  <div v-if="item.color || item.variantStr" class="flex flex-wrap gap-1.5 mt-2">
                    <span v-if="item.color" class="bg-gray-150 text-gray-600 px-2 py-0.5 rounded text-[10px] font-bold">
                      {{ locale === 'bn' ? 'রঙ: ' : 'Color: ' }}{{ item.color }}
                    </span>
                    <span v-if="item.variantStr" class="bg-gray-150 text-gray-600 px-2 py-0.5 rounded text-[10px] font-bold">
                      {{ item.variantStr }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Unit Price -->
              <div class="col-span-1 sm:col-span-2 flex sm:flex-col justify-between sm:justify-center items-center sm:text-center mt-2 sm:mt-0 text-sm">
                <span class="sm:hidden text-xs text-gray-500 font-semibold">{{ locale === 'bn' ? 'ইউনিট মূল্য' : 'Unit Price' }}:</span>
                <span class="font-extrabold text-gray-700">{{ item.price }} {{ t('taka') }}</span>
              </div>

              <!-- Quantity selector -->
              <div class="col-span-1 sm:col-span-2 flex sm:flex-col justify-between sm:justify-center items-center sm:text-center mt-2 sm:mt-0">
                <span class="sm:hidden text-xs text-gray-500 font-semibold">{{ locale === 'bn' ? 'পরিমাণ' : 'Quantity' }}:</span>
                <div class="flex items-center gap-2">
                  <button
                    @click="updateQuantity(item.compositeId || item.id, item.quantity - 1)"
                    class="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100 cursor-pointer active:scale-90 transition-transform"
                  >
                    <Minus class="w-3.5 h-3.5 text-gray-600" />
                  </button>
                  <span class="text-sm font-extrabold w-6 text-center text-gray-800">{{ item.quantity }}</span>
                  <button
                    @click="updateQuantity(item.compositeId || item.id, item.quantity + 1)"
                    class="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100 cursor-pointer active:scale-90 transition-transform"
                  >
                    <Plus class="w-3.5 h-3.5 text-gray-600" />
                  </button>
                </div>
              </div>

              <!-- Total & Remove -->
              <div class="col-span-1 sm:col-span-2 flex sm:flex-col justify-between sm:justify-center items-center sm:text-right mt-2 sm:mt-0 text-sm">
                <span class="sm:hidden text-xs text-gray-500 font-semibold">{{ locale === 'bn' ? 'মোট মূল্য' : 'Total' }}:</span>
                <div class="flex items-center gap-3 w-full sm:justify-end">
                  <span class="font-black text-orange-500">{{ item.price * item.quantity }} {{ t('taka') }}</span>
                  <button
                    @click="removeItem(item.compositeId || item.id)"
                    class="text-red-400 hover:text-red-600 hover:bg-red-50 p-1.5 rounded-lg cursor-pointer transition-colors"
                    aria-label="Remove item"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Order Summary Card (Right Column) -->
      <div class="flex flex-col gap-6">


        <!-- Coupon code section -->
        <div class="bg-white border border-gray-200/80 rounded-2xl p-5 shadow-xs flex flex-col gap-3">
          <h2 class="text-sm font-black text-gray-800 tracking-tight flex items-center gap-2 border-b pb-2">
            <Tag class="w-4 h-4 text-orange-500 shrink-0" />
            {{ locale === 'bn' ? 'প্রোমো কোড / কুপন' : 'Promo Code / Coupon' }}
          </h2>
          
          <div v-if="!isCouponApplied" class="flex gap-2 mt-1">
            <input
              v-model="couponCode"
              type="text"
              class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-xs font-semibold uppercase tracking-wider focus:outline-none focus:border-primary text-gray-800"
              :placeholder="locale === 'bn' ? 'কুপন কোড দিন' : 'Enter coupon code'"
            />
            <button
              @click="applyCoupon"
              class="bg-gray-800 hover:bg-gray-900 text-white font-bold text-xs px-4 py-2 rounded-lg cursor-pointer transition-colors"
            >
              {{ locale === 'bn' ? 'প্রয়োগ' : 'Apply' }}
            </button>
          </div>
          <div v-else class="flex items-center justify-between bg-green-50 text-green-800 px-3 py-2 rounded-lg text-xs font-semibold">
            <span>{{ couponCode.toUpperCase() }} ({{ locale === 'bn' ? '১০% ছাড়' : '10% Discount' }})</span>
            <button @click="removeCoupon" class="text-red-500 hover:text-red-700 font-extrabold cursor-pointer">
              {{ locale === 'bn' ? 'বাতিল' : 'Remove' }}
            </button>
          </div>
        </div>

        <!-- Final Pricing Breakdown -->
        <div class="bg-white border border-gray-200/80 rounded-2xl p-5 shadow-xs flex flex-col gap-4">
          <h2 class="text-sm font-black text-gray-800 tracking-tight border-b pb-2">
            {{ locale === 'bn' ? 'অর্ডার সামারি' : 'Order Summary' }}
          </h2>
          
          <div class="flex flex-col gap-2.5 text-xs text-gray-600 font-bold border-b pb-3 border-dashed">
            <div class="flex justify-between">
              <span>{{ locale === 'bn' ? 'সাবটোটাল' : 'Subtotal' }}</span>
              <span class="text-gray-800">{{ subtotal }} {{ t('taka') }}</span>
            </div>
            
            <div class="flex justify-between items-center text-[10px] text-yellow-600 bg-yellow-50/50 p-2 rounded-lg border border-yellow-150">
              <span class="font-bold">{{ locale === 'bn' ? 'ডেলিভারি চার্জ চেকআউট পেজে হিসাব করা হবে' : 'Shipping fee calculated at checkout' }}</span>
            </div>
            
            <div v-if="isCouponApplied" class="flex justify-between text-green-700 bg-green-50 px-2 py-1.5 rounded">
              <span>{{ locale === 'bn' ? 'ছাড়' : 'Discount' }}</span>
              <span>-{{ couponDiscount }} {{ t('taka') }}</span>
            </div>
          </div>
          
          <!-- Grand Total -->
          <div class="flex justify-between items-center text-gray-800">
            <span class="text-xs font-extrabold">{{ locale === 'bn' ? 'সর্বমোট' : 'Grand Total' }}</span>
            <span class="text-lg font-black text-orange-500">{{ finalTotal }} {{ t('taka') }}</span>
          </div>

          <!-- Checkout Button -->
          <button
            @click="handlePlaceOrder"
            class="w-full bg-primary hover:bg-primary/95 text-white font-extrabold py-3.5 rounded-xl text-center shadow-xs transition-colors cursor-pointer text-sm"
          >
            {{ locale === 'bn' ? 'অর্ডার সম্পন্ন করুন' : 'Confirm Order' }}
          </button>
          
          <!-- Phone Order CTA -->
          <div class="bg-yellow-50/70 border border-yellow-150 rounded-xl p-3 flex items-start gap-3 mt-1">
            <PhoneCall class="w-4 h-4 text-yellow-600 shrink-0 mt-0.5" />
            <div class="flex-1">
              <p class="text-[10px] font-extrabold text-gray-700 leading-tight">
                {{ locale === 'bn' ? 'ফোনে অর্ডার করুন' : 'Order Via Call' }}
              </p>
              <p class="text-[10px] text-gray-500 mt-1 font-bold leading-normal">
                {{ t('call_to_order') }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

