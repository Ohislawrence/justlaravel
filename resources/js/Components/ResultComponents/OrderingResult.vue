<template>
    <div class="space-y-4">
      <div class="font-medium text-gray-700">Your Order:</div>
      
      <ol class="space-y-2 list-decimal pl-5">
        <li 
          v-for="(item, index) in userOrder" 
          :key="index"
          class="p-2 rounded"
          :class="{
            'bg-green-50': isCorrectPosition(index),
            'bg-red-50': !isCorrectPosition(index)
          }"
        >
          {{ item.text }}
        </li>
      </ol>
      
      <div class="mt-4">
        <div class="font-medium text-gray-700 mb-2">Correct Order:</div>
        <ol class="space-y-2 list-decimal pl-5">
          <li 
            v-for="(item, index) in correctOrder" 
            :key="index"
            class="p-2 bg-gray-50 rounded"
          >
            {{ item.text }}
          </li>
        </ol>
      </div>
    </div>
  </template>
  
  <script setup>
  import { computed } from 'vue';
  
  const props = defineProps({
    question: Object,
    response: Array
  });
  
  const userOrder = computed(() => {
    if (!props.response || !props.question.options) return [];
    return props.response.map(index => props.question.options[index]);
  });
  
  const correctOrder = computed(() => {
    return [...(props.question.options || [])].sort((a, b) => {
      return props.question.correct_answers.indexOf(a.id) - 
             props.question.correct_answers.indexOf(b.id);
    });
  });
  
  const isCorrectPosition = (index) => {
    if (!props.response || !props.question.correct_answers) return false;
    return props.response[index] === props.question.correct_answers[index];
  };
  </script>