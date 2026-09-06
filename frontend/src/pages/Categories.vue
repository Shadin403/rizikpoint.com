<script setup>
import { ref, onMounted } from "vue";
import { Grid, ArrowRight } from "@lucide/vue";
import { fetchCategories } from "@/lib/api";
import { useI18n } from "@/lib/i18n";
import CategoryIcon from "@/components/shared/CategoryIcon.vue";
import SkeletonLoader from "@/components/shared/SkeletonLoader.vue";
import { usePageTitle } from "@/composables/usePageTitle";
import { pageTitles } from "@/lib/pageTitles";
usePageTitle(pageTitles.Categories);
const categories = ref([]);
const isLoading = ref(false);
const { locale, t } = useI18n();

onMounted(async () => {
  try {
    isLoading.value = true;
    categories.value = await fetchCategories();
  } catch (err) {
    console.error("Failed to load categories page:", err);
  } finally {
    isLoading.value = false;
  }
});
</script>

<template>
  <div class="px-4 py-8 bg-[#fdfdfd] flex-1">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-2xl font-display font-bold flex items-center gap-2 text-gray-800">
          <Grid class="w-6 h-6 text-green-600" />
          {{ t('all_categories') }}
        </h1>
        <p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ t('categories_desc') }}</p>
      </div>
    </div>

    <div v-if="isLoading" class="mt-4">
      <SkeletonLoader type="categories" :count="8" />
    </div>

    <!-- Empty State -->
    <div v-else-if="!categories || categories.length === 0" class="text-center py-20 bg-gray-50 rounded-xl border border-dashed border-gray-200 mt-8">
      <p class="text-gray-400 text-sm">{{ t('no_categories') }}</p>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <router-link
        v-for="category in categories"
        :key="category.id"
        :to="`/products-list?category=${category.id}`"
        class="border border-gray-100 rounded-xl p-4 hover:shadow-xs hover:border-green-300 transition-all group cursor-pointer flex items-center gap-4 bg-white"
      >
        <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center shrink-0 group-hover:bg-green-600 group-hover:text-white transition-colors">
          <CategoryIcon :name="category.name" class="w-6 h-6 text-green-600 group-hover:text-white transition-colors" />
        </div>
        <div class="flex-1">
          <h3 class="font-semibold text-sm group-hover:text-green-600 transition-colors text-gray-800">{{ category.name }}</h3>
          <p class="text-xs text-gray-400 mt-0.5">
            {{ (category.dealCount || 0) + (category.couponCount || 0) }} {{ t('total_offers') }}
          </p>
        </div>
        <ArrowRight class="w-4 h-4 text-gray-400 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all text-green-600" />
      </router-link>
    </div>
  </div>
</template>

