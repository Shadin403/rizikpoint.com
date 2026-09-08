// Local grocery artwork used only when the admin enables dummy product images.
// Keeping these paths local means cards still look good without an image CDN.
const DUMMY_IMAGES = {
  vegetables: '/dummy-images/vegetables.svg',
  fish: '/dummy-images/fish.svg',
  meat: '/dummy-images/meat.svg',
  fruit: '/dummy-images/fruit.svg',
  spices: '/dummy-images/spices.svg',
  grocery: '/dummy-images/grocery.svg',
};

export function dummyImagesEnabled(getSetting) {
  const value = getSetting('dummy_product_images');
  // If production API settings are temporarily blocked by CORS, use the
  // explicit production fallback. A real "0" from the API still wins.
  if (value === null || value === undefined || value === '') {
    return import.meta.env.VITE_DUMMY_PRODUCT_IMAGES_FALLBACK === '1';
  }
  return ['1', 'true', 'on', 'yes'].includes(String(value).toLowerCase());
}

function textFor(product) {
  return `${product?.title ?? ''} ${product?.categoryName ?? ''} ${product?.category ?? ''}`.toLowerCase();
}

export function dummyProductImage(product) {
  const text = textFor(product);
  if (/(fish|মাছ|prawn|shrimp|চিংড়ি|hilsa|ইলিশ)/i.test(text)) return DUMMY_IMAGES.fish;
  if (/(meat|মাংস|beef|গরু|mutton|খাসি|chicken|মুরগি)/i.test(text)) return DUMMY_IMAGES.meat;
  if (/(fruit|ফল|apple|আপেল|banana|কলা|orange|কমলা|mango|আম)/i.test(text)) return DUMMY_IMAGES.fruit;
  if (/(spice|মসলা|masala|মরিচ|turmeric|হলুদ|cumin|জিরা)/i.test(text)) return DUMMY_IMAGES.spices;
  if (/(vegetable|সবজি|potato|আলু|onion|পেঁয়াজ|tomato|টমেটো|carrot|গাজর|leaf|শাক)/i.test(text)) return DUMMY_IMAGES.vegetables;
  return DUMMY_IMAGES.grocery;
}
