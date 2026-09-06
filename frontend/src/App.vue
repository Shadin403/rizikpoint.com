<script setup>
import Layout from "./components/Layout.vue";
import { useBusinessSettingsProvider, useCategoriesProvider } from "@/composables/useBusinessSettings";

useBusinessSettingsProvider()
useCategoriesProvider()
</script>

<template>
  <Layout />
</template>
