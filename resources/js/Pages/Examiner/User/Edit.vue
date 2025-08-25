<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const hasAdminRole = computed(() => {
  return props.user.roles?.some(role => role.name === 'examinee');
});

const props = defineProps({
  user: Object,
  groups: Array,
  quizzes: Array,
  roles: Array,
  designations: Array,
  organizationMember: Object,
  initialUserType: String,
  initialGroups: Array
});

// Get the first group ID if user has groups
const initialGroupId = computed(() => {
  return props.initialGroups?.length > 0 ? props.initialGroups[0].id : null;
});

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  user_type: props.initialUserType,
  group_id: initialGroupId.value, // Single group ID
  unique_code: props.organizationMember?.unique_code || '',
  designation_id: props.organizationMember?.designation_id || null,
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
                    class="mt-1 block w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
                    :disabled="user.id === $page.props.auth.user.id"
                  >
                    <option value="examiner">Examiner</option>
                    <option value="instructor">Instructor</option>
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

                <!-- Single Group Select -->
                <div>
                  <InputLabel for="group_id" value="Group" />
                  <select
                    v-model="form.group_id"
                    class="mt-1 block w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
                  >
                    <option value="">Select a Group</option>
                    <option 
                      v-for="group in groups" 
                      :value="group.id" 
                      :key="group.id"
                    >
                      {{ group.name }}
                    </option>
                  </select>
                  <InputError class="mt-2" :message="form.errors.group_id" />
                </div>

                <!-- Designation Dropdown -->
                <div>
                  <InputLabel for="designation_id" value="Designation" />
                  <select
                    v-model="form.designation_id"
                    class="mt-1 block w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
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

<style scoped>
/* Custom styles to enhance the green theme */
.PrimaryButton {
  background-color: #10B981;
  border-color: #10B981;
  color: white;
}

.PrimaryButton:hover {
  background-color: #059669;
  border-color: #059669;
}

.PrimaryButton:focus {
  box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
}

input[type="text"],
input[type="email"],
input[type="password"],
select {
  border-color: #d1fae5;
}

input[type="text"]:focus,
input[type="email"]:focus,
input[type="password"]:focus,
select:focus {
  border-color: #10B981;
  box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
}

/* Green-themed select dropdown indicator */
select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2310B981' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
}

/* Green-themed form labels */
label {
  color: #065f46;
}

/* Green-themed input errors */
.InputError {
  color: #047857;
}
</style>