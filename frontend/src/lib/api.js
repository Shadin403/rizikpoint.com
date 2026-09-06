/**
 * api.js — Frontend API Library
 *
 * VITE_API_BASE_URL  = relative path, e.g. /api/v2  (goes through Vite proxy → no CORS)
 * VITE_BACKEND_ORIGIN = full origin,   e.g. http://127.0.0.1:8000  (for image URLs only)
 */

// ─── Base path (RELATIVE — routes through Vite dev proxy) ───────────────────
// IMPORTANT: Must be a relative path like /api/v2, NOT a full URL.
// This way Vite proxy forwards the request to the backend and avoids CORS.
const API_BASE = import.meta.env.VITE_API_BASE_URL || '/api/v2';

// ─── Backend origin for building storage image URLs ──────────────────────────
const BACKEND_ORIGIN = import.meta.env.VITE_BACKEND_ORIGIN || 'http://127.0.0.1:8000';

// --- In-flight + response cache (TTL based) ---------------------------------
// Why: Home triggers fetchBusinessSettings from Layout, Navbar and Footer
// (3x in one frame), and fetchCategories from both Navbar and Home. Without
// de-duplication the same 10 KB payload is requested 3 times, and the slow
// business-settings response is the main reason the home waterfall stretches
// past 12 s on first paint.
//
// This module-level cache:
//   - Returns the same Promise when a request is already in flight
//   - Caches successful responses for CACHE_TTL_MS (default 60s) so
//     navigating Home -> Product -> Home is instant
//   - Is safe to call from any component, multiple times, in parallel
const CACHE_TTL_MS = 60_000;          // default TTL (1 minute)
const LONG_CACHE_TTL_MS = 5 * 60_000;  // business settings (5 minutes)
const LS_PREFIX = 'dlpb_cache:';
const LS_VERSION = 1;
const inflight = new Map();   // key -> Promise
const responses = new Map();  // key -> { ts, data }
// Endpoints that get the long TTL (rarely change)
const LONG_CACHE_ENDPOINTS = new Set(['/business-settings']);
// Endpoints that also persist to localStorage so a refresh is instant
const PERSIST_ENDPOINTS = new Set(['/business-settings']);

function cacheKey(endpoint, options) {
  const method = ((options && options.method) || 'GET').toUpperCase();
  return method + ' ' + endpoint;
}

function getCached(endpoint, options) {
  const key = cacheKey(endpoint, options);
  const inflightPromise = inflight.get(key);
  if (inflightPromise) return inflightPromise;

  const ttl = LONG_CACHE_ENDPOINTS.has(endpoint) ? LONG_CACHE_TTL_MS : CACHE_TTL_MS;
  const cached = responses.get(key);
  if (cached && Date.now() - cached.ts < ttl) {
    return Promise.resolve(cached.data);
  }
  // Fallback to localStorage for persisted endpoints (survives page refresh)
  if (PERSIST_ENDPOINTS.has(endpoint) && (!options || !options.method || options.method.toUpperCase() === 'GET')) {
    try {
      const raw = localStorage.getItem(LS_PREFIX + key);
      if (raw) {
        const parsed = JSON.parse(raw);
        if (parsed && parsed.v === LS_VERSION && Date.now() - parsed.ts < ttl) {
          responses.set(key, { ts: parsed.ts, data: parsed.data });
          return Promise.resolve(parsed.data);
        }
      }
    } catch (e) { /* localStorage may be unavailable */ }
  }
  return null;
}

function setCached(endpoint, options, data) {
  const key = cacheKey(endpoint, options);
  const now = Date.now();
  responses.set(key, { ts: now, data });
  inflight.delete(key);
  // Persist to localStorage for endpoints that benefit from cross-session caching
  if (PERSIST_ENDPOINTS.has(endpoint) && (!options || !options.method || options.method.toUpperCase() === 'GET')) {
    try {
      localStorage.setItem(LS_PREFIX + key, JSON.stringify({ v: LS_VERSION, ts: now, data }));
    } catch (e) { /* quota exceeded or storage disabled */ }
  }
}

function setInflight(endpoint, options, promise) {
  const key = cacheKey(endpoint, options);
  inflight.set(key, promise);
  promise.finally(() => {
    if (inflight.get(key) === promise) inflight.delete(key);
  });
  return promise;
}

/** Exposed for places that mutate server data and need to bust the cache. */
export function bustApiCache(endpointPattern) {
  const clearLs = () => {
    try {
      for (let i = localStorage.length - 1; i >= 0; i--) {
        const k = localStorage.key(i);
        if (k && k.startsWith(LS_PREFIX)) localStorage.removeItem(k);
      }
    } catch (e) {}
  };
  if (!endpointPattern) {
    responses.clear();
    clearLs();
    return;
  }
  for (const key of Array.from(responses.keys())) {
    if (key.includes(endpointPattern)) responses.delete(key);
  }
  try {
    for (let i = localStorage.length - 1; i >= 0; i--) {
      const k = localStorage.key(i);
      if (k && k.startsWith(LS_PREFIX) && k.includes(endpointPattern)) localStorage.removeItem(k);
    }
  } catch (e) {}
}


// Helper: Build full image URL
// Laravel serves images at: http://127.0.0.1:8000/uploads/all/xxx.webp
// NOT at /storage/uploads/... — confirmed by API testing
function imageUrl(path) {
  if (!path) return null;
  if (path.startsWith('http://') || path.startsWith('https://')) return path;
  // Remove leading slash if present to avoid double slash
  const clean = path.startsWith('/') ? path.slice(1) : path;
  return `${BACKEND_ORIGIN}/${clean}`;
}

// ─── Mock Fallback Data ───────────────────────────────────────────────────────
const MOCK_CATEGORIES = [
  { id: 1, name: 'Gadgets',   icon: 'cpu',     dealCount: 8,  couponCount: 2 },
  { id: 2, name: 'Beauty',    icon: 'flower2',  dealCount: 5,  couponCount: 3 },
  { id: 3, name: 'Herbal',    icon: 'leaf',     dealCount: 3,  couponCount: 1 },
  { id: 4, name: 'Lifestyle', icon: 'smile',    dealCount: 6,  couponCount: 4 },
  { id: 5, name: 'Home',      icon: 'home',     dealCount: 7,  couponCount: 2 },
  { id: 6, name: 'Food',      icon: 'utensils', dealCount: 4,  couponCount: 3 },
];

