<script setup>
import { ref, onMounted, computed, watch } from "vue";
import { useRouter } from "vue-router";
import {
  MapPin,
  CreditCard,
  ChevronRight,
  Plus,
  Trash2,
  Loader2,
  CheckCircle2,
  Truck,
  ChevronDown,
  RotateCcw,
  PhoneCall,
  ArrowLeft,
  ShoppingBag,
  Info,
  Check,
} from "@lucide/vue";
import { useCart } from "@/store/cart";
import { useAuth } from "@/store/auth";
import { useI18n } from "@/lib/i18n";
import { toast } from "@/lib/toast";
import { usePageTitle } from "@/composables/usePageTitle";
import { pageTitles } from "@/lib/pageTitles";
import {
  fetchAddresses,
  createAddress,
  fetchPaymentTypes,
  fetchDBCart,
  deleteDBCartItem,
  addToDBCart,
  syncDBCart,
  updateShippingCost,
  placeOrder,
  fetchCountries,
  fetchStatesByCountry,
  fetchCitiesByState,
} from "@/lib/api";

usePageTitle(pageTitles.Checkout);

const router = useRouter();
const { items, clearCart, subtotal } = useCart();
const { user, isAuthenticated } = useAuth();
const { t, locale } = useI18n();

// Component States
const addresses = ref([]);
const paymentMethods = ref([]);
const selectedAddressId = ref(null);
const selectedAddress = computed(() => {
  const list = Array.isArray(addresses.value) ? addresses.value : [];
  return list.find((a) => a.id === selectedAddressId.value) || null;
});
const selectedPaymentType = ref(null);

const countries = ref([]);
const states = ref([]);
const cities = ref([]);

const selectedCountryId = ref("");
const selectedStateId = ref("");
const selectedCityId = ref("");

const addressText = ref("");
const postalCode = ref("");
const phone = ref("");

// Status variables
const loadingAddresses = ref(false);
const loadingPaymentMethods = ref(false);
const loadingGeo = ref(false);
const savingAddress = ref(false);
const syncingCart = ref(false);
const loadingShippingCost = ref(false);
const placingOrder = ref(false);

const showNewAddressForm = ref(false);
const shippingCost = ref(0);
const shippingArea = ref("inside");
const shippingAreas = [
  { id: "inside", labelKey: "inside_dhaka", price: 70 },
  { id: "outside", labelKey: "outside_dhaka", price: 130 },
];
const selectedArea = computed(
  () =>
    shippingAreas.find((a) => a.id === shippingArea.value) || shippingAreas[0],
);
const areaPrice = computed(() =>
  selectedArea.value ? selectedArea.value.price : 0,
);
const displayShipping = computed(
  () => Number(shippingCost.value || 0) + Number(areaPrice.value || 0),
);
const grandTotal = computed(
  () => Number(subtotal.value || 0) + Number(displayShipping.value || 0),
);
const orderSuccess = ref(false);
const placedCombinedOrderId = ref(null);

// Redirect if guest
onMounted(() => {
  if (!isAuthenticated.value) {
    toast({
      title: locale.value === "bn" ? "লগইন প্রয়োজন" : "Authentication Required",
      description:
        locale.value === "bn"
          ? "চেকআউট করতে প্রথমে লগইন করুন।"
          : "Please log in first to checkout.",
      type: "error",
    });
    router.push("/");
    return;
  }

  if (items.value.length === 0) {
    router.push("/cart");
    return;
  }

  initCheckout();
});

// Initialization
async function initCheckout() {
  await syncCartToDB();
  await loadAddresses();
  await loadPaymentMethods();
  await loadGeoData();
}

// Load Saved Addresses
async function loadAddresses() {
  if (!user.value?.id) return;
  loadingAddresses.value = true;
  try {
    const res = await fetchAddresses(user.value.id);
    let raw = res;
    if (raw && raw.data !== undefined) {
      raw = Array.isArray(raw.data) ? raw.data : (raw.data?.data ?? []);
    }
    addresses.value = Array.isArray(raw) ? raw : [];
    // Auto-select default address if exists
    const defaultAddr =
      addresses.value.find((addr) => addr.set_default === 1) ||
      addresses.value[0];
    if (defaultAddr) {
      selectedAddressId.value = defaultAddr.id;
    }
  } catch (err) {
    console.error("Failed to load addresses:", err);
  } finally {
    loadingAddresses.value = false;
  }
}

