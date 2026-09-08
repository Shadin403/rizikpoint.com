<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from "vue";
import {
  ArrowRight,
  ChevronLeft,
  ChevronRight,
  Zap,
  Star,
  TrendingUp,
  Package,
  Truck,
  ShieldCheck,
  CreditCard,
  Headphones,
  Sparkles,
  Award,
  Loader2,
  CheckCircle2,
} from "@lucide/vue";
import {
  fetchBanners,
  fetchFeaturedProducts,
  fetchDeals,
  fetchDealsPaged,
  fetchFlashDeals,
  fetchFlashDealSections,
  fetchHomeSections,
} from "@/lib/api";
import { useI18n } from "@/lib/i18n";
import ProductCard from "@/components/shared/ProductCard.vue";
import SkeletonLoader from "@/components/shared/SkeletonLoader.vue";
import { defineAsyncComponent } from "vue";
import "swiper/css";
const Swiper = defineAsyncComponent(() => import("swiper/vue").then(m => m.Swiper));
const SwiperSlide = defineAsyncComponent(() => import("swiper/vue").then(m => m.SwiperSlide));
import { usePageTitle } from "@/composables/usePageTitle";
import { pageTitles } from "@/lib/pageTitles";
usePageTitle(pageTitles.Home);

const { locale, t } = useI18n();

const swiperBreakpoints = {
  320: {
    slidesPerView: 2,
    spaceBetween: 12,
  },
  480: {
    slidesPerView: 2.2,
    spaceBetween: 14,
  },
  640: {
    slidesPerView: 3,
    spaceBetween: 16,
  },
  768: {
    slidesPerView: 3.5,
    spaceBetween: 16,
  },
  1024: {
    slidesPerView: 4.2,
    spaceBetween: 18,
  },
  1280: {
    slidesPerView: 5,
    spaceBetween: 20,
  },
};

// ── Slider & Mini Banners ──────────────────────────────────────────────────────
const banners = ref([]);
const activeSlide = ref(0);
let slideInterval = null;

function hasBannerOverlay(b) {
  if (!b) return false;
  const title = locale.value === 'bn' ? (b.titleBn || b.title) : b.title;
  const desc  = locale.value === 'bn' ? (b.descriptionBn || b.description) : b.description;
  return !!(title?.trim() || desc?.trim() || b.buttonText?.trim() || b.badge?.trim());
}

const windowWidth = ref(typeof window !== 'undefined' ? window.innerWidth : 1024);

function handleResize() {
  windowWidth.value = window.innerWidth;
}

const isMobile = computed(() => windowWidth.value < 1024);

// Mini banners displayed on desktop right side (only from real database banners)
const sideBanners = computed(() => {
  const explicitSide = banners.value.filter(b => b.type === 'side' || b.type === 'mini');
  if (explicitSide.length > 0) {
    return explicitSide.slice(0, 2);
  }
  if (banners.value.length >= 3) {
    return [banners.value[1], banners.value[2]];
  }
  return [];
});

const hasSideBanners = computed(() => !isMobile.value && sideBanners.value.length > 0);

// Main slider banners for desktop
const mainBanners = computed(() => {
  const explicitMain = banners.value.filter(b => !b.type || b.type === 'main');
  if (explicitMain.length > 0) {
    if (sideBanners.value.length > 0 && explicitMain.length === banners.value.length && banners.value.length >= 3) {
      return [banners.value[0], ...banners.value.slice(3)];
    }
    return explicitMain;
  }
  return banners.value;
});

// Mobile slider banners: merges main + side banners so mobile gets 1 continuous carousel
const mobileBanners = computed(() => {
  const combined = [...banners.value];
  sideBanners.value.forEach(sb => {
    if (!combined.some(b => b.id === sb.id)) {
      combined.push(sb);
    }
  });
  return combined;
});

// Active slider banner list
const sliderBanners = computed(() => {
  return isMobile.value
    ? mobileBanners.value
    : (mainBanners.value.length > 0 ? mainBanners.value : banners.value);
});

