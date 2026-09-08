<script setup>
import { computed } from "vue";
import { ChevronLeft, ChevronRight } from "@lucide/vue";

const props = defineProps({
  currentPage: { type: Number, required: true },
  lastPage:    { type: Number, required: true },
  total:       { type: Number, default: 0 },
  perPage:     { type: Number, default: 0 },
});
const emit = defineEmits(["change"]);

const pages = computed(() => {
  const total = props.lastPage;
  const cur = props.currentPage;
  if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);
  const out = new Set([1, total, cur, cur - 1, cur + 1]);
  return [...out].filter((p) => p >= 1 && p <= total).sort((a, b) => a - b);
});

function go(p) {
  if (p < 1 || p > props.lastPage || p === props.currentPage) return;
  emit("change", p);
}
</script>

<template>
  <nav v-if="lastPage > 1" class="flex flex-col sm:flex-row items-center justify-between gap-3 mt-8" aria-label="Pagination">
    <p class="text-xs text-gray-500">
      Page <span class="font-bold text-gray-700">{{ currentPage }}</span> of
      <span class="font-bold text-gray-700">{{ lastPage }}</span>
      <span v-if="total"> &middot; {{ total }} items</span>
    </p>

    <div class="flex items-center gap-1">
      <button
        type="button"
        @click="go(currentPage - 1)"
        :disabled="currentPage <= 1"
        class="h-9 px-3 inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white text-xs font-bold text-gray-600 hover:border-[#168039] hover:text-[#168039] disabled:opacity-40 disabled:cursor-not-allowed transition-colors cursor-pointer"
      >
        <ChevronLeft class="w-3.5 h-3.5" /> Prev
      </button>

      <template v-for="(p, i) in pages" :key="p + '-' + i">
        <span
          v-if="i > 0 && p - pages[i - 1] > 1"
          class="px-1 text-gray-400 text-xs select-none"
        >...</span>
        <button
          type="button"
          @click="go(p)"
          :aria-current="p === currentPage ? 'page' : undefined"
          class="h-9 min-w-9 px-3 inline-flex items-center justify-center rounded-lg border text-xs font-bold transition-colors cursor-pointer"
          :class="p === currentPage
            ? 'bg-[#168039] text-white border-[#146c30] shadow-2xs'
            : 'bg-white text-gray-600 border-gray-200 hover:border-[#168039] hover:text-[#168039]'"
        >
          {{ p }}
        </button>
      </template>

      <button
        type="button"
        @click="go(currentPage + 1)"
        :disabled="currentPage >= lastPage"
        class="h-9 px-3 inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white text-xs font-bold text-gray-600 hover:border-[#168039] hover:text-[#168039] disabled:opacity-40 disabled:cursor-not-allowed transition-colors cursor-pointer"
      >
        Next <ChevronRight class="w-3.5 h-3.5" />
      </button>
    </div>
  </nav>
</template>
