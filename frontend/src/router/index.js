import { createRouter, createWebHistory } from "vue-router";
import Home from "../pages/Home.vue";
import ProductsList from "../pages/ProductsList.vue";
import DealDetail from "../pages/DealDetail.vue";
import Stores from "../pages/Stores.vue";
import StoreDetail from "../pages/StoreDetail.vue";
import Categories from "../pages/Categories.vue";
import Coupons from "../pages/Coupons.vue";
import Deals from "../pages/Deals.vue";
import Brands from "../pages/Brands.vue";
import Contact from "../pages/Contact.vue";
import StaticPage from "../pages/StaticPage.vue";
import NotFound from "../pages/NotFound.vue";
import UserDashboard from "../pages/UserDashboard.vue";
import Cart from "../pages/Cart.vue";
import Checkout from "../pages/Checkout.vue";
import { useAuth } from "../store/auth";

// Admin panel base URL from env — supports both local and production
const ADMIN_URL = import.meta.env.VITE_ADMIN_URL || "http://127.0.0.1:8000/admin";

const routes = [
  { path: "/", name: "Home", component: Home },
  { path: "/products-list", name: "ProductsList", component: ProductsList },
  { path: "/products/:slug", name: "ProductDetail", component: DealDetail },
  { path: "/deals/:slug", name: "DealDetail", component: DealDetail },
  { path: "/stores", name: "Stores", component: Stores },
  { path: "/stores/:id", name: "StoreDetail", component: StoreDetail },
  { path: "/categories", name: "Categories", component: Categories },
  { path: "/brands", name: "Brands", component: Brands },
  { path: "/contact", name: "Contact", component: Contact },
  { path: "/coupons", name: "Coupons", component: Coupons },
  { path: "/deals", name: "Deals", component: Deals },
  { path: "/cart", name: "Cart", component: Cart },
  { path: "/checkout", name: "Checkout", component: Checkout, meta: { requiresAuth: true } },
  { path: "/dashboard", name: "UserDashboard", component: UserDashboard, meta: { requiresAuth: true } },
  { path: "/about", name: "About", component: StaticPage, props: { slug: "about" } },
  { path: "/terms", name: "Terms", component: StaticPage, props: { slug: "terms" } },
  { path: "/privacy", name: "Privacy", component: StaticPage, props: { slug: "privacy" } },
  { path: "/faq", name: "FAQ", component: StaticPage, props: { slug: "faq" } },
  { path: "/return-policy", name: "ReturnPolicy", component: StaticPage, props: { slug: "return-policy" } },

  // /admin এবং /admin/* — সব backend admin panel এ redirect করে
  {
    path: "/admin",
    name: "AdminRedirect",
    beforeEnter() { window.location.href = ADMIN_URL; },
    component: { template: "<div></div>" }
  },
  {
    path: "/admin/:pathMatch(.*)*",
    name: "AdminRedirectSub",
    beforeEnter() { window.location.href = ADMIN_URL; },
    component: { template: "<div></div>" }
  },

  { path: "/:pathMatch(.*)*", name: "NotFound", component: NotFound }
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior() {
    return { top: 0 };
  }
});

router.beforeEach((to, from, next) => {
  if (to.matched.some(record => record.meta.requiresAuth)) {
    const { isAuthenticated, openAuth } = useAuth();
    if (!isAuthenticated.value) {
      openAuth("login");
      next("/");
    } else {
      next();
    }
  } else {
    next();
  }
});

export default router;

