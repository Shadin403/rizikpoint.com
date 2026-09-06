<script setup>
import { computed } from "vue";
import {
  Coffee, Utensils, Pizza,
  Shirt, Watch, Glasses, Crown,
  Laptop, Smartphone, Monitor, Camera, Tv, Headphones, MousePointer2, Speaker, Cpu, Gamepad2,
  Sparkles, Flower2, Scissors, Heart,
  Hotel, BedDouble, Bath,
  Plane, Car, Bike, Map, Tent,
  GraduationCap, BookOpen, Pen,
  HeartPulse, Stethoscope, Pill, Dumbbell,
  ShoppingCart, Apple, Milk, Egg, Carrot, Wheat,
  House, Sofa, Lamp, Frame, PaintBucket,
  Baby, Puzzle,
  Footprints, ShoppingBag,
  Wrench, Drill, Plug,
  PawPrint, Fish,
  Package, Gift,
  Terminal, Code, Bot,
  Grid3x3
} from "@lucide/vue";

const props = defineProps({
  name: { type: String, required: true },
  icon: { type: String, default: null },
  className: { type: String, default: "" }
});

const ICON_BY_NAME = {
  coffee: Coffee, utensils: Utensils, pizza: Pizza,
  shirt: Shirt, watch: Watch, glasses: Glasses, crown: Crown,
  laptop: Laptop, smartphone: Smartphone, monitor: Monitor, camera: Camera, tv: Tv,
  headphones: Headphones, mouse: MousePointer2, speaker: Speaker, cpu: Cpu, gamepad: Gamepad2,
  sparkles: Sparkles, flower: Flower2, scissors: Scissors, heart: Heart,
  hotel: Hotel, bed: BedDouble, bath: Bath,
  plane: Plane, car: Car, bike: Bike, map: Map, tent: Tent,
  graduation: GraduationCap, book: BookOpen, pen: Pen,
  health: HeartPulse, stethoscope: Stethoscope, pill: Pill, dumbbell: Dumbbell,
  cart: ShoppingCart, apple: Apple, milk: Milk, egg: Egg, carrot: Carrot, wheat: Wheat,
  home: House, sofa: Sofa, lamp: Lamp, frame: Frame, paint: PaintBucket,
  baby: Baby, puzzle: Puzzle,
  footprints: Footprints, bag: ShoppingBag,
  wrench: Wrench, drill: Drill, plug: Plug,
  paw: PawPrint, fish: Fish,
  package: Package, gift: Gift,
  terminal: Terminal, code: Code, bot: Bot,
  grid: Grid3x3,
};

const BACKEND_ORIGIN = "http://127.0.0.1:8000";

function isImagePath(value) {
  if (!value) return false;
  const v = String(value).trim();
  if (v.startsWith("http://") || v.startsWith("https://") || v.startsWith("data:")) return true;
  if (v.startsWith("/") || v.startsWith("uploads/")) return true;
  if (/\.(svg|png|jpe?g|webp|gif|ico|bmp)$/i.test(v)) return true;
  return false;
}

function resolveImageUrl(value) {
  const v = String(value).trim();
  if (v.startsWith("http://") || v.startsWith("https://")) return v;
  if (v.startsWith("data:")) return v;
  const clean = v.startsWith("/") ? v.slice(1) : v;
  return `${BACKEND_ORIGIN}/${clean}`;
}

function matchKeyword(normalized, keywords) {
  return keywords.some(k => normalized.includes(k));
}

const imageIcon = computed(() => {
  if (!props.icon) return null;
  if (!isImagePath(props.icon)) return null;
  return resolveImageUrl(props.icon);
});