function nextSlide() {
  if (sliderBanners.value.length < 2) return;
  activeSlide.value = (activeSlide.value + 1) % sliderBanners.value.length;
}
function prevSlide() {
  if (sliderBanners.value.length < 2) return;
  activeSlide.value =
    (activeSlide.value - 1 + sliderBanners.value.length) % sliderBanners.value.length;
}
function setSlide(i) {
  activeSlide.value = i;
}

watch(sliderBanners, (newVal) => {
  if (activeSlide.value >= newVal.length) {
    activeSlide.value = 0;
  }
});

// ── Product sections ───────────────────────────────────────────────────────────
const featuredProducts = ref([]);
const newArrivals = ref([]);
// flashDealSections = one entry per active flash deal, each with its own products.
const flashDealSections = ref([]);
const homeSections = ref([]);

// ── All Products Section (Paginated 30 items per batch) ──────────────────────
const allProducts = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);
const perPage = 30;

const isLoadingBanners = ref(false);
const isLoadingFeatured = ref(false);
const isLoadingNew = ref(false);
const isLoadingFlash = ref(false);
const isLoadingHomeSections = ref(false);
const isLoadingAll = ref(false);
const isLoadingMore = ref(false);

const hasMore = computed(() => currentPage.value < totalPages.value);

async function loadInitialProducts() {
  isLoadingAll.value = true;
  try {
    const res = await fetchDealsPaged({ page: 1, limit: perPage });
    allProducts.value = res.deals || [];
    if (res.meta) {
      currentPage.value = res.meta.current_page || 1;
      totalPages.value = res.meta.last_page || 1;
    } else {
      currentPage.value = 1;
      totalPages.value = (res.deals && res.deals.length >= perPage) ? 2 : 1;
    }
  } catch (err) {
    console.error("Error loading products", err);
  } finally {
    isLoadingAll.value = false;
  }
}

async function loadMore() {
  if (isLoadingMore.value || !hasMore.value) return;
  isLoadingMore.value = true;
  const nextPage = currentPage.value + 1;
  try {
    const res = await fetchDealsPaged({ page: nextPage, limit: perPage });
    if (res.deals && res.deals.length > 0) {
      const existingIds = new Set(allProducts.value.map((p) => p.id));
      const newItems = res.deals.filter((p) => !existingIds.has(p.id));
      allProducts.value.push(...newItems);
    }
    if (res.meta) {
      currentPage.value = res.meta.current_page || nextPage;
      totalPages.value = res.meta.last_page || currentPage.value;
    } else {
      currentPage.value = nextPage;
      if (!res.deals || res.deals.length < perPage) {
        totalPages.value = currentPage.value;
      }
    }
  } catch (err) {
    console.error("Error loading more products", err);
  } finally {
    isLoadingMore.value = false;
  }
}

onMounted(async () => {
  if (typeof window !== 'undefined') {
    window.addEventListener('resize', handleResize);
  }

  // Load banners
  isLoadingBanners.value = true;
  fetchBanners()
    .then((data) => {
      banners.value = data;
      isLoadingBanners.value = false;
      if (slideInterval) clearInterval(slideInterval);
      slideInterval = setInterval(nextSlide, 5000);
    })
    .catch(() => {
      isLoadingBanners.value = false;
    });

  // Load featured products
  isLoadingFeatured.value = true;
  fetchFeaturedProducts()
    .then((data) => {
      featuredProducts.value = data.slice(0, 12);
      isLoadingFeatured.value = false;
    })
    .catch(() => {
      isLoadingFeatured.value = false;
    });

  // Load initial paginated products (30 per page)
  loadInitialProducts();

  // Load flash deals
  isLoadingFlash.value = true;
  fetchFlashDealSections()
    .then((sections) => {
      flashDealSections.value = sections;
      isLoadingFlash.value = false;
    })
    .catch(() => {
      isLoadingFlash.value = false;
    });

  // Load dynamic Home Sections from Admin Panel
  isLoadingHomeSections.value = true;
  fetchHomeSections()
    .then((sections) => {
      homeSections.value = sections;
      isLoadingHomeSections.value = false;
    })
    .catch(() => {
      isLoadingHomeSections.value = false;
    });
});

