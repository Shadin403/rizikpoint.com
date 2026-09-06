<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import {
  ArrowRight,
  ChevronLeft,
  ChevronRight,
  Zap,
  Star,
  TrendingUp,
  Package,
} from "@lucide/vue";
import {
  fetchBanners,
  fetchFeaturedProducts,
  fetchDeals,
  fetchFlashDeals,
  fetchFlashDealSections,
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
    slidesPerView: 2.2,
    spaceBetween: 12,
  },
  480: {
    slidesPerView: 2.2,
    spaceBetween: 12,
  },
  640: {
    slidesPerView: 3.2,
    spaceBetween: 16,
  },
  768: {
    slidesPerView: 4,
    spaceBetween: 16,
  },
  1024: {
    slidesPerView: 5,
    spaceBetween: 16,
  },
  1280: {
    slidesPerView: 6,
    spaceBetween: 16,
  },
};

// ── Slider ─────────────────────────────────────────────────────────────────────
const banners = ref([]);
const activeSlide = ref(0);
let slideInterval = null;

function nextSlide() {
  activeSlide.value = (activeSlide.value + 1) % banners.value.length;
}
function prevSlide() {
  activeSlide.value =
    (activeSlide.value - 1 + banners.value.length) % banners.value.length;
}
function setSlide(i) {
  activeSlide.value = i;
}

// ── Product sections ───────────────────────────────────────────────────────────
const featuredProducts = ref([]);
const newArrivals = ref([]);
// flashDealSections = one entry per active flash deal, each with its own products.
// Renders one section per deal with the deal's own title.
const flashDealSections = ref([]);
const allProducts = ref([]);
const displayLimit = ref(20);

const isLoadingBanners = ref(false);
const isLoadingFeatured = ref(false);
const isLoadingNew = ref(false);
const isLoadingFlash = ref(false);
const isLoadingAll = ref(false);

