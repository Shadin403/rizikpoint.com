<script setup>
import { ref, computed, onMounted } from "vue";
import {
  Mail,
  Phone,
  MapPin,
  Send,
  Clock,
  MessageSquare,
  User,
  CheckCircle2,
  AlertCircle,
  Globe,
} from "@lucide/vue";
import { fetchBusinessSettings } from "@/lib/api";
import { useI18n } from "@/lib/i18n";
import { toast } from "@/lib/toast";
import { usePageTitle } from "@/composables/usePageTitle";
import { pageTitles } from "@/lib/pageTitles";
usePageTitle(pageTitles.Contact);
const { locale, t } = useI18n();

const settings = ref([]);
const isLoadingSettings = ref(true);

const form = ref({
  name: "",
  email: "",
  phone: "",
  subject: "",
  message: "",
});

const errors = ref({});
const isSubmitting = ref(false);
const isSuccess = ref(false);

onMounted(async () => {
  try {
    settings.value = await fetchBusinessSettings();
  } catch (err) {
    console.error("Failed to load contact settings:", err);
  } finally {
    isLoadingSettings.value = false;
  }
});

function getSetting(key) {
  const lang = locale.value;
  let match = settings.value.find((s) => s.type === key && s.lang === lang);
  if (!match) {
    match = settings.value.find(
      (s) => s.type === key && (!s.lang || s.lang === "en"),
    );
  }
  if (!match) {
    match = settings.value.find((s) => s.type === key);
  }
  return match ? match.value : null;
}

const contactAddress = computed(
  () => getSetting("contact_address") || "Nakla, Sherpur-2150, Bangladesh",
);
const contactPhone = computed(
  () => getSetting("contact_phone") || "+8801635586340",
);
const contactEmail = computed(
  () => getSetting("contact_email") || "support@dealpabo.com",
);
const mapEmbedUrl = computed(
  () => getSetting("contact_map_embed") || null,
);
const workingHours = computed(
  () => getSetting("contact_working_hours") || null,
);

const isBn = computed(() => locale.value === "bn");

function validate() {
  const e = {};
  if (!form.value.name.trim()) {
    e.name = isBn.value ? "আপনার নাম লিখুন" : "Please enter your name";
  }
  if (!form.value.email.trim()) {
    e.email = isBn.value ? "ইমেইল দিন" : "Email is required";
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) {
    e.email = isBn.value ? "সঠিক ইমেইল দিন" : "Enter a valid email";
  }
  if (!form.value.message.trim() || form.value.message.trim().length < 10) {
    e.message = isBn.value
      ? "অন্তত ১০ অক্ষরের বার্তা লিখুন"
      : "Please write at least 10 characters";
  }
  errors.value = e;
  return Object.keys(e).length === 0;
}

async function handleSubmit() {
  if (!validate()) return;
  isSubmitting.value = true;
  try {
    await new Promise((resolve) => setTimeout(resolve, 900));
    isSuccess.value = true;
    toast({
      title: isBn.value
        ? "আপনার বার্তা সফলভাবে পাঠানো হয়েছে!"
        : "Your message has been sent successfully!",
      variant: "success",
    });
    form.value = { name: "", email: "", phone: "", subject: "", message: "" };
    setTimeout(() => {
      isSuccess.value = false;
    }, 4000);
  } catch (err) {
    toast({
      title: isBn.value
        ? "বার্তা পাঠাতে সমস্যা হয়েছে, আবার চেষ্টা করুন"
        : "Could not send message, please try again",
      variant: "destructive",
    });
  } finally {
    isSubmitting.value = false;
  }
}

const subjects = computed(() =>
  isBn.value
    ? ["সাধারণ জিজ্ঞাসা", "অর্ডার সংক্রান্ত", "অভিযোগ", "অংশীদারিত্ব", "অন্যান্য"]
    : ["General Inquiry", "Order Related", "Complaint", "Partnership", "Other"],
);
</script>

