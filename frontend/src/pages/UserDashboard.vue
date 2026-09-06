<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useAuth } from "@/store/auth";
import { useI18n } from "@/lib/i18n";
import { toast } from "@/lib/toast";
import { fetchPurchaseHistory, fetchOrderDetails, fetchOrderItems } from "@/lib/api";
import { usePageTitle } from "@/composables/usePageTitle";
import { pageTitles } from "@/lib/pageTitles";
import {
  User,
  ShoppingBag,
  Tag,
  MapPin,
  Phone,
  Mail,
  Calendar,
  Edit3,
  Save,
  LogOut,
  ArrowRight,
  Gift,
  ShieldCheck,
  CheckCircle2,
  Clock,
  Truck,
  X,
  XCircle,
  Package
} from "@lucide/vue";

usePageTitle(pageTitles.UserDashboard);

const router = useRouter();
const { user, logout } = useAuth();
const { locale } = useI18n();

// If user is null (fallback)
const userName = computed(() => user.value?.name || (locale.value === "bn" ? "ব্যবহারকারী" : "User"));
const userEmail = computed(() => user.value?.email || "user@dealpabo.com");
const userPhone = computed(() => user.value?.phone || "017XXXXXXXX");

// Active Tab State
const activeTab = ref("orders"); // 'orders' | 'profile' | 'coupons'

// Edit Profile States
const isEditing = ref(false);
const editPhone = ref(userPhone.value);
const editAddress = ref(locale.value === "bn" ? "মিরপুর, ঢাকা, বাংলাদেশ" : "Mirpur, Dhaka, Bangladesh");

const handleSaveProfile = () => {
  isEditing.value = false;
  toast({
    title: locale.value === "bn" ? "প্রোফাইল আপডেট হয়েছে" : "Profile Updated",
    description: locale.value === "bn" 
      ? "আপনার প্রোফাইল তথ্য সফলভাবে সংরক্ষণ করা হয়েছে।" 
      : "Your profile information has been successfully saved.",
  });
};

const handleLogoutClick = () => {
  logout();
  router.push("/");
};

// Orders Database (Real backend-fetched history)
const orders = ref([]);
const loadingOrders = ref(false);

const loadOrders = async (silent = false) => {
  if (!user.value?.id) return;
  if (!silent) loadingOrders.value = true;
  try {
    const res = await fetchPurchaseHistory(user.value.id);
    if (res && res.data) {
      orders.value = res.data.map(o => {
        const dStat = o.delivery_status?.toLowerCase() || "pending";
        let icon = Clock;
        
        if (dStat === "delivered") {
          icon = CheckCircle2;
        } else if (dStat === "confirmed") {
          icon = CheckCircle2;
        } else if (dStat === "shipped" || dStat === "on_the_way") {
          icon = Truck;
        } else if (dStat === "picked_up") {
          icon = ShoppingBag;
        } else if (dStat === "cancelled" || dStat === "canceled") {
          icon = XCircle;
        }
        
        return {
          id: o.id,
          code: o.code,
          date: o.date,
          items: o.payment_type, // Displays payment type
          total: o.grand_total,  // Formatted string, e.g. "৳2,500.00"
          status: dStat,
          icon: icon
        };
      });
    }
  } catch (err) {
    console.error("Failed to load purchase history:", err);
  } finally {
    if (!silent) loadingOrders.value = false;
  }
};

const selectedOrder = ref(null);
const orderItems = ref([]);
const loadingOrderDetails = ref(false);
const isOrderDetailsOpen = ref(false);

async function viewOrderDetails(orderId) {
  loadingOrderDetails.value = true;
  isOrderDetailsOpen.value = true;
  selectedOrder.value = null;
  orderItems.value = [];
  try {
    const detailsRes = await fetchOrderDetails(orderId);
    if (detailsRes && detailsRes.data && detailsRes.data.length > 0) {
      selectedOrder.value = detailsRes.data[0];
    }
    const itemsRes = await fetchOrderItems(orderId);
    if (itemsRes && itemsRes.data) {
      orderItems.value = itemsRes.data;
    }
    // Refresh main orders list silently to update statuses in real-time
    loadOrders(true);
  } catch (err) {
    console.error("Failed to load order details:", err);
    toast({
      title: locale.value === "bn" ? "ভুল হয়েছে" : "Error",
      description: locale.value === "bn" ? "অর্ডারের বিবরণ লোড করা যায়নি।" : "Failed to load order details.",
      type: "error"
    });
    isOrderDetailsOpen.value = false;
  } finally {
    loadingOrderDetails.value = false;
  }
}

