// Unsplash grocery photos used only when the admin enables dummy product images.
// Direct image URLs keep the fallback lightweight while giving each category a
// natural product photo instead of the old local SVG artwork.
const DUMMY_IMAGES = {
  vegetables: 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=800&q=82',
  fish: 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?auto=format&fit=crop&w=800&q=82',
  meat: 'https://images.unsplash.com/photo-1604503468506-a8da13d82791?auto=format&fit=crop&w=800&q=82',
  fruit: 'https://images.unsplash.com/photo-1619566636858-adf3ef46400b?auto=format&fit=crop&w=800&q=82',
  spices: 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=800&q=82',
  grocery: 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=800&q=82',
};

export function dummyImagesEnabled(getSetting) {
  const value = getSetting('dummy_product_images');
  // If the older API does not expose this setting yet (or CORS blocks it),
  // keep dummy photos enabled by default. A real "0" from the API still wins.
  if (value === null || value === undefined || value === '') {
    const fallback = import.meta.env.VITE_DUMMY_PRODUCT_IMAGES_FALLBACK;
    return fallback === undefined || fallback === '' || fallback === '1';
  }
  return ['1', 'true', 'on', 'yes'].includes(String(value).toLowerCase());
}

function titleFor(product) {
  return String(product?.title ?? '').toLowerCase();
}

function categoryFor(product) {
  return `${product?.categoryName ?? ''} ${product?.category ?? ''}`.toLowerCase();
}

export function dummyProductImage(product) {
  const title = titleFor(product);
  const category = categoryFor(product);
  const isFish = /(fish|মাছ|prawn|shrimp|চিংড়ি|hilsa|ইলিশ)/i;
  const isMeat = /(meat|মাংস|beef|গরু|mutton|খাসি|chicken|মুরগি)/i;
  const isFruit = /(fruit|ফল|apple|আপেল|banana|কলা|orange|কমলা|mango|আম)/i;
  const isSpice = /(spice|মসলা|masala|মরিচ|turmeric|হলুদ|cumin|জিরা)/i;
  const isVegetable = /(vegetable|সবজি|potato|আলু|onion|পেঁয়াজ|tomato|টমেটো|carrot|গাজর|leaf|শাক)/i;

  // Prefer the product title so a broad category such as "Fruits & Vegetables"
  // does not make every item use the same photo.
  if (isFish.test(title) || isFish.test(category)) return DUMMY_IMAGES.fish;
  if (isMeat.test(title) || isMeat.test(category)) return DUMMY_IMAGES.meat;
  if (isSpice.test(title) || isSpice.test(category)) return DUMMY_IMAGES.spices;
  if (isFruit.test(title)) return DUMMY_IMAGES.fruit;
  if (isVegetable.test(title)) return DUMMY_IMAGES.vegetables;
  if (isFruit.test(category)) return DUMMY_IMAGES.fruit;
  if (isVegetable.test(category)) return DUMMY_IMAGES.vegetables;
  return DUMMY_IMAGES.grocery;
}
