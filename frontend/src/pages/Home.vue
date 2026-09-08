<script setup>
import { computed, onMounted, ref } from "vue";
import { ArrowRight, Clock3, Headphones, Heart, Leaf, Mail, ShieldCheck, ShoppingBasket, Truck } from "@lucide/vue";
import { fetchBanners, fetchBestSellers, fetchCategories, fetchDealsPaged, fetchFeaturedProducts, fetchFlashDealSections, fetchHomeSections } from "@/lib/api";
import { useI18n } from "@/lib/i18n";
import { usePageTitle } from "@/composables/usePageTitle";
import { pageTitles } from "@/lib/pageTitles";
import ProductCard from "@/components/shared/ProductCard.vue";
import CategoryIcon from "@/components/shared/CategoryIcon.vue";
import SkeletonLoader from "@/components/shared/SkeletonLoader.vue";

usePageTitle(pageTitles.Home);
const { locale } = useI18n();
const bn = computed(() => locale.value === "bn");
const categories = ref([]);
const banners = ref([]);
const bestSellers = ref([]);
const featuredProducts = ref([]);
const allProducts = ref([]);
const flashDealSections = ref([]);
const homeSections = ref([]);
const loading = ref(true);
const email = ref("");
const subscribed = ref(false);
const selectedPopularCategory = ref(null);

const popularSource = computed(() => (bestSellers.value.length ? bestSellers.value : allProducts.value));
function categoryNameForProduct(product) {
  if (product?.categoryName) return product.categoryName;
  return categories.value.find((category) => String(category.id) === String(product?.categoryId))?.name || "";
}
function categoryKeyForProduct(product) {
  return product?.categoryId != null
    ? `id:${product.categoryId}`
    : `name:${categoryNameForProduct(product).trim().toLowerCase()}`;
}
const popularCategoryTabs = computed(() => {
  const seen = new Set();
  const tabs = [];
  popularSource.value.forEach((product) => {
    const name = categoryNameForProduct(product).trim();
    if (!name) return;
    const key = categoryKeyForProduct(product);
    if (seen.has(key)) return;
    seen.add(key);
    tabs.push({ key, name });
  });
  return tabs;
});
const popularProducts = computed(() => {
  const products = selectedPopularCategory.value
    ? popularSource.value.filter((product) => categoryKeyForProduct(product) === selectedPopularCategory.value)
    : popularSource.value;
  return products.slice(0, 10);
});
const specialProducts = computed(() => {
  const flashProducts = flashDealSections.value.flatMap((section) => section.products || []);
  const source = featuredProducts.value.length ? featuredProducts.value : flashProducts.length ? flashProducts : allProducts.value.slice(10);
  return source.slice(0, 10);
});
const visibleHomeSections = computed(() => homeSections.value.filter((section) => section.products?.length).slice(0, 2));
const miniBanners = computed(() => banners.value.filter((banner) => banner.type === "mini" || banner.type === "side").slice(0, 2));

function subscribe() {
  if (!email.value.trim()) return;
  subscribed.value = true;
  email.value = "";
}

function selectPopularCategory(key) {
  selectedPopularCategory.value = selectedPopularCategory.value === key ? null : key;
}

onMounted(async () => {
  const results = await Promise.allSettled([
    fetchBanners(), fetchCategories(), fetchBestSellers(), fetchFeaturedProducts(), fetchDealsPaged({ page: 1, limit: 30 }), fetchFlashDealSections(), fetchHomeSections(),
  ]);
  if (results[0].status === "fulfilled") banners.value = results[0].value;
  if (results[1].status === "fulfilled") categories.value = results[1].value;
  if (results[2].status === "fulfilled") bestSellers.value = results[2].value;
  if (results[3].status === "fulfilled") featuredProducts.value = results[3].value;
  if (results[4].status === "fulfilled") allProducts.value = results[4].value.deals || [];
  if (results[5].status === "fulfilled") flashDealSections.value = results[5].value;
  if (results[6].status === "fulfilled") homeSections.value = results[6].value;
  loading.value = false;
});
</script>

