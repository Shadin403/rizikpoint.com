<script setup>
import { ref } from "vue";
import { useAuth } from "@/store/auth";
import { X, Mail, Lock, User, Phone, Eye, EyeOff } from "@lucide/vue";

const {
  user,
  isOpen,
  mode,
  loading,
  error,
  closeAuth,
  setMode,
  login,
  register,
} = useAuth();

const emailOrPhone = ref("");
const password = ref("");

// Register fields
const fullName = ref("");
const confirmPassword = ref("");

const showPassword = ref(false);

const localError = ref("");

async function handleSubmit() {
  localError.value = "";
  
  if (mode.value === "login") {
    if (!emailOrPhone.value.trim() || !password.value) {
      localError.value = "অনুগ্রহ করে সকল ফিল্ড পূরণ করুন";
      return;
    }
    await login(emailOrPhone.value, password.value);
  } else {
    if (!fullName.value.trim() || !emailOrPhone.value.trim() || !password.value) {
      localError.value = "অনুগ্রহ করে সকল ফিল্ড পূরণ করুন";
      return;
    }
    if (password.value !== confirmPassword.value) {
      localError.value = "পাসওয়ার্ড দুটি মেলেনি";
      return;
    }
    if (password.value.length < 6) {
      localError.value = "পাসওয়ার্ড অবশ্যই কমপক্ষে ৬ অক্ষরের হতে হবে";
      return;
    }
    await register(fullName.value, emailOrPhone.value, password.value);
  }
}
</script>

<template>
  <transition name="fade">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-md bg-black/50"
      @click.self="closeAuth"
    >
      <transition name="scale">
        <div
          class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100 flex flex-col"
        >
          <!-- Header Pattern / Theme Accent -->
          <div class="h-2 bg-gradient-to-r from-primary via-emerald-400 to-primary"></div>

          <!-- Close Button -->
          <button
            @click="closeAuth"
            class="absolute top-4 right-4 p-2 rounded-full text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors cursor-pointer"
          >
            <X class="w-5 h-5" />
          </button>

          <div class="p-8">
            <!-- Brand Info -->
            <div class="text-center mb-6">
              <h2 class="text-2xl font-bold text-gray-800 font-display">
                {{ mode === "login" ? "স্বাগতম!" : "নতুন অ্যাকাউন্ট" }}
              </h2>
              <p class="text-xs text-gray-500 mt-1">
                {{ mode === "login" ? "আপনার অ্যাকাউন্টে লগইন করুন" : "সহজে অফার ও ডিল উপভোগ করতে রেজিস্টার করুন" }}
              </p>
            </div>

            <!-- Tab Switcher -->
            <div class="flex bg-gray-100 p-1.5 rounded-2xl mb-6">
              <button
                @click="setMode('login')"
                :class="[
                  'flex-1 text-center py-2 text-sm font-semibold rounded-xl transition-all cursor-pointer',
                  mode === 'login'
                    ? 'bg-white text-primary shadow-sm'
                    : 'text-gray-500 hover:text-gray-800',
                ]"
              >
                লগইন
              </button>
              <button
                @click="setMode('register')"
                :class="[
                  'flex-1 text-center py-2 text-sm font-semibold rounded-xl transition-all cursor-pointer',
                  mode === 'register'
                    ? 'bg-white text-primary shadow-sm'
                    : 'text-gray-500 hover:text-gray-800',
                ]"
              >
                রেজিস্ট্রেশন
              </button>
            </div>

            <!-- Form Error alert -->
            <div
              v-if="error || localError"
              class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 rounded-xl text-xs flex gap-2 items-center"
            >
              <span class="w-1.5 h-1.5 rounded-full bg-red-600 shrink-0"></span>
              <span>{{ localError || error }}</span>
            </div>

            <!-- Forms -->
            <form @submit.prevent="handleSubmit" class="space-y-4">
              <!-- Name (Register Only) -->
              <div v-if="mode === 'register'" class="space-y-1">
                <label class="text-xs font-semibold text-gray-600">নাম</label>
                <div class="relative flex items-center">
                  <User class="absolute left-3.5 w-4 h-4 text-gray-400" />
                  <input
                    type="text"
                    v-model="fullName"
                    placeholder="আপনার পুরো নাম"
                    required
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all"
                  />
                </div>
              </div>

              <!-- Email/Phone -->
              <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-600">ইমেইল অথবা মোবাইল নম্বর</label>
                <div class="relative flex items-center">
                  <Mail class="absolute left-3.5 w-4 h-4 text-gray-400" />
                  <input
                    type="text"
                    v-model="emailOrPhone"
                    placeholder="ইমেইল বা মোবাইল লিখুন"
                    required
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all"
                  />
                </div>
              </div>

              <!-- Password -->
              <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-600">পাসওয়ার্ড</label>
                <div class="relative flex items-center">
                  <Lock class="absolute left-3.5 w-4 h-4 text-gray-400" />
                  <input
                    :type="showPassword ? 'text' : 'password'"
                    v-model="password"
                    placeholder="পাসওয়ার্ড লিখুন"
                    required
                    class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all"
                  />
                  <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute right-3 text-gray-400 hover:text-gray-600 cursor-pointer"
                  >
                    <Eye v-if="!showPassword" class="w-4 h-4" />
                    <EyeOff v-else class="w-4 h-4" />
                  </button>
                </div>
              </div>

              <!-- Confirm Password (Register Only) -->
              <div v-if="mode === 'register'" class="space-y-1">
                <label class="text-xs font-semibold text-gray-600">পাসওয়ার্ড নিশ্চিত করুন</label>
                <div class="relative flex items-center">
                  <Lock class="absolute left-3.5 w-4 h-4 text-gray-400" />
                  <input
                    :type="showPassword ? 'text' : 'password'"
                    v-model="confirmPassword"
                    placeholder="আবার পাসওয়ার্ড লিখুন"
                    required
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all"
                  />
                </div>
              </div>

              <!-- Actions -->
              <button
                type="submit"
                :disabled="loading"
                class="w-full bg-primary hover:bg-primary/95 text-white py-3 rounded-2xl font-bold text-sm transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50"
              >
                <span
                  v-if="loading"
                  class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"
                ></span>
                {{ mode === "login" ? "লগইন করুন" : "অ্যাকাউন্ট তৈরি করুন" }}
              </button>
            </form>
          </div>
        </div>
      </transition>
    </div>
  </transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.scale-enter-active,
.scale-leave-active {
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease;
}
.scale-enter-from,
.scale-leave-to {
  transform: scale(0.9);
  opacity: 0;
}
</style>
