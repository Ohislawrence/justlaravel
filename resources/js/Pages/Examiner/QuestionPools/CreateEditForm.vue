<template>
    <AppLayout :title="editing ? 'Edit Question Pool' : 'Create Question Pool'">
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ editing ? 'Edit' : 'Create' }} Question Pool
        </h2>
      </template>
  
      <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
              <form @submit.prevent="submitForm">
                <div class="grid grid-cols-1 gap-6">
                  <!-- Pool Name -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Pool Name</label>
                    <input 
                      v-model="form.name" 
                      type="text" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                      required
                    >
                    <InputError :message="form.errors.name" class="mt-2" />
                  </div>
  
                  <!-- Associated Quiz -->
                  <div v-if="quizzes.length > 0">
                    <label class="block text-sm font-medium text-gray-700">
                      Associated Quiz (Leave blank for global pool)
                    </label>
                    <select 
                      v-model="form.quiz_id" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                      <option :value="null">Global Pool</option>
                      <option 
                        v-for="quiz in quizzes" 
                        :key="quiz.id" 
                        :value="quiz.id"
                      >
                        {{ quiz.title }}
                      </option>
                    </select>
                    <InputError :message="form.errors.quiz_id" class="mt-2" />
                  </div>
  
                  <!-- Description -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Description (optional)</label>
                    <textarea 
                      v-model="form.description" 
                      rows="3" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    ></textarea>
                    <InputError :message="form.errors.description" class="mt-2" />
                  </div>
  
                  <!-- Questions to Show -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700">
                      Questions to Show (Leave blank to show all)
                    </label>
                    <input 
                      v-model="form.questions_to_show" 
                      type="number" 
                      min="1" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    <InputError :message="form.errors.questions_to_show" class="mt-2" />
                  </div>
  
                  <!-- Form Actions -->
                  <div class="flex justify-end space-x-3">
                    <button 
                      type="button" 
                      @click="cancel" 
                      class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                      Cancel
                    </button>
                    <button 
                      type="submit" 
                      class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition"
                      :disabled="form.processing"
                    >
                      {{ editing ? 'Update' : 'Create' }} Pool
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