// Load Active Payment Methods
async function loadPaymentMethods() {
  loadingPaymentMethods.value = true;
  try {
    const methods = await fetchPaymentTypes();
    paymentMethods.value = methods || [];
    // Auto-select first method (e.g. Cash on Delivery or Bkash)
    if (paymentMethods.value.length > 0) {
      selectedPaymentType.value = paymentMethods.value[0].payment_type_key;
    }
  } catch (err) {
    console.error("Failed to load payment methods:", err);
  } finally {
    loadingPaymentMethods.value = false;
  }
}

// Load Country Geo Data
async function loadGeoData() {
  loadingGeo.value = true;
  try {
    const res = await fetchCountries();
    countries.value = res.data || [];
    // Bangladesh default (usually matches code 'BD' or name 'Bangladesh')
    const bd = countries.value.find(
      (c) => c.name.toLowerCase() === "bangladesh",
    );
    if (bd) {
      selectedCountryId.value = bd.id;
    } else if (countries.value.length > 0) {
      selectedCountryId.value = countries.value[0].id;
    }
  } catch (err) {
    console.error("Failed to load countries:", err);
  } finally {
    loadingGeo.value = false;
  }
}

// Watch Geo Selections
watch(selectedCountryId, async (newVal) => {
  if (!newVal) {
    states.value = [];
    cities.value = [];
    return;
  }
  loadingGeo.value = true;
  try {
    const res = await fetchStatesByCountry(newVal);
    states.value = res.data || [];
    selectedStateId.value = "";
    selectedCityId.value = "";
    cities.value = [];
  } catch (err) {
    console.error("Failed to load states:", err);
  } finally {
    loadingGeo.value = false;
  }
});

watch(selectedStateId, async (newVal) => {
  if (!newVal) {
    cities.value = [];
    return;
  }
  loadingGeo.value = true;
  try {
    const res = await fetchCitiesByState(newVal);
    cities.value = res.data || [];
    selectedCityId.value = "";
  } catch (err) {
    console.error("Failed to load cities:", err);
  } finally {
    loadingGeo.value = false;
  }
});

// Watch address selection to update shipping charge
watch(selectedAddressId, async (newVal) => {
  if (newVal) {
    await updateShippingFee(newVal);
  } else {
    shippingCost.value = 0;
  }
});

// Cart sync routine
async function syncCartToDB() {
  if (!user.value?.id) return;
  syncingCart.value = true;
  try {
    const payloadItems = items.value.map(item => {
      let variant = "";
      if (item.color) {
        variant = item.color;
        if (item.variantStr) {
          variant += "-" + item.variantStr;
        }
      } else {
        variant = item.variantStr || "";
      }
      return {
        id: item.id,
        quantity: item.quantity,
        variant: variant
      };
    });

    await syncDBCart({
      user_id: user.value.id,
      cost_matrix: "1",
      items: payloadItems
    });
  } catch (err) {
    console.error("Cart sync error:", err);
  } finally {
    syncingCart.value = false;
  }
}

// Update shipping fee using selected address ID
async function updateShippingFee(addressId) {
  if (!user.value?.id) return;
  loadingShippingCost.value = true;
  try {
    const res = await updateShippingCost({
      user_id: user.value.id,
      address_id: addressId,
    });
    if (res.result) {
      shippingCost.value = res.value || 0;
    }
  } catch (err) {
    console.error("Failed to calculate shipping fee:", err);
  } finally {
    loadingShippingCost.value = false;
  }
}

// Add New Shipping Address
async function handleCreateAddress() {
  if (!user.value?.id) return;
  if (
    !addressText.value.trim() ||
    !phone.value.trim() ||
    !selectedCountryId.value ||
    !selectedStateId.value ||
    !selectedCityId.value
  ) {
    toast({
      title: locale.value === "bn" ? "ভুল ইনপুট" : "Validation Error",
      description:
        locale.value === "bn"
          ? "অনুগ্রহ করে সকল প্রয়োজনীয় তথ্য পূরণ করুন।"
          : "Please fill out all required fields.",
      type: "error",
    });
    return;
  }

  savingAddress.value = true;
  try {
    const payload = {
      user_id: user.value.id,
      address: addressText.value,
      country_id: selectedCountryId.value,
      state_id: selectedStateId.value,
      city_id: selectedCityId.value,
      postal_code: postalCode.value,
      phone: phone.value,
    };
    const res = await createAddress(payload);
    if (res.result) {
      toast({
        title: locale.value === "bn" ? "ঠিকানা যুক্ত হয়েছে" : "Address Saved",
        description: res.message,
      });
      // Clear fields
      addressText.value = "";
      postalCode.value = "";
      phone.value = "";
      showNewAddressForm.value = false;
      // Reload address list
      await loadAddresses();
    } else {
      toast({
        title: "Error",
        description: res.message || "Failed to save address",
        type: "error",
      });
    }
  } catch (err) {
    console.error("Failed to create address:", err);
  } finally {
    savingAddress.value = false;
  }
}