function closeOrderDetails() {
  isOrderDetailsOpen.value = false;
  selectedOrder.value = null;
  orderItems.value = [];
}

onMounted(() => {
  loadOrders();
});

// Status color classes
const getStatusClass = (status) => {
  switch (status) {
    case "delivered":
      return "bg-green-50 text-green-700 border-green-200";
    case "shipped":
    case "on_the_way":
      return "bg-blue-50 text-blue-700 border-blue-200";
    case "confirmed":
    case "picked_up":
      return "bg-emerald-50 text-emerald-700 border-emerald-200";
    case "pending":
    case "processing":
      return "bg-amber-50 text-amber-700 border-amber-200";
    case "cancelled":
    case "canceled":
      return "bg-red-50 text-red-700 border-red-200";
    default:
      return "bg-gray-50 text-gray-700 border-gray-200";
  }
};

const getStatusLabel = (status) => {
  if (locale.value === "bn") {
    switch (status) {
      case "delivered": return "ডেলিভারড";
      case "shipped": return "শিপড";
      case "on_the_way": return "অন দ্য ওয়ে";
      case "confirmed": return "কনফার্মড";
      case "picked_up": return "পিকড আপ";
      case "pending": return "পেন্ডিং";
      case "processing": return "প্রসেসিং";
      case "cancelled":
      case "canceled": return "বাতিল";
      default: return status.toUpperCase();
    }
  } else {
    switch (status) {
      case "delivered": return "Delivered";
      case "shipped": return "Shipped";
      case "on_the_way": return "On The Way";
      case "confirmed": return "Confirmed";
      case "picked_up": return "Picked Up";
      case "pending": return "Pending";
      case "processing": return "Processing";
      case "cancelled":
      case "canceled": return "Cancelled";
      default: return status.charAt(0).toUpperCase() + status.slice(1);
    }
  }
};

// Local Translations
const tLocal = (key) => {
  const translations = {
    bn: {
      dashboard: "ড্যাশবোর্ড",
      my_orders: "আমার অর্ডারসমূহ",
      profile_info: "প্রোফাইল তথ্য",
      my_coupons: "আমার কুপনসমূহ",
      total_orders: "মোট অর্ডার",
      total_savings: "মোট সাশ্রয়",
      active_coupons: "সক্রিয় কুপন",
      reward_points: "রিওয়ার্ড পয়েন্ট",
      joined: "যুক্ত হয়েছেন",
      customer: "গ্রাহক",
      edit_profile: "প্রোফাইল এডিট",
      save_changes: "পরিবর্তন সংরক্ষণ",
      shipping_address: "ডেলিভারি ঠিকানা",
      phone_number: "মোবাইল নম্বর",
      email_address: "ইমেইল ঠিকানা",
      account_details: "অ্যাকাউন্ট বিবরণী",
      recent_orders: "সাম্প্রতিক অর্ডারসমূহ",
      order_id: "অর্ডার আইডি",
      date: "তারিখ",
      product: "পণ্য",
      total: "সর্বমোট",
      status: "অবস্থা",
      action: "অ্যাকশন",
      track: "ট্র্যাক করুন",
      no_orders: "আপনার কোনো অর্ডার নেই",
      view_details: "বিস্তারিত",
      logout: "লগআউট",
      available_coupons: "আপনার জন্য সেরা কুপন কোডসমূহ",
      copy_code: "কোড কপি করুন",
      copied: "কোড কপি করা হয়েছে!",
      coupon_desc: "আপনার পরবর্তী অর্ডারে অতিরিক্ত ডিসকাউন্ট পেতে ব্যবহার করুন।"
    },
    en: {
      dashboard: "Dashboard",
      my_orders: "My Orders",
      profile_info: "Profile Details",
      my_coupons: "My Coupons",
      total_orders: "Total Orders",
      total_savings: "Total Savings",
      active_coupons: "Active Coupons",
      reward_points: "Reward Points",
      joined: "Joined",
      customer: "Customer",
      edit_profile: "Edit Profile",
      save_changes: "Save Changes",
      shipping_address: "Shipping Address",
      phone_number: "Phone Number",
      email_address: "Email Address",
      account_details: "Account Details",
      recent_orders: "Recent Orders",
      order_id: "Order ID",
      date: "Date",
      product: "Product",
      total: "Total",
      status: "Status",
      action: "Action",
      track: "Track Order",
      no_orders: "You have no orders yet",
      view_details: "Details",
      logout: "Logout",
      available_coupons: "Best Promo Codes For You",
      copy_code: "Copy Code",
      copied: "Copied!",
      coupon_desc: "Use this code during checkout to enjoy exclusive discounts."
    }
  };
  return translations[locale.value]?.[key] || translations["bn"][key] || key;
};

