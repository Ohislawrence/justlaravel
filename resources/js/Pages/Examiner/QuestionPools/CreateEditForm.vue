<template>
  <AppLayout :title="editing ? 'Edit Question Pool' : 'Create Question Pool'">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ editing ? 'Edit' : 'Create' }} Question Pool
      </h2>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
          <div class="p-6 bg-white border-b border-gray-200">
            <form @submit.prevent="submitForm">
              <div class="grid grid-cols-1 gap-6">
                <!-- Pool Name -->
                <div>
                  <label class="block text-sm font-medium text-gray-700">Pool Name</label>
                  <input 
                    v-model="form.name" 
                    type="text" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors duration-200"
                    required
                  >
                  <InputError :message="form.errors.name" class="mt-2" />
                </div>

                

                <!-- Description -->
                <div>
                  <label class="block text-sm font-medium text-gray-700">Description (optional)</label>
                  <textarea 
                    v-model="form.description" 
                    rows="3" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors duration-200"
                  ></textarea>
                  <InputError :message="form.errors.description" class="mt-2" />
                </div>

                

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-4">
                  <button 
                    type="button" 
                    @click="cancel" 
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"
                  >
                    Cancel
                  </button>
                  <button 
                    type="submit" 
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition-colors duration-200 flex items-center"
                    :disabled="form.processing"
                  >
                    <svg v-if="!form.processing" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span v-if="form.processing" class="flex items-center">
                      <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      {{ editing ? 'Updating...' : 'Creating...' }}
                    </span>
                    <span v-else>{{ editing ? 'Update' : 'Create' }} Pool</span>
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

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import { useForm, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
  quizzes: Array,
  pool: Object,
});

const editing = computed(() => !!props.pool);

const form = useForm({
  name: props.pool?.name || '',
  quiz_id: props.pool?.quiz_id || null,
  description: props.pool?.description || '',
  questions_to_show: props.pool?.questions_to_show || null,
});

const submitForm = () => {
  if (editing.value) {
    form.put(route('examiner.question-pools.update', props.pool.id));
  } else {
    form.post(route('examiner.question-pools.store'));
  }
};

const cancel = () => {
  router.visit(route('examiner.question-pools.index'));
};
</script>

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