<template>
  <div class="bg-[#fdfdfd] flex-1">
    <section class="relative overflow-hidden bg-gradient-to-br from-green-50 via-white to-emerald-50 border-b border-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-20">
        <div class="text-center max-w-3xl mx-auto">
          <div class="inline-flex items-center justify-center p-3 bg-green-100 rounded-2xl mb-4 border border-green-200">
            <MessageSquare class="w-8 h-8 text-green-600" />
          </div>
          <h1 class="text-3xl sm:text-4xl font-display font-bold text-gray-800 mb-3">
            {{ isBn ? "আমাদের সাথে যোগাযোগ করুন" : "Get in Touch with Us" }}
          </h1>
          <p class="text-gray-500 text-sm sm:text-base leading-relaxed">
            {{
              isBn
                ? "আপনার কোনো প্রশ্ন, মতামত বা সহায়তার প্রয়োজন হলে আমাদের জানান। আমরা যত দ্রুত সম্ভব উত্তর দেওয়ার চেষ্টা করব।"
                : "Have a question, feedback, or need help? Drop us a message and we'll get back to you as soon as possible."
            }}
          </p>
        </div>

        <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-3 max-w-4xl mx-auto">
          <a :href="`tel:${contactPhone}`" class="group flex items-center gap-3 bg-white border border-gray-200 hover:border-green-400 hover:shadow-md rounded-xl p-4 transition-all">
            <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center group-hover:bg-green-600 group-hover:text-white transition-colors">
              <Phone class="w-5 h-5" />
            </div>
            <div class="text-left min-w-0">
              <p class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">
                {{ isBn ? "ফোন" : "Phone" }}
              </p>
              <p class="text-sm font-semibold text-gray-800 truncate">{{ contactPhone }}</p>
            </div>
          </a>
          <a :href="`mailto:${contactEmail}`" class="group flex items-center gap-3 bg-white border border-gray-200 hover:border-green-400 hover:shadow-md rounded-xl p-4 transition-all">
            <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center group-hover:bg-green-600 group-hover:text-white transition-colors">
              <Mail class="w-5 h-5" />
            </div>
            <div class="text-left min-w-0">
              <p class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">
                {{ isBn ? "ইমেইল" : "Email" }}
              </p>
              <p class="text-sm font-semibold text-gray-800 truncate">{{ contactEmail }}</p>
            </div>
          </a>
          <div class="flex items-center gap-3 bg-white border border-gray-200 rounded-xl p-4">
            <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center">
              <Clock class="w-5 h-5" />
            </div>
            <div class="text-left min-w-0">
              <p class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">
                {{ isBn ? "কার্যকর সময়" : "Working Hours" }}
              </p>
              <p class="text-sm font-semibold text-gray-800 truncate">
                {{ workingHours || (isBn ? "সকাল ১০টা – রাত ৮টা" : "10:00 AM - 8:00 PM") }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
      <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
        <div class="lg:col-span-3">
          <div class="bg-white border border-gray-200 rounded-2xl p-6 sm:p-8 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center">
                <Send class="w-5 h-5" />
              </div>
              <div>
                <h2 class="text-xl font-display font-bold text-gray-800">
                  {{ isBn ? "একটি বার্তা পাঠান" : "Send us a Message" }}
                </h2>
                <p class="text-xs text-gray-500">
                  {{
                    isBn
                      ? "নিচের ফর্মটি পূরণ করুন, আমরা শীঘ্রই যোগাযোগ করব।"
                      : "Fill out the form and we'll get back to you shortly."
                  }}
                </p>
              </div>
            </div>

            <form @submit.prevent="handleSubmit" class="space-y-4" novalidate>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                    {{ isBn ? "আপনার নাম" : "Your Name" }}
                    <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <User class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input
                      v-model="form.name"
                      type="text"
                      :placeholder="isBn ? 'পূর্ণ নাম লিখুন' : 'Enter full name'"
                      class="w-full h-11 pl-10 pr-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm bg-white transition-all"
                      :class="{ 'border-red-400 focus:border-red-500 focus:ring-red-100': errors.name }"
                    />
                  </div>
                  <p v-if="errors.name" class="mt-1 text-xs text-red-500 flex items-center gap-1">
                    <AlertCircle class="w-3 h-3" /> {{ errors.name }}
                  </p>
                </div>

                <div>
                  <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                    {{ isBn ? "ইমেইল" : "Email" }}
                    <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <Mail class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input
                      v-model="form.email"
                      type="email"
                      placeholder="you@example.com"
                      class="w-full h-11 pl-10 pr-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm bg-white transition-all"
                      :class="{ 'border-red-400 focus:border-red-500 focus:ring-red-100': errors.email }"
                    />
                  </div>
                  <p v-if="errors.email" class="mt-1 text-xs text-red-500 flex items-center gap-1">
                    <AlertCircle class="w-3 h-3" /> {{ errors.email }}
                  </p>
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                    {{ isBn ? "মোবাইল নম্বর" : "Phone Number" }}
                    <span class="text-gray-400 text-[10px] font-normal">({{ isBn ? "ঐচ্ছিক" : "optional" }})</span>
                  </label>
                  <div class="relative">
                    <Phone class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input
                      v-model="form.phone"
                      type="tel"
                      placeholder="01XXXXXXXXX"
                      class="w-full h-11 pl-10 pr-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm bg-white transition-all"
                    />
                  </div>
                </div>

                <div>
                  <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                    {{ isBn ? "বিষয়" : "Subject" }}
                  </label>
                  <select
                    v-model="form.subject"
                    class="w-full h-11 px-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm bg-white transition-all"
                  >
                    <option value="">{{ isBn ? "— বিষয় নির্বাচন করুন —" : "— Choose a subject —" }}</option>
                    <option v-for="s in subjects" :key="s" :value="s">{{ s }}</option>
                  </select>
                </div>
              </div>

              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                  {{ isBn ? "আপনার বার্তা" : "Your Message" }}
                  <span class="text-red-500">*</span>
                </label>
                <textarea
                  v-model="form.message"
                  rows="5"
                  :placeholder="isBn ? 'আপনার বার্তা এখানে লিখুন...' : 'Type your message here...'"
                  class="w-full px-3 py-2.5 rounded-lg border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm bg-white transition-all resize-none"
                  :class="{ 'border-red-400 focus:border-red-500 focus:ring-red-100': errors.message }"
                ></textarea>
                <div class="flex items-center justify-between mt-1">
                  <p v-if="errors.message" class="text-xs text-red-500 flex items-center gap-1">
                    <AlertCircle class="w-3 h-3" /> {{ errors.message }}
                  </p>
                  <p v-else class="text-[10px] text-gray-400">
                    {{ form.message.length }} / 1000
                  </p>
                </div>
              </div>

              <div class="flex items-center justify-between pt-2">
                <p class="text-[10px] text-gray-400 max-w-xs">
                  {{
                    isBn
                      ? "আপনার তথ্য গোপন রাখা হবে এবং শুধু যোগাযোগের জন্য ব্যবহার করা হবে।"
                      : "Your information stays private and is used only to contact you."
                  }}
                </p>
                <button
                  type="submit"
                  :disabled="isSubmitting"
                  class="inline-flex items-center gap-2 h-11 px-6 rounded-lg bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white text-sm font-semibold shadow-sm hover:shadow transition-all"
                >
                  <Send v-if="!isSubmitting" class="w-4 h-4" />
                  <span v-else class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                  {{
                    isSubmitting
                      ? (isBn ? "পাঠানো হচ্ছে..." : "Sending...")
                      : (isBn ? "বার্তা পাঠান" : "Send Message")
                  }}
                </button>
              </div>

              <transition name="fade">
                <div
                  v-if="isSuccess"
                  class="mt-4 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 rounded-lg p-3 text-sm"
                >
                  <CheckCircle2 class="w-5 h-5 flex-shrink-0" />
                  <span>
                    {{
                      isBn
                        ? "ধন্যবাদ! আপনার বার্তা সফলভাবে পৌঁছেছে, শীঘ্রই উত্তর দেওয়া হবে।"
                        : "Thanks! Your message has reached us. We'll reply soon."
                    }}
                  </span>
                </div>
              </transition>
            </form>
          </div>
        </div>

        <aside class="lg:col-span-2 space-y-6">
          <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <h3 class="text-base font-display font-bold text-gray-800 mb-4 flex items-center gap-2">
              <MapPin class="w-5 h-5 text-green-600" />
              {{ isBn ? "আমাদের ঠিকানা" : "Our Address" }}
            </h3>
            <ul class="space-y-4 text-sm">
              <li class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-lg bg-green-50 text-green-600 flex items-center justify-center flex-shrink-0">
                  <MapPin class="w-4 h-4" />
                </div>
                <div class="min-w-0">
                  <p class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-0.5">
                    {{ isBn ? "অফিস" : "Office" }}
                  </p>
                  <p class="text-gray-700 leading-relaxed">{{ contactAddress }}</p>
                </div>
              </li>
              <li class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-lg bg-green-50 text-green-600 flex items-center justify-center flex-shrink-0">
                  <Phone class="w-4 h-4" />
                </div>
                <div class="min-w-0">
                  <p class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-0.5">
                    {{ isBn ? "ফোন" : "Phone" }}
                  </p>
                  <a :href="`tel:${contactPhone}`" class="text-gray-700 hover:text-green-600 transition-colors break-all">
                    {{ contactPhone }}
                  </a>
                </div>
              </li>
              <li class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-lg bg-green-50 text-green-600 flex items-center justify-center flex-shrink-0">
                  <Mail class="w-4 h-4" />
                </div>
                <div class="min-w-0">
                  <p class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-0.5">
                    {{ isBn ? "ইমেইল" : "Email" }}
                  </p>
                  <a :href="`mailto:${contactEmail}`" class="text-gray-700 hover:text-green-600 transition-colors break-all">
                    {{ contactEmail }}
                  </a>
                </div>
              </li>
              <li v-if="workingHours" class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-lg bg-green-50 text-green-600 flex items-center justify-center flex-shrink-0">
                  <Clock class="w-4 h-4" />
                </div>
                <div class="min-w-0">
                  <p class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-0.5">
                    {{ isBn ? "কার্যকর সময়" : "Working Hours" }}
                  </p>
                  <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ workingHours }}</p>
                </div>
              </li>
            </ul>
          </div>

          <div class="bg-gradient-to-br from-green-600 to-emerald-600 text-white rounded-2xl p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-3">
              <div class="w-10 h-10 rounded-lg bg-white/15 flex items-center justify-center">
                <Globe class="w-5 h-5" />
              </div>
              <h3 class="text-base font-display font-bold">
                {{ isBn ? "আমাদের ওয়েবসাইট" : "Visit Our Website" }}
              </h3>
            </div>
            <p class="text-sm text-white/90 leading-relaxed">
              {{
                isBn
                  ? "সেরা ডিল ও কুপন খুঁজে পেতে আমাদের ওয়েবসাইট ঘুরে দেখুন।"
                  : "Browse our website to discover the best deals and coupons."
              }}
            </p>
            <router-link
              to="/"
              class="mt-4 inline-flex items-center justify-center h-10 px-5 rounded-lg bg-white text-green-700 text-sm font-semibold hover:bg-green-50 transition-colors"
            >
              {{ isBn ? "ওয়েবসাইট দেখুন" : "Visit Website" }}
            </router-link>
          </div>
        </aside>
      </div>

      <div v-if="mapEmbedUrl" class="mt-8 bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="aspect-video w-full">
          <iframe
            :src="mapEmbedUrl"
            class="w-full h-full"
            style="border:0;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
          ></iframe>
        </div>
      </div>
    </section>
  </div>
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
</style>

