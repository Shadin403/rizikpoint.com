<script setup>


import { ref, onMounted, computed, watch } from "vue";


import { useRoute, useRouter } from "vue-router";


import {


  ArrowLeft,


  Star,


  ShoppingBasket,


  ShoppingBag,


  Pen,


  Package,


  Truck,


  RotateCcw,


  Shield,


  ChevronLeft,


  ChevronRight,


  Share2,


  Info,


  Edit2,


  Trash2,


  X,


  MessageCircle,


  Check,


} from "@lucide/vue";


import {


  fetchDealById,


  fetchDeals,


  fetchVariantPrice,


  fetchDeliveryInfos,


  fetchProductReviews,


  submitProductReview,


  updateProductReview,


  deleteProductReview,


} from "@/lib/api";


import { useCart } from "@/store/cart";


import { useI18n } from "@/lib/i18n";


import { toast } from "@/lib/toast";


import { useAuth } from "@/store/auth";


import { animateFlyToCart } from "@/lib/cart-fly";


import ProductCard from "@/components/shared/ProductCard.vue";


import SkeletonLoader from "@/components/shared/SkeletonLoader.vue";


import { usePageTitle } from "@/composables/usePageTitle";
import { pageTitles } from "@/lib/pageTitles";

usePageTitle(pageTitles.DealDetail);

const route = useRoute();


const router = useRouter();


const { addItem, openCart } = useCart();


const { user, isAuthenticated, openAuth } = useAuth();


const { locale, t } = useI18n();





const deal = ref(null);


const allDeals = ref([]);


const isLoading = ref(false);


const quantity = ref(1);


const activeImageIndex = ref(0);


const isAdded = ref(false);





// Delivery / shipping / return-policy rows driven by admin's Delivery Info page.


const deliverySettings = ref([]);





function humanizeKey(key) {


  return key


    .replace(/[_-]+/g, " ")


    .replace(/\s+/g, " ")


    .trim()


    .replace(/\b\w/g, (c) => c.toUpperCase());


}





function describeKey(key) {


  const k = key.toLowerCase();


  if (


    k.includes("shipping") ||


    k.includes("delivery_charge") ||


    k.includes("charge")


  )


    return { prefix: "৳", suffix: "", icon: "truck" };


  if (k.includes("return") || k.includes("refund") || k.includes("warranty"))


    return { prefix: "", suffix: "days", icon: "rotate" };


  if (k.includes("threshold") || k.includes("min_order"))


    return { prefix: "৳", suffix: "", icon: "truck" };


  return { prefix: "", suffix: "", icon: "info" };


}





async function loadStorefrontSettings() {


  try {


    const list = await fetchDeliveryInfos();


    if (!Array.isArray(list) || list.length === 0) return;


    const rows = list


      .filter(


        (x) =>


          x &&


          x.type &&


          x.value !== "" &&


          x.value !== null &&


          x.value !== undefined,


      )


      .map((x) => {


        const meta = describeKey(x.type);


        return {


          key: x.type,


          label: humanizeKey(x.type),


          value: x.value,


          prefix: meta.prefix,


          suffix: meta.suffix,


          icon: meta.icon,


        };


      });


    deliverySettings.value = rows;


  } catch (_) {


    /* keep defaults */


  }


}





const selectedColor = ref(null); // color object: { name, code }


const selectedOptions = ref({}); // map: { attribute_name: value }


const variantPriceInfo = ref(null); // { price, price_string, stock, image }


const activeTab = ref("description"); // 'description' or 'return'





// ── Resolve image URL from backend path ─────────────────────────────────────


const BACKEND_ORIGIN =


  import.meta.env.VITE_BACKEND_ORIGIN || "http://127.0.0.1:8000";


function resolveImage(path) {


  if (!path) return null;


  if (path.startsWith("http://") || path.startsWith("https://")) return path;


  const clean = path.startsWith("/") ? path.slice(1) : path;


  return `${BACKEND_ORIGIN}/${clean}`;


}





// ── All product images (main + extras from photos[]) ──────────────────────


const productImages = computed(() => {


  if (!deal.value) return [];


  const imgs = [];


  if (deal.value.imageUrl) imgs.push(deal.value.imageUrl);


  if (deal.value.photos?.length) {


    deal.value.photos.forEach((p) => {


      const u = resolveImage(p.path ?? p);


      if (u && !imgs.includes(u)) imgs.push(u);


    });


  }


  return imgs;


});





const activeImage = computed(() => {


  if (variantPriceInfo.value?.image) {


    return resolveImage(variantPriceInfo.value.image);


  }


  return productImages.value[activeImageIndex.value] ?? null;


});





// ── Load product detail ────────────────────────────────────────────────────


const dealSlugOrId = computed(() => route.params.slug || route.params.id || "");





async function loadDealDetail() {


  if (!dealSlugOrId.value) return;


  isLoading.value = true;


  deal.value = null;


  activeImageIndex.value = 0;


  quantity.value = 1;


  selectedColor.value = null;


  selectedOptions.value = {};


  variantPriceInfo.value = null;


  try {


    deal.value = await fetchDealById(dealSlugOrId.value);


    const list = await fetchDeals();


    allDeals.value = list;





    // Default selections


    if (deal.value?.colors?.length > 0) {


      selectedColor.value = deal.value.colors[0];


    }


    if (deal.value?.choice_options?.length > 0) {


      deal.value.choice_options.forEach((opt) => {


        if (opt.options?.length > 0) {


          selectedOptions.value[opt.name] = opt.options[0];


        }


      });


    }





    // Trigger variant calculation on initial load


    updateDynamicVariantInfo();


  } catch (err) {


    console.error("Failed to load product detail:", err);


  } finally {


    isLoading.value = false;


  }


}





onMounted(() => {


  loadDealDetail();


  loadReviews();


  loadStorefrontSettings();


});





// Reviews state & CRUD


const reviews = ref([]);


const reviewMeta = ref(null);


const isReviewLoad = ref(false);


const reviewPage = ref(1);


