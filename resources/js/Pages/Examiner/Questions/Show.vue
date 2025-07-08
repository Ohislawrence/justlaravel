<template>
    <div>
      <h2>{{ question.question }}</h2>
      <div class="mt-4">
        <h3>Pools containing this question:</h3>
        <ul>
          <li v-for="pool in question.pools" :key="pool.id">
            {{ pool.name }}
            <button @click="detach(pool)" class="text-red-500 ml-2">×</button>
          </li>
        </ul>
        
        <PoolSelector 
          :pools="availablePools" 
          :question-id="question.id"
          class="mt-4"
        />
      </div>
    </div>
  </template>
  
  <script setup>
  import { router } from '@inertiajs/vue3';
  
  const props = defineProps({
    question: Object,
    availablePools: Array
  });
  
  const detach = (pool) => {
    router.delete(route('examiner.pools.detach-question', {
      pool: pool.id,
      question: props.question.id
    }), {
      preserveScroll: true
    });
  };
  </script>