<template>
  <main class="rp-home">
    <template v-if="loading">
      <div class="rp-home-loading" aria-busy="true" aria-label="Loading home page">
        <section class="rp-skeleton-hero" aria-hidden="true">
          <div class="rp-container rp-skeleton-hero-copy">
            <div class="rp-skeleton-line rp-skeleton-eyebrow"></div>
            <div class="rp-skeleton-line rp-skeleton-title"></div>
            <div class="rp-skeleton-line rp-skeleton-title rp-skeleton-title-short"></div>
            <div class="rp-skeleton-line rp-skeleton-copy"></div>
            <div class="rp-skeleton-line rp-skeleton-copy rp-skeleton-copy-short"></div>
            <div class="rp-skeleton-actions"><span></span><span></span></div>
            <div class="rp-skeleton-customer"><span></span><span></span><span></span><span></span><i></i></div>
          </div>
          <div class="rp-skeleton-hero-image"></div>
        </section>

        <div class="rp-container rp-skeleton-services" aria-hidden="true">
          <div v-for="i in 3" :key="i" class="rp-skeleton-service"><span></span><div><i></i><i></i></div></div>
        </div>

        <section class="rp-section rp-container" aria-hidden="true">
          <div class="rp-skeleton-heading"><div><i></i><i></i></div><span></span></div>
          <div class="rp-skeleton-categories"><div v-for="i in 8" :key="i"><span></span><i></i></div></div>
        </section>

        <section class="rp-section rp-container" aria-hidden="true">
          <div class="rp-skeleton-heading"><div><i></i><i></i></div><span></span></div>
          <div class="rp-skeleton-products"><div v-for="i in 5" :key="i"><span></span><i></i><i></i><b></b></div></div>
        </section>

        <div class="rp-container rp-skeleton-promos" aria-hidden="true"><span></span><span></span></div>

        <section class="rp-section rp-container" aria-hidden="true">
          <div class="rp-skeleton-heading"><div><i></i><i></i></div><span></span></div>
          <div class="rp-skeleton-products"><div v-for="i in 5" :key="i"><span></span><i></i><i></i><b></b></div></div>
        </section>

        <div class="rp-container rp-skeleton-values" aria-hidden="true"><span v-for="i in 4" :key="i"></span></div>
        <div class="rp-skeleton-newsletter" aria-hidden="true"><div class="rp-container"><span></span><b></b></div></div>
      </div>
    </template>

    <template v-else>
    <section class="rp-hero" aria-labelledby="home-hero-title">
      <img src="/rizikpoint-grocery-hero.png" alt="Fresh ready-to-cook vegetables and fish arranged on a wooden board" class="rp-hero-image" />
      <div class="rp-hero-shade"></div>
      <div class="rp-container rp-hero-content">
        <p class="rp-eyebrow"><Leaf aria-hidden="true" /> {{ bn ? "টাটকা ও স্বাস্থ্যকর" : "Fresh & Healthy" }}</p>
        <h1 id="home-hero-title">{{ bn ? "ব্যস্ত জীবনে রান্না" : "Ready to Cook" }}<br />{{ bn ? "এখন আরও সহজ" : "For Your Busy Life" }}</h1>
        <p class="rp-hero-copy">{{ bn ? "ধোয়া, কাটা ও পরিচ্ছন্নভাবে প্যাক করা সবজি, মাছ, মাংস ও মসলা—কম সময়ে স্বাস্থ্যকর রান্নার জন্য।" : "Pre-washed, cut and hygienically packed vegetables, fish, meat and spices — so you can cook faster and healthier every day." }}</p>
        <div class="rp-hero-actions">
          <router-link to="/products-list" class="rp-btn rp-btn-primary">{{ bn ? "এখনই কিনুন" : "Shop Now" }} <ArrowRight aria-hidden="true" /></router-link>
          <router-link to="/categories" class="rp-btn rp-btn-secondary">{{ bn ? "আরও দেখুন" : "Explore More" }}</router-link>
        </div>
        <div class="rp-customer-note">
          <span class="rp-avatar-stack" aria-hidden="true"><span>R</span><span>P</span><span>✓</span><span>+</span></span>
          <strong>{{ bn ? "২০ হাজার+ সন্তুষ্ট গ্রাহক" : "20K+ Happy Customers" }}</strong>
        </div>
      </div>
    </section>

    <div class="rp-container rp-services" aria-label="Store benefits">
      <div class="rp-service"><span class="rp-service-icon"><Truck aria-hidden="true" /></span><span><strong>{{ bn ? "ফ্রি হোম ডেলিভারি" : "Free Home Delivery" }}</strong><small>{{ bn ? "৳১০০০-এর বেশি অর্ডারে" : "On orders over ৳1000" }}</small></span></div>
      <div class="rp-service"><span class="rp-service-icon"><ShieldCheck aria-hidden="true" /></span><span><strong>{{ bn ? "টাটকা ও স্বাস্থ্যসম্মত" : "Fresh & Hygienic" }}</strong><small>{{ bn ? "পরিষ্কার ও রান্নার জন্য প্রস্তুত" : "Cleaned and ready to cook" }}</small></span></div>
      <div class="rp-service"><span class="rp-service-icon"><Headphones aria-hidden="true" /></span><span><strong>{{ bn ? "গ্রাহক সহায়তা" : "Customer Support" }}</strong><small>{{ bn ? "সপ্তাহে ৭ দিন সাপোর্ট" : "Dedicated support, 7 days" }}</small></span></div>
    </div>

    <section class="rp-section rp-container" aria-labelledby="category-title">
      <div class="rp-section-heading">
        <div><h2 id="category-title">{{ bn ? "ক্যাটাগরি অনুযায়ী কিনুন" : "Shop By Category" }}</h2><p>{{ bn ? "আপনার রান্নাঘরের প্রয়োজনীয় সবকিছু এক জায়গায়।" : "Everything you need for your kitchen, ready for you." }}</p></div>
        <router-link to="/categories" class="rp-view-link">{{ bn ? "সব ক্যাটাগরি" : "View All Categories" }} <ArrowRight aria-hidden="true" /></router-link>
      </div>
      <div v-if="categories.length" class="rp-category-grid">
        <router-link v-for="category in categories.slice(0, 8)" :key="category.id" :to="{ path: '/products-list', query: { category: category.id } }" class="rp-category">
          <span class="rp-category-image"><img v-if="category.imageUrl" :src="category.imageUrl" :alt="category.name" /><CategoryIcon v-else :name="category.name" :icon="category.icon" class-name="w-10 h-10" /></span>
          <span>{{ category.name }}</span>
        </router-link>
      </div>
      <div v-else class="rp-category-grid" aria-hidden="true"><div v-for="i in 8" :key="i" class="rp-category-skeleton"></div></div>
    </section>

    <section class="rp-section rp-container" aria-labelledby="popular-title">
      <div class="rp-section-heading rp-product-heading">
        <div><h2 id="popular-title">{{ bn ? "জনপ্রিয় পণ্য" : "Popular Products" }}</h2><div class="rp-tabs" aria-label="Popular product categories">
          <button type="button" :class="{ active: selectedPopularCategory === null }" :aria-pressed="selectedPopularCategory === null" @click="selectedPopularCategory = null">{{ bn ? "সব" : "All" }}</button>
          <button v-for="tab in popularCategoryTabs" :key="`popular-tab-${tab.key}`" type="button" :class="{ active: selectedPopularCategory === tab.key }" :aria-pressed="selectedPopularCategory === tab.key" @click="selectPopularCategory(tab.key)">{{ tab.name }}</button>
        </div></div>
        <router-link to="/products-list" class="rp-view-link">{{ bn ? "সেরা বিক্রি" : "Best Selling Products" }} <ArrowRight aria-hidden="true" /></router-link>
      </div>
      <SkeletonLoader v-if="loading" type="card" :count="5" />
      <div v-else-if="popularProducts.length" class="rp-product-grid"><ProductCard v-for="product in popularProducts" :key="`popular-${product.id}`" :deal="product" /></div>
      <div v-else class="rp-empty">{{ bn ? "কোনো পণ্য পাওয়া যায়নি।" : "No products available yet." }}</div>
    </section>

    <section class="rp-container rp-promos" aria-label="Promotions">
      <template v-if="miniBanners.length">
        <router-link v-for="banner in miniBanners" :key="`mini-banner-${banner.id}`" :to="banner.link || '/products-list'" class="rp-promo rp-promo-image">
          <img :src="banner.image" :alt="banner.title || 'Promotion'" />
          <span v-if="banner.title || banner.description || banner.buttonText" class="rp-promo-overlay"></span>
          <div class="rp-promo-content">
            <p v-if="banner.badge">{{ banner.badge }}</p>
            <h2 v-if="banner.title">{{ banner.title }}</h2>
            <span v-if="banner.description">{{ banner.description }}</span>
            <strong v-if="banner.buttonText">{{ banner.buttonText }} <ArrowRight aria-hidden="true" /></strong>
          </div>
        </router-link>
      </template>
      <router-link v-if="!miniBanners[0]" to="/products-list" class="rp-promo rp-promo-green">
        <div><p>{{ bn ? "সময় বাঁচান" : "Save Time" }}</p><h2>{{ bn ? "রান্না করুন আরও সহজে" : "Cook Smarter" }}</h2><span>{{ bn ? "পরিষ্কার, কাটা ও স্বাস্থ্যসম্মতভাবে প্যাক করা" : "Freshly cut, hygienically packed" }}</span><strong>{{ bn ? "এখনই কিনুন" : "Shop Now" }} <ArrowRight aria-hidden="true" /></strong></div><ShoppingBasket aria-hidden="true" />
      </router-link>
      <router-link v-if="!miniBanners[1]" to="/deals" class="rp-promo rp-promo-warm">
        <div><p>{{ bn ? "বিশেষ অফার" : "Special Offer" }}</p><h2>{{ bn ? "নতুন গ্রাহকদের জন্য" : "For New Customers" }}</h2><span>{{ bn ? "প্রথম অর্ডারে আকর্ষণীয় ছাড় পান" : "Get a special discount on your first order" }}</span><strong>{{ bn ? "অফার দেখুন" : "View Offer" }} <ArrowRight aria-hidden="true" /></strong></div><Leaf aria-hidden="true" />
      </router-link>
    </section>

    <section class="rp-section rp-container" aria-labelledby="special-title">
      <div class="rp-section-heading rp-product-heading">
        <div><h2 id="special-title">{{ bn ? "বিশেষ পণ্য" : "Special Products" }}</h2><div class="rp-tabs" aria-label="Special product groups"><span class="active">{{ bn ? "নির্বাচিত" : "Featured" }}</span><span>{{ bn ? "নতুন" : "New Arrivals" }}</span><span>{{ bn ? "সেরা রেটিং" : "Top Rated" }}</span></div></div>
        <router-link to="/products-list" class="rp-view-link">{{ bn ? "সব পণ্য" : "View All Products" }} <ArrowRight aria-hidden="true" /></router-link>
      </div>
      <SkeletonLoader v-if="loading" type="card" :count="5" />
      <div v-else-if="specialProducts.length" class="rp-product-grid"><ProductCard v-for="product in specialProducts" :key="`special-${product.id}`" :deal="product" /></div>
      <div v-else class="rp-empty">{{ bn ? "কোনো বিশেষ পণ্য পাওয়া যায়নি।" : "No special products available yet." }}</div>
    </section>

    <section v-for="section in visibleHomeSections" :key="section.id" class="rp-section rp-container" :aria-labelledby="`home-section-${section.id}`">
      <div class="rp-section-heading"><div><h2 :id="`home-section-${section.id}`">{{ section.title }}</h2><p v-if="section.subtitle">{{ section.subtitle }}</p></div><router-link to="/products-list" class="rp-view-link">{{ bn ? "সব দেখুন" : "View All" }} <ArrowRight aria-hidden="true" /></router-link></div>
      <div class="rp-product-grid"><ProductCard v-for="product in section.products.slice(0, 10)" :key="`${section.id}-${product.id}`" :deal="product" /></div>
    </section>

    <div class="rp-container rp-values" aria-label="Why choose us">
      <div><span><Leaf aria-hidden="true" /></span><p><strong>{{ bn ? "১০০% টাটকা" : "100% Fresh" }}</strong><small>{{ bn ? "বিশ্বাসযোগ্য মান" : "Quality you can trust" }}</small></p></div>
      <div><span><Clock3 aria-hidden="true" /></span><p><strong>{{ bn ? "সময় বাঁচায়" : "Saves Time" }}</strong><small>{{ bn ? "জরুরি কাজের জন্য সময়" : "More time for what matters" }}</small></p></div>
      <div><span><Heart aria-hidden="true" /></span><p><strong>{{ bn ? "স্বাস্থ্যকর পছন্দ" : "Healthy Choice" }}</strong><small>{{ bn ? "টাটকা ও পুষ্টিকর" : "Fresh and nutritious" }}</small></p></div>
      <div><span><ShieldCheck aria-hidden="true" /></span><p><strong>{{ bn ? "হালাল ও নিরাপদ" : "Halal & Safe" }}</strong><small>{{ bn ? "যত্ন নিয়ে প্রস্তুত" : "Prepared with care" }}</small></p></div>
    </div>

    <section class="rp-newsletter" aria-labelledby="newsletter-title">
      <div class="rp-container rp-newsletter-inner">
        <div><h2 id="newsletter-title">{{ subscribed ? (bn ? "ধন্যবাদ!" : "Thank you!") : (bn ? "আপডেট ও বিশেষ অফার পান" : "Get Updates & Special Offers") }}</h2><p>{{ subscribed ? (bn ? "পরবর্তী অফারগুলো আমরা আপনার কাছে পৌঁছে দেব।" : "We will keep you posted about our next offers.") : (bn ? "নতুন পণ্য ও ছাড়ের খবর সবার আগে জানুন।" : "Be the first to know about new products and discounts.") }}</p></div>
        <form v-if="!subscribed" class="rp-subscribe" @submit.prevent="subscribe"><Mail aria-hidden="true" /><label for="newsletter-email" class="sr-only">Email address</label><input id="newsletter-email" v-model="email" type="email" required :placeholder="bn ? 'আপনার ইমেইল ঠিকানা' : 'Your email address'" /><button type="submit">{{ bn ? "সাবস্ক্রাইব" : "Subscribe" }}</button></form>
      </div>
    </section>
    </template>
  </main>