onUnmounted(() => {
  if (typeof window !== 'undefined') {
    window.removeEventListener('resize', handleResize);
  }
  if (slideInterval) clearInterval(slideInterval);
});

const recommended = computed(() =>
  featuredProducts.value.length > 0
    ? featuredProducts.value.slice(0, 6)
    : newArrivals.value.slice(0, 6),
);

// Limit each flash-deal section to 12 products so the grid stays tidy when
// a deal has a long product list.
const MAX_PER_SECTION = 12;
</script>

<template>
  <div class="organic-home w-full pb-12 flex flex-col gap-10 bg-stone-50/40">
    <!-- ── Section 1: Hero Banner Skeleton Loading State ────────────────── -->
    <div v-if="isLoadingBanners" class="px-3 sm:px-6 mt-3">
      <div class="grid grid-cols-12 gap-3 sm:gap-5">
        <div
          class="col-span-12 lg:col-span-8 rounded-2xl bg-gradient-to-r from-stone-200 via-stone-100 to-stone-200 h-[260px] sm:h-[340px] lg:h-[400px] animate-pulse"
        ></div>
        <div class="hidden lg:flex lg:col-span-4 flex-col gap-4 h-[400px]">
          <div class="rounded-2xl bg-gradient-to-r from-stone-200 via-stone-100 to-stone-200 flex-1 animate-pulse"></div>
          <div class="rounded-2xl bg-gradient-to-r from-stone-200 via-stone-100 to-stone-200 flex-1 animate-pulse"></div>
        </div>
      </div>
    </div>

    <!-- ── Section 1: Classic Hero Banner Area ────────────────────────── -->
    <div v-else-if="sliderBanners.length > 0" class="px-3 sm:px-6 mt-3">
      <div class="grid grid-cols-12 gap-3 sm:gap-5">
        <!-- Main Banner Slider -->
        <div
          :class="hasSideBanners ? 'col-span-12 lg:col-span-8' : 'col-span-12'"
          class="relative rounded-2xl overflow-hidden h-[260px] sm:h-[340px] lg:h-[400px] shadow-md border border-stone-200/90 group bg-stone-900"
        >
          <router-link
            v-for="(banner, index) in sliderBanners"
            :key="banner.id"
            v-show="activeSlide === index"
            :to="banner.link || '/products-list'"
            class="absolute inset-0 transition-opacity duration-1000 block"
          >
            <img
              :src="banner.image"
              :alt="locale === 'bn' ? banner.titleBn || banner.title : banner.title"
              class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-700 opacity-95"
              loading="lazy"
            />
            <div
              v-if="hasBannerOverlay(banner)"
              class="absolute inset-0 bg-gradient-to-t from-stone-950/95 via-stone-900/40 to-transparent flex flex-col justify-end p-6 sm:p-10 pointer-events-none"
            >
              <div v-if="banner.badge" class="mb-2">
                <span class="bg-amber-500 text-stone-950 text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-widest shadow-xs border border-amber-300">
                  {{ banner.badge }}
                </span>
              </div>
              <h2
                v-if="locale === 'bn' ? (banner.titleBn || banner.title) : banner.title"
                class="text-white text-2xl sm:text-4xl lg:text-5xl font-bold mb-2 font-display leading-tight tracking-tight drop-shadow-md"
              >
                {{ locale === "bn" ? banner.titleBn || banner.title : banner.title }}
              </h2>
              <p
                v-if="locale === 'bn' ? (banner.descriptionBn || banner.description) : banner.description"
                class="text-stone-200 text-xs sm:text-sm md:text-base max-w-xl mb-5 font-sans leading-relaxed line-clamp-2"
              >
                {{ locale === "bn" ? banner.descriptionBn || banner.description : banner.description }}
              </p>
              <span
                v-if="banner.buttonText"
                class="inline-flex items-center gap-2 bg-emerald-800 hover:bg-emerald-900 text-amber-50 font-semibold px-6 py-2.5 rounded-full text-sm w-fit transition-all shadow-md border border-emerald-600/40 pointer-events-auto hover:translate-x-1"
              >
                {{ banner.buttonText }} <ArrowRight class="w-4 h-4" />
              </span>
            </div>
          </router-link>

          <!-- Nav arrows -->
          <button
            v-if="sliderBanners.length > 1"
            :aria-label="locale === 'bn' ? 'আগের ব্যানার' : 'Previous banner'"
            @click="prevSlide"
            class="absolute left-3.5 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-stone-950/60 hover:bg-stone-950/85 text-amber-100 border border-amber-500/30 backdrop-blur-xs flex items-center justify-center cursor-pointer transition-colors z-10 shadow-md"
          >
            <ChevronLeft class="w-5 h-5" />
          </button>
          <button
            v-if="sliderBanners.length > 1"
            :aria-label="locale === 'bn' ? 'পরের ব্যানার' : 'Next banner'"
            @click="nextSlide"
            class="absolute right-3.5 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-stone-950/60 hover:bg-stone-950/85 text-amber-100 border border-amber-500/30 backdrop-blur-xs flex items-center justify-center cursor-pointer transition-colors z-10 shadow-md"
          >
            <ChevronRight class="w-5 h-5" />
          </button>

          <!-- Dots -->
          <div
            v-if="sliderBanners.length > 1"
            class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-10"
          >
            <button
              v-for="(banner, index) in sliderBanners"
              :key="banner.id"
              @click="setSlide(index)"
              :aria-label="`${locale === 'bn' ? 'ব্যানার' : 'Banner'} ${index + 1}`"
              :aria-pressed="activeSlide === index"
              class="h-2 rounded-full transition-all duration-300 cursor-pointer"
              :class="
                activeSlide === index
                  ? 'bg-amber-400 w-7'
                  : 'bg-white/50 w-2 hover:bg-white'
              "
            />
          </div>
        </div>

        <!-- 2 Stacked Side Banners -->
        <div v-if="hasSideBanners" class="hidden lg:flex lg:col-span-4 flex-col gap-4 h-[400px]">
          <router-link
            v-for="mini in sideBanners"
            :key="mini.id"
            :to="mini.link || '/products-list'"
            class="relative rounded-2xl overflow-hidden flex-1 group border border-stone-200/90 shadow-sm hover:shadow-md transition-all duration-300 block bg-stone-900"
          >
            <img
              :src="mini.image"
              :alt="locale === 'bn' ? mini.titleBn || mini.title : mini.title"
              class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105 opacity-95"
              loading="lazy"
            />
            <div
              v-if="hasBannerOverlay(mini)"
              class="absolute inset-0 bg-gradient-to-t from-stone-950/90 via-stone-900/30 to-transparent flex flex-col justify-end p-5 pointer-events-none"
            >
              <div v-if="mini.badge" class="mb-1">
                <span class="bg-amber-500 text-stone-950 text-[9px] font-extrabold px-2 py-0.5 rounded-full uppercase tracking-wider shadow-xs border border-amber-300">
                  {{ mini.badge }}
                </span>
              </div>
              <h3
                v-if="locale === 'bn' ? (mini.titleBn || mini.title) : mini.title"
                class="text-white text-base sm:text-lg font-bold font-display leading-snug line-clamp-1 group-hover:text-amber-200 transition-colors drop-shadow-sm"
              >
                {{ locale === "bn" ? mini.titleBn || mini.title : mini.title }}
              </h3>
              <p
                v-if="locale === 'bn' ? (mini.descriptionBn || mini.description) : mini.description"
                class="text-stone-200 text-xs line-clamp-1 mb-2 font-sans opacity-90"
              >
                {{ locale === "bn" ? mini.descriptionBn || mini.description : mini.description }}
              </p>
              <span
                v-if="mini.buttonText"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-300 group-hover:text-amber-200 transition-all group-hover:translate-x-1 duration-200"
              >
                <span>{{ mini.buttonText }}</span>
                <ArrowRight class="w-3.5 h-3.5" />
              </span>
            </div>
          </router-link>
        </div>
      </div>
    </div>

    <!-- ── Section 2: Flash Deals ─────────────────────────────────────────── -->
    <section v-if="isLoadingFlash || flashDealSections.length > 0" class="px-3 sm:px-4">
      <SkeletonLoader v-if="isLoadingFlash" type="card" :count="6" />
      <div v-else class="flex flex-col gap-6">
        <div
          v-for="section in flashDealSections"
          :key="'flash-section-' + section.id"
        >
          <!-- Section header: title from the deal itself -->
          <div class="flex items-center justify-between mb-3 border-b border-gray-200/80 pb-2">
            <div class="flex items-center gap-2">
              <div
                class="w-7 h-7 bg-red-600 rounded-lg flex items-center justify-center shadow-xs text-white"
              >
                <Zap class="w-4 h-4 fill-white" />
              </div>
              <div>
                <h2 class="text-base sm:text-lg font-bold text-gray-900 leading-tight">
                  {{
                    section.title ||
                    (locale === "bn" ? "ফ্ল্যাশ ডিল" : "Flash Deals")
                  }}
                </h2>
                <p class="text-[11px] text-gray-500 font-sans mt-0.5">
                  {{
                    locale === "bn"
                      ? "সীমিত সময়ের আকর্ষণীয় অফারসমূহ"
                      : "Handpicked limited-time exclusive offers"
                  }}
                </p>
              </div>
            </div>
            <router-link
              v-if="section.id > 0"
              :to="`/deals`"
              class="inline-flex items-center gap-1 text-xs font-semibold text-[#168039] hover:text-[#146c30]"
            >
              {{ t("see_all") }} <ArrowRight class="w-3.5 h-3.5" />
            </router-link>
          </div>

          <!-- Banner image (if the deal has one) -->
          <a
            v-if="section.banner"
            :href="section.id > 0 ? `/deals` : '/products-list'"
            class="block mb-3 rounded-lg overflow-hidden shadow-xs border border-gray-200"
          >
            <img
              :src="section.banner"
              :alt="section.title"
              class="w-full h-32 sm:h-40 object-cover"
            />
          </a>

          <!-- Products Swiper for this specific deal -->
          <swiper
            v-if="section.products.length > 0"
            :breakpoints="swiperBreakpoints"
            class="w-full !pb-1"
          >
            <swiper-slide
              v-for="deal in section.products.slice(0, MAX_PER_SECTION)"
              :key="'flash-' + section.id + '-' + deal.id"
            >
              <ProductCard :deal="deal" />
            </swiper-slide>
          </swiper>
        </div>
      </div>
    </section>

    <!-- ── Dynamic Homepage Sections (Managed from Admin Panel) ────────── -->
    <template v-if="isLoadingHomeSections">
      <section v-for="i in 2" :key="'sec-skel-' + i" class="px-3 sm:px-4">
        <div class="mb-3 space-y-1">
          <div class="h-5 w-48 bg-stone-200 rounded animate-pulse" />
          <div class="h-3 w-64 bg-stone-200 rounded animate-pulse" />
        </div>
        <SkeletonLoader type="card" :count="6" />
      </section>
    </template>

    <template v-else-if="homeSections.length > 0">
      <section
        v-for="sec in homeSections"
        :key="'home-sec-' + sec.id"
        class="px-3 sm:px-4"
      >
        <div>
          <div class="flex items-center justify-between mb-3 border-b border-gray-200/80 pb-2">
            <div class="flex items-center gap-2">
              <div class="w-1.5 h-5 bg-[#168039] rounded-full" />
              <div>
                <h2 class="text-base sm:text-lg font-bold text-gray-900 leading-tight">
                  {{ sec.title }}
                </h2>
                <p v-if="sec.subtitle" class="text-[11px] text-gray-500 font-sans mt-0.5">
                  {{ sec.subtitle }}
                </p>
              </div>
            </div>
            <router-link
              :to="sec.category_id ? `/products-list?category=${sec.category_id}` : '/products-list'"
              class="inline-flex items-center gap-1 text-xs font-semibold text-[#168039] hover:text-[#146c30]"
            >
              {{ t("see_all") }} <ArrowRight class="w-3.5 h-3.5" />
            </router-link>
          </div>

          <swiper
            v-if="sec.products.length > 0"
            :breakpoints="swiperBreakpoints"
            class="w-full !pb-1"
          >
            <swiper-slide v-for="deal in sec.products" :key="'sec-' + sec.id + '-' + deal.id">
              <ProductCard :deal="deal" />
            </swiper-slide>
          </swiper>

          <p v-else class="text-center text-gray-400 py-4 text-xs">
            {{ locale === 'bn' ? 'এই সেকশনে আপাতত কোনো পণ্য নেই।' : 'No products available in this section.' }}
          </p>
        </div>
      </section>
    </template>

    <!-- ── Final Section: All Products ────────────────────────────────────────── -->
    <section class="px-3 sm:px-4">
      <div>
        <div class="flex items-center justify-between mb-4 border-b border-gray-200/80 pb-2">
          <div class="flex items-center gap-2">
            <div class="w-1.5 h-5 bg-[#168039] rounded-full" />
            <div>
              <h2
                class="text-base sm:text-lg font-bold text-gray-900 leading-tight"
              >
                {{ locale === 'bn' ? 'আপনার বাজার জন্য বাছাই' : 'Curated Everyday Collection' }}
              </h2>
              <p class="text-[11px] text-gray-500 font-sans mt-0.5">
                {{ locale === 'bn' ? 'পছন্দের পণ্য দিয়ে পূর্ণ হোক বাজারের ঝুড়ি' : 'Explore all high quality products carefully selected for you' }}
              </p>
            </div>
          </div>
        </div>

        <SkeletonLoader
          v-if="isLoadingAll && allProducts.length === 0"
          type="card"
          :count="12"
        />

        <div
          v-else-if="allProducts.length > 0"
          class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4 transition-all duration-300"
        >
          <ProductCard
            v-for="deal in allProducts"
            :key="'all-' + deal.id"
            :deal="deal"
          />
        </div>

        <!-- Load More Button with beautiful gradient styling -->
        <div v-if="hasMore" class="flex justify-center mt-9 mb-2">
          <button
            @click="loadMore"
            :disabled="isLoadingMore"
            class="bg-gradient-to-r from-[#168039] via-[#157734] to-[#146c30] hover:from-[#146c30] hover:to-[#125e29] text-white font-bold px-9 py-3.5 rounded-full shadow-md hover:shadow-lg hover:shadow-[#168039]/30 hover:-translate-y-0.5 transition-all duration-200 text-xs sm:text-sm cursor-pointer flex items-center gap-2.5 active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed group border border-[#146c30]"
          >
            <Loader2 v-if="isLoadingMore" class="w-4 h-4 animate-spin text-white" />
            <ArrowRight v-else class="w-4 h-4 group-hover:translate-x-1 transition-transform stroke-[2.5]" />
            <span>
              {{ isLoadingMore
                ? (locale === 'bn' ? 'পণ্য লোড হচ্ছে...' : 'Loading products...')
                : t("load_more")
              }}
            </span>
          </button>
        </div>

        <!-- End of Products Message -->
        <div v-else-if="allProducts.length > 0 && !hasMore" class="text-center py-8">
          <div class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 bg-gray-100/80 px-4 py-2 rounded-full border border-gray-200/80">
            <CheckCircle2 class="w-4 h-4 text-[#168039]" />
            <span>{{ locale === 'bn' ? 'সকল পণ্য লোড করা হয়েছে' : 'All products have been loaded' }}</span>
          </div>
        </div>

        <p
          v-else-if="allProducts.length === 0 && !isLoadingAll"
          class="text-center text-gray-400 py-6 text-xs"
        >
          {{ locale === 'bn' ? 'এই মুহূর্তে পণ্য দেখানো যাচ্ছে না। কিছুক্ষণ পরে আবার দেখুন।' : 'Products are unavailable right now. Please check back shortly.' }}
        </p>
      </div>
    </section>
  </div>
</template>

<style scoped>
.scrollbar-none::-webkit-scrollbar {
  display: none;
}
.scrollbar-none {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>

