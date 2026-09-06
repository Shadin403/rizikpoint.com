<script setup>
import { useRoute, useRouter } from "vue-router";
import { useI18n } from "@/lib/i18n";
import { useAuth } from "@/store/auth";
import {
  LayoutGrid,
  ShoppingBag,
  Home,
  Tag,
  CircleUserRound,
} from "lucide-vue-next";

const route  = useRoute();
const router = useRouter();
const { openAuth, user, isAuthenticated } = useAuth();
const { locale } = useI18n();

const navItems = [
  { name: "Categories", path: "/categories", labelEn: "Category", labelBn: "ক্যাটাগরি",  icon: LayoutGrid },
  { name: "Shop",       path: "/products-list", labelEn: "Shop",  labelBn: "শপ",          icon: ShoppingBag,  activePaths: ["/stores", "/products-list"] },
  { name: "HOME",       path: "/",           labelEn: "",         labelBn: "",            icon: Home,             isCenter: true },
  { name: "Deals",      path: "/deals",      labelEn: "Deals",    labelBn: "ডিলস",        icon: Tag },
  { name: "Account",    path: "/dashboard",  labelEn: "Account",  labelBn: "অ্যাকাউন্ট",  icon: CircleUserRound,  action: "profile" },
];

const isActive = (item) => {
  if (!item.path) return false;
  if (item.activePaths) return item.activePaths.some(p => route.path === p || route.path.startsWith(p + "/"));
  return route.path === item.path;
};

function go(item) {
  if (item.action === "profile") {
    if (isAuthenticated.value) {
      router.push("/dashboard");
    } else {
      openAuth();
    }
    return;
  }
  if (item.path && route.path !== item.path) router.push(item.path);
}
</script>

<template>
  <nav class="bn" aria-label="Bottom Navigation">
    <div class="bn__bar">
      <template v-for="item in navItems" :key="item.name">

        <!-- ── CENTER FAB ── -->
        <div v-if="item.isCenter" class="bn__fab-wrap">
          <span class="bn__fab-halo" />
          <button
            type="button"
            class="bn__fab"
            :class="{ 'bn__fab--on': isActive(item) }"
            @click="go(item)"
            aria-label="Home"
          >
            <component :is="item.icon" :size="24" stroke-width="2" color="white" />
          </button>
        </div>

        <!-- ── REGULAR ITEM ── -->
        <button
          v-else
          type="button"
          class="bn__item"
          :class="{ 'bn__item--on': isActive(item) }"
          @click="go(item)"
          :aria-label="item.labelEn"
        >
          <!-- avatar override for account -->
          <span v-if="item.icon === CircleUserRound && user?.avatar" class="bn__avatar">
            <img :src="user.avatar" :alt="user?.name" class="bn__avatar-img" />
          </span>
          <component
            v-else
            :is="item.icon"
            :size="22"
            :stroke-width="isActive(item) ? 2.2 : 1.7"
            class="bn__icon"
          />
          <span class="bn__label">{{ locale === "bn" ? item.labelBn : item.labelEn }}</span>
          <span v-if="isActive(item)" class="bn__dot" />
        </button>

      </template>
    </div>
  </nav>
</template>

<style scoped>
.bn { display: block; }
@media (min-width: 768px) { .bn { display: none !important; } }

/* root */
.bn {
  position: fixed;
  bottom: 0; left: 0; right: 0;
  z-index: 50;
  -webkit-tap-highlight-color: transparent;
}

/* white pill bar */
.bn__bar {
  display: flex;
  align-items: center;
  justify-content: space-around;
  height: 62px;
  background: #fff;
  border-radius: 20px 20px 0 0;
  border-top: 1.5px solid #eef0f3;
  box-shadow: 0 -2px 16px rgba(0,0,0,0.06);
  overflow: visible;
}

/* ── regular item ── */
.bn__item {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 3px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 6px 0 8px;
  position: relative;
  color: #c2c9d6;
  transition: color 0.15s ease, transform 0.1s ease;
  outline: none;
  -webkit-tap-highlight-color: transparent;
}
.bn__item:active       { transform: scale(0.85); }
.bn__item--on          { color: hsl(141, 65%, 34%); }

/* lucide icon color inherits */
.bn__icon { display: block; flex-shrink: 0; }

/* label */
.bn__label {
  font-size: 9.5px;
  font-weight: 600;
  letter-spacing: 0.02em;
  white-space: nowrap;
  line-height: 1;
  font-family: var(--font-display, "Outfit", sans-serif);
}

/* tiny active dot */
.bn__dot {
  position: absolute;
  bottom: 3px;
  left: 50%;
  transform: translateX(-50%);
  width: 4px; height: 4px;
  border-radius: 50%;
  background: hsl(141, 65%, 34%);
}

/* avatar */
.bn__avatar {
  width: 22px; height: 22px;
  border-radius: 50%;
  overflow: hidden;
  border: 1.5px solid hsl(141, 65%, 34%);
  flex-shrink: 0;
}
.bn__avatar-img { width: 100%; height: 100%; object-fit: cover; }

/* ── FAB wrapper ── */
.bn__fab-wrap {
  flex: 0 0 68px;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 22px;   /* lifts above bar */
}

/* static white halo — never scales with button */
.bn__fab-halo {
  position: absolute;
  inset: -5px;
  border-radius: 50%;
  background: #fff;
  pointer-events: none;
  z-index: 0;
}

/* green FAB */
.bn__fab {
  position: relative;
  z-index: 1;
  width: 52px; height: 52px;
  border-radius: 50%;
  border: none;
  cursor: pointer;
  background: hsl(141, 70%, 36%);
  box-shadow:
    0 4px 16px hsla(141, 70%, 36%, 0.42),
    0 1px 4px  rgba(0,0,0,0.10),
    inset 0 1px 0 rgba(255,255,255,0.18);
  display: flex;
  align-items: center;
  justify-content: center;
  transition:
    transform  0.22s cubic-bezier(0.34, 1.56, 0.64, 1),
    background 0.15s ease,
    box-shadow 0.15s ease;
  outline: none;
  -webkit-tap-highlight-color: transparent;
}
/* ✅ hover stays green — no white flash */
.bn__fab:hover {
  background: hsl(141, 70%, 33%);
  transform: translateY(-2px) scale(1.07);
  box-shadow:
    0 8px 22px hsla(141, 70%, 36%, 0.52),
    0 2px 6px  rgba(0,0,0,0.13),
    inset 0 1px 0 rgba(255,255,255,0.20);
}
.bn__fab:active {
  transform: scale(0.91);
  box-shadow: 0 2px 8px hsla(141, 70%, 36%, 0.35);
}
.bn__fab--on {
  background: hsl(141, 70%, 29%);
  box-shadow:
    0 5px 18px hsla(141, 70%, 36%, 0.55),
    inset 0 1px 0 rgba(255,255,255,0.12);
}
</style>