const resolvedIcon = computed(() => {
  if (imageIcon.value) return null;
  if (props.icon) {
    const key = String(props.icon).toLowerCase().trim();
    if (ICON_BY_NAME[key]) return ICON_BY_NAME[key];
  }
  const normalized = (props.name || "").toLowerCase();
  if (matchKeyword(normalized, ["food", "dining", "restaurant", "kitchen"])) return Utensils;
  if (matchKeyword(normalized, ["coffee", "cafe", "tea"])) return Coffee;
  if (matchKeyword(normalized, ["pizza", "burger", "fast food"])) return Pizza;
  if (matchKeyword(normalized, ["women", "fashion", "clothing", "shirt", "t-shirt", "apparel"])) return Shirt;
  if (matchKeyword(normalized, ["men", "gents", "male"])) return Shirt;
  if (matchKeyword(normalized, ["shoe", "footwear", "sandal", "loafer"])) return Footprints;
  if (matchKeyword(normalized, ["watch", "wrist"])) return Watch;
  if (matchKeyword(normalized, ["glass", "sunglass", "spectacle"])) return Glasses;
  if (matchKeyword(normalized, ["jewel", "jewellery", "jewelry", "ring", "necklace"])) return Crown;
  if (matchKeyword(normalized, ["phone", "smartphone", "mobile"])) return Smartphone;
  if (matchKeyword(normalized, ["computer", "laptop", "notebook", "pc"])) return Laptop;
  if (matchKeyword(normalized, ["monitor", "display", "screen"])) return Monitor;
  if (matchKeyword(normalized, ["dslr", "camera", "cctv"])) return Camera;
  if (matchKeyword(normalized, ["tv", "television"])) return Tv;
  if (matchKeyword(normalized, ["headphone", "earphone", "earbud", "headset"])) return Headphones;
  if (matchKeyword(normalized, ["speaker", "audio", "sound"])) return Speaker;
  if (matchKeyword(normalized, ["electronic", "tech", "gadget", "accessor"])) return Cpu;
  if (matchKeyword(normalized, ["gaming", "console", "playstation", "xbox", "nintendo"])) return Gamepad2;
  if (matchKeyword(normalized, ["beauty", "makeup", "cosmetic", "skincare"])) return Sparkles;
  if (matchKeyword(normalized, ["flower", "bouquet"])) return Flower2;
  if (matchKeyword(normalized, ["salon", "hair", "barber", "scissors"])) return Scissors;
  if (matchKeyword(normalized, ["perfume", "fragrance"])) return Heart;
  if (matchKeyword(normalized, ["hotel", "resort", "hostel", "lodging"])) return Hotel;
  if (matchKeyword(normalized, ["bed", "mattress", "bedding"])) return BedDouble;
  if (matchKeyword(normalized, ["bath", "shower", "toilet"])) return Bath;
  if (matchKeyword(normalized, ["travel", "flight", "tour", "ticket", "air"])) return Plane;
  if (matchKeyword(normalized, ["car", "vehicle", "auto"])) return Car;
  if (matchKeyword(normalized, ["bike", "bicycle", "cycle"])) return Bike;
  if (matchKeyword(normalized, ["map", "gps", "direction"])) return Map;
  if (matchKeyword(normalized, ["tent", "camp", "hike"])) return Tent;
  if (matchKeyword(normalized, ["education", "course", "tuition", "class"])) return GraduationCap;
  if (matchKeyword(normalized, ["book", "novel", "ebook"])) return BookOpen;
  if (matchKeyword(normalized, ["pen", "stationery", "pencil"])) return Pen;
  if (matchKeyword(normalized, ["health", "fitness", "gym", "yoga"])) return Dumbbell;
  if (matchKeyword(normalized, ["medical", "medicine", "pharmacy", "doctor"])) return Stethoscope;
  if (matchKeyword(normalized, ["supplement", "vitamin", "pill"])) return Pill;
  if (matchKeyword(normalized, ["grocery", "supermarket", "mart", "daily"])) return ShoppingCart;
  if (matchKeyword(normalized, ["fruit", "apple", "banana", "mango"])) return Apple;
  if (matchKeyword(normalized, ["milk", "dairy", "yogurt", "cheese"])) return Milk;
  if (matchKeyword(normalized, ["egg"])) return Egg;
  if (matchKeyword(normalized, ["vegetable", "carrot", "onion", "potato"])) return Carrot;
  if (matchKeyword(normalized, ["rice", "grain", "wheat", "flour"])) return Wheat;
  if (matchKeyword(normalized, ["home decor", "decoration", "showpiece"])) return Frame;
  if (matchKeyword(normalized, ["sofa", "furniture", "chair", "table"])) return Sofa;
  if (matchKeyword(normalized, ["lamp", "light", "bulb"])) return Lamp;
  if (matchKeyword(normalized, ["paint", "wall", "coating"])) return PaintBucket;
  if (matchKeyword(normalized, ["home", "house", "kitchen"])) return House;
  if (matchKeyword(normalized, ["kid", "baby", "infant", "toddler"])) return Baby;
  if (matchKeyword(normalized, ["toy", "puzzle", "lego"])) return Puzzle;
  if (matchKeyword(normalized, ["sport", "outdoor", "cricket", "football", "badminton"])) return Dumbbell;
  if (matchKeyword(normalized, ["bag", "backpack", "luggage", "handbag"])) return ShoppingBag;
  if (matchKeyword(normalized, ["tool", "hardware", "drill", "wrench"])) return Wrench;
  if (matchKeyword(normalized, ["appliance", "fridge", "refrigerator", "washing", "ac", "air conditioner"])) return Tv;
  if (matchKeyword(normalized, ["pet", "dog", "cat", "aquarium"])) return PawPrint;
  if (matchKeyword(normalized, ["fish", "fishing"])) return Fish;
  if (matchKeyword(normalized, ["gift", "present"])) return Gift;
  if (matchKeyword(normalized, ["software", "app", "saas", "subscription"])) return Terminal;
  return Grid3x3;
});
</script>

<template>
  <img v-if="imageIcon" :src="imageIcon" :alt="name" :class="className" />
  <component v-else-if="resolvedIcon" :is="resolvedIcon" :class="className" />
  <Grid3x3 v-else :class="className" />
</template>




