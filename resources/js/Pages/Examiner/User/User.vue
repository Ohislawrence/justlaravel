<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router, Link, usePage } from '@inertiajs/vue3';
import { defineProps, ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';
import FeatureLimitModal from '@/Components/FeatureLimitModal.vue'

const props = defineProps({
    users: Object,
    designations: Array,
    organization_id: Object,
    examineeLimit: Number,
    examinerLimit: Number,
    currentexamineeCount: Number,
    currentexaminerCount: Number,
});

const showModal = ref(false)
const modalFeature = ref('')
const modalMessage = ref('')

function openFeatureModal(featureName, message) {
  modalFeature.value = featureName
  modalMessage.value = message
  showModal.value = true
}

const page = usePage();
const showDesignationModal = ref(false);
const designationForm = ref({
    name: '',
    organization_id: null
});

const openDesignationModal = () => {
    showDesignationModal.value = true;
};

const createDesignation = () => {
    router.post(route('examiner.users.designations.store'), designationForm.value, {
        preserveScroll: true,
        onSuccess: () => {
            showDesignationModal.value = false;
            designationForm.value.name = '';
        },
        onError: (errors) => {
            console.error('Error creating designation:', errors);
        }
    });
};

const deleteUser = (id) => {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(route('examiner.user.destroy', id), {
            preserveScroll: true
        });
    }
};

function addUser() {
  const canAddExaminee = props.currentexamineeCount < props.examineeLimit
  const canAddExaminer = props.currentexaminerCount < props.examinerLimit

  if (!canAddExaminee && !canAddExaminer) {
    openFeatureModal(
      'Add User',
      'You have reached the user limit. Upgrade your plan to add more users.'
    )
    return
  }

  router.visit(route('examiner.user.create'))
};
</script>

<template>
    <AppLayout title="Users">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                User Management
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Buttons aligned to the right, stacked on small screens -->
                        <div class="flex flex-col sm:flex-row justify-end items-start sm:items-center mb-6 gap-2">
                            <button @click="openDesignationModal"
                                class="w-full sm:w-auto text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                                <i class="bx bx-plus mr-1"></i> New Designation
                            </button>
                            <button @click="addUser"
                                class="w-full sm:w-auto text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                                <i class="bx bx-plus mr-1"></i> Add User
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Designation
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Unique ID
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Created On
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ user.email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span v-if="user.organization_member?.designation"
                                                class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                                {{ user.organization_member.designation.name }}
                                            </span>
                                            <span v-else class="text-sm text-gray-500">-</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                                {{ user.organization_member.unique_code || 'No ID' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ new Date(user.created_at).toLocaleDateString() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                             <!-- Action Buttons - Styled like Quizzes -->
                                             <div class="flex justify-start items-center gap-2">
                                                <Link :href="route('examiner.user.edit', user.id)"
                                                    class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition duration-150 ease-in-out">
                                                    Edit
                                                </Link>
                                                <button @click="deleteUser(user.id)"
                                                    class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition duration-150 ease-in-out">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="users.data.length === 0">
                                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No users found.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination - Updated to match Quizzes style -->
                        <Pagination :links="users.links" class="mt-6" />

                    </div>
                </div>
            </div>
        </div>

        <!-- Designation Creation Modal -->
        <Modal :show="showDesignationModal" @close="showDesignationModal = false" maxWidth="md">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Create New Designation</h2>

                <form @submit.prevent="createDesignation">
                    <div class="mb-4">
                        <InputLabel for="designation" value="Designation Name" />
                        <TextInput id="designation" v-model="designationForm.name" type="text" class="mt-1 block w-full"
                            required autofocus placeholder="e.g. Senior Developer, HR Manager" />
                        <!-- Note: InputError component usage depends on your specific setup -->
                        <!-- <InputError v-if="$page.props.errors.name" :message="$page.props.errors.name" class="mt-2" /> -->
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" @click="showDesignationModal = false"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            :disabled="designationForm.processing">
                            <span v-if="designationForm.processing">Creating...</span>
                            <span v-else>Create Designation</span>
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
        <!-- Modal -->
    <FeatureLimitModal 
      v-model="showModal"
      :featureName="modalFeature"
      :message="modalMessage"
    />
    </AppLayout>
</template>