const MOCK_STORES = [
  { id: 1, name: 'Daraz',    logoUrl: 'https://logos-world.net/wp-content/uploads/2022/05/Daraz-Logo.png',    description: 'Leading online marketplace in South Asia.', website: 'https://www.daraz.com.bd',    dealCount: 5, couponCount: 2 },
  { id: 2, name: 'Chaldal',  logoUrl: 'https://cdn.chaldal.com/_mp_images/logo.png',                         description: 'Online grocery store in Dhaka, Bangladesh.',  website: 'https://chaldal.com',        dealCount: 4, couponCount: 2 },
  { id: 3, name: 'Foodpanda',logoUrl: 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/cb/Foodpanda_logo.svg/1200px-Foodpanda_logo.svg.png', description: 'Order food from your favorite restaurants.', website: 'https://www.foodpanda.com.bd', dealCount: 3, couponCount: 1 },
  { id: 4, name: 'Pathao',   logoUrl: 'https://upload.wikimedia.org/wikipedia/commons/e/ee/Pathao_Logo_2018.png', description: 'Ride-sharing, food delivery and courier service.', website: 'https://pathao.com', dealCount: 3, couponCount: 2 },
  { id: 5, name: 'Shajgoj',  logoUrl: 'https://images.shajgoj.com/wp-content/uploads/2018/10/shajgoj-logo.png', description: 'Top beauty care and cosmetics platform in BD.',  website: 'https://www.shajgoj.com', dealCount: 4, couponCount: 1 },
];

const MOCK_DEALS = [
  { id: 1,  title: 'Daraz 11.11 Mega Sale — Up to 80% Off on Electronics',       originalPrice: 18000, discountedPrice: 14500, discountPercent: 19, storeId: 1, storeName: 'Daraz',    storeLogoUrl: MOCK_STORES[0].logoUrl, categoryId: 1, categoryName: 'Gadgets',   imageUrl: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&auto=format&fit=crop&q=60', expiresAt: new Date(Date.now() + 24*3600000).toISOString(), usedCount: 150, featured: true,  dealUrl: 'https://daraz.com.bd', createdAt: new Date().toISOString() },
  { id: 2,  title: 'Foodpanda Voucher — Flat 150৳ Off on Top Restaurants',       originalPrice: 500,   discountedPrice: 350,   discountPercent: 30, storeId: 3, storeName: 'Foodpanda',storeLogoUrl: MOCK_STORES[2].logoUrl, categoryId: 4, categoryName: 'Lifestyle', imageUrl: 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=500&auto=format&fit=crop&q=60', expiresAt: new Date(Date.now() + 12*3600000).toISOString(), usedCount: 300, featured: true,  dealUrl: 'https://foodpanda.com.bd', createdAt: new Date().toISOString() },
  { id: 3,  title: 'Shajgoj Organic Herbal Face Mask — Buy 1 Get 1 Free',        originalPrice: 900,   discountedPrice: 450,   discountPercent: 50, storeId: 5, storeName: 'Shajgoj',  storeLogoUrl: MOCK_STORES[4].logoUrl, categoryId: 3, categoryName: 'Herbal',    imageUrl: 'https://images.unsplash.com/photo-1556228720-195a672e8a03?w=500&auto=format&fit=crop&q=60', expiresAt: new Date(Date.now() + 48*3600000).toISOString(), usedCount: 95,  featured: true,  dealUrl: 'https://shajgoj.com', createdAt: new Date().toISOString() },
  { id: 4,  title: 'Chaldal Fresh Summer Vegetables Bundle (5kg)',                originalPrice: 350,   discountedPrice: 299,   discountPercent: 15, storeId: 2, storeName: 'Chaldal',  storeLogoUrl: MOCK_STORES[1].logoUrl, categoryId: 5, categoryName: 'Home',      imageUrl: 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=500&auto=format&fit=crop&q=60', expiresAt: new Date(Date.now() + 3*3600000).toISOString(),  usedCount: 220, featured: false, dealUrl: 'https://chaldal.com', createdAt: new Date().toISOString() },
  { id: 5,  title: 'Pathao Rides — 50% Off up to 50৳ on Next 3 Rides',           originalPrice: 200,   discountedPrice: 100,   discountPercent: 50, storeId: 4, storeName: 'Pathao',   storeLogoUrl: MOCK_STORES[3].logoUrl, categoryId: 4, categoryName: 'Lifestyle', imageUrl: 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?w=500&auto=format&fit=crop&q=60', expiresAt: new Date(Date.now() + 5*3600000).toISOString(),  usedCount: 500, featured: true,  dealUrl: 'https://pathao.com', createdAt: new Date().toISOString() },
  { id: 6,  title: 'Samsung Galaxy M14 5G — Exclusive Pricing',                  originalPrice: 19999, discountedPrice: 16500, discountPercent: 17, storeId: 1, storeName: 'Daraz',    storeLogoUrl: MOCK_STORES[0].logoUrl, categoryId: 1, categoryName: 'Gadgets',   imageUrl: 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&auto=format&fit=crop&q=60', expiresAt: new Date(Date.now() + 15*3600000).toISOString(), usedCount: 40,  featured: false, dealUrl: 'https://daraz.com.bd', createdAt: new Date().toISOString() },
  { id: 7,  title: "L'Oreal Skin Active Brightening Face Serum (30ml)",           originalPrice: 1600,  discountedPrice: 1250,  discountPercent: 22, storeId: 5, storeName: 'Shajgoj',  storeLogoUrl: MOCK_STORES[4].logoUrl, categoryId: 2, categoryName: 'Beauty',    imageUrl: 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?w=500&auto=format&fit=crop&q=60', expiresAt: new Date(Date.now() + 30*3600000).toISOString(), usedCount: 88,  featured: false, dealUrl: 'https://shajgoj.com', createdAt: new Date().toISOString() },
  { id: 8,  title: 'Premium Aromatic Chinigura Rice 5kg — Fresh & Clean',        originalPrice: 750,   discountedPrice: 680,   discountPercent: 9,  storeId: 2, storeName: 'Chaldal',  storeLogoUrl: MOCK_STORES[1].logoUrl, categoryId: 5, categoryName: 'Home',      imageUrl: 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=500&auto=format&fit=crop&q=60', expiresAt: new Date(Date.now() + 20*3600000).toISOString(), usedCount: 175, featured: false, dealUrl: 'https://chaldal.com', createdAt: new Date().toISOString() },
  { id: 9,  title: 'Apex Women\'s Leather Sandals — Elegant Collection',          originalPrice: 2200,  discountedPrice: 1800,  discountPercent: 18, storeId: 4, storeName: 'Pathao',   storeLogoUrl: MOCK_STORES[3].logoUrl, categoryId: 4, categoryName: 'Lifestyle', imageUrl: 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=500&auto=format&fit=crop&q=60', expiresAt: new Date(Date.now() + 72*3600000).toISOString(), usedCount: 65,  featured: true,  dealUrl: 'https://pathao.com', createdAt: new Date().toISOString() },
  { id: 10, title: 'Chaldal Chicken Egg Premium Package — 30 Pcs Box',            originalPrice: 420,   discountedPrice: 380,   discountPercent: 9,  storeId: 2, storeName: 'Chaldal',  storeLogoUrl: MOCK_STORES[1].logoUrl, categoryId: 5, categoryName: 'Home',      imageUrl: 'https://images.unsplash.com/photo-1516448424440-9dbca97779c1?w=500&auto=format&fit=crop&q=60', expiresAt: new Date(Date.now() + 24*3600000).toISOString(), usedCount: 380, featured: false, dealUrl: 'https://chaldal.com', createdAt: new Date().toISOString() },
  { id: 11, title: 'Daraz Bluetooth Smart Watch — IP68 Waterproof Sleep Tracker', originalPrice: 3500,  discountedPrice: 2800,  discountPercent: 20, storeId: 1, storeName: 'Daraz',    storeLogoUrl: MOCK_STORES[0].logoUrl, categoryId: 1, categoryName: 'Gadgets',   imageUrl: 'https://images.unsplash.com/photo-1542496658-e33a6d0d50f6?w=500&auto=format&fit=crop&q=60', expiresAt: new Date(Date.now() + 48*3600000).toISOString(), usedCount: 145, featured: true,  dealUrl: 'https://daraz.com.bd', createdAt: new Date().toISOString() },
  { id: 12, title: 'Foodpanda KFC Voucher — Flat 200৳ Off Coupon',               originalPrice: 600,   discountedPrice: 400,   discountPercent: 33, storeId: 3, storeName: 'Foodpanda',storeLogoUrl: MOCK_STORES[2].logoUrl, categoryId: 4, categoryName: 'Lifestyle', imageUrl: 'https://images.unsplash.com/photo-1513639776629-7b61b0ac23c3?w=500&auto=format&fit=crop&q=60', expiresAt: new Date(Date.now() + 8*3600000).toISOString(),  usedCount: 290, featured: false, dealUrl: 'https://foodpanda.com.bd', createdAt: new Date().toISOString() },
  { id: 13, title: 'Shajgoj CeraVe Hydrating Cleanser for Dry Skin (236ml)',     originalPrice: 1850,  discountedPrice: 1550,  discountPercent: 16, storeId: 5, storeName: 'Shajgoj',  storeLogoUrl: MOCK_STORES[4].logoUrl, categoryId: 2, categoryName: 'Beauty',    imageUrl: 'https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?w=500&auto=format&fit=crop&q=60', expiresAt: new Date(Date.now() + 40*3600000).toISOString(), usedCount: 110, featured: true,  dealUrl: 'https://shajgoj.com', createdAt: new Date().toISOString() },
  { id: 14, title: "Bata Men's Casual Loafers — Comfortable Leather Shoes",       originalPrice: 3200,  discountedPrice: 2400,  discountPercent: 25, storeId: 4, storeName: 'Pathao',   storeLogoUrl: MOCK_STORES[3].logoUrl, categoryId: 4, categoryName: 'Lifestyle', imageUrl: 'https://images.unsplash.com/photo-1533867617858-e7b97e060509?w=500&auto=format&fit=crop&q=60', expiresAt: new Date(Date.now() + 60*3600000).toISOString(), usedCount: 74,  featured: false, dealUrl: 'https://pathao.com', createdAt: new Date().toISOString() },
  { id: 15, title: 'Sony WH-1000XM5 Wireless Noise Cancelling Headphones',       originalPrice: 38000, discountedPrice: 32000, discountPercent: 15, storeId: 1, storeName: 'Daraz',    storeLogoUrl: MOCK_STORES[0].logoUrl, categoryId: 1, categoryName: 'Gadgets',   imageUrl: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&auto=format&fit=crop&q=60', expiresAt: new Date(Date.now() + 48*3600000).toISOString(), usedCount: 22,  featured: true,  dealUrl: 'https://daraz.com.bd', createdAt: new Date().toISOString() },
  { id: 16, title: 'Poco X6 Pro 5G Smartphone — 12GB RAM, 512GB Storage',        originalPrice: 42000, discountedPrice: 38500, discountPercent: 8,  storeId: 1, storeName: 'Daraz',    storeLogoUrl: MOCK_STORES[0].logoUrl, categoryId: 1, categoryName: 'Gadgets',   imageUrl: 'https://images.unsplash.com/photo-1567581935884-e214c4d321d1?w=500&auto=format&fit=crop&q=60', expiresAt: new Date(Date.now() + 72*3600000).toISOString(), usedCount: 43,  featured: true,  dealUrl: 'https://daraz.com.bd', createdAt: new Date().toISOString() },
  { id: 17, title: 'Neutrogena Hydro Boost Water Gel Moisturizer',               originalPrice: 1950,  discountedPrice: 1650,  discountPercent: 15, storeId: 5, storeName: 'Shajgoj',  storeLogoUrl: MOCK_STORES[4].logoUrl, categoryId: 2, categoryName: 'Beauty',    imageUrl: 'https://images.unsplash.com/photo-1526947425960-945c6e72858f?w=500&auto=format&fit=crop&q=60', expiresAt: new Date(Date.now() + 30*3600000).toISOString(), usedCount: 112, featured: true,  dealUrl: 'https://shajgoj.com', createdAt: new Date().toISOString() },
  { id: 18, title: 'Pathao Food Discount — 35% Off on Selected Restaurants',     originalPrice: 300,   discountedPrice: 195,   discountPercent: 35, storeId: 4, storeName: 'Pathao',   storeLogoUrl: MOCK_STORES[3].logoUrl, categoryId: 4, categoryName: 'Lifestyle', imageUrl: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=500&auto=format&fit=crop&q=60', expiresAt: new Date(Date.now() + 6*3600000).toISOString(),  usedCount: 420, featured: true,  dealUrl: 'https://pathao.com', createdAt: new Date().toISOString() },
];

const MOCK_COUPONS = [
  { id: 1, title: 'Save 500৳ on Electronics above 5000৳ purchase',         code: 'DARAZ500',     discountType: 'fixed',      discountValue: 500, verified: true, usedCount: 1024, storeId: 1, storeName: 'Daraz',     storeLogoUrl: MOCK_STORES[0].logoUrl, categoryId: 1, usedToday: 120 },
  { id: 2, title: 'Flat 150৳ Off on Foodpanda orders above 400৳',          code: 'FOODPANDA150', discountType: 'fixed',      discountValue: 150, verified: true, usedCount: 2541, storeId: 3, storeName: 'Foodpanda', storeLogoUrl: MOCK_STORES[2].logoUrl, categoryId: 4, usedToday: 340 },
  { id: 3, title: '20% Discount on Cosmetics & Skin Care products',        code: 'SHAJGOJ20',    discountType: 'percentage', discountValue: 20,  verified: true, usedCount: 532,  storeId: 5, storeName: 'Shajgoj',   storeLogoUrl: MOCK_STORES[4].logoUrl, categoryId: 2, usedToday: 60  },
  { id: 4, title: 'Free Home Delivery on grocery orders above 499৳',       code: 'CHALDALFREE',  discountType: 'fixed',      discountValue: 70,  verified: true, usedCount: 819,  storeId: 2, storeName: 'Chaldal',   storeLogoUrl: MOCK_STORES[1].logoUrl, categoryId: 5, usedToday: 95  },
  { id: 5, title: '50% Discount on Pathao bike and car rides',             code: 'PATHAO50',     discountType: 'percentage', discountValue: 50,  verified: true, usedCount: 1400, storeId: 4, storeName: 'Pathao',    storeLogoUrl: MOCK_STORES[3].logoUrl, categoryId: 4, usedToday: 180 },
];

const MOCK_BANNERS = [
  { id: 1, image: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1200&auto=format&fit=crop&q=80', title: 'Smart Gadgets at Best Prices',   titleBn: 'সেরা দামে স্মার্ট গ্যাজেটস',   description: 'Get top discounts on our exclusive gadget collection today.',              descriptionBn: 'আমাদের এক্সক্লুসিভ গ্যাজেট কালেকশনে পাচ্ছেন সেরা ডিসকাউন্ট।', link: '/products-list' },
  { id: 2, image: 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=1200&auto=format&fit=crop&q=80', title: 'Exclusive Beauty Products',       titleBn: 'এক্সক্লুসিভ বিউটি প্রোডাক্টস',  description: 'Special offers on 100% original cosmetics & skincare products!',          descriptionBn: '১০০% অরিজিনাল কসমেটিকস ও স্কিনকেয়ার পণ্যে বিশেষ ছাড়!',       link: '/products-list' },
  { id: 3, image: 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=1200&auto=format&fit=crop&q=80', title: 'Daily Grocery Essentials',        titleBn: 'দৈনিক প্রয়োজনীয় বাজার',        description: 'Get fresh vegetables and daily grocery items delivered home.',            descriptionBn: 'নিত্যপ্রয়োজনীয় গ্রোসারি ডেলিভারি পান ঘরে বসেই দ্রুততম সময়ে।', link: '/products-list' },
];

// ─── Data Mappers ─────────────────────────────────────────────────────────────

/**
 * Maps a backend product object to the frontend deal/product shape.
 * Backend list item fields: id, name, thumbnail_image, has_discount,
 *   stroked_price ("?1,250.00"), main_price ("?1,250.00"), rating, sales
 * Backend detail item fields: all of above + calculable_price, description,
 *   shop_name, shop_logo, photos[], brand, category_id, featured, num_of_sale
 */
function parsePrice(str) {
  if (typeof str === 'number') return str;
  if (!str) return 0;
  // Remove currency symbol and commas: "?1,250.00" -> 1250
  return parseFloat(String(str).replace(/[^0-9.]/g, '')) || 0;
}

function mapProduct(p) {
  if (!p) return null;


  const origPrice  = parsePrice(p.stroked_price) || parsePrice(p.unit_price) || parsePrice(p.price) || 0;
  const mainPrice  = parsePrice(p.main_price) || parsePrice(p.calculable_price) || origPrice;

  // Determine discounted price and percent
  let discountedPrice = mainPrice;
  let discountPercent = 0;

  if (p.has_discount && origPrice > 0 && mainPrice < origPrice) {
    discountedPrice = mainPrice;
    discountPercent = Math.round(((origPrice - mainPrice) / origPrice) * 100);
  } else {
    // Fallback: manual discount calculation from unit_price + discount fields
    const rawDiscount = parseFloat(p.discount ?? 0);
    const discountType = p.discount_type ?? 'percent';
    const rawOrig = parsePrice(p.unit_price) || origPrice;
    if (discountType === 'percent' && rawDiscount > 0) {
      discountedPrice = Math.round(rawOrig * (1 - rawDiscount / 100));
      discountPercent = Math.round(rawDiscount);
    } else if (discountType === 'flat' && rawDiscount > 0) {
      discountedPrice = Math.max(0, rawOrig - rawDiscount);
      discountPercent = rawOrig > 0 ? Math.round((rawDiscount / rawOrig) * 100) : 0;
    }
  }

  // Thumbnail: prefer thumbnail_image, fallback to first photo
  const photos = p.photos ?? [];
  const thumb = p.thumbnail_image || (photos[0]?.path ?? null);

  return {
    id:             p.id,
    title:          p.name ?? p.title ?? 'Untitled Product',
    description:    p.details ?? p.description ?? '',
    originalPrice:  origPrice || discountedPrice,
    discountedPrice: discountedPrice,
    discountPercent: discountPercent,
    imageUrl:       imageUrl(thumb),
    storeId:        p.seller_id ?? p.user_id ?? null,
    storeName:      p.shop_name ?? p.shop?.name ?? p.seller?.shop?.name ?? p.brand?.name ?? '',
    storeLogoUrl:   imageUrl(p.shop_logo ?? p.shop?.image ?? null),
    categoryId:     p.category_id ?? null,
    categoryName:   p.category?.name ?? '',
    featured:       !!(p.featured),
    rating:         parseFloat(p.rating ?? 0),
    numOfSale:      parseInt(p.num_of_sale ?? p.sales ?? 0),
    usedCount:      parseInt(p.num_of_sale ?? p.sales ?? 0),
    dealUrl:        p.link ?? null,
    createdAt:      p.created_at ?? new Date().toISOString(),
    expiresAt:      null,
    slug:           p.slug ?? '',
    colors:         p.colors ?? [],
    choice_options: p.choice_options ?? [],
    photos:         p.photos ?? [],
    current_stock:  p.current_stock ?? 0,
    returnPolicy:   p.return_policy ?? '',
  };
}

/**
 * Maps a backend category object to the frontend shape.
 * Backend fields: id, name, banner, icon, number_of_children, links
 */
function mapCategory(c) {
  if (!c) return null;
  return {
    id:          c.id,
    name:        c.name,
    icon:        c.icon ?? 'grid',
    imageUrl:    imageUrl(c.banner ?? null),
    dealCount:   parseInt(c.products_count ?? c.product_count ?? c.number_of_children ?? 0),
    couponCount: parseInt(c.coupon_count ?? 0),
  };
}

/**
 * Maps a backend shop/seller object to the frontend store shape.
 */
function mapStore(s) {
  if (!s) return null;
  return {
    id:          s.id,
    name:        s.name,
    logoUrl:     imageUrl(s.image ?? s.logo ?? null),
    description: s.address ?? s.description ?? '',
    website:     s.website ?? null,
    dealCount:   parseInt(c.products_count ?? c.product_count ?? c.number_of_children ?? 0),
    couponCount: parseInt(s.coupon_count ?? 0),
  };
}

/**
 * Maps a backend flash deal product to the frontend deal shape.
 */
function mapFlashDeal(item) {
  if (!item) return null;
  const product = item.product ?? item;
  const base = mapProduct(product);
  if (!base) return null;

  const flashDiscount = parseFloat(item.discount ?? 0);
  const flashDiscountType = item.discount_type ?? 'percent';
  if (flashDiscountType === 'percent' && flashDiscount > 0) {
    base.discountPercent  = Math.round(flashDiscount);
    base.discountedPrice  = Math.round(base.originalPrice * (1 - flashDiscount / 100));
  } else if (flashDiscountType === 'flat' && flashDiscount > 0) {
    base.discountedPrice  = Math.max(0, base.originalPrice - flashDiscount);
    base.discountPercent  = base.originalPrice > 0 ? Math.round((flashDiscount / base.originalPrice) * 100) : 0;
  }
  return base;
}

/**
 * Maps backend banner/slider object.
 */
function mapBanner(b) {
  if (!b) return null;
  return {
    id:            b.id,
    image:         imageUrl(b.photo ?? b.banner ?? b.image ?? null),
    title:         b.title ?? b.name ?? '',
    titleBn:       b.title_bn ?? b.title ?? '',
    description:   b.description ?? '',
    descriptionBn: b.description_bn ?? b.description ?? '',
    link:          b.link ?? b.url ?? '/products-list',
    buttonText:    b.button_text ?? b.buttonText ?? 'Order Now',
  };
}


// ─── Generic fetch helper ─────────────────────────────────────────────────────

async function apiFetch(endpoint, options = {}) {
  const method = (options.method || 'GET').toUpperCase();
  if (method === 'GET') {
    const cached = getCached(endpoint, options);
    if (cached && options.cache !== 'no-store') return cached;
  }

  const url = API_BASE + endpoint;
  const promise = (async () => {
    const fetchOptions = { ...options };
    if (fetchOptions.body && typeof fetchOptions.body === 'object' && !(fetchOptions.body instanceof FormData) && !(fetchOptions.body instanceof Blob)) {
      fetchOptions.body = JSON.stringify(fetchOptions.body);
    }
    const token = localStorage.getItem('token');
    const headers = {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
      ...(options.headers || {})
    };
    const res = await fetch(url, {
      ...fetchOptions,
      headers
    });
    if (!res.ok) {
      let payload = null;
      try { payload = await res.json(); } catch (_) {}
      const msg = (payload && (payload.message || payload.error)) || 'API ' + res.status + ': ' + endpoint;
      const err = new Error(msg);
      err.status = res.status;
      err.data = payload;
      err.endpoint = endpoint;
      throw err;
    }
    return res.json();
  })();

  if (method === 'GET' && options.cache !== 'no-store') {
    setInflight(endpoint, options, promise);
    try {
      const data = await promise;
      setCached(endpoint, options, data);
      return data;
    } catch (err) { throw err; }
  }

  return promise;
}

// ─── Public API Functions ─────────────────────────────────────────────────────

/**
 * Fetch all products (deals).
 * Params: { categoryId, storeId, featured, search, page, limit }
 */
export async function fetchDeals(params = {}) {
  try {
    const query = new URLSearchParams();
    if (params.categoryId) query.set('category_id', params.categoryId);
    if (params.storeId)    query.set('seller_id', params.storeId);
    if (params.search)     query.set('name', params.search);
    if (params.page)       query.set('page', params.page);
    if (params.limit)      query.set('limit', params.limit ?? 20);

    const qs = query.toString();

    if (params.featured) {
      const data = await apiFetch(`/products/featured${qs ? '?' + qs : ''}`);
      const items = Array.isArray(data) ? data : (data.products ?? data.data ?? []);
      return items.map(mapProduct).filter(Boolean);
    }

    if (params.search) {
      const data = await apiFetch(`/products/search?${qs}`);
      const items = Array.isArray(data) ? data : (data.products ?? data.data ?? []);
      return items.map(mapProduct).filter(Boolean);
    }

    const data = await apiFetch(`/products${qs ? '?' + qs : ''}`);
    const items = Array.isArray(data) ? data : (data.products ?? data.data ?? []);
    return items.map(mapProduct).filter(Boolean);
  } catch (err) {
    console.warn('API unavailable, returning empty deals.', err.message);
    return [];
  }
}

/**
 * Fetch a paginated list of products (deals) and return deals + pagination meta + links.
 * Params: { categoryId, storeId, search, page, limit }
 * Returns: { deals: [...], meta: { current_page, last_page, total, per_page }, links: { next, prev, ... } }
 */
export async function fetchDealsPaged(params = {}) {
  try {
    const query = new URLSearchParams();
    if (params.categoryId) query.set('category_id', params.categoryId);
    if (params.storeId)    query.set('seller_id', params.storeId);
    if (params.search)     query.set('name', params.search);
    if (params.page)       query.set('page', params.page);
    if (params.limit)      query.set('limit', params.limit ?? 20);

    // /products/search returns paginated Laravel meta/links, /products also does.
    const endpoint = params.search ? `/products/search` : `/products`;
    const data = await apiFetch(`${endpoint}${query.toString() ? '?' + query.toString() : ''}`);
    const items = Array.isArray(data) ? data : (data.data ?? []);
    const deals = items.map(mapProduct).filter(Boolean);
    return {
      deals,
      meta: data?.meta ?? null,
      links: data?.links ?? null,
    };
  } catch (err) {
    console.warn('Paged deals API unavailable, returning empty.', err.message);
    return { deals: [], meta: null, links: null };
  }
}


/**

 * Lightweight real-time product search for the navbar search bar.

 * Params: { search, categoryId, limit }

 * Returns: Array of mapped products (max `limit`, default 8).

 * Designed to be debounced from the UI side.

 */

export async function searchProducts(params = {}) {

  const term = (params.search || '').trim();

  if (!term) return [];

  try {

    const query = new URLSearchParams();

    query.set('name', term);

    if (params.categoryId) query.set('category_id', params.categoryId);

    query.set('limit', params.limit ?? 8);



    const data = await apiFetch(`/products/search?${query.toString()}`);

    const items = Array.isArray(data) ? data : (data.products ?? data.data ?? []);

    return items.map(mapProduct).filter(Boolean);

  } catch (err) {

    console.warn('Live search unavailable, returning empty.', err.message);

    return [];

  }

}
/**
 * Fetch a single product/deal by ID or Slug.
 */
export async function fetchDealById(idOrSlug) {
  try {
    const data = await apiFetch(`/products/${idOrSlug}`);
    // Backend returns { data: [...product], success, status } (Collection wraps in array)
    // Wait, ProductDetailCollection returns 'data' which is an array!
    const productData = data.data ?? data.product ?? data;
    const rawProduct = Array.isArray(productData) ? productData[0] : productData;
    return mapProduct(rawProduct) ?? null;
  } catch (err) {
    console.warn(`API unavailable, returning null for deal #${idOrSlug}.`, err.message);
    return null;
  }
}

/**
 * Fetch variant price and stock information from backend.
 */
export async function fetchVariantPrice(productId, colorHex, selectedOptions) {
  try {
    const params = new URLSearchParams();
    params.append('id', productId);
    if (colorHex) {
      // Strip leading '#' if present
      const cleanColor = colorHex.startsWith('#') ? colorHex.slice(1) : colorHex;
      params.append('color', cleanColor);
    }
    if (selectedOptions && selectedOptions.length > 0) {
      params.append('variants', selectedOptions.join(','));
    }
    const data = await apiFetch(`/products/variant/price?${params.toString()}`);
    return data;
  } catch (err) {
    console.error("Failed to fetch variant price:", err);
    return null;
  }
}

/**
 * Fetch today's flash deals.
 *
 * The backend's /api/v2/flash-deals endpoint now returns each active flash
 * deal together with its full nested product list (in ProductMiniCollection
 * shape). We flatten every deal's products into a single array, so the
 * homepage "ফ্ল্যাশ ডিল" section can render with one round-trip.
 *
 * Falls back to /products/todays-deal (then to mock data) when no
 * active flash deals exist.
 */
export async function fetchFlashDeals() {
  try {
    const data = await apiFetch('/flash-deals');
    const flashList = Array.isArray(data) ? data : (data.data ?? []);

    // Flatten every deal's products into one list.
    // New shape: fd.products[] — already in ProductMiniCollection form.
    // Legacy shape (older backend): fd.flash_deal_products[] — each row is
    // a pivot that may wrap a `product` object plus discount overrides.
    const products = [];
    for (const fd of flashList) {
      const nested = fd.products ?? fd.flash_deal_products ?? [];
      for (const item of nested) {
        // New shape: product-shaped directly. Legacy shape: { product, discount, discount_type }.
        const isPivot = item && (item.product !== undefined || item.discount !== undefined || item.discount_type !== undefined);
        const mapped = isPivot
          ? mapFlashDeal(item)               // legacy pivot: apply override then map
          : mapProduct(item);                // new shape: map straight through
        if (mapped) products.push(mapped);
      }
    }

    // Fallback: if no active flash deals exist, use today's deal products.
    if (products.length === 0) {
      const td = await apiFetch('/products/todays-deal');
      const tdItems = Array.isArray(td) ? td : (td.data ?? []);
      const tdMapped = tdItems.map(mapProduct).filter(Boolean);
      if (tdMapped.length > 0) return tdMapped;
    }

    return products;
  } catch (err) {
    console.warn('API unavailable, returning empty flash deals.', err.message);
    return [];
  }
}

/**
 * Fetch active flash deals as a list of sections — one section per deal.
 * Each section keeps the deal's own title, banner, slug, and products so
 * the homepage can render each deal as its own card row.
 *
 * Shape returned:
 *   [
 *     { id, title, slug, banner, date, startDate, products: [...] },
 *     { id, title, slug, banner, date, startDate, products: [...] },
 *     ...
 *   ]
 *
 * If no deals are active, falls back to /products/todays-deal wrapped in
 * a single section titled "Today's Deal".
 */
export async function fetchFlashDealSections() {
  try {
    const data = await apiFetch('/flash-deals');
    const flashList = Array.isArray(data) ? data : (data.data ?? []);

    const sections = [];
    for (const fd of flashList) {
      const nested = fd.products ?? fd.flash_deal_products ?? [];
      const products = [];
      for (const item of nested) {
        const isPivot = item && (item.product !== undefined || item.discount !== undefined || item.discount_type !== undefined);
        const mapped = isPivot ? mapFlashDeal(item) : mapProduct(item);
        if (mapped) products.push(mapped);
      }

      // Skip empty deals — they have nothing to render
      if (products.length === 0) continue;

      sections.push({
        id:        fd.id,
        title:     fd.title || 'Flash Deal',
        slug:      fd.slug || null,
        banner:    imageUrl(fd.banner || null),
        date:      fd.date ? Number(fd.date) * 1000 : null,   // unix s -> ms
        startDate: fd.start_date ? Number(fd.start_date) * 1000 : null,
        products,
      });
    }

    if (sections.length > 0) return sections;

    // Fallback: today's deal products as a single section
    const td = await apiFetch('/products/todays-deal');
    const tdItems = Array.isArray(td) ? td : (td.data ?? []);
    const tdMapped = tdItems.map(mapProduct).filter(Boolean);
    if (tdMapped.length > 0) {
      return [{
        id: 0,
        title: "Today's Deal",
        slug: null,
        banner: null,
        date: null,
        startDate: null,
        products: tdMapped,
      }];
    }

    return [];
  } catch (err) {
    console.warn('API unavailable, returning empty flash deal sections.', err.message);
    return [];
  }
}

/**
 * Fetch today's deal products.
 */
export async function fetchTodaysDeals() {
  try {
    const data = await apiFetch('/products/todays-deal');
    const items = Array.isArray(data) ? data : (data.products ?? data.data ?? []);
    return items.map(mapProduct).filter(Boolean);
  } catch (err) {
    console.warn('API unavailable, returning empty todays deals.', err.message);
    return [];
  }
}

/**
 * Fetch featured products.
 */
export async function fetchFeaturedProducts() {
  try {
    const data = await apiFetch('/products/featured');
    const items = Array.isArray(data) ? data : (data.products ?? data.data ?? []);
    return items.map(mapProduct).filter(Boolean);
  } catch (err) {
    console.warn('API unavailable, returning empty featured products.', err.message);
    return [];
  }
}

/**
 * Fetch best-selling products.
 */
export async function fetchBestSellers() {
  try {
    const data = await apiFetch('/products/best-seller');
    const items = Array.isArray(data) ? data : (data.products ?? data.data ?? []);
    return items.map(mapProduct).filter(Boolean);
  } catch (err) {
    console.warn('API unavailable, returning empty best sellers.', err.message);
    return [];
  }
}

/**
 * Fetch all categories.
 */
export async function fetchCategories() {
  try {
    const data = await apiFetch('/categories');
    const items = Array.isArray(data) ? data : (data.categories ?? data.data ?? []);
    return items.map(mapCategory).filter(Boolean);
  } catch (err) {
    console.warn('API unavailable, returning empty categories.', err.message);
    return [];
  }
}

/**
 * Fetch filter categories (for sidebar).
 * Tries /filter/categories first, falls back to /categories.
 */
/**
 * Fetch sub-categories under a given parent category id.
 * Returns array of { id, name, icon, dealCount, ... }.
 */
export async function fetchSubCategories(categoryId) {
  try {
    const data = await apiFetch(`/sub-categories/${categoryId}`);
    const items = Array.isArray(data) ? data : (data.data ?? data.categories ?? []);
    return items.map(mapCategory).filter(Boolean);
  } catch (err) {
    console.warn(`Sub-categories API unavailable for categoryId=${categoryId}.`, err.message);
    return [];
  }
}

export async function fetchFilterCategories() {
  try {
    let data;
    try {
      data = await apiFetch('/filter/categories');
    } catch {
      data = await apiFetch('/categories');
    }
    const items = Array.isArray(data) ? data : (data.data ?? data.categories ?? []);
    return items.map(mapCategory).filter(Boolean);
  } catch (err) {
    console.warn('API unavailable, returning empty filter categories.', err.message);
    return [];
  }
}

/**
 * Fetch filter brands.
 */
export async function fetchFilterBrands() {
  try {
    const data = await apiFetch('/filter/brands');
    const items = Array.isArray(data) ? data : (data.brands ?? data.data ?? []);
    return items.map(b => ({
      id:   b.id,
      name: b.name,
      logo: imageUrl(b.logo ?? null),
    })).filter(Boolean);
  } catch (err) {
    console.warn('API unavailable, returning empty brands.', err.message);
    return [];
  }
}

/**
 * Fetch all brands.
 */
export async function fetchBrands() {
  try {
    const data = await apiFetch('/brands');
    const items = Array.isArray(data) ? data : (data.data ?? []);
    return items.map(b => ({
      id:   b.id,
      name: b.name,
      logo: imageUrl(b.logo ?? null),
    })).filter(Boolean);
  } catch (err) {
    console.warn('API unavailable, returning empty brands.', err.message);
    return [];
  }
}


/**
 * Fetch all shops/stores.
 */
export async function fetchStores() {
  try {
    const data = await apiFetch('/shops');
    const items = Array.isArray(data) ? data : (data.shops ?? data.data ?? []);
    return items.map(mapStore).filter(Boolean);
  } catch (err) {
    console.warn('API unavailable, returning empty stores.', err.message);
    return [];
  }
}

/**
 * Fetch a single shop/store by ID, with its products.
 */
export async function fetchStoreById(id) {
  try {
    const [storeData, productsData] = await Promise.all([
      apiFetch(`/shops/details/${id}`),
      apiFetch(`/shops/products/all/${id}`),
    ]);
    const store = mapStore(storeData.shop ?? storeData);
    const products = (productsData.products ?? productsData.data ?? []).map(mapProduct).filter(Boolean);
    return store ? { ...store, deals: products, coupons: [] } : null;
  } catch (err) {
    console.warn(`API unavailable, returning null for store #${id}.`, err.message);
    return null;
  }
}

/**
 * Fetch published sliders for the hero section (public endpoint).
 */
export async function fetchBanners() {
  try {
    const data = await apiFetch('/sliders');
    const items = Array.isArray(data) ? data : (data.data ?? data.sliders ?? data.banners ?? []);
    return items.map(mapBanner).filter(b => b && b.image);
  } catch (err) {
    console.warn('Sliders API unavailable, returning empty.', err.message);
    return [];
  }
}


// ─── Reviews ────────────────────────────────────────────────────────────────

/**
 * Fetch approved reviews for a product (paginated Laravel collection).
 * Returns { data: [...reviews], meta: { total, average, rating_breakdown } }.
 */
export async function fetchProductReviews(productId, page = 1) {
  try {
    const data = await apiFetch('/reviews/product/' + productId + '?page=' + page + '&_t=' + Date.now(), { cache: 'no-store' });
    const items = data.data ?? [];
    return {
      data: items,
      meta: data.meta ?? null,
    };
  } catch (err) {
    console.warn('Reviews API unavailable.', err.message);
    return { data: [], meta: null };
  }
}

/**
 * Submit a new review for a product. Pass `userId` (legacy) or rely on auth.
 */
export async function submitProductReview({ productId, userId, rating, comment }) {
  return apiFetch('/reviews/submit', {
    method: 'POST',
    body: { product_id: productId, user_id: userId, rating, comment },
  });
}

/**
 * Update an existing review. Only the owner may update it.
 */
export async function updateProductReview(reviewId, { rating, comment }) {
  return apiFetch(`/reviews/${reviewId}`, {
    method: 'POST',
    body: { rating, comment },
  });
}

/**
 * Delete a review. Only the owner may delete it.
 */
export async function deleteProductReview(reviewId) {
  return apiFetch(`/reviews/${reviewId}`, { method: 'DELETE' });
}

// ─── Admin Slider CRUD ────────────────────────────────────────────────────────

/**
 * Fetch ALL sliders (published + unpublished) — for admin panel.
 */
export async function fetchAdminSliders() {
  try {
    const data = await apiFetch('/sliders/all');
    const items = Array.isArray(data) ? data : (data.data ?? []);
    return items.map(s => ({
      id:        s.id,
      title:     s.title ?? '',
      image:     imageUrl(s.photo ?? null),
      photo:     s.photo ?? null,
      link:      s.link ?? '/products-list',
      published: s.published,
    }));
  } catch (err) {
    console.warn('Admin sliders fetch failed.', err.message);
    return [];
  }
}

/**
 * Create a new slider (multipart form data).
 * @param {FormData} formData — must include photo, title, link, published
 */
export async function createSlider(formData) {
  const url = `${API_BASE}/sliders`;
  const res = await fetch(url, {
    method: 'POST',
    headers: { Accept: 'application/json' },
    body: formData,
  });
    if (!res.ok) {
      let payload = null;
      try { payload = await res.json(); } catch (_) { /* not json */ }
      const msg = (payload && (payload.message || payload.error)) || `API ${res.status}: ${endpoint}`;
      const err = new Error(msg);
      err.status = res.status;
      err.data = payload;
      err.endpoint = endpoint;
      throw err;
    }
  return res.json();
}

/**
 * Update a slider (multipart form data — supports image replacement).
 */
export async function updateSlider(id, formData) {
  const url = `${API_BASE}/sliders/${id}`;
  const res = await fetch(url, {
    method: 'POST',
    headers: { Accept: 'application/json' },
    body: formData,
  });
    if (!res.ok) {
      let payload = null;
      try { payload = await res.json(); } catch (_) { /* not json */ }
      const msg = (payload && (payload.message || payload.error)) || `API ${res.status}: ${endpoint}`;
      const err = new Error(msg);
      err.status = res.status;
      err.data = payload;
      err.endpoint = endpoint;
      throw err;
    }
  return res.json();
}

/**
 * Delete a slider by ID.
 */
export async function deleteSlider(id) {
  const url = `${API_BASE}/sliders/${id}`;
  const res = await fetch(url, {
    method: 'DELETE',
    headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
  });
    if (!res.ok) {
      let payload = null;
      try { payload = await res.json(); } catch (_) { /* not json */ }
      const msg = (payload && (payload.message || payload.error)) || `API ${res.status}: ${endpoint}`;
      const err = new Error(msg);
      err.status = res.status;
      err.data = payload;
      err.endpoint = endpoint;
      throw err;
    }
  return res.json();
}

/**
 * Toggle slider published/unpublished.
 */
export async function toggleSlider(id) {
  const url = `${API_BASE}/sliders/${id}/toggle`;
  const res = await fetch(url, {
    method: 'PATCH',
    headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
  });
    if (!res.ok) {
      let payload = null;
      try { payload = await res.json(); } catch (_) { /* not json */ }
      const msg = (payload && (payload.message || payload.error)) || `API ${res.status}: ${endpoint}`;
      const err = new Error(msg);
      err.status = res.status;
      err.data = payload;
      err.endpoint = endpoint;
      throw err;
    }
  return res.json();
}



let fetchedCouponsCache = [];

/**
 * Fetch all coupons.
 */
export async function fetchCoupons(params = {}) {
  try {
    const query = new URLSearchParams();
    if (params.categoryId) query.set('category_id', params.categoryId);
    if (params.storeId)    query.set('seller_id', params.storeId);
    const qs = query.toString();
    const data = await apiFetch(`/coupons${qs ? '?' + qs : ''}`);
    const items = Array.isArray(data) ? data : (data.coupons ?? data.data ?? []);
    const mapped = items.map(c => ({
      id:            c.id,
      title:         c.title ?? c.code,
      description:   c.details ?? '',
      code:          c.code,
      discountType:  c.discount_type ?? 'fixed',
      discountValue: parseFloat(c.discount ?? 0),
      verified:      true,
      usedCount:     parseInt(c.no_of_uses ?? 0),
      storeId:       c.user_id ?? null,
      storeName:     c.seller?.shop?.name ?? '',
      storeLogoUrl:  imageUrl(c.seller?.shop?.image ?? null),
      categoryId:    null,
      usedToday:     0,
    })).filter(Boolean);
    fetchedCouponsCache = mapped;
    return mapped;
  } catch (err) {
    console.warn('API unavailable, returning empty coupons.', err.message);
    return [];
  }
}

/**
 * Reveal a coupon code.
 */
export async function revealCoupon(id) {
  const coupon = fetchedCouponsCache.find(c => c.id === Number(id));
  if (coupon) {
    return { code: coupon.code };
  }
  throw new Error('Coupon not found');
}

/**
 * Fetch general site stats.
 */
export async function fetchStats() {
  try {
    const data = await apiFetch('/business-settings');
    const settings = Array.isArray(data) ? data : (data.data ?? []);
    const get = (key) => settings.find(s => s.type === key)?.value ?? null;
    return {
      totalDeals:      parseInt(get('product_count') ?? 0),
      totalCoupons:    0,
      totalStores:     parseInt(get('seller_count') ?? 0),
      totalCategories: parseInt(get('category_count') ?? 0),
      dealsAddedToday: 0,
      totalSavings:    0,
    };
  } catch (err) {
    console.warn('API unavailable, returning zero stats.', err.message);
    return {
      totalDeals:      0,
      totalCoupons:    0,
      totalStores:     0,
      totalCategories: 0,
      dealsAddedToday: 0,
      totalSavings:    0,
    };
  }
}

/**
 * Fetch all business settings.
 */
/**
 * Fetch the storefront delivery info rows (key/value pairs) from the dedicated table.
 * Returns an array of { type, value } items.
 */
export async function fetchDeliveryInfos() {
  try {
    const data = await apiFetch('/delivery-infos');
    const items = Array.isArray(data) ? data : (data.data ?? []);
    return items;
  } catch (err) {
    console.warn('Delivery info API unavailable.', err.message);
    return [];
  }
}

export async function fetchBusinessSettings() {
  try {
    const data = await apiFetch('/business-settings');
    return Array.isArray(data) ? data : (data.data ?? []);
  } catch (err) {
    console.warn('API unavailable, returning empty settings.', err.message);
    return [];
  }
}

/**
 * Fire-and-forget prefetch for the most common endpoints. Call this once on
 * app boot (e.g. main.js) so that by the time any page mounts and asks for
 * business-settings, categories or sliders, the data is already in cache.
 */
export function prefetchHotData() {
  // Don't await - we want the calls to start, not block boot.
  fetchBusinessSettings().catch(() => {});
  fetchCategories().catch(() => {});
  try { apiFetch('/sliders').catch(() => {}); } catch (e) {}
}

/**
 * Log in a user.
 */
export async function apiLogin(email, password) {
  const url = `${API_BASE}/auth/login`;
  const res = await fetch(url, {
    method: 'POST',
    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, password }),
  });
  const data = await res.json();
  if (!res.ok || !data.result) {
    throw new Error(data.message || 'মোবাইল/ইমেইল অথবা পাসওয়ার্ড সঠিক নয়');
  }
  return data;
}

/**
 * Sign up a new user.
 */
export async function apiSignup(name, emailOrPhone, password, registerBy = 'email') {
  const url = `${API_BASE}/auth/signup`;
  const res = await fetch(url, {
    method: 'POST',
    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
    body: JSON.stringify({
      name,
      email_or_phone: emailOrPhone,
      password,
      register_by: registerBy,
    }),
  });
  const data = await res.json();
  if (!res.ok || !data.result) {
    throw new Error(data.message || 'রেজিস্ট্রেশন করতে ব্যর্থ হয়েছে');
  }
  return data;
}

/**
 * Fetch saved shipping addresses for a user.
 */
export function fetchAddresses(userId) {
  return apiFetch(`/user/shipping/address/${userId}`);
}

/**
 * Create a new shipping address.
 */
export function createAddress(payload) {
  return apiFetch('/user/shipping/create', {
    method: 'POST',
    body: payload,
  });
}

/**
 * Fetch active payment methods.
 */
export function fetchPaymentTypes() {
  return apiFetch('/payment-types');
}

/**
 * Fetch cart items saved in database.
 */
export function fetchDBCart(userId) {
  return apiFetch(`/carts/${userId}`, {
    method: 'POST',
  });
}

/**
 * Delete a database cart item by cart ID.
 */
export function deleteDBCartItem(cartId) {
  return apiFetch(`/carts/${cartId}`, {
    method: 'DELETE',
  });
}

/**
 * Add a product/variant to the database cart.
 */
export function addToDBCart(payload) {
  return apiFetch('/carts/add', {
    method: 'POST',
    body: payload,
  });
}

/**
 * Synchronize local cart items to the database in a single batch request.
 */
export function syncDBCart(payload) {
  return apiFetch('/carts/sync', {
    method: 'POST',
    body: payload,
  });
}

/**
 * Update shipping address in cart and calculate shipping cost.
 */
export function updateShippingCost(payload) {
  return apiFetch('/shipping_cost', {
    method: 'POST',
    body: payload,
  });
}

/**
 * Place the final order.
 */
export function placeOrder(payload) {
  return apiFetch('/order/store', {
    method: 'POST',
    body: payload,
  });
}

/**
 * Fetch list of active countries.
 */
export function fetchCountries() {
  return apiFetch('/countries');
}

/**
 * Fetch states/divisions by country ID.
 */
export function fetchStatesByCountry(countryId) {
  return apiFetch(`/states-by-country/${countryId}`);
}

/**
 * Fetch cities/districts by state ID.
 */
export function fetchCitiesByState(stateId) {
  return apiFetch(`/cities-by-state/${stateId}`);
}

/**
 * Fetch purchase history / order history for a user.
 */
export function fetchPurchaseHistory(userId) {
  return apiFetch(`/purchase-history/${userId}`);
}

/**
 * Fetch purchase history details by order ID.
 */
export function fetchOrderDetails(orderId) {
  return apiFetch(`/purchase-history-details/${orderId}`);
}

/**
 * Fetch purchase history items by order ID.
 */
export function fetchOrderItems(orderId) {
  return apiFetch(`/purchase-history-items/${orderId}`);
}

