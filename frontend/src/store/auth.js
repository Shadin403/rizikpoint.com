import { reactive, computed } from "vue";
import { apiLogin, apiSignup } from "@/lib/api";
import { toast } from "@/lib/toast";

// Helper to safely parse JSON
const parseJSON = (str) => {
  try {
    return str ? JSON.parse(str) : null;
  } catch {
    return null;
  }
};

const state = reactive({
  user: parseJSON(localStorage.getItem("user")),
  token: localStorage.getItem("token") || null,
  isOpen: false,
  mode: "login", // 'login' | 'register'
  loading: false,
  error: null,
});

const isAuthenticated = computed(() => !!state.token);
const currentUser = computed(() => state.user);
const isOpen = computed(() => state.isOpen);
const mode = computed(() => state.mode);
const loading = computed(() => state.loading);
const error = computed(() => state.error);

function openAuth(targetMode = "login") {
  state.mode = targetMode;
  state.error = null;
  state.isOpen = true;
}

function closeAuth() {
  state.isOpen = false;
  state.error = null;
}

function setMode(targetMode) {
  state.mode = targetMode;
  state.error = null;
}

async function login(email, password) {
  state.loading = true;
  state.error = null;
  try {
    const data = await apiLogin(email, password);
    state.token = data.access_token;
    state.user = data.user;
    localStorage.setItem("token", data.access_token);
    localStorage.setItem("user", JSON.stringify(data.user));
    
    toast({
      title: "সফল লগইন",
      description: "আপনি সফলভাবে লগইন করেছেন!",
    });
    
    closeAuth();
  } catch (err) {
    state.error = err.message || "লগইন করতে ব্যর্থ হয়েছে";
  } finally {
    state.loading = false;
  }
}

async function register(name, emailOrPhone, password) {
  state.loading = true;
  state.error = null;
  
  // Basic validation
  if (!name.trim() || !emailOrPhone.trim() || !password) {
    state.error = "অনুগ্রহ করে সকল ফিল্ড পূরণ করুন";
    state.loading = false;
    return;
  }

  // Determine signup type
  const isEmail = emailOrPhone.includes("@");
  const registerBy = isEmail ? "email" : "phone";

  try {
    await apiSignup(name, emailOrPhone, password, registerBy);
    
    // Auto-login after registration
    await login(emailOrPhone, password);
  } catch (err) {
    state.error = err.message || "রেজিস্ট্রেশন করতে ব্যর্থ হয়েছে";
  } finally {
    state.loading = false;
  }
}

function logout() {
  state.user = null;
  state.token = null;
  localStorage.removeItem("token");
  localStorage.removeItem("user");
  
  toast({
    title: "লগআউট",
    description: "আপনি সফলভাবে লগআউট করেছেন!",
  });
}

export function useAuth() {
  return {
    user: currentUser,
    token: computed(() => state.token),
    isAuthenticated,
    isOpen,
    mode,
    loading,
    error,
    openAuth,
    closeAuth,
    setMode,
    login,
    register,
    logout,
  };
}