</template>

<style scoped>
.rp-home { --rp-green: #075c32; --rp-green-dark: #064526; --rp-orange: #ed6a25; --rp-ink: #101512; --rp-muted: #667069; background: #fff; color: var(--rp-ink); }
.rp-home-loading { overflow: hidden; background: #fff; }
.rp-skeleton-hero { min-height: 560px; position: relative; overflow: hidden; background: #f6f8f6; }
.rp-skeleton-hero-copy { position: relative; z-index: 1; min-height: 560px; padding-block: 110px 80px; display: flex; flex-direction: column; justify-content: center; gap: 14px; }
.rp-skeleton-line, .rp-skeleton-heading i, .rp-skeleton-heading span, .rp-skeleton-service i, .rp-skeleton-promos span, .rp-skeleton-newsletter span, .rp-skeleton-newsletter b { display: block; border-radius: 6px; background: linear-gradient(90deg, #e7ece8 25%, #f5f7f5 50%, #e7ece8 75%); background-size: 200% 100%; animation: rp-skeleton-shimmer 1.4s ease-in-out infinite; }
.rp-skeleton-eyebrow { width: 150px; height: 16px; }
.rp-skeleton-title { width: min(530px, 68vw); height: 58px; border-radius: 10px; }
.rp-skeleton-title-short { width: min(420px, 56vw); }
.rp-skeleton-copy { width: min(430px, 60vw); height: 14px; margin-top: 8px; }
.rp-skeleton-copy-short { width: min(340px, 48vw); margin-top: 0; }
.rp-skeleton-actions { display: flex; gap: 14px; margin-top: 18px; }
.rp-skeleton-actions span { width: 142px; height: 48px; border-radius: 6px; background: #dce7df; animation: rp-skeleton-pulse 1.4s ease-in-out infinite; }
.rp-skeleton-actions span + span { width: 138px; background: #e8eeea; }
.rp-skeleton-customer { display: flex; align-items: center; gap: 0; margin-top: 22px; }
.rp-skeleton-customer span { width: 30px; height: 30px; margin-left: -7px; border: 2px solid #f6f8f6; border-radius: 50%; background: #dce7df; animation: rp-skeleton-pulse 1.4s ease-in-out infinite; }
.rp-skeleton-customer span:first-child { margin-left: 0; }
.rp-skeleton-customer i { width: 135px; height: 12px; margin-left: 14px; border-radius: 5px; background: #e0e8e2; animation: rp-skeleton-pulse 1.4s ease-in-out infinite; }
.rp-skeleton-hero-image { position: absolute; inset: 0 0 0 48%; background: linear-gradient(135deg, #eef2ee, #e3eae4); opacity: .9; animation: rp-skeleton-pulse 1.8s ease-in-out infinite; }
.rp-skeleton-services { min-height: 102px; margin-top: -42px; position: relative; z-index: 2; display: grid; grid-template-columns: repeat(3, 1fr); align-items: center; padding: 20px 30px; border: 1px solid #edf1ed; border-radius: 8px; background: #fff; box-shadow: 0 16px 40px rgba(17,50,30,.08); }
.rp-skeleton-service { display: flex; align-items: center; justify-content: center; gap: 14px; padding: 8px 20px; }
.rp-skeleton-service + .rp-skeleton-service { border-left: 1px solid #edf1ed; }
.rp-skeleton-service > span { width: 34px; height: 34px; border-radius: 50%; background: #dfe9e1; animation: rp-skeleton-pulse 1.4s ease-in-out infinite; }
.rp-skeleton-service div { display: flex; flex-direction: column; gap: 7px; }
.rp-skeleton-service i:first-child { width: 130px; height: 13px; }
.rp-skeleton-service i:last-child { width: 105px; height: 10px; }
.rp-skeleton-heading { display: flex; align-items: flex-end; justify-content: space-between; gap: 22px; margin-bottom: 25px; }
.rp-skeleton-heading > div { display: flex; flex-direction: column; gap: 9px; }
.rp-skeleton-heading i:first-child { width: 220px; height: 30px; }
.rp-skeleton-heading i:last-child { width: 280px; height: 12px; }
.rp-skeleton-heading > span { width: 110px; height: 13px; }
.rp-skeleton-categories { display: grid; grid-template-columns: repeat(8, minmax(0, 1fr)); gap: 20px; }
.rp-skeleton-categories > div { display: flex; flex-direction: column; align-items: center; gap: 13px; }
.rp-skeleton-categories span { width: min(100%, 110px); aspect-ratio: 1; border-radius: 50%; background: #edf1ed; animation: rp-skeleton-pulse 1.4s ease-in-out infinite; }
.rp-skeleton-categories i { width: 70px; height: 11px; border-radius: 5px; background: #e7ece8; animation: rp-skeleton-pulse 1.4s ease-in-out infinite; }
.rp-skeleton-products { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 24px; }
.rp-skeleton-products > div { display: flex; flex-direction: column; gap: 10px; }
.rp-skeleton-products span { aspect-ratio: 1; border-radius: 8px; background: #edf1ed; animation: rp-skeleton-pulse 1.4s ease-in-out infinite; }
.rp-skeleton-products i { width: 88%; height: 12px; border-radius: 5px; background: #e7ece8; animation: rp-skeleton-pulse 1.4s ease-in-out infinite; }
.rp-skeleton-products i:nth-child(3) { width: 62%; }
.rp-skeleton-products b { width: 52%; height: 28px; margin-top: 3px; border-radius: 5px; background: #dce7df; animation: rp-skeleton-pulse 1.4s ease-in-out infinite; }
.rp-skeleton-promos { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; padding-top: 58px; }
.rp-skeleton-promos span { min-height: 250px; border-radius: 8px; }
.rp-skeleton-values { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; padding-block: 68px 48px; }
.rp-skeleton-values span { height: 58px; border-radius: 30px; background: #edf4ee; animation: rp-skeleton-pulse 1.4s ease-in-out infinite; }
.rp-skeleton-newsletter { min-height: 130px; padding-block: 30px; background: #f0f8f1; }
.rp-skeleton-newsletter .rp-container { display: flex; justify-content: space-between; align-items: center; gap: 40px; }
.rp-skeleton-newsletter span { width: 330px; height: 24px; }
.rp-skeleton-newsletter b { width: min(480px, 42%); height: 50px; border-radius: 5px; }
@keyframes rp-skeleton-shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
@keyframes rp-skeleton-pulse { 50% { opacity: .55; } }
.rp-container { width: min(1180px, calc(100% - 40px)); margin-inline: auto; }
.rp-hero { position: relative; min-height: 560px; overflow: hidden; background: #f7f6f1; }
.rp-hero-image { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: center; }
.rp-hero-shade { position: absolute; inset: 0; background: linear-gradient(90deg, rgba(255,255,255,.98) 0%, rgba(255,255,255,.93) 32%, rgba(255,255,255,.35) 52%, transparent 68%); }
.rp-hero-content { position: relative; z-index: 1; min-height: 560px; display: flex; flex-direction: column; justify-content: center; align-items: flex-start; padding-block: 64px 92px; }
.rp-eyebrow { display: flex; align-items: center; gap: 8px; color: var(--rp-green); font-size: 15px; font-weight: 700; }
.rp-eyebrow svg { width: 18px; height: 18px; }
.rp-hero h1 { margin: 12px 0 14px; max-width: 590px; font-size: clamp(42px, 5vw, 68px); line-height: 1.04; letter-spacing: -.045em; font-weight: 800; }
.rp-hero-copy { max-width: 510px; color: #59615b; font-size: 16px; line-height: 1.65; }
.rp-hero-actions { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 28px; }
.rp-btn { min-height: 48px; padding: 0 24px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; gap: 9px; font-size: 14px; font-weight: 700; transition: background-color .2s, color .2s, border-color .2s; }
.rp-btn svg { width: 17px; }
.rp-btn-primary { color: #fff; background: var(--rp-green); border: 1px solid var(--rp-green); }
.rp-btn-primary:hover { background: var(--rp-green-dark); }
.rp-btn-secondary { color: var(--rp-ink); background: #fff; border: 1px solid #dce2dd; }
.rp-btn-secondary:hover { border-color: var(--rp-green); color: var(--rp-green); }
.rp-customer-note { display: flex; align-items: center; gap: 12px; margin-top: 28px; font-size: 13px; }
.rp-avatar-stack { display: flex; }
.rp-avatar-stack span { width: 30px; height: 30px; margin-left: -7px; display: grid; place-items: center; border: 2px solid white; border-radius: 50%; background: #dfeee3; color: var(--rp-green); font-size: 11px; font-weight: 800; }
.rp-avatar-stack span:first-child { margin-left: 0; }
.rp-services { position: relative; z-index: 3; margin-top: -42px; min-height: 102px; padding: 20px 30px; display: grid; grid-template-columns: repeat(3, 1fr); align-items: center; border: 1px solid #e9eee9; border-radius: 8px; background: rgba(255,255,255,.96); box-shadow: 0 16px 40px rgba(17,50,30,.10); backdrop-filter: blur(8px); }
.rp-service { display: flex; align-items: center; justify-content: center; gap: 15px; padding: 8px 24px; }
.rp-service + .rp-service { border-left: 1px solid #e7ebe8; }
.rp-service-icon { color: var(--rp-green); }
.rp-service-icon svg { width: 34px; height: 34px; stroke-width: 1.7; }
.rp-service strong, .rp-service small { display: block; }
.rp-service strong { font-size: 14px; }
.rp-service small { margin-top: 5px; color: var(--rp-muted); font-size: 12px; }
.rp-section { padding-top: 64px; }
.rp-section-heading { display: flex; justify-content: space-between; align-items: flex-end; gap: 24px; margin-bottom: 25px; }
.rp-section-heading h2 { font-size: clamp(25px, 2.2vw, 34px); line-height: 1.2; letter-spacing: -.035em; font-weight: 800; }
.rp-section-heading p { margin-top: 7px; color: var(--rp-muted); font-size: 14px; }
.rp-view-link { display: inline-flex; align-items: center; gap: 8px; padding-block: 8px; border-bottom: 1px solid #9ea7a0; font-size: 12px; font-weight: 700; white-space: nowrap; }
.rp-view-link:hover { color: var(--rp-green); border-color: var(--rp-green); }
.rp-view-link svg { width: 14px; height: 14px; }
.rp-category-grid { display: grid; grid-template-columns: repeat(8, minmax(0, 1fr)); gap: 20px; }
.rp-category { display: flex; flex-direction: column; align-items: center; gap: 14px; color: #202622; text-align: center; font-size: 13px; font-weight: 700; }
.rp-category-image { width: min(100%, 110px); aspect-ratio: 1; display: grid; place-items: center; overflow: hidden; border-radius: 50%; background: linear-gradient(145deg, #f5f7f3, #e9eee8); color: var(--rp-green); box-shadow: inset 0 0 0 1px #e5ebe5; transition: transform .2s, box-shadow .2s; }
.rp-category:hover .rp-category-image { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(7,92,50,.14), inset 0 0 0 1px #c9ddcf; }
.rp-category-image img { width: 100%; height: 100%; object-fit: cover; }
.rp-category-skeleton { aspect-ratio: 1; border-radius: 50%; background: #edf0ed; animation: pulse 1.5s infinite; }
.rp-product-heading { align-items: center; }
.rp-tabs { display: flex; flex-wrap: wrap; gap: 26px; margin-top: 14px; color: #69716c; font-size: 12px; }
.rp-tabs span, .rp-tabs button { position: relative; padding-bottom: 8px; }
.rp-tabs button { border: 0; padding-inline: 0; color: inherit; background: transparent; font: inherit; cursor: pointer; }
.rp-tabs button:hover { color: var(--rp-ink); }
.rp-tabs .active { color: var(--rp-ink); font-weight: 700; }
.rp-tabs .active::after { content: ""; position: absolute; left: 0; bottom: 0; width: 18px; height: 2px; background: var(--rp-green); }
.rp-product-grid { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 24px; }
.rp-empty { padding: 48px 20px; border-radius: 8px; background: #f6f8f6; color: var(--rp-muted); text-align: center; }
.rp-promos { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; padding-top: 58px; }
.rp-promo { min-height: 250px; padding: 34px 38px; position: relative; overflow: hidden; display: flex; align-items: center; border-radius: 8px; }
.rp-promo > div { position: relative; z-index: 2; max-width: 72%; }
.rp-promo p { font-size: 16px; font-weight: 800; }
.rp-promo h2 { margin-top: 3px; font-size: clamp(28px, 3vw, 42px); line-height: .98; letter-spacing: -.04em; font-weight: 800; }
.rp-promo span { display: block; margin-top: 12px; font-size: 13px; }
.rp-promo strong { width: fit-content; min-height: 42px; margin-top: 20px; padding: 0 18px; display: inline-flex; align-items: center; gap: 7px; border-radius: 5px; background: var(--rp-green); color: white; font-size: 13px; }
.rp-promo strong svg { width: 14px; }
.rp-promo > svg { position: absolute; right: 28px; bottom: 15px; width: 150px; height: 150px; stroke-width: .9; opacity: .18; }
.rp-promo-green { background: linear-gradient(120deg, #d8f0d4, #bfe6bb); color: #102214; }
.rp-promo-warm { background: linear-gradient(120deg, #fff0df, #ffe0c5); color: #8d2d17; }
.rp-promo-image { color: #fff; background: #1f3326; }
.rp-promo-image > img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
.rp-promo-overlay { position: absolute; inset: 0; background: transparent; pointer-events: none; }
.rp-promo-image .rp-promo-content { max-width: 76%; }
.rp-promo-image h2 { font-size: clamp(24px, 2.5vw, 34px); line-height: 1.08; }
.rp-promo-image p { color: #d9f3df; }
.rp-promo-image span:not(.rp-promo-overlay) { color: rgba(255,255,255,.88); }
.rp-values { margin-top: 68px; padding-block: 28px 48px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
.rp-values > div { display: flex; align-items: center; gap: 14px; }
.rp-values > div > span { width: 58px; height: 58px; flex: 0 0 58px; display: grid; place-items: center; border-radius: 50%; background: #eaf6eb; color: var(--rp-green); }
.rp-values svg { width: 27px; height: 27px; stroke-width: 1.7; }
.rp-values strong, .rp-values small { display: block; }
.rp-values strong { font-size: 13px; }
.rp-values small { margin-top: 4px; color: var(--rp-muted); font-size: 11px; }
.rp-newsletter { background: linear-gradient(90deg, #f0f8f1, #e6f4e8); }
.rp-newsletter-inner { min-height: 130px; display: flex; align-items: center; justify-content: space-between; gap: 40px; }
.rp-newsletter h2 { font-size: 24px; font-weight: 800; letter-spacing: -.025em; }
.rp-newsletter p { margin-top: 7px; color: var(--rp-muted); font-size: 13px; }
.rp-subscribe { width: min(480px, 100%); min-height: 50px; position: relative; display: flex; align-items: center; border: 1px solid #dce7dd; border-radius: 5px; background: white; overflow: hidden; }
.rp-subscribe > svg { width: 18px; margin-left: 16px; color: #8b948d; }
.rp-subscribe input { min-width: 0; flex: 1; align-self: stretch; padding: 0 14px; outline: none; font-size: 13px; }
.rp-subscribe button { align-self: stretch; padding: 0 28px; background: var(--rp-green); color: white; font-size: 13px; font-weight: 700; }
.rp-subscribe button:hover { background: var(--rp-green-dark); }
@keyframes pulse { 50% { opacity: .5; } }
@media (max-width: 1023px) {
  .rp-hero, .rp-hero-content { min-height: 500px; }
  .rp-skeleton-hero, .rp-skeleton-hero-copy { min-height: 500px; }
  .rp-category-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
  .rp-skeleton-categories { grid-template-columns: repeat(4, minmax(0, 1fr)); }
  .rp-product-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
  .rp-skeleton-products { grid-template-columns: repeat(3, minmax(0, 1fr)); }
  .rp-services { padding-inline: 12px; }
  .rp-skeleton-services { padding-inline: 12px; }
  .rp-service { padding-inline: 12px; }
  .rp-skeleton-service { padding-inline: 12px; }
  .rp-values { grid-template-columns: repeat(2, 1fr); }
  .rp-skeleton-values { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 767px) {
  .rp-container { width: min(100% - 28px, 1180px); }
  .rp-hero { min-height: 570px; }
  .rp-skeleton-hero, .rp-skeleton-hero-copy { min-height: 570px; }
  .rp-skeleton-hero-image { inset: 42% 0 0; }
  .rp-skeleton-hero-copy { padding-top: 54px; }
  .rp-hero-image { object-position: 67% center; }
  .rp-hero-shade { background: linear-gradient(180deg, rgba(255,255,255,.94) 0%, rgba(255,255,255,.93) 52%, rgba(255,255,255,.58) 74%, rgba(255,255,255,.2) 100%); }
  .rp-hero-content { min-height: 570px; justify-content: flex-start; padding-top: 54px; }
  .rp-hero h1 { font-size: 42px; max-width: 400px; }
  .rp-hero-copy { max-width: 420px; font-size: 14px; }
  .rp-services { margin-top: -32px; padding: 16px; grid-template-columns: 1fr; }
  .rp-skeleton-services { margin-top: -32px; padding: 16px; grid-template-columns: 1fr; }
  .rp-service { justify-content: flex-start; padding: 12px; }
  .rp-skeleton-service { justify-content: flex-start; padding: 12px; }
  .rp-service + .rp-service { border-left: 0; border-top: 1px solid #e7ebe8; }
  .rp-skeleton-service + .rp-skeleton-service { border-left: 0; border-top: 1px solid #edf1ed; }
  .rp-section { padding-top: 48px; }
  .rp-section-heading { align-items: flex-start; }
  .rp-product-heading { flex-direction: column; }
  .rp-product-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
  .rp-skeleton-products { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
  .rp-promos { grid-template-columns: 1fr; padding-top: 48px; }
  .rp-skeleton-promos { grid-template-columns: 1fr; padding-top: 48px; }
  .rp-promo { min-height: 220px; padding: 28px; }
  .rp-skeleton-promos span { min-height: 220px; }
  .rp-newsletter-inner { padding-block: 30px; flex-direction: column; align-items: flex-start; gap: 22px; }
  .rp-skeleton-newsletter .rp-container { align-items: flex-start; flex-direction: column; gap: 22px; }
  .rp-skeleton-newsletter span, .rp-skeleton-newsletter b { width: 100%; }
}
@media (max-width: 479px) {
  .rp-hero h1 { font-size: 35px; }
  .rp-customer-note { align-items: flex-start; flex-direction: column; }
  .rp-category-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px 8px; }
  .rp-skeleton-categories { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px 8px; }
  .rp-category { font-size: 10px; }
  .rp-category-image { width: 68px; }
  .rp-section-heading h2 { font-size: 24px; }
  .rp-view-link { display: none; }
  .rp-tabs { gap: 16px; }
  .rp-promo > div { max-width: 82%; }
  .rp-values { grid-template-columns: 1fr; }
  .rp-skeleton-values { grid-template-columns: 1fr; }
  .rp-subscribe > svg { display: none; }
  .rp-subscribe button { padding-inline: 16px; }
}
@media (prefers-reduced-motion: reduce) { .rp-category-image, .rp-btn { transition: none; } }
</style>