const reviewTotalPages = computed(() =>


  Math.max(


    1,


    Math.ceil(


      (reviewMeta.value && reviewMeta.value.total


        ? reviewMeta.value.total


        : 0) /


        (reviewMeta.value && reviewMeta.value.per_page


          ? reviewMeta.value.per_page


          : 10),


    ),


  ),


);


const reviewBreakdown = computed(() =>


  reviewMeta.value && reviewMeta.value.rating_breakdown


    ? reviewMeta.value.rating_breakdown


    : { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 },


);


const reviewAverage = computed(() =>


  reviewMeta.value && reviewMeta.value.average ? reviewMeta.value.average : 0,


);


const myExistingReview = computed(


  () => reviews.value.find((r) => r.is_mine) || null,


);





const showReviewForm = ref(false);


const editingReview = ref(null);


const reviewForm = ref({ rating: 5, comment: "" });


const isSubmittingReview = ref(false);


async function loadReviews() {


  if (!deal.value || !deal.value.id) return;


  isReviewLoad.value = true;


  try {


    const res = await fetchProductReviews(deal.value.id, reviewPage.value);


    reviews.value = res.data || [];


    reviewMeta.value = res.meta || null;


  } catch (err) {


    console.warn("Failed to load reviews", err);


  } finally {


    isReviewLoad.value = false;


  }


}





watch(


  () => deal.value && deal.value.id,


  (id) => {


    if (id && (!reviews.value.length || reviewMeta.value === null)) {


      loadReviews();


    }


  },


  { immediate: true },


);





function openReviewForm(review) {


  if (!isAuthenticated.value) {


    openAuth("login");


    toast({


      title: locale.value === "bn" ? "লগইন প্রয়োজন" : "Login required",


    });


    return;


  }


  editingReview.value = review || null;


  reviewForm.value = {


    rating: review ? review.rating : 5,


    comment: review ? review.comment : "",


  };


  showReviewForm.value = true;


}


function closeReviewForm() {


  showReviewForm.value = false;


  editingReview.value = null;


  reviewForm.value = { rating: 5, comment: "" };


}


async function submitReview() {


  if (!isAuthenticated.value) {


    openAuth("login");


    return;


  }


  if (!deal.value || !deal.value.id) return;


  if (reviewForm.value.rating < 1 || reviewForm.value.rating > 5) return;


  isSubmittingReview.value = true;


  try {


    if (editingReview.value) {


      await updateProductReview(editingReview.value.id, {


        rating: reviewForm.value.rating,


        comment: reviewForm.value.comment,


      });


      toast({


        title: locale.value === "bn" ? "রিভিউ আপডেট হয়েছে" : "Review updated",


      });


    } else {


      await submitProductReview({


        productId: deal.value.id,


        userId: user.value && user.value.id,


        rating: reviewForm.value.rating,


        comment: reviewForm.value.comment,


      });


      toast({


        title: locale.value === "bn" ? "রিভিউ জমা হয়েছে" : "Review submitted",


      });


    }


    closeReviewForm();


    await loadReviews();


    await loadDealDetail();


  } catch (err) {


    const fieldErrors =


      err && err.data && err.data.errors


        ? Object.values(err.data.errors).flat().join(" ")


        : "";


    const desc = (err && err.message) || fieldErrors || "";


    toast({


      title: locale.value === "bn" ? "সমস্যা হয়েছে" : "Something went wrong",


      description: desc,


    });


  } finally {


    isSubmittingReview.value = false;


  }


}


async function removeReview(review) {


  if (!isAuthenticated.value) {


    openAuth("login");


    return;


  }


  const ok = window.confirm(


    locale.value === "bn"


      ? "আপনি কি নিশ্চিত যে রিভিউটি মুছে ফেলতে চান?"


      : "Delete this review?",


  );


  if (!ok) return;


  try {


    await deleteProductReview(review.id);


    toast({


      title:


        locale.value === "bn" ? "রিভিউ মুছে ফেলা হয়েছে" : "Review deleted",


    });


    await loadReviews();


    await loadDealDetail();


  } catch (err) {


    const fieldErrors =


      err && err.data && err.data.errors


        ? Object.values(err.data.errors).flat().join(" ")


        : "";


    const desc = (err && err.message) || fieldErrors || "";


    toast({


      title: locale.value === "bn" ? "মুছতে সমস্যা হয়েছে" : "Delete failed",


      description: desc,


    });


  }


}


function changeReviewPage(p) {


  if (p < 1 || p > reviewTotalPages.value || p === reviewPage.value) return;


  reviewPage.value = p;


  loadReviews();


}





watch(


  () => [route.params.slug, route.params.id],


  () => {


    reviews.value = [];


    reviewMeta.value = null;


    reviewPage.value = 1;


    loadDealDetail();


    loadReviews();


  },


);


// ── Update variant info based on selections ───────────────────────────────


async function updateDynamicVariantInfo() {


  if (!deal.value) return;


  const productId = deal.value.id;


  const colorHex = selectedColor.value ? selectedColor.value.code : "";


  const optionsArray = Object.values(selectedOptions.value);





  const info = await fetchVariantPrice(productId, colorHex, optionsArray);


  if (info) {


    variantPriceInfo.value = info;


  }


}





// ── Computed active stock count ───────────────────────────────────────────


const activeStock = computed(() => {


  return variantPriceInfo.value


    ? variantPriceInfo.value.stock


    : (deal.value?.current_stock ?? 0);


});





// ── Related products ─────────────────────────────────────────────────────


const related = computed(() => {


  if (!deal.value) return [];


  return allDeals.value


    .filter(


      (d) => d.id !== deal.value.id && d.categoryId === deal.value.categoryId,


    )


    .slice(0, 8);


});





// ── Savings ──────────────────────────────────────────────────────────────


const savings = computed(() => {


  if (


    deal.value?.originalPrice &&


    deal.value?.discountedPrice &&


    deal.value.discountedPrice < deal.value.originalPrice


  ) {


    return Math.round(deal.value.originalPrice - deal.value.discountedPrice);


  }


  return null;


});





