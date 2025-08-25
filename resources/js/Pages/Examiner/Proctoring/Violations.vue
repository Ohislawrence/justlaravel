<script setup>
defineProps({
  violations: Array,
  attemptId: Number
});
</script>

<template>
  <ShowProctor 
    :attempt-id="attemptId" 
    :initial-violations="violations" 
  />
</template>