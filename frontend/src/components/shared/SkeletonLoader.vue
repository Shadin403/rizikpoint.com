<script setup>
defineProps({
  type: { type: String, default: "card" }, // 'card' | 'list' | 'details' | 'categories'
  count: { type: Number, default: 1 }
});
</script>

<template>
  <div class="w-full">
    <!-- Card Grid Skeleton -->
    <div v-if="type === 'card'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
      <div v-for="i in count" :key="i" class="bg-white rounded-lg border border-gray-100 p-2 flex flex-col gap-2.5 animate-pulse">
        <div class="aspect-square bg-gray-200 rounded-md w-full" />
        <div class="h-4 bg-gray-200 rounded w-5/6" />
        <div class="h-3 bg-gray-200 rounded w-2/3" />
        <div class="flex items-center gap-2">
          <div class="h-4 bg-gray-200 rounded w-1/3" />
          <div class="h-3 bg-gray-200 rounded w-1/4" />
        </div>
        <div class="h-8 bg-gray-200 rounded w-full mt-1" />
        <div class="h-8 bg-gray-200 rounded w-full" />
      </div>
    </div>

    <!-- List Skeleton (for Coupons or Deals) -->
    <div v-else-if="type === 'list'" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
      <div v-for="i in count" :key="i" class="border border-gray-100 rounded-lg p-5 bg-white flex flex-col sm:flex-row gap-4 animate-pulse">
        <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gray-200 rounded-lg shrink-0 mx-auto" />
        <div class="flex-1 flex flex-col gap-2 pt-1">
          <div class="h-4 bg-gray-200 rounded w-3/4" />
          <div class="h-3 bg-gray-200 rounded w-5/6" />
          <div class="h-3 bg-gray-200 rounded w-1/2 mt-2" />
          <div class="flex items-center justify-between mt-auto pt-3">
            <div class="h-4 bg-gray-200 rounded w-1/4" />
            <div class="h-8 bg-gray-200 rounded w-1/3" />
          </div>
        </div>
      </div>
    </div>

    <!-- Category Grid Skeleton -->
    <div v-else-if="type === 'categories'" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <div v-for="i in count" :key="i" class="border border-gray-100 rounded-xl p-4 bg-white flex items-center gap-4 animate-pulse">
        <div class="w-12 h-12 rounded-full bg-gray-200 shrink-0" />
        <div class="flex-1 flex flex-col gap-1.5">
          <div class="h-4 bg-gray-200 rounded w-1/2" />
          <div class="h-3 bg-gray-200 rounded w-1/3" />
        </div>
      </div>
    </div>

    <!-- Details View Skeleton -->
    <div v-else-if="type === 'details'" class="animate-pulse">
      <div class="mx-3 mb-4 bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm p-4">
        <!-- 2 column responsive layout skeleton -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <!-- Left Col: Image -->
          <div class="aspect-square bg-gray-200 rounded-lg w-full" />
          
          <!-- Right Col: Details -->
          <div class="flex flex-col gap-4 py-2">
            <div class="h-6 bg-gray-200 rounded w-5/6" />
            <div class="h-6 bg-gray-200 rounded w-2/3" />
            
            <div class="my-4">
              <div class="h-8 bg-gray-200 rounded w-1/3" />
            </div>

            <div class="h-12 bg-gray-200 rounded w-full" />
            <div class="h-12 bg-gray-200 rounded w-full" />

            <div class="border-t border-gray-100 pt-4 space-y-2 mt-4">
              <div class="h-8 bg-gray-200 rounded w-full" />
              <div class="h-8 bg-gray-200 rounded w-full" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
