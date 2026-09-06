<script setup>
import { ref, onMounted } from "vue";
import { Plus, Pencil, Trash2, ToggleLeft, ToggleRight, Link, Image as ImageIcon, X, Check, Loader } from "@lucide/vue";
import {
  fetchAdminSliders,
  createSlider,
  updateSlider,
  deleteSlider,
  toggleSlider,
} from "@/lib/api";
import { toast } from "@/lib/toast";
import { usePageTitle } from "@/composables/usePageTitle";
import { pageTitles } from "@/lib/pageTitles";
usePageTitle(pageTitles.AdminSliders);
function toastSuccess(msg) { toast({ title: msg, variant: 'success' }); }
function toastError(msg) { toast({ title: msg, variant: 'destructive' }); }


// ── State ────────────────────────────────────────────────────────────────────
const sliders   = ref([]);
const isLoading = ref(false);
const isSaving  = ref(false);

// Modal state
const showModal  = ref(false);
const editTarget = ref(null); // null = create, slider object = edit
const form = ref({ title: "", link: "/products-list", published: 1 });
const photoFile   = ref(null);
const photoPreview = ref(null);
const confirmDeleteId = ref(null);

// ── Load ─────────────────────────────────────────────────────────────────────
async function load() {
  isLoading.value = true;
  try {
    sliders.value = await fetchAdminSliders();
  } finally {
    isLoading.value = false;
  }
}
onMounted(load);

// ── Modal helpers ─────────────────────────────────────────────────────────────
function openCreate() {
  editTarget.value = null;
  form.value = { title: "", link: "/products-list", published: 1 };
  photoFile.value = null;
  photoPreview.value = null;
  showModal.value = true;
}

function openEdit(slider) {
  editTarget.value = slider;
  form.value = { title: slider.title, link: slider.link, published: slider.published };
  photoFile.value = null;
  photoPreview.value = slider.image || null;
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  editTarget.value = null;
  photoFile.value = null;
  photoPreview.value = null;
}

function onPhotoChange(e) {
  const file = e.target.files[0];
  if (!file) return;
  photoFile.value = file;
  photoPreview.value = URL.createObjectURL(file);
}

function clearPhoto() {
  photoFile.value = null;
  photoPreview.value = editTarget.value?.image || null;
}

// ── Save (Create / Update) ────────────────────────────────────────────────────
async function save() {
  isSaving.value = true;
  try {
    const fd = new FormData();
    fd.append("title",     form.value.title);
    fd.append("link",      form.value.link);
    fd.append("published", form.value.published);
    if (photoFile.value) fd.append("photo", photoFile.value);

    if (editTarget.value) {
      await updateSlider(editTarget.value.id, fd);
      toastSuccess("Slider updated!");



    } else {
      await createSlider(fd);
      toastSuccess("Slider created!");

    }
    closeModal();
    await load();
  } catch (err) {
    toastError(err.message || "Save failed.");

  } finally {
    isSaving.value = false;
  }
}

// ── Delete ────────────────────────────────────────────────────────────────────
async function confirmDelete(id) {
  try {
    await deleteSlider(id);
    toastSuccess("Slider deleted.");

    confirmDeleteId.value = null;
    await load();
  } catch (err) {
    toastError(err.message || "Delete failed.");

  }
}

// ── Toggle ────────────────────────────────────────────────────────────────────
async function toggle(slider) {
  try {
    const res = await toggleSlider(slider.id);
    slider.published = res.published;
    toastSuccess(res.message);

  } catch (err) {
    toastError(err.message || "Toggle failed.");

  }
}
</script>

