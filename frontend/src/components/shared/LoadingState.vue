<script setup>
defineProps({
  message: { type: String, default: "লোড হচ্ছে..." }
});
</script>

<template>
  <div class="flex flex-col items-center justify-center py-12 gap-3">
    <div class="w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin" />
    <p class="text-sm text-gray-500 font-medium">{{ message }}</p>
  </div>
</template>
