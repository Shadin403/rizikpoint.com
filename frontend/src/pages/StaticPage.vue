<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute } from "vue-router";
import {
  FileText,
  Shield,
  HelpCircle,
  RotateCcw,
  Info,
  Mail,
  Clock,
  Phone,
} from "@lucide/vue";
import { fetchBusinessSettings } from "@/lib/api";
import { useI18n } from "@/lib/i18n";
import { usePageTitle } from "@/composables/usePageTitle";
import { pageTitles } from "@/lib/pageTitles";

const route = useRoute();
const { locale, t } = useI18n();
const slug = String(route.params.slug || "about");
const titleConfig = pageTitles[slug] ? pageTitles[slug] : pageTitles.About;
usePageTitle(titleConfig);
const isBn = computed(() => locale.value === "bn");

const settings = ref([]);
onMounted(async () => {
  try {
    settings.value = await fetchBusinessSettings();
  } catch (err) {
    console.error("Failed to load settings for static page", err);
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

const contactEmail = computed(
  () => getSetting("contact_email") || "support@dealpabo.com",
);

const config = computed(() => {
  const slug = String(route.params.slug || "");
  const map = {
    about: {
      title: isBn.value ? "আমাদের সম্পর্কে" : "About Us",
      icon: Info,
      gradient: "from-blue-500 to-indigo-600",
    },
    terms: {
      title: isBn.value ? "শর্তাবলী" : "Terms & Conditions",
      icon: FileText,
      gradient: "from-emerald-500 to-teal-600",
    },
    privacy: {
      title: isBn.value ? "গোপনীয়তা নীতি" : "Privacy Policy",
      icon: Shield,
      gradient: "from-purple-500 to-pink-600",
    },
    faq: {
      title: isBn.value ? "প্রশ্নোত্তর" : "Frequently Asked Questions",
      icon: HelpCircle,
      gradient: "from-amber-500 to-orange-600",
    },
    "return-policy": {
      title: isBn.value ? "রিটার্ন পলিসি" : "Return Policy",
      icon: RotateCcw,
      gradient: "from-rose-500 to-red-600",
    },
  };
  return (
    map[slug] || {
      title: isBn.value ? "পেজ" : "Page",
      icon: FileText,
      gradient: "from-gray-500 to-gray-700",
    }
  );
});

const IconComp = computed(() => config.value.icon);
const pageContent = computed(() => getSetting(`static_page_${route.params.slug}`) || null);

// FAQ data shown when no backend content exists
const faqs = computed(() =>
  isBn.value
    ? [
        { q: "অর্ডার কিভাবে করব?", a: "আপনার পছন্দের পণ্য নির্বাচন করে 'Add to Cart' বাটনে ক্লিক করুন এবং চেকআউট পেজে গিয়ে আপনার ঠিকানা ও পেমেন্ট তথ্য দিয়ে অর্ডার সম্পন্ন করুন।" },
        { q: "পেমেন্ট কিভাবে করতে হয়?", a: "আমরা বিকাশ, নগদ, রকেট এবং ক্যাশ অন ডেলিভারি সুবিধা দিই।" },
        { q: "ডেলিভারি কত দিনে পাব?", a: "ঢাকার ভিতরে ১-২ কার্যদিবস, ঢাকার বাইরে ৩-৫ কার্যদিবস।" },
        { q: "রিটার্ন বা রিফান্ড পলিসি কী?", a: "পণ্য গ্রহণের ৩ দিনের মধ্যে সমস্যা থাকলে রিটার্ন/এক্সচেঞ্জ করতে পারবেন।" },
      ]
    : [
        { q: "How do I place an order?", a: "Select your desired product, click 'Add to Cart', then go to checkout and provide your address and payment info to complete the order." },
        { q: "What payment methods do you accept?", a: "We accept bKash, Nagad, Rocket, and Cash on Delivery." },
        { q: "How long does delivery take?", a: "Inside Dhaka: 1-2 business days. Outside Dhaka: 3-5 business days." },
        { q: "What is your return/refund policy?", a: "You may return or exchange a product within 3 days of receipt if there is a defect." },
      ],
);
</script>

<template>
  <div class="bg-[#fdfdfd] flex-1">
    <!-- Hero -->
    <section :class="['relative overflow-hidden text-white']" :style="{ background: 'linear-gradient(135deg, var(--tw-gradient-stops))' }">
      <div :class="['bg-gradient-to-br', config.gradient, 'border-b border-gray-100']">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
          <div class="text-center max-w-3xl mx-auto">
            <div class="inline-flex items-center justify-center p-3 bg-white/15 backdrop-blur-sm rounded-2xl mb-4 border border-white/20">
              <component :is="IconComp" class="w-8 h-8" />
            </div>
            <h1 class="text-3xl sm:text-4xl font-display font-bold mb-3">
              {{ config.title }}
            </h1>
            <p class="text-white/90 text-sm sm:text-base">
              {{ isBn ? "আপনার যেকোনো প্রশ্নের উত্তর এখানে পাবেন।" : "Find answers to your questions here." }}
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Content -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
      <div class="bg-white border border-gray-200 rounded-2xl p-6 sm:p-10 shadow-sm">
        <!-- Backend-driven content (if available) -->
        <div v-if="pageContent" v-html="pageContent" class="prose prose-sm sm:prose-base max-w-none text-gray-700 leading-relaxed"></div>

        <!-- Default placeholder body -->
        <div v-else-if="route.params.slug === 'faq'" class="space-y-3">
          <details
            v-for="(f, i) in faqs"
            :key="i"
            class="group border border-gray-200 rounded-xl overflow-hidden"
          >
            <summary class="cursor-pointer flex items-center justify-between p-4 hover:bg-gray-50 font-semibold text-gray-800 text-sm">
              <span class="flex-1">{{ f.q }}</span>
              <component :is="IconComp" class="w-4 h-4 text-primary group-open:rotate-180 transition-transform" />
            </summary>
            <div class="px-4 pb-4 text-sm text-gray-600 leading-relaxed">
              {{ f.a }}
            </div>
          </details>
        </div>

        <div v-else class="space-y-4 text-gray-700 leading-relaxed text-sm sm:text-base">
          <p v-if="isBn">
            এই পেজটি শীঘ্রই আপডেট করা হবে। আপাতত আমাদের সাথে যোগাযোগ করতে নিচের যেকোনো মাধ্যম ব্যবহার করুন — আমরা সাহায্য করতে প্রস্তুত।
          </p>
          <p v-else>
            This page is being updated. In the meantime, feel free to reach out to us through any of the channels below — we're here to help.
          </p>
        </div>
      </div>

      <!-- Contact strip -->
      <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-3">
        <a :href="`mailto:${contactEmail}`" class="flex items-center gap-3 bg-white border border-gray-200 rounded-xl p-4 hover:border-primary/40 hover:shadow-sm transition-all">
          <div class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
            <Mail class="w-5 h-5" />
          </div>
          <div class="text-left min-w-0">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">{{ isBn ? "ইমেইল" : "Email" }}</p>
            <p class="text-sm font-semibold text-gray-800 truncate">{{ contactEmail }}</p>
          </div>
        </a>
        <a href="tel:01635586340" class="flex items-center gap-3 bg-white border border-gray-200 rounded-xl p-4 hover:border-primary/40 hover:shadow-sm transition-all">
          <div class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
            <Phone class="w-5 h-5" />
          </div>
          <div class="text-left min-w-0">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">{{ isBn ? "ফোন" : "Phone" }}</p>
            <p class="text-sm font-semibold text-gray-800 truncate">01635-586340</p>
          </div>
        </a>
        <div class="flex items-center gap-3 bg-white border border-gray-200 rounded-xl p-4">
          <div class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
            <Clock class="w-5 h-5" />
          </div>
          <div class="text-left min-w-0">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">{{ isBn ? "সময়" : "Hours" }}</p>
            <p class="text-sm font-semibold text-gray-800 truncate">{{ isBn ? "সকাল ১০টা – রাত ৮টা" : "10 AM – 8 PM" }}</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
:deep(h1), :deep(h2), :deep(h3) {
  color: #1f2937;
  font-weight: 700;
  margin-top: 1.5rem;
  margin-bottom: 0.75rem;
}
:deep(p) {
  margin-bottom: 0.75rem;
}
:deep(ul) {
  list-style: disc;
  margin-left: 1.5rem;
  margin-bottom: 0.75rem;
}
:deep(ol) {
  list-style: decimal;
  margin-left: 1.5rem;
  margin-bottom: 0.75rem;
}
:deep(a) {
  color: #10b981;
  text-decoration: underline;
}
</style>
