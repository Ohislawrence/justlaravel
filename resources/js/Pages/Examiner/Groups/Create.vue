<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';

const props = defineProps({
  organization: Object,
});

const form = useForm({
  name: '',
  description: '',
  organization_id: props.organization.id,
});

const submit = () => {
  form.post(route('examiner.groups.store', props.organization.slug));
};
</script>

<template>
  <AppLayout title="Create New Group">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="fw-bold py-3 mb-4">
          Create New Group in {{ organization.name }}
        </h2>
        <Link 
          :href="route('examiner.groups.index', organization.slug)"
          class="text-sm text-gray-600 hover:text-gray-900"
        >
          Back to Groups
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <form @submit.prevent="submit">
              <div class="grid grid-cols-1 gap-6">
                <!-- Group Name -->
                <div>
                  <InputLabel for="name" value="Group Name *" />
                  <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                  />
                  <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <!-- Description -->
                <div>
                  <InputLabel for="description" value="Description" />
                  <Textarea
                    id="description"
                    v-model="form.description"
                    class="mt-1 block w-full"
                    rows="3"
                  />
                  <InputError class="mt-2" :message="form.errors.description" />
                </div>

                <!-- Organization ID (hidden) -->
                  <input type="hidden" v-model="form.organization_id" />

                <!-- Submit Button -->
                <div class="flex items-center justify-end mt-6">
                  <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Create Group
                  </PrimaryButton>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>