onMounted(async () => {
  // Load banners
  isLoadingBanners.value = true;
  fetchBanners()
    .then((data) => {
      banners.value = data;
      isLoadingBanners.value = false;
      slideInterval = setInterval(nextSlide, 4500);
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

  // Load new arrivals (latest products)
  isLoadingNew.value = true;
  fetchDeals({ limit: 40 })
    .then((data) => {
      newArrivals.value = data.slice(0, 12);
      allProducts.value = data;
      isLoadingNew.value = false;
      isLoadingAll.value = false;
    })
    .catch(() => {
      isLoadingNew.value = false;
      isLoadingAll.value = false;
    });

  // Load flash deals — one section per active deal.
  // Each section keeps its own title (e.g. "Flash Sale", "Flash sale 2"),
  // banner, slug, and products so the homepage renders them as separate
  // rows automatically — no UI changes needed when the admin creates a
  // new flash deal.
  isLoadingFlash.value = true;
  fetchFlashDealSections()
    .then((sections) => {
      flashDealSections.value = sections;
      isLoadingFlash.value = false;
    })
    .catch(() => {
      isLoadingFlash.value = false;
    });
});

onUnmounted(() => {
  if (slideInterval) clearInterval(slideInterval);
});

const hasMore = computed(() => displayLimit.value < allProducts.value.length);
const pagedProducts = computed(() =>
  allProducts.value.slice(0, displayLimit.value),
);
function loadMore() {
  displayLimit.value += 20;
}

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
  <div class="w-full pb-8 flex flex-col gap-8">
    <!-- ── Section 1: Hero Slider ─────────────────────────────────────────── -->
    <div
      class="relative mx-3 rounded-2xl overflow-hidden h-[240px] sm:h-[360px] md:h-[420px] shadow-md group"
    >
      <!-- Loading skeleton -->
      <div
        v-if="isLoadingBanners"
        class="w-full h-full bg-gradient-to-r from-gray-100 to-gray-200 animate-pulse flex items-center justify-center"
      >
        <div
          class="w-12 h-12 border-4 border-green-400 border-t-transparent rounded-full animate-spin"
        ></div>
      </div>

      <template v-else-if="banners.length > 0">
        <div
          v-for="(banner, index) in banners"
          :key="banner.id"
          v-show="activeSlide === index"
          class="absolute inset-0 transition-opacity duration-1000"
        >
          <img
            :src="banner.image"
            :alt="locale === 'bn' ? banner.titleBn : banner.title"
            class="w-full h-full object-cover"
            loading="lazy"
          />
          <div
            class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent flex flex-col justify-end p-5 sm:p-8 md:p-12"
          >
            <h1
              class="text-white text-xl sm:text-3xl md:text-4xl font-extrabold mb-2 font-display leading-tight drop-shadow-lg"
            >
              {{
                locale === "bn" ? banner.titleBn || banner.title : banner.title
              }}
            </h1>
            <p
              class="text-gray-200 text-xs sm:text-sm md:text-base max-w-lg mb-4 leading-normal line-clamp-2"
            >
              {{
                locale === "bn"
                  ? banner.descriptionBn || banner.description
                  : banner.description
              }}
            </p>
            <router-link
              :to="banner.link || '/products-list'"
              class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-500 text-white font-bold px-5 py-2.5 rounded-xl text-sm w-fit transition-all shadow-lg hover:shadow-green-500/30 cursor-pointer"
            >
              {{ banner.buttonText || t("order_now") }} <ArrowRight class="w-4 h-4" />
            </router-link>
          </div>
        </div>

        <!-- Nav arrows -->
        <button
          @click="prevSlide"
          class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-black/25 hover:bg-black/50 text-white flex items-center justify-center backdrop-blur-sm transition-all opacity-0 group-hover:opacity-100 cursor-pointer hidden sm:flex"
        >
          <ChevronLeft class="w-5 h-5" />
        </button>
        <button
          @click="nextSlide"
          class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-black/25 hover:bg-black/50 text-white flex items-center justify-center backdrop-blur-sm transition-all opacity-0 group-hover:opacity-100 cursor-pointer hidden sm:flex"
        >
          <ChevronRight class="w-5 h-5" />
        </button>

        <!-- Dots -->
        <div
          class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-1.5 z-10"
        >
          <button
            v-for="(banner, index) in banners"
            :key="banner.id"
            @click="setSlide(index)"
            class="h-2 rounded-full transition-all duration-300 cursor-pointer"
            :class="
              activeSlide === index
                ? 'bg-green-400 w-6'
                : 'bg-white/50 w-2 hover:bg-white'
            "
          />
        </div>
      </template>

      <!-- Fallback if no banners -->
      <div
        v-else
        class="w-full h-full bg-gradient-to-br from-green-700 via-green-600 to-emerald-500 flex flex-col items-center justify-center text-white p-8 text-center"
      >
        <h1 class="text-2xl sm:text-4xl font-extrabold mb-2">
          {{ locale === "bn" ? "সেরা দামে সেরা পণ্য" : "Best Deals Every Day" }}
        </h1>
        <p class="text-green-100 text-sm mb-4">
          {{
            locale === "bn"
              ? "আমাদের সাথে থাকুন এবং সাশ্রয়ী মূল্যে কেনাকাটা করুন।"
              : "Shop smarter and save more with exclusive offers."
          }}
        </p>
        <router-link
          to="/products-list"
          class="bg-white text-green-700 font-bold px-5 py-2 rounded-xl text-sm hover:bg-green-50 transition-colors"
        >
          {{ t("order_now") }} <ArrowRight class="inline w-4 h-4 ml-1" />
        </router-link>
      </div>
    </div>

    <!-- ── Section 2: Flash Deals ───────────────────────────────────────────
         Renders one card-row per active flash deal created in the admin panel.
         Each row uses the deal's own title (e.g. "Flash Sale", "Flash sale 2")
         and shows up to 12 products from that deal. Adding a new flash deal
         in the admin panel automatically creates a new section here — no
         frontend change needed. -->
    <section v-if="isLoadingFlash || flashDealSections.length > 0" class="px-3">
      <SkeletonLoader v-if="isLoadingFlash" type="card" :count="6" />
      <div v-else class="flex flex-col gap-8">
        <div
          v-for="section in flashDealSections"
          :key="'flash-section-' + section.id"
        >
          <!-- Section header: title from the deal itself -->
          <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
              <div
                class="w-7 h-7 bg-red-600 rounded-lg flex items-center justify-center shadow-sm"
              >
                <Zap class="w-4 h-4 text-white fill-white" />
              </div>
              <div>
                <h2
                  class="text-base font-bold text-gray-800 font-display leading-none"
                >
                  {{
                    section.title ||
                    (locale === "bn" ? "ফ্ল্যাশ ডিল" : "Flash Deals")
                  }}
                </h2>
                <p class="text-[10px] text-gray-400 mt-0.5">
                  {{
                    locale === "bn"
                      ? "সীমিত সময়ের অফার"
                      : "Limited time offers"
                  }}
                </p>
              </div>
            </div>
            <router-link
              v-if="section.id > 0"
              :to="`/deals`"
              class="flex items-center gap-1 text-red-600 text-xs font-bold hover:underline"
            >
              {{ t("see_all") }} <ArrowRight class="w-3.5 h-3.5" />
            </router-link>
          </div>

          <!-- Banner image (if the deal has one) -->
          <a
            v-if="section.banner"
            :href="section.id > 0 ? `/deals` : '/products-list'"
            class="block mb-4 rounded-xl overflow-hidden shadow-sm"
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
            class="w-full !pb-2"
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

    <!-- ── Section 3: Recommended Products ───────────────────────────────── -->
    <section class="px-3">
      <div class="flex items-center justify-between mb-3">
        <div>
          <div class="flex items-center gap-2">
            <div class="w-1.5 h-5 bg-green-600 rounded-full" />
            <h2
              class="text-lg font-bold text-gray-800 font-display leading-none"
            >
              {{ t("recommended") }}
            </h2>
          </div>
          <p class="text-xs text-gray-500 mt-1.5 ml-3.5">
            {{ t("recommended_desc") }}
          </p>
        </div>
        <router-link
          to="/products-list"
          class="flex items-center gap-1 text-green-600 text-xs font-bold hover:underline"
        >
          {{ t("see_all") }} <ArrowRight class="w-3.5 h-3.5" />
        </router-link>
      </div>
      <SkeletonLoader v-if="isLoadingFeatured" type="card" :count="6" />
      <swiper
        v-else-if="recommended.length > 0"
        :breakpoints="swiperBreakpoints"
        class="w-full !pb-2"
      >
        <swiper-slide v-for="deal in recommended" :key="'rec-' + deal.id">
          <ProductCard :deal="deal" />
        </swiper-slide>
      </swiper>
      <p v-else class="text-center text-gray-400 py-6 text-sm">
        {{ t("no_deals_found") }}
      </p>
    </section>

    <!-- ── Section 4: New Arrivals ────────────────────────────────────────── -->
    <section class="px-3">
      <div class="flex items-center justify-between mb-3">
        <div>
          <div class="flex items-center gap-2">
            <div class="w-1.5 h-5 bg-blue-500 rounded-full" />
            <h2
              class="text-lg font-bold text-gray-800 font-display leading-none"
            >
              {{ t("new_arrivals") }}
            </h2>
          </div>
          <p class="text-xs text-gray-500 mt-1.5 ml-3.5">
            {{ t("new_arrivals_desc") }}
          </p>
        </div>
        <router-link
          to="/products-list"
          class="flex items-center gap-1 text-green-600 text-xs font-bold hover:underline"
        >
          {{ t("see_all") }} <ArrowRight class="w-3.5 h-3.5" />
        </router-link>
      </div>
      <SkeletonLoader v-if="isLoadingNew" type="card" :count="6" />
      <swiper
        v-else-if="newArrivals.length > 0"
        :breakpoints="swiperBreakpoints"
        class="w-full !pb-2"
      >
        <swiper-slide
          v-for="deal in newArrivals.slice(0, 6)"
          :key="'new-' + deal.id"
        >
          <ProductCard :deal="deal" />
        </swiper-slide>
      </swiper>
      <p v-else class="text-center text-gray-400 py-6 text-sm">
        {{ t("no_deals_found") }}
      </p>
    </section>

    <!-- ── Section 5: Featured Products ─────────────────────────────────── -->
    <section v-if="featuredProducts.length > 6" class="px-3">
      <div class="flex items-center justify-between mb-3">
        <div>
          <div class="flex items-center gap-2">
            <div class="w-1.5 h-5 bg-yellow-500 rounded-full" />
            <h2
              class="text-lg font-bold text-gray-800 font-display leading-none"
            >
              {{ t("featured_deals") }}
            </h2>
          </div>
          <p class="text-xs text-gray-500 mt-1.5 ml-3.5">
            {{ t("featured_deals_desc") }}
          </p>
        </div>
        <router-link
          to="/products-list"
          class="flex items-center gap-1 text-green-600 text-xs font-bold hover:underline"
        >
          {{ t("see_all") }} <ArrowRight class="w-3.5 h-3.5" />
        </router-link>
      </div>
      <swiper :breakpoints="swiperBreakpoints" class="w-full !pb-2">
        <swiper-slide
          v-for="deal in featuredProducts.slice(6, 12)"
          :key="'feat-' + deal.id"
        >
          <ProductCard :deal="deal" />
        </swiper-slide>
      </swiper>
    </section>

    <!-- ── Section 6: All Products ────────────────────────────────────────── -->
    <section class="px-3">
      <div class="flex items-center justify-between mb-3">
        <div>
          <div class="flex items-center gap-2">
            <div class="w-1.5 h-5 bg-green-600 rounded-full" />
            <h2
              class="text-lg font-bold text-gray-800 font-display leading-none"
            >
              {{ t("all_products_section") }}
            </h2>
          </div>
          <p class="text-xs text-gray-500 mt-1.5 ml-3.5">
            {{ t("all_products_desc") }}
          </p>
        </div>
      </div>

      <SkeletonLoader
        v-if="isLoadingAll && pagedProducts.length === 0"
        type="card"
        :count="12"
      />

      <div
        v-else-if="pagedProducts.length > 0"
        class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4"
      >
        <ProductCard
          v-for="deal in pagedProducts"
          :key="'all-' + deal.id"
          :deal="deal"
        />
      </div>

      <div v-if="hasMore" class="flex justify-center mt-8">
        <button
          @click="loadMore"
          class="bg-white hover:bg-gray-50 text-green-700 font-bold px-8 py-3 rounded-full border border-green-300 hover:border-green-500 transition-all shadow-sm hover:shadow-md cursor-pointer"
        >
          {{ t("load_more") }}
        </button>
      </div>

      <p
        v-else-if="pagedProducts.length === 0 && !isLoadingAll"
        class="text-center text-gray-400 py-6 text-sm"
      >
        {{ t("no_deals_found") }}
      </p>
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

