<template>
    <div class="space-y-6">
      <!-- Question Text with Blanks -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Question Text with Blanks</label>
        <div class="mt-1 relative">
          <textarea
            ref="textareaRef"
            v-model="modelValue.question"
            rows="4"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            placeholder="Enter your question text. Use [blank] to indicate where blanks should appear."
            @input="updateBlanks"
          ></textarea>
          <div class="absolute right-2 bottom-2">
            <button
              @click="insertBlank"
              type="button"
              class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Insert Blank
            </button>
          </div>
        </div>
        <p class="mt-1 text-sm text-gray-500">
          Use [blank] tags to mark where blanks should appear. Example: "The capital of France is [blank]."
        </p>
      </div>
  
      <!-- Blank Answers -->
      <div v-if="blanks.length > 0">
        <label class="block text-sm font-medium text-gray-700">Blank Answers</label>
        <div class="mt-2 space-y-3">
          <div v-for="(blank, index) in blanks" :key="index" class="flex items-start">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-blue-100 text-blue-800 mr-3">
              Blank #{{ index + 1 }}
            </span>
            <div class="flex-1">
              <input
                v-model="modelValue.correct_answers[index]"
                type="text"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                :placeholder="'Answer for blank #' + (index + 1)"
              >
              <div class="mt-1 flex space-x-2">
                <input
                  v-model="modelValue.settings.case_sensitive[index]"
                  type="checkbox"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                >
                <label class="text-sm text-gray-600">Case sensitive</label>
              </div>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Alternative Answers -->
      <div v-if="blanks.length > 0">
        <label class="block text-sm font-medium text-gray-700">Alternative Answers</label>
        <div class="mt-2 space-y-3">
          <div v-for="(blank, index) in blanks" :key="'alt-' + index" class="space-y-2">
            <div class="flex items-center">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-blue-100 text-blue-800 mr-3">
                Blank #{{ index + 1 }}
              </span>
              <button
                @click="addAlternative(index)"
                type="button"
                class="inline-flex items-center px-2 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Add Alternative
              </button>
            </div>
            <div v-for="(alt, altIndex) in modelValue.settings.alternatives[index]" :key="altIndex" class="ml-8 flex items-center">
              <input
                v-model="modelValue.settings.alternatives[index][altIndex]"
                type="text"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                :placeholder="'Alternative ' + (altIndex + 1)"
              >
              <button
                @click="removeAlternative(index, altIndex)"
                type="button"
                class="ml-2 inline-flex items-center p-1 border border-transparent rounded-full text-red-700 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
              >
                <XMarkIcon class="h-4 w-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Feedback Options -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Feedback</label>
        <textarea
          v-model="modelValue.settings.feedback"
          rows="2"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
          placeholder="Optional feedback to show after answering"
        ></textarea>
      </div>
    </div>
  </template>
  
  <script setup>
  import { XMarkIcon } from '@heroicons/vue/24/solid';
  import { ref, watch, onMounted } from 'vue';
  
  const textareaRef = ref(null);

  const props = defineProps({
    modelValue: {
      type: Object,
      required: true,
      default: () => ({
        type: 'fill_in_the_blank',
        question: '',
        correct_answers: [], // Initialize as empty array
        settings: {
          case_sensitive: [],
          alternatives: [],
          feedback: ''
        }
      })
    }
  });
  
  const emit = defineEmits(['update:modelValue']);
  
  // Track blank positions
  const blanks = ref([]);
  
  // Initialize arrays safely
  const initializeArrays = () => {
    if (!Array.isArray(props.modelValue.correct_answers)) {
      props.modelValue.correct_answers = [];
    }
    if (!Array.isArray(props.modelValue.settings.case_sensitive)) {
      props.modelValue.settings.case_sensitive = [];
    }
    if (!Array.isArray(props.modelValue.settings.alternatives)) {
      props.modelValue.settings.alternatives = [];
    }
  };
  
  // Update blanks based on question text
  const updateBlanks = () => {
    initializeArrays(); // Ensure arrays exist
    
    const blankRegex = /\[blank\]/gi;
    const matches = props.modelValue.question.match(blankRegex);
    blanks.value = matches ? Array(matches.length).fill('') : [];
    
    // Initialize or trim arrays to match blanks count
    while (props.modelValue.correct_answers.length < blanks.value.length) {
      props.modelValue.correct_answers.push('');
    }
    while (props.modelValue.settings.case_sensitive.length < blanks.value.length) {
      props.modelValue.settings.case_sensitive.push(false);
    }
    while (props.modelValue.settings.alternatives.length < blanks.value.length) {
      props.modelValue.settings.alternatives.push([]);
    }
    
    // Trim arrays if blanks were removed
    if (props.modelValue.correct_answers.length > blanks.value.length) {
      props.modelValue.correct_answers = props.modelValue.correct_answers.slice(0, blanks.value.length);
    }
    if (props.modelValue.settings.case_sensitive.length > blanks.value.length) {
      props.modelValue.settings.case_sensitive = props.modelValue.settings.case_sensitive.slice(0, blanks.value.length);
    }
    if (props.modelValue.settings.alternatives.length > blanks.value.length) {
      props.modelValue.settings.alternatives = props.modelValue.settings.alternatives.slice(0, blanks.value.length);
    }
  };
  
  // Insert [blank] at cursor position
  const insertBlank = () => {
  if (!textareaRef.value) return; // Safety check
  
  const startPos = textareaRef.value.selectionStart;
  const endPos = textareaRef.value.selectionEnd;
  const text = textareaRef.value.value;
  
  textareaRef.value.value = text.substring(0, startPos) + '[blank]' + text.substring(endPos);
  textareaRef.value.focus();
  textareaRef.value.selectionStart = startPos + 7;
  textareaRef.value.selectionEnd = startPos + 7;
  
  // Trigger update
  emit('update:modelValue', {
    ...props.modelValue,
    question: textareaRef.value.value
  });
};
  
  // Add alternative answer for a blank
  const addAlternative = (blankIndex) => {
    props.modelValue.settings.alternatives[blankIndex].push('');
  };
  
  // Remove alternative answer
  const removeAlternative = (blankIndex, altIndex) => {
    props.modelValue.settings.alternatives[blankIndex].splice(altIndex, 1);
  };
  
  // Initialize on mount
  onMounted(() => {
  initializeArrays();
  updateBlanks();
});
</script>