// Copy coupon helper
const copiedCoupon = ref(null);
const copyCoupon = (code) => {
  navigator.clipboard.writeText(code);
  copiedCoupon.value = code;
  toast({
    title: locale.value === "bn" ? "কোড কপি হয়েছে!" : "Code Copied!",
    description: `${code} ${locale.value === "bn" ? "ক্লিপবোর্ডে কপি করা হয়েছে।" : "copied to clipboard."}`
  });
  setTimeout(() => {
    if (copiedCoupon.value === code) copiedCoupon.value = null;
  }, 2000);
};

// Mock Coupons
const coupons = [
  { code: "DEAL50", discount: "50৳", descEn: "Flat 50৳ off on purchases above 500৳", descBn: "৫০০৳ ক্রয়ে ফ্ল্যাট ৫০৳ ছাড়" },
  { code: "FREEHIP", discount: "FREE", descEn: "Free delivery for orders over 1000৳", descBn: "১০০০৳ এর বেশি অর্ডারে ফ্রি ডেলিভারি" }
];
</script>

<template>
  <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 mb-12">
    <!-- Header Hero Banner -->
    <div class="relative bg-white rounded-3xl border border-gray-150 p-6 md:p-8 shadow-xs overflow-hidden flex flex-col md:flex-row items-center md:justify-between gap-6 mb-8">
      <!-- Glow background -->
      <div class="absolute -right-16 -top-16 w-64 h-64 bg-primary/5 rounded-full blur-3xl pointer-events-none" />
      <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-emerald-500/5 rounded-full blur-3xl pointer-events-none" />

      <div class="flex flex-col md:flex-row items-center gap-6 z-10 text-center md:text-left">
        <!-- Avatar Circle -->
        <div class="relative w-24 h-24 rounded-full bg-primary/10 border-4 border-white shadow-md flex items-center justify-center text-primary font-bold text-3xl">
          {{ userName.charAt(0).toUpperCase() }}
          <span class="absolute bottom-0 right-1 w-5 h-5 bg-green-500 border-2 border-white rounded-full" />
        </div>
        
        <div>
          <div class="flex items-center justify-center md:justify-start gap-2">
            <h1 class="text-2xl font-bold text-gray-900 font-display">{{ userName }}</h1>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary border border-primary/20 flex items-center gap-1">
              <ShieldCheck class="w-3 h-3" />
              {{ tLocal('customer') }}
            </span>
          </div>
          <p class="text-xs text-gray-500 mt-1 flex items-center justify-center md:justify-start gap-1">
            <Mail class="w-3.5 h-3.5" /> {{ userEmail }}
          </p>
          <p class="text-xs text-gray-400 mt-1 flex items-center justify-center md:justify-start gap-1">
            <Calendar class="w-3.5 h-3.5" /> {{ tLocal('joined') }}: Jun 2026
          </p>
        </div>
      </div>

      <button
        @click="handleLogoutClick"
        class="shrink-0 flex items-center gap-2 px-5 py-2.5 bg-red-50 text-red-650 hover:bg-red-100 hover:text-red-700 transition-all font-semibold rounded-xl text-xs border border-red-200 cursor-pointer z-10"
      >
        <LogOut class="w-4 h-4" />
        {{ tLocal('logout') }}
      </button>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
      <!-- Stat item 1 -->
      <div class="bg-white p-5 rounded-2xl border border-gray-150 hover:shadow-xs hover:border-primary/20 transition-all flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
          <ShoppingBag class="w-6 h-6" />
        </div>
        <div>
          <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">{{ tLocal('total_orders') }}</p>
          <p class="text-lg font-bold text-gray-900 mt-0.5">{{ orders.length }}</p>
        </div>
      </div>

      <!-- Stat item 2 -->
      <div class="bg-white p-5 rounded-2xl border border-gray-150 hover:shadow-xs hover:border-primary/20 transition-all flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center">
          <Gift class="w-6 h-6" />
        </div>
        <div>
          <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">{{ tLocal('total_savings') }}</p>
          <p class="text-lg font-bold text-gray-900 mt-0.5">{{ locale === 'bn' ? '৳০' : '৳0' }}</p>
        </div>
      </div>

      <!-- Stat item 3 -->
      <div class="bg-white p-5 rounded-2xl border border-gray-150 hover:shadow-xs hover:border-primary/20 transition-all flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center">
          <Tag class="w-6 h-6" />
        </div>
        <div>
          <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">{{ tLocal('active_coupons') }}</p>
          <p class="text-lg font-bold text-gray-900 mt-0.5">02</p>
        </div>
      </div>

      <!-- Stat item 4 -->
      <div class="bg-white p-5 rounded-2xl border border-gray-150 hover:shadow-xs hover:border-primary/20 transition-all flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
          <User class="w-6 h-6" />
        </div>
        <div>
          <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">{{ tLocal('reward_points') }}</p>
          <p class="text-lg font-bold text-gray-900 mt-0.5">{{ locale === 'bn' ? '০ পিটি' : '0 pt' }}</p>
        </div>
      </div>
    </div>

    <!-- Main Navigation Content Tabs -->
    <div class="flex flex-col lg:flex-row gap-8">
      <!-- Left sidebar navigation tabs -->
      <div class="w-full lg:w-64 shrink-0 flex flex-row lg:flex-col gap-2 p-1.5 bg-gray-100/80 rounded-2xl border border-gray-200 overflow-x-auto">
        <button
          @click="activeTab = 'orders'"
          class="flex-1 lg:flex-initial flex items-center justify-center lg:justify-start gap-3 px-4 py-3 rounded-xl text-xs font-semibold transition-all cursor-pointer whitespace-nowrap"
          :class="activeTab === 'orders' ? 'bg-white text-primary shadow-xs' : 'text-gray-600 hover:bg-white/50'"
        >
          <ShoppingBag class="w-4 h-4" />
          {{ tLocal('my_orders') }}
        </button>

        <button
          @click="activeTab = 'profile'"
          class="flex-1 lg:flex-initial flex items-center justify-center lg:justify-start gap-3 px-4 py-3 rounded-xl text-xs font-semibold transition-all cursor-pointer whitespace-nowrap"
          :class="activeTab === 'profile' ? 'bg-white text-primary shadow-xs' : 'text-gray-600 hover:bg-white/50'"
        >
          <User class="w-4 h-4" />
          {{ tLocal('profile_info') }}
        </button>

        <button
          @click="activeTab = 'coupons'"
          class="flex-1 lg:flex-initial flex items-center justify-center lg:justify-start gap-3 px-4 py-3 rounded-xl text-xs font-semibold transition-all cursor-pointer whitespace-nowrap"
          :class="activeTab === 'coupons' ? 'bg-white text-primary shadow-xs' : 'text-gray-600 hover:bg-white/50'"
        >
          <Tag class="w-4 h-4" />
          {{ tLocal('my_coupons') }}
        </button>
      </div>

      <!-- Right Tab Content Panel -->
      <div class="flex-1 bg-white rounded-3xl border border-gray-150 p-6 md:p-8 shadow-xs">
        
        <!-- Tab 1: Orders -->
        <div v-if="activeTab === 'orders'">
          <h2 class="text-lg font-bold text-gray-900 font-display mb-6">{{ tLocal('recent_orders') }}</h2>
          <div v-if="orders.length === 0" class="text-center py-16 flex flex-col items-center justify-center">
            <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 mb-4 border border-gray-150">
              <ShoppingBag class="w-7 h-7" />
            </div>
            <h3 class="text-sm font-bold text-gray-800">{{ tLocal('no_orders') }}</h3>
            <p class="text-xs text-gray-500 mt-1 max-w-xs leading-relaxed">
              {{ locale === 'bn' ? 'আপনি এখনও কোনো অর্ডার করেননি। আমাদের আকর্ষণীয় ডিলগুলো দেখতে শপে যান।' : 'You have not placed any orders yet. Visit our shop to explore hot deals.' }}
            </p>
            <router-link
              to="/products-list"
              class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary/95 text-white transition-all font-semibold rounded-xl text-xs shadow-xs shadow-primary/15 cursor-pointer"
            >
              {{ locale === 'bn' ? 'শপিং শুরু করুন' : 'Start Shopping' }}
              <ArrowRight class="w-3.5 h-3.5" />
            </router-link>
          </div>
          <div v-else class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="border-b border-gray-150 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                  <th class="py-3 px-4">{{ tLocal('order_id') }}</th>
                  <th class="py-3 px-4">{{ tLocal('date') }}</th>
                  <th class="py-3 px-4">{{ tLocal('product') }}</th>
                  <th class="py-3 px-4">{{ tLocal('total') }}</th>
                  <th class="py-3 px-4">{{ tLocal('status') }}</th>
                  <th class="py-3 px-4 text-right">{{ tLocal('action') }}</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="order in orders" :key="order.id" class="text-xs text-gray-700 hover:bg-gray-50/50 transition-colors">
                  <td class="py-4 px-4 font-bold text-primary cursor-pointer hover:underline" @click="viewOrderDetails(order.id)">#{{ order.code }}</td>
                  <td class="py-4 px-4 whitespace-nowrap">{{ order.date }}</td>
                  <td class="py-4 px-4 font-semibold text-gray-800">{{ order.items }}</td>
                  <td class="py-4 px-4 font-bold text-gray-900">{{ order.total }}</td>
                  <td class="py-4 px-4 whitespace-nowrap">
                    <span 
                      class="px-2.5 py-1 rounded-full text-[10px] font-bold border inline-flex items-center gap-1.5"
                      :class="getStatusClass(order.status)"
                    >
                      <component :is="order.icon" class="w-3.5 h-3.5 shrink-0" />
                      {{ getStatusLabel(order.status) }}
                    </span>
                  </td>
                  <td class="py-4 px-4 text-right whitespace-nowrap">
                    <button 
                      @click="viewOrderDetails(order.id)"
                      class="text-primary hover:underline font-bold text-[11px] cursor-pointer flex items-center gap-1 justify-end ml-auto"
                    >
                      {{ tLocal('view_details') }}
                      <ArrowRight class="w-3 h-3" />
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Tab 2: Profile Settings -->
        <div v-else-if="activeTab === 'profile'">
          <div class="flex items-center justify-between mb-8">
            <h2 class="text-lg font-bold text-gray-900 font-display">{{ tLocal('account_details') }}</h2>
            <button
              v-if="!isEditing"
              @click="isEditing = true"
              class="flex items-center gap-1 px-3.5 py-1.5 rounded-lg border border-gray-200 hover:border-primary/30 hover:bg-primary/5 text-primary text-[11px] font-bold transition-all cursor-pointer"
            >
              <Edit3 class="w-3.5 h-3.5" />
              {{ tLocal('edit_profile') }}
            </button>
            <button
              v-else
              @click="handleSaveProfile"
              class="flex items-center gap-1 px-3.5 py-1.5 rounded-lg bg-primary hover:bg-primary/95 text-white text-[11px] font-bold transition-all cursor-pointer shadow-sm shadow-primary/10"
            >
              <Save class="w-3.5 h-3.5" />
              {{ tLocal('save_changes') }}
            </button>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Full Name -->
            <div class="flex flex-col gap-1.5">
              <label class="text-[10px] font-bold text-gray-450 uppercase tracking-wider flex items-center gap-1">
                <User class="w-3.5 h-3.5" /> {{ locale === 'bn' ? 'সম্পূর্ণ নাম' : 'Full Name' }}
              </label>
              <input
                type="text"
                disabled
                :value="userName"
                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-xs font-semibold text-gray-500"
              />
            </div>

            <!-- Email Address -->
            <div class="flex flex-col gap-1.5">
              <label class="text-[10px] font-bold text-gray-450 uppercase tracking-wider flex items-center gap-1">
                <Mail class="w-3.5 h-3.5" /> {{ tLocal('email_address') }}
              </label>
              <input
                type="email"
                disabled
                :value="userEmail"
                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-xs font-semibold text-gray-500"
              />
            </div>

            <!-- Phone Number -->
            <div class="flex flex-col gap-1.5">
              <label class="text-[10px] font-bold text-gray-450 uppercase tracking-wider flex items-center gap-1">
                <Phone class="w-3.5 h-3.5" /> {{ tLocal('phone_number') }}
              </label>
              <input
                type="tel"
                :disabled="!isEditing"
                v-model="editPhone"
                class="w-full px-4 py-2.5 rounded-xl border transition-all text-xs font-semibold outline-none"
                :class="isEditing ? 'border-primary bg-white focus:ring-1 focus:ring-primary text-gray-800' : 'border-gray-200 bg-gray-50 text-gray-500'"
              />
            </div>

            <!-- Shipping Address -->
            <div class="flex flex-col gap-1.5 md:col-span-2">
              <label class="text-[10px] font-bold text-gray-450 uppercase tracking-wider flex items-center gap-1">
                <MapPin class="w-3.5 h-3.5" /> {{ tLocal('shipping_address') }}
              </label>
              <textarea
                rows="3"
                :disabled="!isEditing"
                v-model="editAddress"
                class="w-full px-4 py-2.5 rounded-xl border transition-all text-xs font-semibold outline-none resize-none"
                :class="isEditing ? 'border-primary bg-white focus:ring-1 focus:ring-primary text-gray-800' : 'border-gray-200 bg-gray-50 text-gray-500'"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Tab 3: Coupons -->
        <div v-else-if="activeTab === 'coupons'">
          <h2 class="text-lg font-bold text-gray-900 font-display mb-2">{{ tLocal('available_coupons') }}</h2>
          <p class="text-xs text-gray-500 mb-6">{{ tLocal('coupon_desc') }}</p>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div
              v-for="coupon in coupons"
              :key="coupon.code"
              class="relative bg-gradient-to-br from-emerald-50 to-primary/5 border border-primary/20 rounded-2xl p-5 flex items-center justify-between gap-4 overflow-hidden"
            >
              <!-- Sparkly/Dot background design -->
              <span class="absolute -right-6 -bottom-6 w-20 h-20 bg-primary/10 rounded-full" />
              
              <div class="flex-1">
                <div class="flex items-center gap-2">
                  <span class="px-2 py-0.5 bg-primary text-white font-black text-xs rounded-lg">{{ coupon.discount }}</span>
                  <span class="text-xs font-bold text-gray-700">{{ locale === "bn" ? coupon.descBn : coupon.descEn }}</span>
                </div>
                <p class="text-[10px] text-gray-400 mt-1 font-semibold">{{ coupon.code }}</p>
              </div>

              <button
                @click="copyCoupon(coupon.code)"
                class="shrink-0 px-3.5 py-1.5 rounded-xl bg-white border border-primary/30 hover:bg-primary hover:text-white transition-all text-[11px] font-bold text-primary shadow-xs cursor-pointer"
              >
                {{ copiedCoupon === coupon.code ? tLocal('copied') : tLocal('copy_code') }}
              </button>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Order Details Modal -->
    <div v-if="isOrderDetailsOpen" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
      <!-- Backdrop -->
      <div class="fixed inset-0 bg-black/45 backdrop-blur-xs transition-opacity" @click="closeOrderDetails" />
      
      <!-- Modal Content Wrapper -->
      <div class="relative bg-white rounded-3xl w-full max-w-3xl shadow-[0_20px_50px_rgba(0,0,0,0.15)] overflow-hidden z-10 flex flex-col max-h-[85vh] border border-gray-100">
        
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
          <div>
            <h3 class="text-sm font-extrabold text-gray-800">
              {{ locale === 'bn' ? 'অর্ডার বিবরণী' : 'Order Details' }} 
              <span v-if="selectedOrder" class="text-primary font-black ml-1">#{{ selectedOrder.code }}</span>
            </h3>
            <p v-if="selectedOrder" class="text-[10px] text-gray-500 font-semibold mt-1">
              {{ locale === 'bn' ? 'অর্ডার তারিখ:' : 'Order Date:' }} {{ selectedOrder.date }}
            </p>
          </div>
          <button @click="closeOrderDetails" class="text-gray-400 hover:text-gray-700 p-1.5 hover:bg-gray-100 rounded-full transition-colors cursor-pointer">
            <X class="w-4.5 h-4.5" />
          </button>
        </div>

        <!-- Body -->
        <div class="p-6 overflow-y-auto flex-1 flex flex-col gap-6">
          <div v-if="loadingOrderDetails" class="py-12 text-center text-gray-500 font-bold">
            <div class="w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mx-auto mb-3" />
            {{ locale === 'bn' ? 'লোড হচ্ছে...' : 'Loading...' }}
          </div>
          <div v-else-if="selectedOrder" class="flex flex-col gap-6">
            
            <!-- Details Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <!-- Summary Card -->
              <div class="bg-gray-50/70 rounded-2xl p-4 border border-gray-100 flex flex-col gap-2">
                <h4 class="text-[11px] font-extrabold text-gray-400 uppercase tracking-wider mb-1">
                  {{ locale === 'bn' ? 'সংক্ষিপ্ত তথ্য' : 'Summary' }}
                </h4>
                <div class="text-xs text-gray-700 flex flex-col gap-1.5">
                  <div class="flex justify-between"><span class="text-gray-450 font-semibold">{{ locale === 'bn' ? 'পেমেন্ট পদ্ধতি:' : 'Payment Method:' }}</span> <span class="font-extrabold text-gray-800">{{ selectedOrder.payment_type }}</span></div>
                  <div class="flex justify-between"><span class="text-gray-450 font-semibold">{{ locale === 'bn' ? 'পেমেন্ট স্ট্যাটাস:' : 'Payment Status:' }}</span> 
                    <span class="font-black" :class="selectedOrder.payment_status === 'paid' ? 'text-green-600' : 'text-red-500'">
                      {{ selectedOrder.payment_status_string }}
                    </span>
                  </div>
                  <div class="flex justify-between"><span class="text-gray-450 font-semibold">{{ locale === 'bn' ? 'ডেলিভারি স্ট্যাটাস:' : 'Delivery Status:' }}</span> <span class="font-black text-primary">{{ selectedOrder.delivery_status_string }}</span></div>
                </div>
              </div>

              <!-- Shipping Info Card -->
              <div class="bg-gray-50/70 rounded-2xl p-4 border border-gray-100 flex flex-col gap-2 md:col-span-2">
                <h4 class="text-[11px] font-extrabold text-gray-400 uppercase tracking-wider mb-1">
                  {{ locale === 'bn' ? 'ডেলিভারি ঠিকানা' : 'Shipping Address' }}
                </h4>
                <div v-if="selectedOrder.shipping_address" class="text-xs text-gray-700 flex flex-col gap-1">
                  <p class="font-extrabold text-gray-800">{{ selectedOrder.shipping_address.name }}</p>
                  <p class="text-gray-500 font-semibold flex items-center gap-1 mt-0.5"><Phone class="w-3 h-3 shrink-0" /> {{ selectedOrder.shipping_address.phone }}</p>
                  <p class="text-gray-500 font-semibold flex items-center gap-1"><Mail class="w-3 h-3 shrink-0" v-if="selectedOrder.shipping_address.email" /> {{ selectedOrder.shipping_address.email }}</p>
                  <p class="text-gray-600 font-bold mt-1.5 flex items-start gap-1">
                    <MapPin class="w-3.5 h-3.5 shrink-0 text-gray-400 mt-0.5" /> 
                    <span>
                      {{ selectedOrder.shipping_address.address }}, 
                      {{ selectedOrder.shipping_address.city }}, 
                      {{ selectedOrder.shipping_address.state }}, 
                      {{ selectedOrder.shipping_address.country }}
                      <span v-if="selectedOrder.shipping_address.postal_code"> - {{ selectedOrder.shipping_address.postal_code }}</span>
                    </span>
                  </p>
                </div>
              </div>
            </div>

            <!-- Items Table -->
            <div class="border border-gray-150 rounded-2xl overflow-hidden shadow-2xs">
              <div class="grid grid-cols-12 gap-3 px-4 py-2.5 bg-gray-50 border-b border-gray-150 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                <div class="col-span-6">{{ locale === 'bn' ? 'পণ্য' : 'Product' }}</div>
                <div class="col-span-2 text-center">{{ locale === 'bn' ? 'মূল্য' : 'Price' }}</div>
                <div class="col-span-2 text-center">{{ locale === 'bn' ? 'পরিমাণ' : 'Qty' }}</div>
                <div class="col-span-2 text-right">{{ locale === 'bn' ? 'মোট' : 'Total' }}</div>
              </div>
              <div class="divide-y divide-gray-100">
                <div v-for="item in orderItems" :key="item.id" class="grid grid-cols-12 gap-3 items-center px-4 py-3 text-xs text-gray-700 hover:bg-gray-50/20 transition-colors">
                  <div class="col-span-6 flex items-center gap-3 min-w-0">
                    <img 
                      v-if="item.product_thumbnail_image"
                      :src="item.product_thumbnail_image" 
                      :alt="item.product_name" 
                      class="w-10 h-10 object-cover rounded-lg border border-gray-100 shrink-0 bg-gray-50"
                    />
                    <div v-else class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-100 shrink-0 bg-gray-50">
                      <ShoppingBag class="w-5 h-5 text-gray-300" />
                    </div>
                    <div class="flex flex-col gap-1 min-w-0">
                      <span class="font-extrabold text-gray-800 truncate leading-snug" :title="item.product_name">{{ item.product_name }}</span>
                      <span v-if="item.variation" class="inline-block text-[9px] font-bold bg-gray-100 text-gray-500 rounded px-1.5 py-0.5 w-max">
                        {{ item.variation }}
                      </span>
                    </div>
                  </div>
                  <div class="col-span-2 text-center font-bold text-gray-600">{{ item.price }}</div>
                  <div class="col-span-2 text-center font-extrabold text-gray-800">{{ item.quantity }}</div>
                  <div class="col-span-2 text-right font-black text-gray-900">
                    ৳{{ (parseFloat(item.price.replace(/[^\d.]/g, '')) * item.quantity).toFixed(2) }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Summary Totals -->
            <div class="flex justify-end">
              <div class="w-full sm:w-72 flex flex-col gap-2.5 text-xs text-gray-700 bg-gray-50/30 border border-gray-100 rounded-2xl p-4">
                <div class="flex justify-between"><span class="text-gray-450 font-semibold">{{ locale === 'bn' ? 'উপ-মোট:' : 'Sub Total:' }}</span> <span class="font-bold text-gray-850">{{ selectedOrder.subtotal }}</span></div>
                <div class="flex justify-between"><span class="text-gray-450 font-semibold">{{ locale === 'bn' ? 'ভ্যাট / ট্যাক্স:' : 'Tax:' }}</span> <span class="font-bold text-gray-850">{{ selectedOrder.tax }}</span></div>
                <div class="flex justify-between"><span class="text-gray-450 font-semibold">{{ locale === 'bn' ? 'শিপিং চার্জ:' : 'Shipping Cost:' }}</span> <span class="font-extrabold text-gray-850">{{ selectedOrder.shipping_cost }}</span></div>
                <div v-if="selectedOrder.coupon_discount && parseFloat(selectedOrder.coupon_discount.replace(/[^\d.]/g, '')) > 0" class="flex justify-between text-green-600"><span class="font-semibold">{{ locale === 'bn' ? 'কুপন ডিসকাউন্ট:' : 'Coupon Discount:' }}</span> <span class="font-extrabold">-{{ selectedOrder.coupon_discount }}</span></div>
                <div class="border-t border-gray-150 pt-2.5 flex justify-between text-sm"><span class="font-extrabold text-gray-900">{{ locale === 'bn' ? 'মোট মূল্য:' : 'Grand Total:' }}</span> <span class="font-black text-primary">{{ selectedOrder.grand_total }}</span></div>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<style scoped>
.shadow-xs {
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}
</style>

