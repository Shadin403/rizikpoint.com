<script setup>
import { AlertCircle } from "@lucide/vue";
import { useI18n } from "@/lib/i18n";
import { usePageTitle } from "@/composables/usePageTitle";
import { pageTitles } from "@/lib/pageTitles";
usePageTitle(pageTitles.NotFound);
const { t } = useI18n();
</script>

<template>
  <div class="min-h-[50vh] w-full flex items-center justify-center bg-gray-50/50 p-6 flex-1">
    <div class="w-full max-w-md bg-white border border-gray-200 rounded-2xl shadow-sm p-6 text-center">
      <div class="flex flex-col items-center gap-3">
        <AlertCircle class="h-12 w-12 text-red-500 animate-pulse" />
        <h1 class="text-xl font-bold text-gray-900 font-display">{{ t('not_found_title') }}</h1>
        <p class="text-xs text-gray-500 leading-relaxed mt-1">
          {{ t('not_found_desc') }}
        </p>
        <router-link to="/" class="mt-4 bg-green-600 hover:bg-green-700 text-white font-semibold text-xs px-5 py-2.5 rounded-lg transition-colors cursor-pointer">
          {{ t('go_home') }}
        </router-link>
      </div>
    </div>
  </div>
</template>

