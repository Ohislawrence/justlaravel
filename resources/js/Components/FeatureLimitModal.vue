<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  modelValue: { type: Boolean, required: true },
  featureName: { type: String, default: '' },
  message: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(props.modelValue);

watch(() => props.modelValue, val => isOpen.value = val);

const close = () => {
  isOpen.value = false;
  emit('update:modelValue', false);
};
</script>

<template>
  <transition name="fade">
    <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
      <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
        <h2 class="text-xl font-bold mb-4">{{ featureName }} Unavailable</h2>
        <p class="text-gray-700 mb-6">
          {{ message }}
        </p>
        <div class="flex justify-end">
          <button @click="close" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            OK
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
