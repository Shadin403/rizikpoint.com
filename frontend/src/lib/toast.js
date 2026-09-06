import { reactive } from "vue";

export const toastState = reactive({
  visible: false,
  title: "",
  description: "",
  type: "success" // 'success' | 'destructive'
});

let toastTimeout = null;

export function toast({ title, description, variant = "success" }) {
  if (toastTimeout) clearTimeout(toastTimeout);
  
  toastState.title = title;
  toastState.description = description;
  toastState.type = variant;
  toastState.visible = true;

  toastTimeout = setTimeout(() => {
    toastState.visible = false;
  }, 3000);
}
