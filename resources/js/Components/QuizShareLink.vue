<template>
    <div class="share-quiz">
      <h3 class="text-lg font-medium mb-2">Share Quiz</h3>
      <div class="flex items-center space-x-2">
        <input 
          ref="linkInput"
          type="text" 
          :value="shareLink" 
          readonly
          class="flex-1 px-3 py-2 border rounded-md"
        />
        <button 
          @click="copyLink"
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
        >
          Copy Link
        </button>
      </div>
      <p v-if="copied" class="mt-2 text-green-600">Link copied!</p>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import { useRoute } from 'vue-router';
  
  const props = defineProps({
    quizId: {
      type: String,
      required: true
    }
  });
  
  const shareLink = ref('');
  const copied = ref(false);
  const linkInput = ref(null);
  
  onMounted(async () => {
    // Fetch shareable link from backend
    const response = await axios.get(`/api/quizzes/${props.quizId}/share-link`);
    shareLink.value = response.data.link;
  });
  
  const copyLink = () => {
    linkInput.value.select();
    document.execCommand('copy');
    copied.value = true;
    setTimeout(() => copied.value = false, 2000);
  };
  </script>