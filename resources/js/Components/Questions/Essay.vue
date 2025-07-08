<template>
  <div class="space-y-4">
    <!-- Word count requirements -->
    <div v-if="hasWordLimit" class="text-sm text-gray-600">
      <span v-if="question.settings.min_words && question.settings.max_words">
        Your answer should be between {{ question.settings.min_words }} and {{ question.settings.max_words }} words.
      </span>
      <span v-else-if="question.settings.min_words">
        Minimum {{ question.settings.min_words }} words required.
      </span>
      <span v-else-if="question.settings.max_words">
        Maximum {{ question.settings.max_words }} words allowed.
      </span>
    </div>

    <!-- Answer textarea -->
    <textarea
      v-model="answerText"
      rows="8"
      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
      :placeholder="textareaPlaceholder"
      :class="{ 'border-red-300': showWordCountError }"
      @input="handleInput"
    ></textarea>

    <!-- Word count and validation -->
    <div class="flex justify-between items-center">
      <div class="text-sm" :class="wordCountClass">
        Word count: {{ wordCount }}
        <span v-if="showWordCountError" class="ml-2 text-red-600">
          ({{ wordCountError }})
        </span>
      </div>
      <div v-if="isRichTextEnabled" class="text-xs text-blue-600">
        Rich text formatting available
      </div>
    </div>

    <!-- Sample answer -->
    <div v-if="question.settings?.sample_answer" class="mt-4 p-4 bg-gray-50 rounded-md border border-gray-200">
      <h4 class="text-sm font-medium text-gray-700 mb-2">Example Answer</h4>
      <div class="text-sm text-gray-600" v-html="formattedSampleAnswer"></div>
    </div>

    <!-- File attachments -->
    <div v-if="question.settings?.allow_attachments" class="mt-4">
      <label class="block text-sm font-medium text-gray-700 mb-2">Attachments</label>
      <input
        type="file"
        @change="handleFileUpload"
        multiple
        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
      >
      <p class="mt-1 text-xs text-gray-500">
        {{ attachmentInstructions }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  question: {
    type: Object,
    required: true,
    default: () => ({
      settings: {
        min_words: 0,
        max_words: 0,
        sample_answer: '',
        allow_rich_text: false,
        allow_attachments: false
      }
    })
  },
  answer: {
    type: [String, Object],
    default: ''
  }
});

const emit = defineEmits(['update']);

// Initialize answer data
const answerText = ref('');
const attachments = ref([]);

// Set initial value
watch(() => props.answer, (newAnswer) => {
  if (typeof newAnswer === 'string') {
    answerText.value = newAnswer;
  } else if (newAnswer?.text) {
    answerText.value = newAnswer.text;
    attachments.value = newAnswer.attachments || [];
  }
}, { immediate: true });

// Computed properties
const hasWordLimit = computed(() => {
  return props.question.settings?.min_words > 0 || props.question.settings?.max_words > 0;
});

const isRichTextEnabled = computed(() => {
  return props.question.settings?.allow_rich_text;
});

const wordCount = computed(() => {
  return answerText.value.trim() ? answerText.value.trim().split(/\s+/).length : 0;
});

const showWordCountError = computed(() => {
  const settings = props.question.settings || {};
  return (settings.min_words > 0 && wordCount.value < settings.min_words) ||
         (settings.max_words > 0 && wordCount.value > settings.max_words);
});

const wordCountError = computed(() => {
  const settings = props.question.settings || {};
  if (settings.min_words > 0 && wordCount.value < settings.min_words) {
    return `${settings.min_words - wordCount.value} more words required`;
  }
  if (settings.max_words > 0 && wordCount.value > settings.max_words) {
    return `${wordCount.value - settings.max_words} words over limit`;
  }
  return '';
});

const wordCountClass = computed(() => {
  return showWordCountError.value ? 'text-red-600' : 'text-gray-500';
});

const textareaPlaceholder = computed(() => {
  const settings = props.question.settings || {};
  if (settings.min_words > 0 && settings.max_words > 0) {
    return `Write your answer here (${settings.min_words}-${settings.max_words} words)`;
  }
  if (settings.min_words > 0) {
    return `Write your answer here (at least ${settings.min_words} words)`;
  }
  if (settings.max_words > 0) {
    return `Write your answer here (up to ${settings.max_words} words)`;
  }
  return 'Write your answer here';
});

const attachmentInstructions = computed(() => {
  return isRichTextEnabled.value
    ? 'You may attach supporting documents or use rich text formatting below'
    : 'You may attach supporting documents if needed';
});

const formattedSampleAnswer = computed(() => {
  if (!props.question.settings?.sample_answer) return '';
  return props.question.settings.sample_answer.replace(/\n/g, '<br>');
});

// Methods
const handleInput = () => {
  emitUpdate();
};

const handleFileUpload = (event) => {
  attachments.value = Array.from(event.target.files);
  emitUpdate();
};

const emitUpdate = () => {
  if (props.question.settings?.allow_attachments) {
    emit('update', {
      text: answerText.value,
      attachments: attachments.value
    });
  } else {
    emit('update', answerText.value);
  }
};
</script>