<template>
    <div class="relative">
      <select 
        v-model="selectedPool"
        @change="handleAttach"
        class="block w-full p-2 border rounded"
      >
        <option value="">Add to pool...</option>
        <option 
          v-for="pool in pools" 
          :key="pool.id" 
          :value="pool.id"
        >
          {{ pool.name }} ({{ pool.questions_count }})
        </option>
      </select>
      <button 
        v-if="showCreate && selectedPool === ''"
        @click="showCreateModal = true"
        class="absolute right-0 top-0 p-2"
      >
        +
      </button>
    </div>
  
    <PoolCreateModal 
      v-if="showCreateModal"
      @close="showCreateModal = false"
      @created="handlePoolCreated"
    />
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { router } from '@inertiajs/vue3';
  
  const props = defineProps({
    pools: Array,
    questionId: Number,
    showCreate: {
      type: Boolean,
      default: true
    }
  });
  
  const selectedPool = ref('');
  const showCreateModal = ref(false);
  
  const handleAttach = () => {
    if (selectedPool.value) {
      router.post(route('pools.attach-question', {
        pool_id: selectedPool.value,
        question_id: props.questionId
      }), {
        preserveScroll: true
      });
      selectedPool.value = '';
    }
  };
  
  const handlePoolCreated = (newPool) => {
    props.pools.push(newPool);
    selectedPool.value = newPool.id;
    handleAttach();
  };
  </script>