// Submit Order
async function handleSubmitOrder() {
  if (!user.value?.id) return;
  if (!selectedAddressId.value) {
    toast({
      title: locale.value === "bn" ? "ঠিকানা নির্বাচন করুন" : "Select Address",
      description:
        locale.value === "bn"
          ? "অনুগ্রহ করে একটি ডেলিভারি ঠিকানা নির্বাচন করুন।"
          : "Please select or add a shipping address first.",
      type: "error",
    });
    return;
  }
  if (!selectedPaymentType.value) {
    toast({
      title:
        locale.value === "bn"
          ? "পেমেন্ট পদ্ধতি নির্বাচন করুন"
          : "Select Payment",
      description:
        locale.value === "bn"
          ? "অনুগ্রহ করে একটি পেমেন্ট পদ্ধতি নির্বাচন করুন।"
          : "Please select a payment method.",
      type: "error",
    });
    return;
  }

  placingOrder.value = true;
  try {
    // 1. Sync cart again in case they updated anything
    await syncCartToDB();
    // 2. Set address & calculate shipping again
    await updateShippingFee(selectedAddressId.value);
    // 3. Place order
    const res = await placeOrder({
      user_id: user.value.id,
      payment_type: selectedPaymentType.value,
      shipping_cost: displayShipping.value,
    });

    if (res.result) {
      placedCombinedOrderId.value = res.combined_order_id;
      orderSuccess.value = true;
      clearCart();
      toast({
        title:
          locale.value === "bn" ? "অর্ডার সম্পন্ন হয়েছে!" : "Order Placed!",
        description:
          locale.value === "bn"
            ? "আপনার অর্ডারটি সফলভাবে গৃহীত হয়েছে।"
            : "Your order has been placed successfully.",
      });
    } else {
      toast({
        title: "Failed",
        description: res.message || "Order placement failed",
        type: "error",
      });
    }
  } catch (err) {
    console.error("Failed to place order:", err);
    toast({
      title: "Error",
      description: err.message || "Order placement failed",
      type: "error",
    });
  } finally {
    placingOrder.value = false;
  }
}

// Localized helper
const tLocal = (key) => {
  const dict = {
    bn: {
      checkout: "চেকআউট",
      shipping_address: "ডেলিভারি ঠিকানা",
      select_saved_address: "সংরক্ষিত ঠিকানা নির্বাচন করুন",
      no_saved_addresses:
        "আপনার কোনো সংরক্ষিত ঠিকানা নেই। নিচের ফর্মটি পূরণ করে একটি নতুন ঠিকানা যুক্ত করুন।",
      new_address: "নতুন ঠিকানা যুক্ত করুন",
      street_address: "গ্রাম/রাস্তা/বাসা নম্বর",
      phone: "মোবাইল নম্বর",
      postal_code: "পোস্টাল কোড (ঐচ্ছিক)",
      country: "দেশ",
      division: "বিভাগ/স্টেট",
      district: "জেলা/সিটি",
      save_address: "ঠিকানা সংরক্ষণ করুন",
      payment_method: "পেমেন্ট পদ্ধতি",
      order_summary: "অর্ডার বিবরণী",
      subtotal: "উপ-মোট",
      shipping_cost: "ডেলিভারি চার্জ",
      grand_total: "সর্বমোট",
      confirm_order: "অর্ডার সম্পন্ন করুন",
      processing: "প্রসেসিং হচ্ছে...",
      success_title: "অর্ডার সফল হয়েছে!",
      success_desc:
        "ধন্যবাদ! আপনার অর্ডারটি সফলভাবে আমাদের সিস্টেমে যুক্ত হয়েছে।",
      order_code: "অর্ডার কোড / আইডি",
      back_home: "হোম পেজে ফিরে যান",
      view_orders: "অর্ডার ট্র্যাক করুন",
      delivery_charge_alert:
        "ডেলিভারি চার্জ জেলা ও ওজনের উপর ভিত্তি করে পরিবর্তিত হতে পারে।",
      shipping_area: "শিপিং এরিয়া",
      inside_dhaka: "ঢাকা শহরের ভিতরে",
      outside_dhaka: "ঢাকা শহরের বাইরে",
    },
    en: {
      checkout: "Checkout",
      shipping_address: "Shipping Address",
      select_saved_address: "Select Saved Address",
      no_saved_addresses:
        "You don't have any saved addresses. Fill out the form below to add one.",
      new_address: "Add New Address",
      street_address: "Village/Street/House Name",
      phone: "Mobile Number",
      postal_code: "Postal Code (Optional)",
      country: "Country",
      division: "Division/State",
      district: "District/City",
      save_address: "Save Address",
      payment_method: "Payment Method",
      order_summary: "Order Summary",
      subtotal: "Subtotal",
      shipping_cost: "Shipping Cost",
      grand_total: "Grand Total",
      confirm_order: "Confirm Order",
      processing: "Processing...",
      success_title: "Order Placed Successfully!",
      success_desc:
        "Thank you for shopping! Your order has been placed in our system.",
      order_code: "Order Code / ID",
      back_home: "Go back to Home",
      view_orders: "Track Your Order",
      delivery_charge_alert:
        "Delivery charge may vary depending on district and product weight.",
      shipping_area: "Shipping Area",
      inside_dhaka: "Inside Dhaka City",
      outside_dhaka: "Outside Dhaka City",
    },
  };
  return dict[locale.value]?.[key] || dict["bn"][key] || key;
};
</script>

