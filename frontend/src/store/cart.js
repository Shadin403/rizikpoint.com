import { reactive, computed } from "vue";

const CART_STORAGE_KEY = "dealpabo_cart_items";

// Helper to load cart items from localStorage safely
function loadCartItems() {
  try {
    const saved = localStorage.getItem(CART_STORAGE_KEY);
    return saved ? JSON.parse(saved) : [];
  } catch (e) {
    console.error("Failed to parse cart items from localStorage:", e);
    return [];
  }
}

// Helper to save cart items to localStorage safely
function saveCartItems() {
  try {
    localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(state.items));
  } catch (e) {
    console.error("Failed to save cart items to localStorage:", e);
  }
}

const state = reactive({
  items: loadCartItems(),
  isOpen: false,
});

const totalItems = computed(() => {
  return state.items.reduce((sum, item) => sum + item.quantity, 0);
});

const subtotal = computed(() => {
  return state.items.reduce((sum, item) => sum + item.price * item.quantity, 0);
});

function addItem(item) {
  const variantStr = item.variantStr || '';
  const color = item.color || '';
  const compositeId = `${item.id}-${variantStr}-${color}`;

  const existing = state.items.find((i) => (i.compositeId || i.id) === compositeId);
  const qtyToAdd = item.quantity || 1;
  if (existing) {
    existing.quantity += qtyToAdd;
  } else {
    state.items.push({
      compositeId,
      id: item.id,
      title: item.title,
      imageUrl: item.imageUrl ?? null,
      price: item.price,
      variantStr: variantStr,
      color: color,
      quantity: qtyToAdd,
    });
  }
  saveCartItems();
}

function removeItem(compositeId) {
  state.items = state.items.filter((i) => (i.compositeId || i.id) !== compositeId);
  saveCartItems();
}

function updateQuantity(compositeId, quantity) {
  if (quantity <= 0) {
    removeItem(compositeId);
    return;
  }
  const item = state.items.find((i) => (i.compositeId || i.id) === compositeId);
  if (item) {
    item.quantity = quantity;
    saveCartItems();
  }
}

function clearCart() {
  state.items = [];
  saveCartItems();
}

function openCart() {
  state.isOpen = true;
}

function closeCart() {
  state.isOpen = false;
}

export function useCart() {
  return {
    items: computed(() => state.items),
    isOpen: computed(() => state.isOpen),
    totalItems,
    subtotal,
    addItem,
    removeItem,
    updateQuantity,
    clearCart,
    openCart,
    closeCart,
  };
}
