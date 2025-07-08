<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Multiselect from '@vueform/multiselect';

const props = defineProps({
  user: Object,
  groups: Array,
  quizzes: Array,
  roles: Array,
});

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  user_type: props.user.user_type,
  groups: (props.user.groups || []).map(group => group.id),
  quizzes: (props.user.assigned_quizzes || []).map(quiz => quiz.id),
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.put(route('examiner.user.update', props.user.id), {
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
                    readonly
                />
                <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <!-- Role -->
                

                <!-- User Type -->
                <div class="mb-3">
                  <label for="user_type" class="form-label">User Type</label>
                  <select
                    v-model="form.user_type"
                    class="form-select"
                    id="user_type"
                    :class="{ 'is-invalid': form.errors.user_type }"
                  >
                    <option disabled value="">Unchanged</option>
                    <option value="admin">Admin (full access)</option>
                    <option value="examiner">Examiner (staff/teacher)</option>
                    <option value="examinee">Examinee (student/candidate)</option>
                  </select>
                  <div class="invalid-feedback" v-if="form.errors.user_type">{{ form.errors.user_type }}</div>
                </div>

                <!-- Groups -->
                <div>
                  <InputLabel for="groups" value="Groups" />
                  <Multiselect
                    id="groups"
                    v-model="form.groups"
                    :options="groups.map(group => ({ value: group.id, label: group.name }))"
                    mode="tags"
                    placeholder="Select groups"
                    class="mt-1"
                  />
                  <InputError class="mt-2" :message="form.errors.groups" />
                </div>

                <!-- Quizzes -->
                <div>
                  <InputLabel for="quizzes" value="Assigned Quizzes" />
                  <Multiselect
                    id="quizzes"
                    v-model="form.quizzes"
                    :options="quizzes.map(quiz => ({ value: quiz.id, label: quiz.name }))"
                    mode="tags"
                    placeholder="Select quizzes"
                    class="mt-1"
                  />
                  <InputError class="mt-2" :message="form.errors.quizzes" />
                </div>

                <!-- Password -->
                <div>
                  <InputLabel for="password" value="New Password (leave blank to keep current)" />
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