<template>
  <div class="min-h-screen bg-gray-50 p-4 md:p-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
          <ImageIcon class="w-6 h-6 text-indigo-500" /> Sliders
        </h1>
        <p class="text-sm text-gray-500 mt-0.5">Manage hero banner sliders displayed on the homepage.</p>
      </div>
      <button
        @click="openCreate"
        class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl font-semibold shadow transition-all text-sm"
      >
        <Plus class="w-4 h-4" /> Add Slider
      </button>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="flex justify-center py-24">
      <Loader class="w-8 h-8 text-indigo-400 animate-spin" />
    </div>

    <!-- Empty -->
    <div
      v-else-if="sliders.length === 0"
      class="text-center py-24 bg-white rounded-2xl border border-dashed border-gray-200 text-gray-400"
    >
      <ImageIcon class="w-12 h-12 mx-auto mb-3 opacity-30" />
      <p class="font-medium">No sliders yet.</p>
      <button @click="openCreate" class="mt-3 text-indigo-600 text-sm underline underline-offset-2">Create your first slider</button>
    </div>

    <!-- Slider grid -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
      <div
        v-for="slider in sliders"
        :key="slider.id"
        class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col group hover:shadow-md transition-all"
      >
        <!-- Image -->
        <div class="relative h-40 bg-gray-100">
          <img
            v-if="slider.image"
            :src="slider.image"
            :alt="slider.title"
            class="w-full h-full object-cover"
          />
          <div v-else class="w-full h-full flex items-center justify-center">
            <ImageIcon class="w-10 h-10 text-gray-300" />
          </div>

          <!-- Status badge -->
          <span
            :class="slider.published ? 'bg-green-500' : 'bg-gray-400'"
            class="absolute top-2 right-2 text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider"
          >
            {{ slider.published ? "Live" : "Draft" }}
          </span>
        </div>

        <!-- Body -->
        <div class="p-3 flex-1 flex flex-col gap-1">
          <p class="font-semibold text-gray-800 text-sm line-clamp-1">{{ slider.title || "(No title)" }}</p>
          <a
            :href="slider.link"
            target="_blank"
            class="flex items-center gap-1 text-xs text-indigo-500 hover:underline truncate"
          >
            <Link class="w-3 h-3 shrink-0" /> {{ slider.link }}
          </a>
        </div>

        <!-- Actions -->
        <div class="px-3 pb-3 flex items-center gap-2">
          <!-- Toggle published -->
          <button
            @click="toggle(slider)"
            :title="slider.published ? 'Unpublish' : 'Publish'"
            class="flex items-center gap-1 text-xs font-medium transition-colors"
            :class="slider.published ? 'text-green-600 hover:text-green-700' : 'text-gray-400 hover:text-gray-600'"
          >
            <ToggleRight v-if="slider.published" class="w-5 h-5" />
            <ToggleLeft v-else class="w-5 h-5" />
            {{ slider.published ? "Published" : "Draft" }}
          </button>

          <div class="ml-auto flex items-center gap-1.5">
            <button
              @click="openEdit(slider)"
              class="p-1.5 rounded-lg text-gray-500 hover:bg-indigo-50 hover:text-indigo-600 transition-colors"
              title="Edit"
            >
              <Pencil class="w-4 h-4" />
            </button>
            <button
              @click="confirmDeleteId = slider.id"
              class="p-1.5 rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600 transition-colors"
              title="Delete"
            >
              <Trash2 class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ── Create / Edit Modal ─────────────────────────────────────────────── -->
    <Teleport to="body">
      <transition name="modal-fade">
        <div
          v-if="showModal"
          class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
          @click.self="closeModal"
        >
          <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <!-- Modal header -->
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
              <h2 class="font-bold text-gray-800">{{ editTarget ? "Edit Slider" : "Add Slider" }}</h2>
              <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                <X class="w-5 h-5" />
              </button>
            </div>

            <!-- Modal body -->
            <div class="p-5 space-y-4">
              <!-- Image upload -->
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Banner Image</label>
                <div class="relative rounded-xl overflow-hidden bg-gray-100 h-40 border-2 border-dashed border-gray-200 hover:border-indigo-400 transition-colors">
                  <img
                    v-if="photoPreview"
                    :src="photoPreview"
                    class="w-full h-full object-cover"
                    alt="preview"
                  />
                  <div v-else class="w-full h-full flex flex-col items-center justify-center text-gray-400 gap-1">
                    <ImageIcon class="w-8 h-8 opacity-50" />
                    <span class="text-xs">Click to upload</span>
                  </div>
                  <input
                    type="file"
                    accept="image/*"
                    class="absolute inset-0 opacity-0 cursor-pointer"
                    @change="onPhotoChange"
                  />
                  <button
                    v-if="photoPreview"
                    @click.stop="clearPhoto"
                    class="absolute top-2 right-2 bg-white/80 hover:bg-white text-gray-600 rounded-full p-1 shadow transition-colors"
                  >
                    <X class="w-4 h-4" />
                  </button>
                </div>
                <p class="text-[10px] text-gray-400 mt-1">Recommended: 1200×400px, max 4MB</p>
              </div>

              <!-- Title -->
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Title</label>
                <input
                  v-model="form.title"
                  type="text"
                  placeholder="e.g. Summer Sale 2026"
                  class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition"
                />
              </div>

              <!-- Link -->
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Link URL</label>
                <input
                  v-model="form.link"
                  type="text"
                  placeholder="/products-list"
                  class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition"
                />
              </div>

              <!-- Published toggle -->
              <div class="flex items-center justify-between bg-gray-50 rounded-xl px-3 py-2.5">
                <span class="text-sm font-medium text-gray-700">Publish immediately</span>
                <button
                  @click="form.published = form.published ? 0 : 1"
                  :class="form.published ? 'bg-green-500' : 'bg-gray-300'"
                  class="relative w-10 h-5 rounded-full transition-colors"
                >
                  <span
                    :class="form.published ? 'translate-x-5' : 'translate-x-0.5'"
                    class="absolute top-0.5 left-0 w-4 h-4 bg-white rounded-full shadow transition-transform"
                  />
                </button>
              </div>
            </div>

            <!-- Modal footer -->
            <div class="px-5 pb-5 flex gap-3 justify-end">
              <button
                @click="closeModal"
                class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors"
              >
                Cancel
              </button>
              <button
                @click="save"
                :disabled="isSaving"
                class="flex items-center gap-2 px-5 py-2 text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow transition-all disabled:opacity-60"
              >
                <Loader v-if="isSaving" class="w-4 h-4 animate-spin" />
                <Check v-else class="w-4 h-4" />
                {{ isSaving ? "Saving…" : (editTarget ? "Update" : "Create") }}
              </button>
            </div>
          </div>
        </div>
      </transition>
    </Teleport>

    <!-- ── Delete Confirm Dialog ───────────────────────────────────────────── -->
    <Teleport to="body">
      <transition name="modal-fade">
        <div
          v-if="confirmDeleteId !== null"
          class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
          @click.self="confirmDeleteId = null"
        >
          <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
            <div class="w-12 h-12 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-3">
              <Trash2 class="w-6 h-6" />
            </div>
            <h3 class="font-bold text-gray-800 mb-1">Delete Slider?</h3>
            <p class="text-sm text-gray-500 mb-5">This action cannot be undone. The image will also be deleted.</p>
            <div class="flex gap-3 justify-center">
              <button
                @click="confirmDeleteId = null"
                class="px-5 py-2 text-sm border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors"
              >
                Cancel
              </button>
              <button
                @click="confirmDelete(confirmDeleteId)"
                class="px-5 py-2 text-sm font-semibold bg-red-600 hover:bg-red-700 text-white rounded-xl shadow transition-all"
              >
                Delete
              </button>
            </div>
          </div>
        </div>
      </transition>
    </Teleport>
  </div>
</template>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
</style>

