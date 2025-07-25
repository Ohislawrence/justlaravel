<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  organization: Object,
  designations: Array,
  groups: Array,
});

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  designation_id: null,
  unique_code: '',
  groups: [],
});

const submitForm = () => {
  form.post(`/landlord/organizations/${props.organization.id}/users`);
};
</script>

<template>
  <AppLayout title="Create Examiner">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Create Examiner for {{ organization.name }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <form @submit.prevent="submitForm">
              <!-- Name -->
              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                  Full Name
                </label>
                <input
                  v-model="form.name"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  id="name"
                  type="text"
                  placeholder="John Doe"
                  :class="{ 'border-red-500': form.errors.name }"
                >
                <p v-if="form.errors.name" class="text-red-500 text-xs italic">
                  {{ form.errors.name }}
                </p>
              </div>

              <!-- Email -->
              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                  Email
                </label>
                <input
                  v-model="form.email"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  id="email"
                  type="email"
                  placeholder="email@example.com"
                  :class="{ 'border-red-500': form.errors.email }"
                >
                <p v-if="form.errors.email" class="text-red-500 text-xs italic">
                  {{ form.errors.email }}
                </p>
              </div>

              <!-- Password -->
              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                  Password
                </label>
                <input
                  v-model="form.password"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  id="password"
                  type="password"
                  placeholder="********"
                  :class="{ 'border-red-500': form.errors.password }"
                >
                <p v-if="form.errors.password" class="text-red-500 text-xs italic">
                  {{ form.errors.password }}
                </p>
              </div>

              <!-- Password Confirmation -->
              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password_confirmation">
                  Confirm Password
                </label>
                <input
                  v-model="form.password_confirmation"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  id="password_confirmation"
                  type="password"
                  placeholder="********"
                >
              </div>

              <!-- Unique Code -->
              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="unique_code">
                  Employee/Staff ID
                </label>
                <input
                  v-model="form.unique_code"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  id="unique_code"
                  type="text"
                  placeholder="EMP-12345"
                  :class="{ 'border-red-500': form.errors.unique_code }"
                >
                <p v-if="form.errors.unique_code" class="text-red-500 text-xs italic">
                  {{ form.errors.unique_code }}
                </p>
              </div>

              <!-- Designation -->
              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="designation_id">
                  Designation
                </label>
                <select
                  v-model="form.designation_id"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  id="designation_id"
                  :class="{ 'border-red-500': form.errors.designation_id }"
                >
                  <option value="">Select Designation</option>
                  <option 
                    v-for="designation in designations" 
                    :key="designation.id" 
                    :value="designation.id"
                  >
                    {{ designation.name }}
                  </option>
                </select>
                <p v-if="form.errors.designation_id" class="text-red-500 text-xs italic">
                  {{ form.errors.designation_id }}
                </p>
              </div>

              <!-- Groups -->
              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="groups">
                  Assign to Groups
                </label>
                <select
                  v-model="form.groups"
                  multiple
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  id="groups"
                  :class="{ 'border-red-500': form.errors.groups }"
                >
                  <option v-for="group in groups" :key="group.id" :value="group.id">
                    {{ group.name }}
                  </option>
                </select>
                <p v-if="form.errors.groups" class="text-red-500 text-xs italic">
                  {{ form.errors.groups }}
                </p>
                <p class="text-gray-600 text-xs italic mt-1">Hold Ctrl/Cmd to select multiple groups</p>
              </div>

              <div class="flex items-center justify-end mt-6">
                <button
                  type="submit"
                  class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                  :disabled="form.processing"
                >
                  <span v-if="form.processing">Creating...</span>
                  <span v-else>Create Examiner</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>