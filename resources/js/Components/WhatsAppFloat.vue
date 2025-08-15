<template>
    <a 
      :href="whatsappLink"
      target="_blank"
      rel="noopener noreferrer"
      class="fixed bottom-4 right-4 z-50 group"
      aria-label="Chat on WhatsApp"
    >
      <!-- Button -->
      <div
        class="relative bg-green-500 text-white rounded-full p-3 shadow-lg 
               hover:bg-green-600 hover:scale-110 transition-transform duration-300 
               flex items-center justify-center"
      >
        <!-- WhatsApp Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" 
             viewBox="0 0 32 32" 
             class="w-5 h-5 fill-current">
          <path d="M16 3C9.373 3 4 8.373 4 15c0 2.572.844 4.96 2.26 6.923L4 29l7.285-2.28A11.92 11.92 0 0016 27c6.627 0 12-5.373 12-12S22.627 3 16 3zm.002 21.5c-1.937 0-3.844-.506-5.5-1.463l-.395-.23-3.422 1.072 1.14-3.333-.262-.41A8.99 8.99 0 017 15c0-4.963 4.037-9 9.002-9C21.963 6 26 10.037 26 15s-4.037 9.5-9.998 9.5zm4.963-6.165c-.272-.136-1.61-.793-1.86-.883-.25-.09-.43-.136-.61.136-.18.272-.7.883-.858 1.063-.157.181-.316.205-.588.068-.272-.137-1.148-.424-2.19-1.35-.81-.723-1.357-1.615-1.515-1.887-.157-.272-.017-.419.12-.556.123-.124.272-.318.408-.477.136-.158.181-.272.272-.454.091-.181.045-.34-.022-.477-.068-.137-.608-1.456-.834-1.994-.22-.528-.444-.455-.61-.464-.157-.007-.34-.009-.522-.009s-.477.068-.727.34c-.25.272-.955.933-.955 2.275s.978 2.641 1.114 2.822c.136.181 1.927 3.046 4.675 4.268.654.283 1.162.453 1.56.58.664.212 1.268.187 1.742.114.531-.079 1.635-.668 1.865-1.31.23-.642.23-1.191.16-1.31-.068-.115-.25-.183-.522-.319z"/>
        </svg>
  
        <!-- Tooltip -->
        <span
          class="absolute right-full mr-3 top-1/2 transform -translate-y-1/2 
                 bg-gray-900 text-white text-xs rounded py-1 px-2 opacity-0 
                 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap"
        >
          Chat with us
        </span>
      </div>
    </a>
  </template>
  
  <script setup>
  import { computed } from 'vue';
  
  const whatsappNumber = '2348117297730';
  const whatsappMessage = 'Hello! I have a question about QuizPortal Online.';
  const whatsappLink = computed(() => {
    return `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(whatsappMessage)}`;
  });
  </script>
  