// ── Star rating ──────────────────────────────────────────────────────────


const starRating = computed(() => Math.round(deal.value?.rating ?? 0));





// ── Clean and resolve relative images/broken icons in HTML ───────────────


const cleanedDescription = computed(() => {


  if (!deal.value?.description) return "";


  let html = deal.value.description;





  // 1. Replace broken ajkerdeal checkmark arrows with a premium inline green checkmark SVG


  const arrowSvg = `<span class="inline-flex items-center justify-center mr-2 text-green-500" style="display: inline-flex; vertical-align: middle;"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg></span>`;





  html = html.replace(/<img[^>]*dealdetails_arrow\.svg[^>]*>/gi, arrowSvg);





  // 2. Resolve relative image sources


  html = html.replace(


    /(src=")(uploads\/|uploads\/all\/)/gi,


    `$1${BACKEND_ORIGIN}/$2`,


  );


  html = html.replace(


    /(src=")\/(uploads\/|uploads\/all\/)/gi,


    `$1${BACKEND_ORIGIN}/$2`,


  );





  return html;


});





// ── Format price ─────────────────────────────────────────────────────────


function fmt(n) {


  return Number(n || 0).toLocaleString("en-BD");


}





// ── Share ─────────────────────────────────────────────────────────────────


function handleShare() {


  if (navigator.share) {


    navigator.share({ title: deal.value?.title, url: window.location.href });


  } else {


    navigator.clipboard.writeText(window.location.href);


    toast({


      title: locale.value === "bn" ? "লিঙ্ক কপি হয়েছে!" : "Link copied!",


    });


  }


}





// ── Add to cart ────────────────────────────────────────────────────────────


function handleAddToCart(event) {


  if (!deal.value) return;





  if (activeStock.value <= 0) {


    toast({


      title: locale.value === "bn" ? "স্টক নেই!" : "Out of Stock!",


      description:


        locale.value === "bn"


          ? "দুঃখিত, এই প্রোডাক্টটি বর্তমানে স্টকে নেই।"


          : "Sorry, this product is currently out of stock.",


      type: "error",


    });


    return;


  }





  const price = variantPriceInfo.value


    ? variantPriceInfo.value.price


    : (deal.value.discountedPrice ?? deal.value.originalPrice ?? 0);


  const color = selectedColor.value ? selectedColor.value.name : "";


  const optionsArray = Object.values(selectedOptions.value);


  const variantStr = optionsArray.join("-");





  const imgUrl = variantPriceInfo.value?.image


    ? resolveImage(variantPriceInfo.value.image)


    : resolveImage(deal.value.imageUrl);





  addItem({


    id: deal.value.id,


    title: deal.value.title,


    imageUrl: imgUrl,


    price: price,


    color: color,


    variantStr: variantStr,


    quantity: quantity.value,


  });





  toast({ title: t("item_added"), description: t("item_added_desc") });


  animateFlyToCart(event, imgUrl);





  isAdded.value = true;


  setTimeout(() => {


    isAdded.value = false;


  }, 1800);


}





// ── Order now ──────────────────────────────────────────────────────────────


function handleOrder() {


  if (!deal.value) return;





  if (activeStock.value <= 0) {


    toast({


      title: locale.value === "bn" ? "স্টক নেই!" : "Out of Stock!",


      description:


        locale.value === "bn"


          ? "দুঃখিত, এই প্রোডাক্টটি বর্তমানে স্টকে নেই।"


          : "Sorry, this product is currently out of stock.",


      type: "error",


    });


    return;


  }





  const price = variantPriceInfo.value


    ? variantPriceInfo.value.price


    : (deal.value.discountedPrice ?? deal.value.originalPrice ?? 0);


  const color = selectedColor.value ? selectedColor.value.name : "";


  const optionsArray = Object.values(selectedOptions.value);


  const variantStr = optionsArray.join("-");





  addItem({


    id: deal.value.id,


    title: deal.value.title,


    imageUrl: variantPriceInfo.value?.image


      ? resolveImage(variantPriceInfo.value.image)


      : (deal.value.imageUrl ?? null),


    price: price,


    color: color,


    variantStr: variantStr,


    quantity: quantity.value,


  });





  openCart();


}


</script>





<template>


  <div class="pb-10 bg-gray-50 min-h-screen overflow-x-hidden">


    <!-- Back nav -->


    <div class="sticky top-0 z-20 bg-white border-b border-gray-200 shadow-xs">


      <div


        class="flex items-center justify-between px-4 py-3 max-w-5xl mx-auto"


      >


        <button


          @click="router.back()"


          class="flex items-center gap-1.5 text-gray-600 hover:text-green-600 text-sm font-semibold transition-colors cursor-pointer"


        >


          <ArrowLeft class="w-4 h-4" />


          {{ locale === "bn" ? "পেছনে" : "Back" }}


        </button>


        <button


          v-if="deal"


          @click="handleShare"


          class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-green-600 transition-colors cursor-pointer"


        >


          <Share2 class="w-4 h-4" />


        </button>


      </div>


    </div>





    <!-- Loading Skeleton -->


    <div v-if="isLoading" class="px-4 pt-4 max-w-5xl mx-auto">


      <SkeletonLoader type="details" />


    </div>





    <!-- Not Found -->


    <div


      v-else-if="!deal"


      class="flex flex-col items-center justify-center py-24 px-4 text-center"


    >


      <Package class="w-16 h-16 text-gray-200 mb-4" />


      <h1 class="text-xl font-bold text-gray-700 mb-2">


        {{ locale === "bn" ? "পণ্যটি পাওয়া যায়নি" : "Product Not Found" }}


      </h1>


      <p class="text-sm text-gray-400 mb-6">


        {{


          locale === "bn"


            ? "এই পণ্যটি হয়তো মুছে ফেলা হয়েছে বা পাওয়া যাচ্ছে না।"


            : "This product may have been removed or is unavailable."


        }}


      </p>


      <router-link


        to="/products-list"


        class="bg-green-600 hover:bg-green-700 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition-colors"


      >


        ← {{ locale === "bn" ? "সকল পণ্য দেখুন" : "View all products" }}


      </router-link>


    </div>





    <!-- Product Detail -->


    <div v-else class="max-w-5xl mx-auto px-2 pt-5 space-y-5">


      <!-- ── Main card ── -->


      <div


        class="bg-white rounded-2xl shadow-xs border border-gray-100 overflow-hidden"


      >


        <div class="grid grid-cols-1 md:grid-cols-2 gap-0">


          <!-- Image column -->


          <div


            class="relative bg-gray-50 min-w-0 border-b md:border-b-0 md:border-r border-gray-100"


          >


            <!-- Main image -->


            <div class="aspect-square relative overflow-hidden">


              <img


                v-if="activeImage"


                :src="activeImage"


                :alt="deal.title"


                class="w-full h-full object-cover"


                loading="eager"


              />


              <div


                v-else


                class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200"


              >


                <ShoppingBag class="w-20 h-20 text-gray-300" />


              </div>





              <!-- Discount badge -->


              <div


                v-if="deal.discountPercent > 0"


                class="absolute top-3 right-3 z-10"


              >


                <div


                  class="bg-red-600 text-white font-extrabold px-3 py-1.5 rounded-xl text-xs shadow-lg flex items-center gap-1"


                >


                  <span>-{{ deal.discountPercent }}%</span>


                </div>


              </div>





              <!-- Arrow nav for multiple images -->


              <template v-if="productImages.length > 1">


                <button


                  @click="


                    activeImageIndex =


                      (activeImageIndex - 1 + productImages.length) %


                      productImages.length


                  "


                  class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-black/25 hover:bg-black/50 text-white rounded-full flex items-center justify-center backdrop-blur-sm cursor-pointer"


                >


                  <ChevronLeft class="w-4 h-4" />


                </button>


                <button


                  @click="


                    activeImageIndex =


                      (activeImageIndex + 1) % productImages.length


                  "


                  class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-black/25 hover:bg-black/50 text-white rounded-full flex items-center justify-center backdrop-blur-sm cursor-pointer"


                >


                  <ChevronRight class="w-4 h-4" />


                </button>


              </template>


            </div>





            <!-- Thumbnail strip -->


            <div


              v-if="productImages.length > 1"


              class="flex gap-2 p-3 overflow-x-auto hide-scrollbar border-t border-gray-100 bg-white"


            >


              <button


                v-for="(img, i) in productImages"


                :key="i"


                @click="activeImageIndex = i"


                class="shrink-0 w-14 h-14 rounded-lg overflow-hidden border-2 transition-all cursor-pointer"


                :class="


                  activeImageIndex === i


                    ? 'border-green-500'


                    : 'border-gray-200 hover:border-gray-400'


                "


              >


                <img :src="img" class="w-full h-full object-cover" />


              </button>


            </div>


          </div>





          <!-- Info column -->


          <div class="p-5 md:p-7 flex flex-col justify-between min-w-0">


            <div>


              <!-- Store & Category badges -->


              <div class="flex items-center flex-wrap gap-2 mb-3">


                <span


                  v-if="deal.storeName"


                  class="bg-green-50 text-green-700 text-[10px] font-bold px-2.5 py-1 rounded-lg border border-green-200 uppercase tracking-wider"


                >


                  {{ deal.storeName }}


                </span>


                <span


                  v-if="deal.categoryName"


                  class="bg-gray-100 text-gray-600 text-[10px] font-semibold px-2.5 py-1 rounded-lg"


                >


                  {{ deal.categoryName }}


                </span>


              </div>





              <!-- Product name -->


              <h1


                class="text-lg sm:text-2xl font-bold text-gray-800 leading-snug mb-4 font-display"


              >


                {{ deal.title }}


              </h1>





              <!-- Star rating -->


              <div class="flex items-center gap-1.5 mb-5">


                <div class="flex gap-0.5">


                  <Star


                    v-for="i in 5"


                    :key="i"


                    class="w-4 h-4"


                    :class="


                      i <= starRating


                        ? 'fill-yellow-400 text-yellow-400'


                        : 'fill-gray-200 text-gray-200'


                    "


                  />


                </div>


                <span class="text-xs text-gray-500 font-semibold">


                  {{


                    deal.rating > 0


                      ? deal.rating.toFixed(1)


                      : locale === "bn"


                        ? "কোনো রেটিং নেই"


                        : "No ratings yet"


                  }}


                  <span v-if="deal.numOfSale > 0" class="ml-1 text-gray-400"


                    >({{ deal.numOfSale }}


                    {{ locale === "bn" ? "বিক্রি" : "sold" }})</span


                  >


                </span>


              </div>





              <!-- Price box -->


              <div


                class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-4 mb-5 border border-green-100"


              >


                <div class="flex items-baseline gap-2 flex-wrap">


                  <span


                    class="text-3xl font-extrabold text-green-700 font-display"


                  >


                    {{


                      variantPriceInfo


                        ? variantPriceInfo.price_string


                        : "৳" + fmt(deal.discountedPrice ?? deal.originalPrice)


                    }}


                  </span>


                  <span


                    v-if="!variantPriceInfo && savings"


                    class="text-sm text-gray-400 line-through font-mono"


                  >


                    ৳{{ fmt(deal.originalPrice) }}


                  </span>


                </div>


                <div


                  v-if="!variantPriceInfo && savings"


                  class="flex items-center gap-2 mt-1.5"


                >


                  <span


                    class="bg-red-100 text-red-600 font-bold text-xs px-2 py-0.5 rounded-lg"


                  >


                    {{


                      locale === "bn"


                        ? `৳${fmt(savings)} সাশ্রয়!`


                        : `Save ৳${fmt(savings)}!`


                    }}


                  </span>


                  <span class="text-xs text-gray-400"


                    >(-{{ deal.discountPercent }}%)</span


                  >


                </div>


                <!-- Dynamic Stock status with indicator dot -->


                <div class="mt-2.5 flex items-center gap-1.5">


                  <span


                    class="inline-block w-2.5 h-2.5 rounded-full"


                    :class="


                      activeStock > 0


                        ? 'bg-green-500 animate-pulse'


                        : 'bg-red-500'


                    "


                  ></span>


                  <span


                    class="text-xs font-semibold"


                    :class="activeStock > 0 ? 'text-green-700' : 'text-red-600'"


                  >


                    {{


                      activeStock > 0


                        ? locale === "bn"


                          ? `স্টকে আছে (${activeStock} টি)`


                          : `In Stock (${activeStock} pcs)`


                        : locale === "bn"


                          ? "স্টক নেই"


                          : "Out of Stock"


                    }}


                  </span>


                </div>


              </div>





              <!-- Colors Selector -->


              <div v-if="deal.colors && deal.colors.length > 0" class="mb-5">


                <span class="text-xs font-bold text-gray-700 block mb-2">


                  {{ locale === "bn" ? "রঙ নির্বাচন করুন:" : "Select Color:" }}


                  <span class="text-green-600 font-semibold ml-1">{{


                    selectedColor?.name


                  }}</span>


                </span>


                <div class="flex flex-wrap gap-2.5">


                  <button


                    v-for="color in deal.colors"


                    :key="color.code"


                    @click="


                      selectedColor = color;


                      updateDynamicVariantInfo();


                    "


                    class="w-8 h-8 rounded-full border-2 transition-all relative flex items-center justify-center cursor-pointer hover:scale-110 active:scale-95 shadow-sm"


                    :style="{ backgroundColor: color.code }"


                    :class="


                      selectedColor?.code === color.code


                        ? 'border-green-600 ring-2 ring-green-100'


                        : 'border-gray-300 hover:border-gray-400'


                    "


                    :title="color.name"


                  >


                    <!-- White dot for selected color -->


                    <span


                      v-if="selectedColor?.code === color.code"


                      class="w-2 h-2 rounded-full"


                      :class="


                        color.code.toLowerCase() === '#ffffff'


                          ? 'bg-black'


                          : 'bg-white'


                      "


                    />


                  </button>


                </div>


              </div>





              <!-- Choice Options (Attributes) -->


              <div


                v-if="deal.choice_options && deal.choice_options.length > 0"


                class="space-y-4 mb-5"


              >


                <div


                  v-for="opt in deal.choice_options"


                  :key="opt.name"


                  class="flex flex-col"


                >


                  <span class="text-xs font-bold text-gray-700 block mb-2">


                    {{ opt.title }}:


                    <span class="text-green-600 font-semibold ml-1">{{


                      selectedOptions[opt.name]


                    }}</span>


                  </span>


                  <div class="flex flex-wrap gap-2">


                    <button


                      v-for="val in opt.options"


                      :key="val"


                      @click="


                        selectedOptions[opt.name] = val;


                        updateDynamicVariantInfo();


                      "


                      class="px-3.5 py-1.5 rounded-xl border text-xs font-bold transition-all cursor-pointer hover:bg-gray-50 active:scale-95"


                      :class="


                        selectedOptions[opt.name] === val


                          ? 'border-green-600 bg-green-50 text-green-700 shadow-sm ring-2 ring-green-50'


                          : 'border-gray-200 text-gray-600 hover:border-gray-300 bg-white'


                      "


                    >


                      {{ val }}


                    </button>


                  </div>


                </div>


              </div>





              <!-- Quantity selector -->


              <div class="flex items-center gap-3 mb-5">


                <span class="text-xs font-bold text-gray-600">{{


                  locale === "bn" ? "পরিমাণ:" : "Qty:"


                }}</span>


                <div


                  class="flex items-center border border-gray-200 rounded-xl overflow-hidden bg-white"


                >


                  <button


                    @click="quantity = Math.max(1, quantity - 1)"


                    class="w-9 h-9 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer font-bold text-lg"


                  >


                    −


                  </button>


                  <span


                    class="w-10 text-center font-bold text-gray-800 text-sm select-none"


                    >{{ quantity }}</span


                  >


                  <button


                    @click="quantity++"


                    class="w-9 h-9 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer font-bold text-lg"


                  >


                    +


                  </button>


                </div>


              </div>





              <!-- Delivery info (dynamic, driven by admin Delivery Info page) -->


              <div


                v-if="deliverySettings.length > 0"


                class="rounded-xl overflow-hidden border border-gray-200 mb-6 text-xs"


              >


                <div


                  v-for="(row, idx) in deliverySettings"


                  :key="row.key"


                  class="flex items-center gap-2 px-4 py-2.5"


                  :class="


                    idx < deliverySettings.length - 1


                      ? 'border-b border-gray-100'


                      : idx === 0


                        ? 'bg-gray-50/50'


                        : ''


                  "


                >


                  <Truck


                    v-if="row.icon === 'truck'"


                    class="w-3.5 h-3.5 text-green-600 shrink-0"


                  />


                  <RotateCcw


                    v-else-if="row.icon === 'rotate'"


                    class="w-3.5 h-3.5 text-orange-500 shrink-0"


                  />


                  <Info v-else class="w-3.5 h-3.5 text-gray-500 shrink-0" />


                  <span class="text-gray-600 font-medium flex-1">{{


                    row.label


                  }}</span>


                  <span


                    class="font-bold font-mono"


                    :class="


                      row.icon === 'rotate' ? 'text-green-700' : 'text-gray-800'


                    "


                  >


                    <template v-if="row.prefix">{{ row.prefix }}</template


                    >{{ row.value


                    }}<template v-if="row.suffix">


                      {{ locale === "bn" ? "দিন" : row.suffix }}</template


                    >


                  </span>


                </div>


              </div>


            </div>





            <!-- Action buttons -->


            <div class="flex flex-col sm:flex-row gap-3">


              <button


                @click="handleOrder"


                class="flex-1 flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white font-bold py-3.5 px-4 rounded-xl transition-colors shadow-sm hover:shadow-md cursor-pointer text-sm"


              >


                <ShoppingBasket class="w-5 h-5" />


                {{ t("order_now") }}


              </button>


              <button


                @click="handleAddToCart"


                class="flex-1 flex items-center justify-center gap-2 border-2 font-bold py-3.5 px-4 rounded-xl transition-all duration-300 cursor-pointer text-sm relative overflow-hidden"


                :class="isAdded 


                  ? 'bg-primary border-primary text-white scale-[0.98]' 


                  : 'border-green-600 text-green-700 hover:bg-green-50 bg-white hover:scale-[1.02] active:scale-[0.97]'


                "


                :disabled="isAdded"


              >


                <transition name="btn-text-slide" mode="out-in">


                  <div v-if="isAdded" class="flex items-center justify-center gap-1.5" key="added">


                    <Check class="w-5 h-5 text-white animate-bounce-short" />


                    <span>{{ locale === 'bn' ? 'যোগ করা হয়েছে!' : 'Added!' }}</span>


                  </div>


                  <div v-else class="flex items-center justify-center gap-1.5" key="add">


                    <ShoppingBag class="w-5 h-5" />


                    <span>{{ t("add_to_cart") }}</span>


                  </div>


                </transition>


              </button>


            </div>


          </div>


        </div>


      </div>





      <!-- ── Description & Return Policy Tabs ── -->


      <div


        class="bg-white rounded-2xl shadow-xs border border-gray-100 overflow-hidden"


      >


        <!-- Tabs Header -->


        <div


          class="flex items-center justify-center gap-4 py-4 border-b border-gray-100 bg-gray-50/30"


        >


          <button


            @click="activeTab = 'description'"


            class="px-5 py-2 text-xs font-bold transition-all duration-200 cursor-pointer rounded-full"


            :class="


              activeTab === 'description'


                ? 'bg-green-600 text-white shadow-sm'


                : 'text-gray-600 hover:text-gray-900 bg-transparent'


            "


          >


            {{ locale === "bn" ? "পণ্যের বিস্তারিত" : "Product Details" }}


          </button>


          <button


            @click="activeTab = 'return'"


            class="px-5 py-2 text-xs font-bold transition-all duration-200 cursor-pointer rounded-full"


            :class="


              activeTab === 'return'


                ? 'bg-green-600 text-white shadow-sm'


                : 'text-gray-600 hover:text-gray-900 bg-transparent'


            "


          >


            {{ locale === "bn" ? "রিটার্ন পলিসি" : "Return Policy" }}


          </button>


        </div>





        <!-- Tab Content -->


        <div class="p-5 md:p-7 flow-root">


          <!-- Description Tab -->


          <div


            v-show="activeTab === 'description'"


            class="text-sm text-gray-600 leading-relaxed"


          >


            <div v-if="deal.description" v-html="cleanedDescription" />


            <div v-else class="text-center py-6 text-gray-400">


              {{


                locale === "bn"


                  ? "কোনো বিবরণ পাওয়া যায়নি।"


                  : "No description available."


              }}


            </div>


          </div>





          <!-- Return Policy Tab -->


          <div


            v-show="activeTab === 'return'"


            class="text-sm text-gray-600 leading-relaxed"


          >


            <div class="space-y-3">


              <h3 class="font-bold text-gray-800 mb-2">


                {{


                  locale === "bn"


                    ? "ফেরত এবং পরিবর্তন নীতি"


                    : "Return & Exchange Policy"


                }}


              </h3>


              <div


                v-if="deal && deal.returnPolicy && deal.returnPolicy.trim()"


                v-html="deal.returnPolicy"


                class="prose prose-sm max-w-none text-gray-700"


              ></div>


              <ul v-else class="list-disc pl-5 space-y-2">


                <template v-if="locale === 'bn'">


                  <li>


                    ডেলিভারি পাোয়ার পর পণ্যটি ত্রুটিপূর্ণ বা ক্ষতিগ্রস্ত হলে ৩


                    দিনের মধ্যে পরিবর্তন বা ফেরত নেওয়া যাবে।


                  </li>


                  <li>


                    ব্যবহারের চিহ্নযুক্ত বা শারীরিকভাবে ক্ষতিগ্রস্ত পণ্য ফেরত বা


                    পরিবর্তনের জন্য প্রযোজ্য হবে না।


                  </li>


                  <li>


                    পণ্য ফেরতের সময় মূল বক্স, প্যাকেজিং এবং ক্যাশ মেমো থাকতে


                    হবে।


                  </li>


                  <li>


                    কোনো পণ্য পরিবর্নের ক্ষেত্রে ডেলিভারি চার্জ প্রযোজ্য হতে


                    পারে।


                  </li>


                </template>


                <template v-else>


                  <li>


                    If the product is defective or damaged upon delivery, you


                    can return or exchange it within 7 days.


                  </li>


                  <li>


                    Products showing signs of use or physical damage are not


                    eligible for return or exchange.


                  </li>


                  <li>


                    Original box, packaging, and invoice must be returned along


                    with the product.


                  </li>


                  <li>


                    Delivery charges may apply for exchanges unless the return


                    is due to our error.


                  </li>


                </template>


              </ul>


            </div>


          </div>


        </div>


      </div>





      <!-- ── Trust badges ── -->


      <div class="grid grid-cols-3 gap-3">


        <div


          class="bg-white rounded-xl shadow-xs border border-gray-100 p-3 flex flex-col items-center text-center gap-1.5"


        >


          <Shield class="w-6 h-6 text-green-600" />


          <span class="text-[10px] font-bold text-gray-700">{{


            locale === "bn" ? "নিরাপদ পেমেন্ট" : "Secure Payment"


          }}</span>


        </div>


        <div


          class="bg-white rounded-xl shadow-xs border border-gray-100 p-3 flex flex-col items-center text-center gap-1.5"


        >


          <Truck class="w-6 h-6 text-blue-500" />


          <span class="text-[10px] font-bold text-gray-700">{{


            locale === "bn" ? "দ্রুত ডেলিভারি" : "Fast Delivery"


          }}</span>


        </div>


        <div


          class="bg-white rounded-xl shadow-xs border border-gray-100 p-3 flex flex-col items-center text-center gap-1.5"


        >


          <RotateCcw class="w-6 h-6 text-orange-500" />


          <span class="text-[10px] font-bold text-gray-700">{{


            locale === "bn" ? "৭ দিন রিটার্ন" : "7-Day Return"


          }}</span>


        </div>


      </div>





      <!-- ── Ratings & Reviews ── -->


      <div


        class="bg-white rounded-2xl shadow-xs border border-gray-100 p-5 md:p-7"


      >


        <h2


          class="font-display font-bold text-gray-800 text-base mb-5 pb-2 border-b border-gray-100"


        >


          {{ t("ratings_reviews_title") }}


        </h2>





        <!-- Summary + breakdown bars -->


        <div class="flex flex-col sm:flex-row gap-6 mb-5">


          <div


            class="flex flex-col items-center justify-center w-full sm:w-28 shrink-0 bg-gray-50 rounded-2xl py-5 border border-gray-100"


          >


            <span class="text-4xl font-extrabold text-gray-800 font-display">


              {{


                reviewAverage > 0


                  ? Number(reviewAverage).toFixed(1)


                  : deal.rating > 0


                    ? deal.rating.toFixed(1)


                    : "0.0"


              }}


            </span>


            <div class="flex mt-1.5 gap-0.5">


              <Star


                v-for="i in 5"


                :key="i"


                class="w-3.5 h-3.5"


                :class="


                  i <=


                  Math.round(


                    reviewAverage > 0 ? reviewAverage : deal.rating || 0,


                  )


                    ? 'fill-yellow-400 text-yellow-400'


                    : 'fill-gray-200 text-gray-200'


                "


              />


            </div>


            <span class="text-[10px] text-gray-500 mt-1 font-medium"


              >{{ reviewMeta && reviewMeta.total ? reviewMeta.total : 0 }}


              {{ t("total_ratings") }}</span


            >


          </div>


          <div class="flex-1 flex flex-col gap-2">


            <div


              v-for="n in [5, 4, 3, 2, 1]"


              :key="n"


              class="flex items-center gap-2"


            >


              <span class="text-xs text-gray-500 w-3 font-medium">{{ n }}</span>


              <Star class="w-3 h-3 fill-yellow-400 text-yellow-400 shrink-0" />


              <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">


                <div


                  class="h-full bg-yellow-400 rounded-full"


                  :style="


                    'width:' +


                    ((reviewBreakdown[n] || 0) /


                      Math.max(


                        1,


                        reviewMeta && reviewMeta.total ? reviewMeta.total : 0,


                      )) *


                      100 +


                    '%'


                  "


                />


              </div>


              <span class="text-xs text-gray-500 w-4 text-right">{{


                reviewBreakdown[n] || 0


              }}</span>


            </div>


          </div>


        </div>





        <!-- Action button: edit own review if exists, else write new -->


        <button


          v-if="!myExistingReview"


          @click="openReviewForm(null)"


          class="w-full flex items-center justify-center gap-2 bg-gray-900 hover:bg-gray-800 text-white font-bold py-3 rounded-xl transition-colors cursor-pointer text-sm"


        >


          <Pen class="w-4 h-4" />


          {{ t("write_review") }}


        </button>


        <div v-else class="flex gap-2">


          <button


            @click="openReviewForm(myExistingReview)"


            class="flex-1 flex items-center justify-center gap-2 bg-gray-900 hover:bg-gray-800 text-white font-bold py-3 rounded-xl transition-colors cursor-pointer text-sm"


          >


            <Edit2 class="w-4 h-4" />


            {{ locale === "bn" ? "আপনার রিভিউ সম্পাদনা" : "Edit your review" }}


          </button>


          <button


            @click="removeReview(myExistingReview)"


            class="flex items-center justify-center gap-2 bg-red-50 hover:bg-red-100 text-red-600 font-bold py-3 px-4 rounded-xl transition-colors cursor-pointer text-sm border border-red-200"


            :title="locale === 'bn' ? 'মুছুন' : 'Delete'"


          >


            <Trash2 class="w-4 h-4" />


          </button>


        </div>





        <!-- Reviews list -->


        <div class="mt-6 space-y-4">


          <div


            v-if="isReviewLoad"


            class="text-center text-xs text-gray-400 py-4"


          >


            <span class="animate-pulse">{{


              locale === "bn" ? "লোড হচ্ছে..." : "Loading..."


            }}</span>


          </div>


          <div


            v-else-if="reviews.length === 0"


            class="text-center text-xs text-gray-400 py-6 border border-dashed border-gray-200 rounded-xl"


          >


            <MessageCircle class="w-6 h-6 mx-auto mb-1 text-gray-300" />


            {{


              locale === "bn"


                ? "এখনো কোনো রিভিউ নেই। আপনিই প্রথম হোন!"


                : "No reviews yet. Be the first!"


            }}


          </div>


          <div


            v-for="r in reviews"


            :key="r.id"


            class="border border-gray-100 rounded-xl p-4"


          >


            <div class="flex items-center gap-3 mb-2">


              <img


                v-if="r.avatar"


                :src="r.avatar"


                :alt="r.user_name"


                class="w-9 h-9 rounded-full object-cover border border-gray-200"


              />


              <div


                v-else


                class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-sm font-bold text-primary"


              >


                {{ (r.user_name || "?").charAt(0) }}


              </div>


              <div class="flex-1 min-w-0">


                <p class="text-sm font-bold text-gray-800 truncate">


                  {{ r.user_name }}


                </p>


                <div class="flex items-center gap-1.5">


                  <div class="flex gap-0.5">


                    <Star


                      v-for="i in 5"


                      :key="i"


                      class="w-3 h-3"


                      :class="


                        i <= r.rating


                          ? 'fill-yellow-400 text-yellow-400'


                          : 'fill-gray-200 text-gray-200'


                      "


                    />


                  </div>


                  <span class="text-[10px] text-gray-400">• {{ r.time }}</span>


                </div>


              </div>


              <div v-if="r.is_mine" class="flex items-center gap-1">


                <button


                  @click="openReviewForm(r)"


                  class="p-1.5 rounded-md text-gray-500 hover:text-primary hover:bg-gray-100"


                  :title="locale === 'bn' ? 'সম্পাদনা' : 'Edit'"


                >


                  <Edit2 class="w-3.5 h-3.5" />


                </button>


                <button


                  @click="removeReview(r)"


                  class="p-1.5 rounded-md text-gray-500 hover:text-red-600 hover:bg-red-50"


                  :title="locale === 'bn' ? 'মুছুন' : 'Delete'"


                >


                  <Trash2 class="w-3.5 h-3.5" />


                </button>


              </div>


            </div>


            <p


              v-if="r.comment"


              class="text-sm text-gray-700 leading-relaxed whitespace-pre-line"


            >


              {{ r.comment }}


            </p>


            <p v-else class="text-xs text-gray-400 italic">


              {{ locale === "bn" ? "কোনো মন্তব্য নেই" : "No comment" }}


            </p>


          </div>





          <!-- Pagination -->


          <div


            v-if="reviewTotalPages > 1"


            class="flex items-center justify-center gap-1 pt-2"


          >


            <button


              @click="changeReviewPage(reviewPage - 1)"


              :disabled="reviewPage <= 1"


              class="h-8 px-3 rounded-lg border border-gray-200 text-xs font-bold text-gray-600 hover:border-green-500 hover:text-green-600 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"


            >


              Prev


            </button>


            <span class="text-xs text-gray-500 px-2"


              >{{ reviewPage }} / {{ reviewTotalPages }}</span


            >


            <button


              @click="changeReviewPage(reviewPage + 1)"


              :disabled="reviewPage >= reviewTotalPages"


              class="h-8 px-3 rounded-lg border border-gray-200 text-xs font-bold text-gray-600 hover:border-green-500 hover:text-green-600 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"


            >


              Next


            </button>


          </div>


        </div>


      </div>





      <!-- ── Review Form Modal ── -->


      <transition name="fade">


        <div


          v-if="showReviewForm"


          class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm"


          @click.self="closeReviewForm"


        >


          <div


            class="bg-white w-full sm:max-w-md rounded-t-2xl sm:rounded-2xl shadow-2xl p-5 md:p-6"


          >


            <div class="flex items-center justify-between mb-4">


              <h3 class="font-display font-bold text-lg text-gray-800">


                {{


                  editingReview


                    ? locale === "bn"


                      ? "রিভিউ সম্পাদনা"


                      : "Edit review"


                    : locale === "bn"


                      ? "রিভিউ লিখুন"


                      : "Write a review"


                }}


              </h3>


              <button


                @click="closeReviewForm"


                class="p-1.5 rounded-md hover:bg-gray-100 text-gray-500 cursor-pointer"


              >


                <X class="w-4 h-4" />


              </button>


            </div>


            <div class="mb-4">


              <label


                class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider"


                >{{ locale === "bn" ? "আপনার রেটিং" : "Your rating" }}</label


              >


              <div class="flex items-center gap-1">


                <button


                  v-for="i in 5"


                  :key="i"


                  type="button"


                  @click="reviewForm.rating = i"


                  class="p-1 cursor-pointer transition-transform hover:scale-110"


                >


                  <Star


                    class="w-7 h-7"


                    :class="


                      i <= reviewForm.rating


                        ? 'fill-yellow-400 text-yellow-400'


                        : 'fill-gray-200 text-gray-200'


                    "


                  />


                </button>


                <span class="ml-2 text-sm font-bold text-gray-700"


                  >{{ reviewForm.rating }} / 5</span


                >


              </div>


            </div>


            <div class="mb-4">


              <label


                class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider"


                >{{


                  locale === "bn" ? "মন্তব্য (ঐচ্ছিক)" : "Comment (optional)"


                }}</label


              >


              <textarea


                v-model="reviewForm.comment"


                rows="4"


                maxlength="1000"


                :placeholder="


                  locale === 'bn'


                    ? 'আপনার অভিজ্ঞতা লিখুন...'


                    : 'Share your experience...'


                "


                class="w-full border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 resize-none"


              ></textarea>


            </div>


            <div class="flex items-center gap-2">


              <button


                type="button"


                @click="closeReviewForm"


                class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 text-sm font-bold hover:bg-gray-50 cursor-pointer"


              >


                {{ locale === "bn" ? "বাতিল" : "Cancel" }}


              </button>


              <button


                type="button"


                @click="submitReview"


                :disabled="isSubmittingReview"


                class="flex-1 py-2.5 rounded-xl bg-gray-900 hover:bg-gray-800 text-white text-sm font-bold disabled:opacity-50 cursor-pointer"


              >


                <span v-if="isSubmittingReview" class="animate-pulse">{{


                  locale === "bn" ? "পাঠানো হচ্ছে..." : "Submitting..."


                }}</span>


                <span v-else>{{


                  editingReview


                    ? locale === "bn"


                      ? "আপডেট"


                      : "Update"


                    : locale === "bn"


                      ? "জমা দিন"


                      : "Submit"


                }}</span>


              </button>


            </div>


          </div>


        </div>


      </transition>


      <!-- ── Related Products ── -->


      <div v-if="related.length > 0">


        <div class="flex items-center justify-between mb-3">


          <div class="flex items-center gap-2">


            <div class="w-1.5 h-5 bg-green-600 rounded-full" />


            <h2


              class="font-display font-bold text-gray-800 text-base leading-none"


            >


              {{ t("related_products") }}


            </h2>


          </div>


          <router-link


            to="/products-list"


            class="text-green-600 text-xs font-bold hover:underline"


          >


            {{ t("see_all") }}


          </router-link>


        </div>


        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">


          <ProductCard v-for="d in related" :key="d.id" :deal="d" />


        </div>


      </div>


    </div>


  </div>


</template>





<style>


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


  50% { transform: translateY(-3.5px); }


}


.animate-bounce-short {


  animation: bounce-short 0.4s ease-out 1;


}


</style>





