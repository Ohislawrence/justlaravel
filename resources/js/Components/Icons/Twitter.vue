<template>
    <component :is="twitterIcon" :class="class" />
  </template>
  
  <script setup>
  import { XMarkIcon as TwitterIcon } from '@heroicons/vue/24/outline';
  
  defineProps({
    class: {
      type: String,
      default: 'h-6 w-6'
    }
  });
  
  const twitterIcon = TwitterIcon;
  </script>