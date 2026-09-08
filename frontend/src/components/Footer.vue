<script setup>
import { computed } from "vue";
import { useI18n } from "@/lib/i18n";
import { useBusinessSettings } from "@/composables/useBusinessSettings";
import { MapPin, Phone, Mail, ChevronRight } from "@lucide/vue";

const { locale, t } = useI18n();
const { get } = useBusinessSettings();

function g(key) { return get(key, locale.value) || get(key) }

const appName = computed(() => g("app_name") || "RizikPoint");
const footerLogo = computed(() => g("footer_logo"));
const aboutUsDescription = computed(() => g("about_us_description") || "");
const playStoreLink = computed(() => g("play_store_link"));
const appStoreLink = computed(() => g("app_store_link"));

const contactAddress = computed(() => g("contact_address") || "Nakla,Sherpur-2150");
const contactPhone = computed(() => g("contact_phone") || "01635586340");
const contactEmail = computed(() => g("contact_email") || "support@dealpabo.com");

const widgetOneTitle = computed(
  () => g("widget_one") || (locale.value === "bn" ? "প্রয়োজনীয় লিংক" : "Useful Links"),
);

const widgetOneLabels = computed(() => {
  try {
    const val = g("widget_one_labels");
    if (Array.isArray(val)) return val;
    return val ? JSON.parse(val) : [];
  } catch {
    return [];
  }
});

const widgetOneLinks = computed(() => {
  try {
    const val = g("widget_one_links");
    if (Array.isArray(val)) return val;
    return val ? JSON.parse(val) : [];
  } catch {
    return [];
  }
});

const copyrightText = computed(
  () => g("frontend_copyright_text") || `© ${new Date().getFullYear()} ${appName.value}. All Rights Reserved.`,
);

const showSocialLinks = computed(() => g("show_social_links") === "on");
const facebookLink = computed(() => g("facebook_link"));
const twitterLink = computed(() => g("twitter_link"));
const instagramLink = computed(() => g("instagram_link"));
const youtubeLink = computed(() => g("youtube_link"));
const linkedinLink = computed(() => g("linkedin_link"));

const paymentMethodImages = computed(() => {
  const val = g("payment_method_images");
  return Array.isArray(val) ? val : [];
});

const hasSocials = computed(() => {
  return (
    facebookLink.value ||
    twitterLink.value ||
    instagramLink.value ||
    youtubeLink.value ||
    linkedinLink.value
  );
});
</script>