<template>
  <div class="flex-1 w-full max-w-7xl mx-auto py-4 px-2 sm:px-4 min-h-screen">
    <!-- Success Page Screen -->
    <div
      v-if="orderSuccess"
      class="max-w-2xl mx-auto bg-white rounded-3xl border border-gray-150 p-8 shadow-md text-center py-16 flex flex-col items-center gap-6 my-10"
    >
      <div
        class="w-20 h-20 rounded-full bg-green-50 flex items-center justify-center border border-green-200 text-green-550 animate-bounce"
      >
        <CheckCircle2 class="w-12 h-12" />
      </div>
      <div>
        <h1 class="text-2xl font-black text-gray-900 font-display">
          {{ tLocal("success_title") }}
        </h1>
        <p class="text-sm text-gray-500 mt-2 font-bold">
          {{ tLocal("success_desc") }}
        </p>
      </div>

      <div
        v-if="placedCombinedOrderId"
        class="bg-gray-50 border border-gray-150 rounded-2xl p-4 w-full max-w-md flex flex-col gap-2 text-xs"
      >
        <div class="flex justify-between font-bold text-gray-700">
          <span>{{ tLocal("order_code") }}:</span>
          <span class="text-primary font-black"
            >#{{ placedCombinedOrderId }}</span
          >
        </div>
      </div>

      <div class="flex flex-col sm:flex-row gap-3 w-full max-w-md mt-4">
        <router-link
          to="/dashboard"
          class="flex-1 bg-primary hover:bg-primary/95 text-white font-extrabold py-3.5 rounded-xl text-center shadow-xs transition-colors cursor-pointer text-sm"
        >
          {{ tLocal("view_orders") }}
        </router-link>
        <router-link
          to="/"
          class="flex-1 bg-gray-50 hover:bg-gray-100 text-gray-700 border border-gray-200 font-extrabold py-3.5 rounded-xl text-center transition-colors cursor-pointer text-sm"
        >
          {{ tLocal("back_home") }}
        </router-link>
      </div>
    </div>

    <!-- Main Checkout Page -->
    <div v-else class="flex flex-col gap-6">
      <!-- Breadcrumb -->
      <div class="flex items-center gap-2 text-xs font-semibold text-gray-500">
        <router-link to="/" class="hover:text-primary transition-colors">{{
          t("home")
        }}</router-link>
        <ChevronRight class="w-3.5 h-3.5" />
        <router-link to="/cart" class="hover:text-primary transition-colors">{{
          locale === "bn" ? "কার্ট" : "Cart"
        }}</router-link>
        <ChevronRight class="w-3.5 h-3.5" />
        <span class="text-gray-800">{{ tLocal("checkout") }}</span>
      </div>

      <!-- Loading Screen while syncing cart -->
      <div
        v-if="syncingCart"
        class="text-center py-20 flex flex-col items-center justify-center gap-4"
      >
        <Loader2 class="w-10 h-10 text-primary animate-spin" />
        <p class="text-sm font-extrabold text-gray-500">
          {{ locale === "bn" ? "কার্ট সিঙ্ক হচ্ছে..." : "Syncing cart..." }}
        </p>
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left Column: Address and Payment (8 Columns) -->
        <div class="lg:col-span-8 flex flex-col gap-6">
          <!-- Step 1: Shipping Address -->
          <div
            class="bg-white rounded-3xl border border-gray-150 p-6 md:p-8 shadow-xs"
          >
            <div
              class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-6"
            >
              <div
                class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-black text-sm"
              >
                1
              </div>
              <h2
                class="text-lg font-black text-gray-900 font-display flex items-center gap-2"
              >
                <MapPin class="w-5 h-5 text-primary" />
                {{ tLocal("shipping_address") }}
              </h2>
            </div>

            <!-- Loader -->
            <div v-if="loadingAddresses" class="flex justify-center py-6">
              <Loader2 class="w-6 h-6 text-primary animate-spin" />
            </div>

            <div v-else class="flex flex-col gap-4">
              <!-- Address Selection list -->
              <div v-if="addresses.length > 0" class="flex flex-col gap-3">
                <p class="text-xs font-bold text-gray-500">
                  {{ tLocal("select_saved_address") }}:
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <div
                    v-for="addr in addresses"
                    :key="addr.id"
                    @click="selectedAddressId = addr.id"
                    class="relative border rounded-2xl p-4 flex gap-3 cursor-pointer transition-all hover:shadow-xs"
                    :class="
                      selectedAddressId === addr.id
                        ? 'border-primary bg-primary/5 text-primary shadow-xs'
                        : 'border-gray-200 hover:border-gray-300 text-gray-700'
                    "
                  >
                    <div class="shrink-0 mt-0.5">
                      <div
                        class="w-4.5 h-4.5 rounded-full border flex items-center justify-center"
                        :class="
                          selectedAddressId === addr.id
                            ? 'border-primary bg-primary text-white'
                            : 'border-gray-300 bg-white'
                        "
                      >
                        <Check
                          v-if="selectedAddressId === addr.id"
                          class="w-3.5 h-3.5 stroke-[3]"
                        />
                      </div>
                    </div>
                    <div class="flex-1 text-xs leading-relaxed font-bold">
                      <p class="text-gray-900 font-black">{{ addr.phone }}</p>
                      <p class="text-gray-600 mt-1 font-semibold">
                        {{ addr.address }}
                      </p>
                      <p class="text-gray-400 mt-0.5 text-[10px] font-semibold">
                        {{ addr.city_name }}, {{ addr.state_name }},
                        {{ addr.country_name }}
                      </p>
                      <span
                        v-if="addr.set_default === 1"
                        class="inline-block mt-2 px-2 py-0.5 rounded-full text-[9px] bg-primary/10 text-primary uppercase tracking-wide border border-primary/20"
                      >
                        {{ locale === "bn" ? "ডিফল্ট" : "Default" }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <div
                v-else
                class="text-xs font-semibold text-gray-500 bg-gray-50 border border-gray-150 p-4 rounded-2xl flex items-start gap-2.5"
              >
                <Info class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" />
                <span>{{ tLocal("no_saved_addresses") }}</span>
              </div>

              <!-- Toggles New Address Form -->
              <button
                v-if="!showNewAddressForm"
                @click="showNewAddressForm = true"
                class="w-fit flex items-center gap-2 border border-dashed border-gray-300 hover:border-primary text-gray-600 hover:text-primary transition-all px-4 py-2.5 rounded-xl font-extrabold text-xs cursor-pointer bg-white mt-2"
              >
                <Plus class="w-4 h-4" />
                {{ tLocal("new_address") }}
              </button>

              <!-- Create Address Form -->
              <div
                v-if="showNewAddressForm || addresses.length === 0"
                class="border border-gray-150 rounded-2xl p-5 bg-gray-50/55 flex flex-col gap-4 mt-2"
              >
                <div class="flex items-center justify-between">
                  <h3
                    class="text-xs font-black text-gray-800 uppercase tracking-wider"
                  >
                    {{ tLocal("new_address") }}
                  </h3>
                  <button
                    v-if="addresses.length > 0"
                    @click="showNewAddressForm = false"
                    class="text-xs font-bold text-gray-450 hover:text-gray-700 transition-colors cursor-pointer"
                  >
                    {{ locale === "bn" ? "বাতিল" : "Cancel" }}
                  </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Phone input -->
                  <div class="flex flex-col gap-1">
                    <label
                      class="text-[10px] font-bold text-gray-500 uppercase tracking-wider"
                      >{{ tLocal("phone") }}
                      <span class="text-red-500">*</span></label
                    >
                    <input
                      v-model="phone"
                      type="tel"
                      placeholder="017XXXXXXXX"
                      class="w-full text-xs font-semibold text-gray-800 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3.5 py-2.5 bg-white outline-hidden transition-all"
                    />
                  </div>

                  <!-- Postal code -->
                  <div class="flex flex-col gap-1">
                    <label
                      class="text-[10px] font-bold text-gray-500 uppercase tracking-wider"
                      >{{ tLocal("postal_code") }}</label
                    >
                    <input
                      v-model="postalCode"
                      type="text"
                      placeholder="1200"
                      class="w-full text-xs font-semibold text-gray-800 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3.5 py-2.5 bg-white outline-hidden transition-all"
                    />
                  </div>

                  <!-- Country Select -->
                  <div class="flex flex-col gap-1">
                    <label
                      class="text-[10px] font-bold text-gray-500 uppercase tracking-wider"
                      >{{ tLocal("country") }}
                      <span class="text-red-500">*</span></label
                    >
                    <select
                      v-model="selectedCountryId"
                      class="w-full text-xs font-semibold text-gray-800 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3.5 py-2.5 bg-white outline-hidden transition-all"
                    >
                      <option value="" disabled>
                        {{
                          locale === "bn" ? "নির্বাচন করুন" : "Select Country"
                        }}
                      </option>
                      <option v-for="c in countries" :key="c.id" :value="c.id">
                        {{ c.name }}
                      </option>
                    </select>
                  </div>

                  <!-- Division/State Select -->
                  <div class="flex flex-col gap-1">
                    <label
                      class="text-[10px] font-bold text-gray-500 uppercase tracking-wider"
                      >{{ tLocal("division") }}
                      <span class="text-red-500">*</span></label
                    >
                    <select
                      v-model="selectedStateId"
                      :disabled="!selectedCountryId || loadingGeo"
                      class="w-full text-xs font-semibold text-gray-800 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3.5 py-2.5 bg-white outline-hidden transition-all disabled:bg-gray-100 disabled:cursor-not-allowed"
                    >
                      <option value="">
                        {{
                          locale === "bn"
                            ? "বিভাগ নির্বাচন করুন"
                            : "Select Division"
                        }}
                      </option>
                      <option v-for="s in states" :key="s.id" :value="s.id">
                        {{ s.name }}
                      </option>
                    </select>
                  </div>

                  <!-- City/District Select -->
                  <div class="flex flex-col gap-1 md:col-span-2">
                    <label
                      class="text-[10px] font-bold text-gray-500 uppercase tracking-wider"
                      >{{ tLocal("district") }}
                      <span class="text-red-500">*</span></label
                    >
                    <select
                      v-model="selectedCityId"
                      :disabled="!selectedStateId || loadingGeo"
                      class="w-full text-xs font-semibold text-gray-800 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3.5 py-2.5 bg-white outline-hidden transition-all disabled:bg-gray-100 disabled:cursor-not-allowed"
                    >
                      <option value="">
                        {{
                          locale === "bn"
                            ? "জেলা নির্বাচন করুন"
                            : "Select District"
                        }}
                      </option>
                      <option v-for="c in cities" :key="c.id" :value="c.id">
                        {{ c.name }}
                      </option>
                    </select>
                  </div>

                  <!-- Street address -->
                  <div class="flex flex-col gap-1 md:col-span-2">
                    <label
                      class="text-[10px] font-bold text-gray-500 uppercase tracking-wider"
                      >{{ tLocal("street_address") }}
                      <span class="text-red-500">*</span></label
                    >
                    <textarea
                      v-model="addressText"
                      rows="2"
                      placeholder="e.g. House 12, Road 5, Mirpur-10"
                      class="w-full text-xs font-semibold text-gray-800 border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3.5 py-2.5 bg-white outline-hidden transition-all resize-none"
                    ></textarea>
                  </div>
                </div>

                <button
                  @click="handleCreateAddress"
                  :disabled="savingAddress"
                  class="w-full bg-primary hover:bg-primary/95 text-white font-extrabold py-3.5 rounded-xl text-center shadow-xs transition-colors cursor-pointer text-xs mt-2 disabled:bg-primary/75 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                  <Loader2 v-if="savingAddress" class="w-4 h-4 animate-spin" />
                  {{ tLocal("save_address") }}
                </button>
              </div>
            </div>
          </div>

          <!-- Selected Delivery Address (read-only summary box) -->
          <div
            v-if="selectedAddress"
            class="bg-white rounded-2xl border border-primary/30 bg-primary/5 p-4 flex gap-3 items-start shadow-xs"
          >
            <div class="shrink-0 mt-0.5">
              <div
                class="w-5 h-5 rounded-full bg-primary text-white flex items-center justify-center"
              >
                <Check class="w-3.5 h-3.5 stroke-[3]" />
              </div>
            </div>
            <div class="flex-1 text-xs leading-relaxed">
              <p
                class="text-[10px] font-black uppercase tracking-wider text-primary mb-1"
              >
                {{ locale === "bn" ? "নির্বাচিত ঠিকানা" : "Selected Address" }}
              </p>
              <p class="text-gray-900 font-black">
                {{ selectedAddress.address }}
              </p>
              <p class="text-gray-500 mt-0.5 font-semibold">
                {{ selectedAddress.city_name }},
                {{ selectedAddress.state_name }},
                {{ selectedAddress.country_name }}
              </p>
              <p class="text-gray-400 mt-0.5 text-[10px] font-semibold">
                {{ selectedAddress.phone }}
              </p>
            </div>
          </div>

          <!-- Step 2: Payment Method Selection -->
          <div
            class="bg-white rounded-3xl border border-gray-150 p-6 md:p-8 shadow-xs"
          >
            <div
              class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-6"
            >
              <div
                class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-black text-sm"
              >
                2
              </div>
              <h2
                class="text-lg font-black text-gray-900 font-display flex items-center gap-2"
              >
                <CreditCard class="w-5 h-5 text-primary" />
                {{ tLocal("payment_method") }}
              </h2>
            </div>

            <!-- Loader -->
            <div v-if="loadingPaymentMethods" class="flex justify-center py-6">
              <Loader2 class="w-6 h-6 text-primary animate-spin" />
            </div>

            <div v-else class="flex flex-col gap-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                <div
                  v-for="method in paymentMethods"
                  :key="method.payment_type_key"
                  @click="selectedPaymentType = method.payment_type_key"
                  class="relative border rounded-2xl p-4 flex flex-col items-center text-center gap-3 cursor-pointer transition-all hover:shadow-xs group"
                  :class="
                    selectedPaymentType === method.payment_type_key
                      ? 'border-primary bg-primary/5 text-primary shadow-xs'
                      : 'border-gray-200 hover:border-gray-300 text-gray-700'
                  "
                >
                  <div class="absolute top-2.5 right-2.5">
                    <div
                      class="w-4 h-4 rounded-full border flex items-center justify-center"
                      :class="
                        selectedPaymentType === method.payment_type_key
                          ? 'border-primary bg-primary text-white'
                          : 'border-gray-300 bg-white'
                      "
                    >
                      <Check
                        v-if="selectedPaymentType === method.payment_type_key"
                        class="w-2.5 h-2.5 stroke-[3]"
                      />
                    </div>
                  </div>
                  <img
                    :src="method.image"
                    :alt="method.name"
                    class="h-10 object-contain w-fit"
                  />
                  <span class="text-xs font-black text-gray-800">{{
                    method.name
                  }}</span>
                </div>
              </div>

              <!-- Display Details of Selected Payment Mode -->
              <div
                v-if="selectedPaymentType"
                class="mt-4 border border-gray-150 rounded-2xl p-4 bg-gray-50/55 text-xs"
              >
                <div
                  v-for="method in paymentMethods"
                  :key="method.payment_type_key"
                >
                  <div
                    v-if="selectedPaymentType === method.payment_type_key"
                    class="flex flex-col gap-2 font-bold text-gray-600"
                  >
                    <p
                      class="text-gray-900 font-extrabold flex items-center gap-1.5 text-xs"
                    >
                      <Info class="w-4 h-4 text-primary shrink-0" />
                      {{ method.title }}
                    </p>
                    <div
                      v-if="method.details"
                      class="mt-2 text-[11px] leading-relaxed text-gray-500 font-medium whitespace-pre-wrap animate-fade-in"
                      v-html="method.details"
                    ></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column: Order Summary & Confirm (4 Columns) -->
        <div class="lg:col-span-4 flex flex-col gap-6">
          <div
            class="bg-white rounded-3xl border border-gray-150 p-6 md:p-8 shadow-xs sticky top-4"
          >
            <h2
              class="text-base font-black text-gray-900 font-display border-b border-gray-100 pb-3.5 mb-4 flex items-center gap-2"
            >
              <ShoppingBag class="w-5 h-5 text-primary" />
              {{ tLocal("order_summary") }}
            </h2>

            <!-- Items summary -->
            <div
              class="flex flex-col gap-4 max-h-[220px] overflow-y-auto pr-1 border-b border-gray-100 pb-4 mb-4"
            >
              <div
                v-for="item in items"
                :key="item.compositeId || item.id"
                class="flex gap-3 items-center"
              >
                <img
                  :src="item.imageUrl"
                  :alt="item.title"
                  class="w-12 h-12 object-cover rounded-xl border border-gray-150 shrink-0"
                />
                <div class="flex-1 text-xs">
                  <h4
                    class="font-extrabold text-gray-800 line-clamp-1 leading-tight"
                  >
                    {{ item.title }}
                  </h4>
                  <div
                    class="flex items-center gap-1 text-[10px] text-gray-400 mt-1 font-semibold"
                  >
                    <span
                      v-if="item.color"
                      class="px-1.5 py-0.5 rounded-sm bg-gray-100"
                      >{{ item.color }}</span
                    >
                    <span
                      v-if="item.variantStr"
                      class="px-1.5 py-0.5 rounded-sm bg-gray-100"
                      >{{ item.variantStr }}</span
                    >
                    <span class="ml-auto font-black text-gray-650"
                      >x{{ item.quantity }}</span
                    >
                  </div>
                </div>
                <div class="text-xs font-black text-gray-900 whitespace-nowrap">
                  {{ (item.price * item.quantity).toLocaleString() }}
                  {{ t("taka") }}
                </div>
              </div>
            </div>

            <!-- Totals -->
            <div
              class="flex flex-col gap-2.5 border-b border-gray-100 pb-4 mb-4 text-xs font-bold text-gray-500"
            >
              <div class="flex justify-between items-center">
                <span>{{ tLocal("subtotal") }}</span>
                <span class="text-gray-800"
                  >{{ subtotal.toLocaleString() }} {{ t("taka") }}</span
                >
              </div>
              <div class="flex flex-col gap-1.5">
                <label
                  class="text-[10px] font-black uppercase tracking-wider text-gray-500"
                  >{{ tLocal("shipping_area") }}</label
                >
                <div class="relative">
                  <Truck
                    class="w-4 h-4 text-primary absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"
                  />
                  <select
                    v-model="shippingArea"
                    class="w-full appearance-none cursor-pointer pl-9 pr-9 py-2.5 text-xs font-extrabold text-gray-800 bg-white border-2 border-gray-200 hover:border-primary/40 rounded-xl shadow-xs hover:shadow-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"
                  >
                    <option
                      v-for="area in shippingAreas"
                      :key="area.id"
                      :value="area.id"
                    >
                      {{ tLocal(area.labelKey) }} — ৳{{ area.price }}
                    </option>
                  </select>
                  <ChevronDown
                    class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"
                  />
                </div>
                <p class="text-[10px] font-bold text-primary">
                  {{
                    selectedArea
                      ? tLocal(selectedArea.labelKey) +
                        " (" +
                        selectedArea.price +
                        " " +
                        t("taka") +
                        ")"
                      : ""
                  }}
                </p>
              </div>
              <div class="flex justify-between items-center">
                <span>{{ tLocal("shipping_cost") }}</span>
                <span v-if="loadingShippingCost" class="text-gray-850"
                  ><Loader2
                    class="w-3.5 h-3.5 animate-spin text-primary inline"
                /></span>
                <span v-else class="text-gray-850 font-black">{{
                  displayShipping > 0
                    ? displayShipping + " " + t("taka")
                    : "0 " + t("taka")
                }}</span>
              </div>
              <p
                class="text-[10px] text-yellow-600 bg-yellow-50 border border-yellow-150 p-2 rounded-lg font-semibold leading-normal flex items-start gap-1"
              >
                <Info class="w-3.5 h-3.5 text-yellow-600 shrink-0 mt-0.5" />
                <span>{{ tLocal("delivery_charge_alert") }}</span>
              </p>
            </div>

            <!-- Grand Total -->
            <div class="flex justify-between items-center mb-5">
              <span class="text-xs font-black text-gray-800">{{
                tLocal("grand_total")
              }}</span>
              <span class="text-lg font-black text-orange-500"
                >{{ grandTotal.toLocaleString() }} {{ t("taka") }}</span
              >
            </div>

            <!-- Submit Button -->
            <button
              @click="handleSubmitOrder"
              :disabled="placingOrder || loadingShippingCost || syncingCart"
              class="w-full bg-primary hover:bg-primary/95 text-white font-extrabold py-4 rounded-xl text-center shadow-xs transition-colors cursor-pointer text-sm disabled:bg-primary/75 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              <Loader2 v-if="placingOrder" class="w-4 h-4 animate-spin" />
              {{
                placingOrder ? tLocal("processing") : tLocal("confirm_order")
              }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

