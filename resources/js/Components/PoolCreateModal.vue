<template>
    <Modal @close="close">
      <template #header>
        <h2>Create New Pool</h2>
      </template>
      
      <form @submit.prevent="submit">
        <TextInput v-model="form.name" label="Pool Name" required />
        <Textarea v-model="form.description" label="Description" />
        
        <div class="mt-4 flex justify-end">
          <button type="submit" class="btn-primary">
            Create Pool
          </button>
        </div>
      </form>
    </Modal>
  </template>
  
  <script setup>
  import { useForm } from '@inertiajs/vue3';
  
  const emit = defineEmits(['close', 'created']);
  
  const form = useForm({
    name: '',
    description: ''
  });
  
  const submit = () => {
    form.post(route('examiner.question-pools.store'), {
      onSuccess: (response) => {
        emit('created', response.props.pool);
        emit('close');
      }
    });
  };
  
  const close = () => {
    emit('close');
  };
  </script>