<template>
  <footer class="bg-secondary text-gray-300 pt-16 border-t border-border">
    <div class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 pb-8">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-8 mb-12">
        <!-- Column 1: About & App Links -->
        <div class="md:col-span-5 flex flex-col gap-5">
          <!-- Logo -->
          <router-link to="/" class="inline-block">
            <img
              v-if="footerLogo"
              :src="footerLogo"
              alt="Footer Logo"
              class="h-10 w-auto object-contain"
            />
            <span v-else class="text-xl font-bold text-white font-display">
              {{ appName }}
            </span>
          </router-link>

          <!-- Description -->
          <div
            v-if="aboutUsDescription"
            class="text-xs leading-relaxed text-gray-400 about-us-description"
            v-html="aboutUsDescription"
          ></div>
          <p v-else class="text-xs leading-relaxed">
            {{ locale === 'bn' ? 'অর্গানিক পণ্য ও প্রতিদিনের গ্রোসারি—আপনার রান্নাঘরের প্রয়োজন মেটাতে কেনাকাটা করুন নিজের মতো।' : 'Organic favourites and everyday groceries. Shop for your kitchen, your home, and your everyday routine.' }}
          </p>

          <!-- App Links -->
          <div
            v-if="playStoreLink || appStoreLink"
            class="flex flex-wrap gap-3 mt-2"
          >
            <a
              v-if="playStoreLink"
              :href="playStoreLink"
              target="_blank"
              rel="noopener noreferrer"
              class="hover:scale-105 transition-transform"
            >
              <img
                src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg"
                alt="Google Play Store"
                class="h-9 w-auto"
              />
            </a>
            <a
              v-if="appStoreLink"
              :href="appStoreLink"
              target="_blank"
              rel="noopener noreferrer"
              class="hover:scale-105 transition-transform"
            >
              <img
                src="https://upload.wikimedia.org/wikipedia/commons/3/3c/Download_on_the_App_Store_Badge.svg"
                alt="App Store"
                class="h-9 w-auto"
              />
            </a>
          </div>
        </div>

        <!-- Column 2: Useful Links -->
        <div class="md:col-span-3 flex flex-col gap-4 md:pl-6">
          <h4
            class="text-white font-bold text-sm uppercase tracking-wider relative inline-block"
          >
            {{ widgetOneTitle }}
            <span class="absolute bottom-0 left-0 w-8 h-0.5 bg-primary"></span>
          </h4>
          <ul v-if="widgetOneLabels.length > 0" class="space-y-2 mt-2">
            <li v-for="(label, idx) in widgetOneLabels" :key="idx">
              <a
                :href="widgetOneLinks[idx] || '#'"
                class="flex items-center gap-1.5 text-xs text-gray-400 hover:text-primary transition-colors py-0.5 group"
              >
                <ChevronRight
                  class="w-3.5 h-3.5 text-gray-600 group-hover:text-primary transition-colors"
                />
                {{ label }}
              </a>
            </li>
          </ul>
          <ul v-else class="space-y-2 mt-2">
            <li>
              <router-link
                to="/deals"
                class="flex items-center gap-1.5 text-xs hover:text-primary transition-colors py-0.5 group"
              >
                <ChevronRight
                  class="w-3.5 h-3.5 text-gray-600 group-hover:text-primary transition-colors"
                />
                {{ t("deals") }}
              </router-link>
            </li>
            <li>
              <router-link
                to="/coupons"
                class="flex items-center gap-1.5 text-xs hover:text-primary transition-colors py-0.5 group"
              >
                <ChevronRight
                  class="w-3.5 h-3.5 text-gray-600 group-hover:text-primary transition-colors"
                />
                {{ t("coupons") }}
              </router-link>
            </li>
            <li>
              <router-link
                to="/categories"
                class="flex items-center gap-1.5 text-xs hover:text-primary transition-colors py-0.5 group"
              >
                <ChevronRight
                  class="w-3.5 h-3.5 text-gray-600 group-hover:text-primary transition-colors"
                />
                {{ t("categories") }}
              </router-link>
            </li>
                <li>
                  <router-link
                    to="/contact"
                    class="flex items-center gap-1.5 text-xs hover:text-primary transition-colors py-0.5 group"
                  >
                    <ChevronRight
                      class="w-3.5 h-3.5 text-gray-600 group-hover:text-primary transition-colors"
                    />
                    {{ t("contact") }}
                  </router-link>
                </li>
          </ul>
        </div>

        <!-- Column 3: Contact Info & Socials -->
        <div class="md:col-span-4 flex flex-col gap-4">
          <h4
            class="text-white font-bold text-sm uppercase tracking-wider relative inline-block"
          >
            {{ locale === "bn" ? "যোগাযোগ করুন" : "Contact Info" }}
            <span class="absolute bottom-0 left-0 w-8 h-0.5 bg-primary"></span>
          </h4>

          <div class="flex flex-col gap-3 mt-2">
            <!-- Location -->
            <div class="flex items-start gap-2.5">
              <MapPin class="w-4 h-4 text-primary shrink-0 mt-0.5" />
              <span class="text-xs leading-relaxed text-gray-400">{{
                contactAddress
              }}</span>
            </div>

            <!-- Phone -->
            <div class="flex items-center gap-2.5">
              <Phone class="w-4 h-4 text-primary shrink-0" />
              <a
                :href="`tel:${contactPhone}`"
                class="text-xs text-gray-400 hover:text-primary transition-colors"
              >
                {{ contactPhone }}
              </a>
            </div>

            <!-- Email -->
            <div class="flex items-center gap-2.5">
              <Mail class="w-4 h-4 text-primary shrink-0" />
              <a
                :href="`mailto:${contactEmail}`"
                class="text-xs text-gray-400 hover:text-primary transition-colors"
              >
                {{ contactEmail }}
              </a>
            </div>
          </div>

          <!-- Social Media Links -->
          <div
            v-if="showSocialLinks && hasSocials"
            class="flex items-center gap-2.5 mt-2"
          >
            <a
              v-if="facebookLink"
              :href="facebookLink"
              target="_blank"
              rel="noopener noreferrer"
              class="w-8 h-8 rounded-full bg-gray-800 hover:bg-[#1877f2] flex items-center justify-center transition-all duration-300 hover:-translate-y-0.5 cursor-pointer text-white"
            >
              <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                <path
                  d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"
                />
              </svg>
            </a>
            <a
              v-if="twitterLink"
              :href="twitterLink"
              target="_blank"
              rel="noopener noreferrer"
              class="w-8 h-8 rounded-full bg-gray-800 hover:bg-[#1da1f2] flex items-center justify-center transition-all duration-300 hover:-translate-y-0.5 cursor-pointer text-white"
            >
              <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                <path
                  d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"
                />
              </svg>
            </a>
            <a
              v-if="instagramLink"
              :href="instagramLink"
              target="_blank"
              rel="noopener noreferrer"
              class="w-8 h-8 rounded-full bg-gray-800 hover:bg-[#e1306c] flex items-center justify-center transition-all duration-300 hover:-translate-y-0.5 cursor-pointer text-white"
            >
              <svg
                class="w-4 h-4 fill-none stroke-current stroke-2"
                viewBox="0 0 24 24"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
              </svg>
            </a>
            <a
              v-if="youtubeLink"
              :href="youtubeLink"
              target="_blank"
              rel="noopener noreferrer"
              class="w-8 h-8 rounded-full bg-gray-800 hover:bg-[#ff0000] flex items-center justify-center transition-all duration-300 hover:-translate-y-0.5 cursor-pointer text-white"
            >
              <svg
                class="w-4 h-4 fill-none stroke-current stroke-2"
                viewBox="0 0 24 24"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path
                  d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"
                />
                <polygon
                  fill="currentColor"
                  points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"
                />
              </svg>
            </a>
            <a
              v-if="linkedinLink"
              :href="linkedinLink"
              target="_blank"
              rel="noopener noreferrer"
              class="w-8 h-8 rounded-full bg-gray-800 hover:bg-[#0077b5] flex items-center justify-center transition-all duration-300 hover:-translate-y-0.5 cursor-pointer text-white"
            >
              <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                <path
                  d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2z"
                />
                <circle cx="4" cy="4" r="2" />
              </svg>
            </a>
          </div>
        </div>
      </div>

      <!-- Payment Gateways and Copyright -->
      <div
        class="border-t border-gray-850 pt-8 flex flex-col md:flex-row items-center justify-between gap-4 mt-6"
      >
        <!-- Copyright text -->
        <div
          class="text-xs text-gray-500 text-center md:text-left flex flex-col gap-1"
        >
          <div>© 2026 {{ appName }} . All Rights Reserved.</div>
          <div>
            {{ t("developed_by") }}
            <a
              href="https://wa.me/+8801635586340"
              target="_blank"
              rel="noopener noreferrer"
              class="text-primary font-semibold hover:underline"
            >
              Shadin Sharkar
            </a>
          </div>
        </div>

        <!-- Payment Method Images -->
        <div
          v-if="paymentMethodImages.length > 0"
          class="flex flex-wrap justify-center gap-2"
        >
          <img
            v-for="(img, idx) in paymentMethodImages"
            :key="idx"
            :src="img"
            alt="Payment Method"
            class="h-6 w-auto object-contain bg-white/5 hover:bg-white/10 transition-colors p-1 rounded border border-gray-800"
          />
        </div>
      </div>
    </div>
  </footer>
</template>

<style>
/* CSS class overrides for translatable HTML editor values */
.about-us-description p,
.copyright-content p {
  margin-bottom: 0.5rem;
}
.about-us-description a,
.copyright-content a {
  color: var(--color-primary, #10b981);
  text-decoration: underline;
}
.about-us-description a:hover,
.copyright-content a:hover {
  opacity: 0.9;
}
.border-gray-850 {
  border-color: rgba(255, 255, 255, 0.05);
}
</style>

