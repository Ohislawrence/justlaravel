<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps, ref } from 'vue';
import FeatureLimitModal from '@/Components/FeatureLimitModal.vue';
import { router, Link, usePage } from '@inertiajs/vue3';

// Props
const props = defineProps({
  groups: Object,
  designations: Array,
  examineeLimit: Number,
  examinerLimit: Number,
  currentexamineeCount: Number,
  currentexaminerCount: Number,
});

// Inertia form object
const form = useForm({
  name: '',
  email: '',
  unique_code: '',
  password: '',
  user_type: '',
  designation_id: null, 
  groups: [],
  notify_user: false,
});

const showModal = ref(false)
const modalFeature = ref('')
const modalMessage = ref('')

function openFeatureModal(featureName, message) {
  modalFeature.value = featureName
  modalMessage.value = message
  showModal.value = true
}

// Submit handler
const submitForm = () => {
  const canAddExaminee = props.currentexamineeCount < props.examineeLimit;
  const canAddExaminer = props.currentexaminerCount < props.examinerLimit;

  if (form.user_type == 'examinee' && !canAddExaminee) {
    openFeatureModal(
      'Add Examinee',
      'You have reached the examinee limit. Upgrade your plan to add more examinees.'
    );
    return;
  }

  if (form.user_type == 'examiner' && !canAddExaminer) {
    openFeatureModal(
      'Add Examiner',
      'You have reached the examiner limit. Upgrade your plan to add more examiners.'
    );
    return;
  }
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

                <div class="mb-3">
                  <label class="form-label" for="unique_code">Identification Number</label>
                  <input
                    v-model="form.unique_code"
                    type="text"
                    class="form-control"
                    id="unique_code"
                    placeholder="U09CH1767"
                    :class="{ 'is-invalid': form.errors.unique_code }"
                  />
                  <div class="invalid-feedback" v-if="form.errors.unique_code">{{ form.errors.unique_code }}</div>
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
                    <option value="examiner">Examiner (full access)</option>
                    <option value="instructor">Instructor (staff/teacher)</option>
                    <option value="examinee">Examinee (student/candidate)</option>
                  </select>
                  <div class="invalid-feedback" v-if="form.errors.user_type">{{ form.errors.user_type }}</div>
                </div>

                <!-- Designation Dropdown -->
                <div class="mb-3">
                  <label for="designation_id" class="form-label">Designation</label>
                  <select
                    v-model="form.designation_id"
                    class="form-select"
                    id="designation_id"
                    :class="{ 'is-invalid': form.errors.designation_id }"
                  >
                    <option disabled value="">Select Designation</option>
                    <option 
                      v-for="designation in designations" 
                      :key="designation.id" 
                      :value="designation.id"
                    >
                      {{ designation.name }}
                    </option>
                  </select>
                  <div class="invalid-feedback" v-if="form.errors.designation_id">
                    {{ form.errors.designation_id }}
                  </div>
                </div>

                <!-- Groups -->
                <div class="mb-3">
                  <label for="groups" class="form-label">Assign Group</label>
                  <select
                    v-model="form.group"
                    class="form-select"
                    id="groups"
                    :class="{ 'is-invalid': form.errors.group }"
                  >
                    <option disabled value="">-- Select a group --</option>
                    <option v-for="group in props.groups.data" :key="group.id" :value="group.id">
                      {{ group.name }}
                    </option>
                  </select>
                  <div class="invalid-feedback" v-if="form.errors.group">
                    {{ form.errors.group }}
                  </div>
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
                <button type="submit" class="btn btn-success" :disabled="form.processing">
                  <span v-if="form.processing">Creating...</span>
                  <span v-else>Create User</span>
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <FeatureLimitModal 
      v-model="showModal"
      :featureName="modalFeature"
      :message="modalMessage"
    />
  </AppLayout>
</template>

<style scoped>
/* Custom styles to enhance the green theme */
.btn-success {
  background-color: #10B981;
  border-color: #10B981;
  color: white;
}

.btn-success:hover {
  background-color: #059669;
  border-color: #059669;
}

.btn-success:focus {
  box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
}

.form-control:focus {
  border-color: #10B981;
  box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
}

.form-select:focus {
  border-color: #10B981;
  box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
}

.form-check-input:checked {
  background-color: #10B981;
  border-color: #10B981;
}

.card-header {
  background-color: #ecfdf5;
  border-bottom: 1px solid #d1fae5;
}

.card {
  border: 1px solid #d1fae5;
  border-radius: 0.375rem;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.is-invalid {
  border-color: #ef4444;
}

.invalid-feedback {
  color: #ef4444;
}

/* Green-themed select dropdown indicator */
.form-select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2310B981' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
}
</style>