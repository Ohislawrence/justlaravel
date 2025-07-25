<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Multiselect from '@vueform/multiselect';

const hasAdminRole = computed(() => {
  return props.user.roles?.some(role => role.name === 'admin');
});

const props = defineProps({
  user: Object,
  groups: Array,
  quizzes: Array,
  roles: Array,
  designations: Array, // Changed from Object to Array
  organizationMember: Object,
  initialUserType: String,
  initialGroups: Array
});

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  user_type: props.initialUserType,
  groups: props.initialGroups,
  unique_code: props.organizationMember?.unique_code || '',
  designation_id: props.organizationMember?.designation_id || null, // Added this line
  quizzes: (props.user.assigned_quizzes || []).map(quiz => quiz.id),
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.put(route('examiner.user.update', {
    user: props.user.id,
    organization: props.organizationMember?.organization_id
  }), {
    onSuccess: () => form.reset('password', 'password_confirmation'),
  });
};
</script>

<template>
  <AppLayout title="Edit User">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Edit User: {{ user.name }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <form @submit.prevent="submit">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                  <InputLabel for="name" value="Name" />
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

                <!-- Email -->
                <div>
                  <InputLabel for="email" value="Email" />
                  <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    :readonly="!hasAdminRole"
                  />
                  <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <!-- User Type -->
                <div>
                  <InputLabel for="user_type" value="User Type" />
                  <select
                    v-model="form.user_type"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    :disabled="user.id === $page.props.auth.user.id"
                  >
                    <option value="admin">Admin</option>
                    <option value="examiner">Examiner</option>
                    <option value="examinee">Examinee</option>
                  </select>
                  <InputError class="mt-2" :message="form.errors.user_type" />
                </div>

                <!-- Unique Code (for examinees) -->
                <div v-if="form.user_type === 'examinee'">
                  <InputLabel for="unique_code" value="Unique Code" />
                  <TextInput
                    id="unique_code"
                    v-model="form.unique_code"
                    type="text"
                    class="mt-1 block w-full"
                  />
                  <InputError class="mt-2" :message="form.errors.unique_code" />
                </div>

                <!-- Groups Multiselect -->
                <div>
                  <InputLabel for="groups" value="Groups" />
                  <Multiselect
                    v-model="form.groups"
                    :options="groups.map(group => ({ value: group.id, label: group.name }))"
                    mode="tags"
                    placeholder="Select groups"
                  />
                </div>

                <!-- Designation Dropdown -->
                <div class="mb-4">
                  <InputLabel for="designation_id" value="Designation" />
                  <select
                    v-model="form.designation_id"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    :class="{ 'is-invalid': form.errors.designation_id }"
                  >
                    <option value="">Select Designation</option>
                    <option 
                      v-for="designation in designations" 
                      :value="designation.id" 
                      :key="designation.id"
                    >
                      {{ designation.name }}
                    </option>
                  </select>
                  <InputError class="mt-2" :message="form.errors.designation_id" />
                </div>

                

                <!-- Password -->
                <div>
                  <InputLabel for="password" value="New Password" />
                  <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                  />
                  <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <!-- Confirm Password -->
                <div>
                  <InputLabel for="password_confirmation" value="Confirm Password" />
                  <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                  />
                  <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>
              </div>

              <div class="flex items-center justify-end mt-6">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                  <span v-if="form.processing">Updating...</span>
                  <span v-else>Update User</span>
                </PrimaryButton>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style src="@vueform/multiselect/themes/default.css"></style>