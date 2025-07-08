<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

// Props
const props = defineProps({
  groups: Object,
  quizzes: Array,
});

// Inertia form object (automatically tracks data, errors, and states)
const form = useForm({
  name: '',
  email: '',
  password: '',
  user_type: '',
  groups: [],
  quizzes: [],
  notify_user: false,
});

// Submit handler
const submitForm = () => {
  form.post('/examiner/user');
};
</script>

<template>
  <AppLayout title="Add User">
    <div class="container-xxl flex-grow-1 container-p-y">
      <h4 class="fw-bold py-3 mb-4">Create a User</h4>

      <div class="row">
        <div class="col-xl">
          <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Login Details</h5>
              <small class="text-muted float-end">Required fields</small>
            </div>
            <div class="card-body">
              <form @submit.prevent="submitForm">
                <!-- Full Name -->
                <div class="mb-3">
                  <label class="form-label" for="name">Full Name</label>
                  <input
                    v-model="form.name"
                    type="text"
                    class="form-control"
                    id="name"
                    placeholder="John Doe"
                    :class="{ 'is-invalid': form.errors.name }"
                  />
                  <div class="invalid-feedback" v-if="form.errors.name">{{ form.errors.name }}</div>
                </div>

                <!-- Email -->
                <div class="mb-3">
                  <label class="form-label" for="email">Email</label>
                  <input
                    v-model="form.email"
                    type="email"
                    class="form-control"
                    id="email"
                    placeholder="email@example.com"
                    :class="{ 'is-invalid': form.errors.email }"
                  />
                  <div class="invalid-feedback" v-if="form.errors.email">{{ form.errors.email }}</div>
                </div>

                <!-- Password -->
                <div class="mb-3">
                  <label class="form-label" for="password">Password</label>
                  <input
                    v-model="form.password"
                    type="password"
                    class="form-control"
                    id="password"
                    placeholder="Password"
                    :class="{ 'is-invalid': form.errors.password }"
                  />
                  <div class="invalid-feedback" v-if="form.errors.password">{{ form.errors.password }}</div>
                </div>

                <!-- User Type -->
                <div class="mb-3">
                  <label for="user_type" class="form-label">User Type</label>
                  <select
                    v-model="form.user_type"
                    class="form-select"
                    id="user_type"
                    :class="{ 'is-invalid': form.errors.user_type }"
                  >
                    <option disabled value="">Select One</option>
                    <option value="admin">Admin (full access)</option>
                    <option value="examiner">Examiner (staff/teacher)</option>
                    <option value="examinee">Examinee (student/candidate)</option>
                  </select>
                  <div class="invalid-feedback" v-if="form.errors.user_type">{{ form.errors.user_type }}</div>
                </div>

                <!-- Groups -->
                <div class="mb-3">
                  <label for="groups" class="form-label">Assign Group(s)</label>
                  <select
                    v-model="form.groups"
                    multiple
                    class="form-select"
                    id="groups"
                    :class="{ 'is-invalid': form.errors.groups }"
                  >
                    <option v-for="group in props.groups.data" :key="group.id" :value="group.id">
                      {{ group.name }}
                    </option>
                  </select>
                  <div class="invalid-feedback" v-if="form.errors.groups">{{ form.errors.groups }}</div>
                </div>

                <!-- Quizzes -->
                <div class="mb-3">
                  <label for="quizzes" class="form-label">Assign Quiz(zes)</label>
                  <select
                    v-model="form.quizzes"
                    multiple
                    class="form-select"
                    id="quizzes"
                    :class="{ 'is-invalid': form.errors.quizzes }"
                  >
                    <option v-for="quiz in props.quizzes.data" :key="quiz.id" :value="quiz.id">
                      {{ quiz.title }}
                    </option>
                  </select>
                  <div class="invalid-feedback" v-if="form.errors.quizzes">{{ form.errors.quizzes }}</div>
                </div>

                <!-- Notification Checkbox -->
                <div class="mb-3">
                    <div class="form-check">
                    <input
                        v-model="form.notify_user"
                        class="form-check-input"
                        type="checkbox"
                        id="notify-user"
                        :class="{ 'is-invalid': form.errors.notify_user }"
                    />
                    <label class="form-check-label" for="notify-user">
                        Notify user via email about account creation
                    </label>
                    <div class="invalid-feedback" v-if="form.errors.notify_user">
                        {{ form.errors.notify_user }}
                    </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                  <span v-if="form.processing">Creating...</span>
                  <span v-else>Create User</span>
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
