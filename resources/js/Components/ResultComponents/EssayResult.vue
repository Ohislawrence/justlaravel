<template>
    <div class="space-y-4">
      <div>
        <div class="font-medium text-gray-700 mb-1">Your Answer:</div>
        <div class="p-4 bg-gray-50 rounded border border-gray-200 whitespace-pre-wrap">
          {{ response?.text || response || "(No answer provided)" }}
        </div>
        <div class="text-sm text-gray-600 mt-1">
          Word count: {{ wordCount }}
          <span v-if="hasWordLimit" class="ml-2">
            (Required: {{ question.settings.min_words }}-{{ question.settings.max_words }} words)
          </span>
        </div>
      </div>
      
      <div v-if="response?.attachments?.length > 0" class="mt-4">
        <div class="font-medium text-gray-700 mb-2">Attachments:</div>
        <div class="space-y-2">
          <div v-for="(file, index) in response.attachments" :key="index" class="flex items-center">
            <PaperClipIcon class="h-4 w-4 text-gray-500 mr-2" />
            <span class="text-sm text-gray-700">{{ file.name }}</span>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { PaperClipIcon } from '@heroicons/vue/24/outline';
  import { computed } from 'vue';
  
  const props = defineProps({
    question: Object,
    response: [String, Object]
  });
  
  const wordCount = computed(() => {
    const text = props.response?.text || props.response || '';
    return text.trim() ? text.trim().split(/\s+/).length : 0;
  });
  
  const hasWordLimit = computed(() => {
    return props.question.settings?.min_words > 0 || props.question.settings?.max_words > 0;
  });
  </script>