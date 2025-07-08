<template>
    <div class="space-y-6">
      <!-- Word Count Settings -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Minimum Word Count</label>
          <input
            v-model.number="modelValue.settings.min_words"
            type="number"
            min="0"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Maximum Word Count</label>
          <input
            v-model.number="modelValue.settings.max_words"
            type="number"
            min="0"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
          >
        </div>
      </div>
  
      <!-- Formatting Options -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Formatting Requirements</label>
        <div class="mt-2 space-y-2">
          <label class="inline-flex items-center">
            <input
              v-model="modelValue.settings.allow_rich_text"
              type="checkbox"
              class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
            <span class="ml-2 text-sm text-gray-600">Allow rich text formatting</span>
          </label>
          <label class="inline-flex items-center">
            <input
              v-model="modelValue.settings.require_paragraphs"
              type="checkbox"
              class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
            <span class="ml-2 text-sm text-gray-600">Require multiple paragraphs</span>
          </label>
        </div>
      </div>
  
      <!-- Sample Answer -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Sample Answer (Optional)</label>
        <textarea
          v-model="modelValue.settings.sample_answer"
          rows="4"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
          placeholder="Provide an example of a good answer"
        ></textarea>
      </div>
  
      <!-- Grading Rubric -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Grading Rubric</label>
        <div class="mt-2 space-y-3">
          <div v-for="(criterion, index) in modelValue.settings.rubric" :key="index" class="flex items-start">
            <div class="flex items-center h-5 mt-1.5">
              <input
                v-model="criterion.points"
                type="number"
                min="0"
                class="focus:ring-blue-500 h-8 w-16 text-blue-600 border-gray-300 rounded"
              >
            </div>
            <div class="ml-3 flex-1">
              <input
                v-model="criterion.description"
                type="text"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                placeholder="Criterion description"
              >
              <button
                @click="removeCriterion(index)"
                class="mt-1 text-sm text-red-600 hover:text-red-800"
              >
                Remove criterion
              </button>
            </div>
          </div>
          <button
            @click="addCriterion"
            type="button"
            class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Add Grading Criterion
          </button>
        </div>
      </div>
  
      <!-- Additional Settings -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Additional Settings</label>
        <div class="mt-2 space-y-2">
          <label class="inline-flex items-center">
            <input
              v-model="modelValue.settings.enable_plagiarism_check"
              type="checkbox"
              class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
            <span class="ml-2 text-sm text-gray-600">Enable plagiarism check</span>
          </label>
          <label class="inline-flex items-center">
            <input
              v-model="modelValue.settings.allow_attachments"
              type="checkbox"
              class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
            <span class="ml-2 text-sm text-gray-600">Allow file attachments</span>
          </label>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  const props = defineProps({
    modelValue: {
      type: Object,
      required: true,
      default: () => ({
        type: 'essay',
        correct_answers: [], // Not used for essay questions
        settings: {
          min_words: 100,
          max_words: 500,
          allow_rich_text: true,
          require_paragraphs: false,
          sample_answer: '',
          rubric: [],
          enable_plagiarism_check: false,
          allow_attachments: false
        }
      })
    }
  });
  
  const emit = defineEmits(['update:modelValue']);
  
  const addCriterion = () => {
    props.modelValue.settings.rubric.push({
      points: 5,
      description: ''
    });
  };
  
  const removeCriterion = (index) => {
    props.modelValue.settings.rubric.splice(index, 1);
  };
  </script>