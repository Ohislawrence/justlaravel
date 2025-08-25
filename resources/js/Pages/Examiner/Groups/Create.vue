<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';

const props = defineProps({
  organization: Object,
});

const form = useForm({
  name: '',
  description: '',
  organization_id: props.organization.id,
});

const submit = () => {
  form.post(route('examiner.groups.store', props.organization.slug));
};
</script>

<template>
  <AppLayout title="Create New Group">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Create New Group in {{ organization.name }}
        </h2>
        <Link 
          :href="route('examiner.groups.index', organization.slug)"
          class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition-colors duration-200"
        >
          <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Back to Groups
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
          <div class="p-6 bg-white border-b border-gray-200">
            <form @submit.prevent="submit">
              <div class="grid grid-cols-1 gap-6">
                <!-- Group Name -->
                <div>
                  <InputLabel for="name" value="Group Name *" />
                  <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full focus:ring-green-500 focus:border-green-500 transition-colors duration-200"
                    required
                    autofocus
                  />
                  <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <!-- Description -->
                <div>
                  <InputLabel for="description" value="Description" />
                  <Textarea
                    id="description"
                    v-model="form.description"
                    class="mt-1 block w-full focus:ring-green-500 focus:border-green-500 transition-colors duration-200"
                    rows="3"
                  />
                  <InputError class="mt-2" :message="form.errors.description" />
                </div>

                <!-- Organization ID (hidden) -->
                <input type="hidden" v-model="form.organization_id" />

                <!-- Submit Button -->
                <div class="flex items-center justify-end mt-6">
                  <button
                    type="submit"
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition-colors duration-200 flex items-center"
                    :class="{ 'opacity-25 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                  >
                    <svg v-if="!form.processing" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span v-if="form.processing" class="flex items-center">
                      <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      Creating...
                    </span>
                    <span v-else>Create Group</span>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Enhanced green-themed styling */
.bg-green-600 {
    background-color: #10B981;
}

.bg-green-700:hover {
    background-color: #059669;
}

.focus\:ring-green-500:focus {
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.5);
}

/* Border styling */
.border-gray-100 {
    border-color: #f3f4f6;
}

/* Transition effects */
.transition-colors {
    transition: all 0.2s ease;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .grid {
        grid-template-columns: 1fr;
    }
    
    .gap-6 > *:not(:last-child) {
        margin-bottom: 1.5rem;
    }
}
</style>