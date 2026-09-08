<script setup>
import { computed } from 'vue';
import { ArrowRight, Leaf, ShoppingBasket, Package, ChevronRight } from '@lucide/vue';
import { useI18n } from '@/lib/i18n';
import { useCategories } from '@/composables/useBusinessSettings';
import CategoryIcon from './shared/CategoryIcon.vue';

const props = defineProps({ products: { type: Array, default: () => [] } });
const { locale } = useI18n();
const { categories } = useCategories();
const bn = computed(() => locale.value === 'bn');
const picks = computed(() => props.products.filter(p => p.imageUrl).slice(0, 3));
function imageSource(path) {
  return /^https?:\/\//.test(path) ? path : `${import.meta.env.VITE_BACKEND_ORIGIN || 'http://127.0.0.1:8000'}/${path.replace(/^\//, '')}`;
}
</script>

<template>
  <section class="organic-intro" :aria-label="bn ? 'প্রতিদিনের বাজার' : 'Everyday groceries'">
    <div class="organic-hero">
      <div class="organic-hero-copy">
        <span class="organic-eyebrow"><Leaf :size="16" aria-hidden="true" /> {{ bn ? 'অর্গানিক ও গ্রোসারি' : 'ORGANIC & GROCERY' }}</span>
        <h1>{{ bn ? 'প্রতিদিনের বাজারে,' : 'Everyday essentials,' }}<br><span>{{ bn ? 'প্রকৃতির ছোঁয়া।' : 'a little closer to nature.' }}</span></h1>
        <p>{{ bn ? 'রান্নাঘরের প্রয়োজন থেকে পছন্দের অর্গানিক পণ্য—নিজের মতো বেছে নিন, এক জায়গায়।' : 'From kitchen staples to your organic favourites. Find what belongs in your basket, all in one place.' }}</p>
        <div class="organic-hero-actions">
          <router-link to="/products-list" class="organic-button">{{ bn ? 'বাজার শুরু করুন' : 'Shop groceries' }} <ArrowRight :size="18" aria-hidden="true" /></router-link>
          <router-link to="/categories" class="organic-text-link">{{ bn ? 'ক্যাটাগরি দেখুন' : 'Explore categories' }} <ChevronRight :size="16" aria-hidden="true" /></router-link>
        </div>
        <div class="organic-hero-note"><ShoppingBasket :size="17" aria-hidden="true" /> {{ bn ? 'আপনার পছন্দে, আপনার প্রতিদিনের বাজার' : 'Your favourites. Your everyday basket.' }}</div>
      </div>
      <div class="organic-selection">
        <div class="organic-selection-heading"><span>{{ bn ? 'আপনার বাজারের ঝুড়ি' : 'IN YOUR EVERYDAY BASKET' }}</span><Leaf :size="24" aria-hidden="true" /></div>
        <div v-if="picks.length" class="organic-picks">
          <router-link v-for="product in picks" :key="product.id" :to="`/products/${product.slug || product.id}`" class="organic-pick">
            <img :src="imageSource(product.imageUrl)" alt="" @error="$event.target.style.visibility = 'hidden'">
            <span>{{ product.title }}</span>
            <ArrowRight :size="16" aria-hidden="true" />
          </router-link>
        </div>
        <div v-else class="organic-basket-placeholder"><ShoppingBasket :size="80" :stroke-width="1" aria-hidden="true" /><p>{{ bn ? 'ভালো খাবার, সুন্দর প্রতিদিন' : 'Good food. Better everyday.' }}</p></div>
        <router-link to="/products-list" class="organic-selection-footer">{{ bn ? 'সব পণ্য ঘুরে দেখুন' : 'Discover the collection' }} <ArrowRight :size="18" aria-hidden="true" /></router-link>
      </div>
    </div>
    <div class="organic-service-strip">
      <div><Leaf aria-hidden="true" /><span><strong>{{ bn ? 'আপনার পছন্দের বাজার' : 'Groceries your way' }}</strong><small>{{ bn ? 'প্রয়োজন অনুযায়ী পণ্য বাছুন' : 'Choose what you need' }}</small></span></div>
      <div><ShoppingBasket aria-hidden="true" /><span><strong>{{ bn ? 'সহজে কেনাকাটা' : 'A simpler shop' }}</strong><small>{{ bn ? 'পছন্দের পণ্য কার্টে রাখুন' : 'Keep your favourites in your cart' }}</small></span></div>
      <div><Package aria-hidden="true" /><span><strong>{{ bn ? 'অর্ডার এক জায়গায়' : 'Orders in one place' }}</strong><small>{{ bn ? 'অ্যাকাউন্টে অর্ডারের তথ্য দেখুন' : 'Find order details in your account' }}</small></span></div>
    </div>
    <div v-if="categories.length" class="organic-categories">
      <div class="organic-section-heading"><div><span class="organic-eyebrow">{{ bn ? 'যা প্রয়োজন, সহজেই খুঁজুন' : 'FIND YOUR ESSENTIALS' }}</span><h2>{{ bn ? 'ক্যাটাগরি ধরে কেনাকাটা' : 'Shop by category' }}</h2></div><router-link to="/categories" class="organic-text-link">{{ bn ? 'সব দেখুন' : 'View all' }} <ArrowRight :size="16" aria-hidden="true" /></router-link></div>
      <div class="organic-category-grid">
        <router-link v-for="category in categories.slice(0, 8)" :key="category.id" :to="{ path: '/products-list', query: { category: category.id } }" class="organic-category">
          <span class="organic-category-icon"><CategoryIcon :name="category.name" :icon="category.icon" className="w-7 h-7" /></span>
          <span>{{ category.name }}</span>
        </router-link>
      </div>
    </div>
  </section>
</template>
