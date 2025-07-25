<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router, Link, usePage } from '@inertiajs/vue3';
import { defineProps, ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Modal from '@/Components/Modal.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

defineProps({
    users: Object,
    designations: Array, // Add this prop if you're passing designations from backend
    organization_id: Object,
});

const page = usePage();
const showDesignationModal = ref(false);
const designationForm = ref({
    name: '',
    organization_id: null // Will be set when modal opens
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
</script>

<template>
    <AppLayout title="Users">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">User Management</h5>
                    </div>
                    <div class="d-flex gap-2">
                        <button @click="openDesignationModal" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i> New Designation
                        </button>
                        <Link :href="route('examiner.user.create')" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i> Add User
                        </Link>
                    </div>
                </div>

                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Designation</th>
                                <th>Unique ID</th>
                                <th>Created On</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr v-for="user in users.data" :key="user.id">
                                <td><strong>{{ user.name }}</strong></td>
                                <td>{{ user.email }}</td>
                                <td>
                                    <span v-if="user.organization_member?.designation" class="badge bg-label-info">
                                        {{ user.organization_member.designation.name }}
                                    </span>
                                    <span v-else class="text-muted">-</span>
                                </td>
                                <td>
                                    <span class="badge bg-label-primary">
                                        {{ user.organization_member.unique_code || 'No ID' }}
                                    </span>
                                </td>
                                <td>{{ new Date(user.created_at).toLocaleDateString() }}</td>
                                <td>
                                    <span class="badge bg-label-success me-1">Active</span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <Link :href="route('examiner.user.edit', user.id)" class="dropdown-item">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </Link>
                                            <a href="javascript:void(0);" class="dropdown-item" @click="deleteUser(user.id)">
                                                <i class="bx bx-trash me-1"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="users.data.length === 0">
                                <td colspan="7" class="text-center py-4">No users found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer" v-if="users.links.length > 3">
                    <div class="d-flex justify-content-end">
                        <nav>
                            <ul class="pagination mb-0">
                                <li v-for="(link, key) in users.links" :key="key" class="page-item"
                                    :class="{ active: link.active, disabled: !link.url }">
                                    <ResponsiveNavLink class="page-link" :href="link.url || '#'" v-html="link.label" />
                                </li>
                            </ul>
                        </nav>
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
                        <TextInput
                            id="designation"
                            v-model="designationForm.name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                            autofocus
                            placeholder="e.g. Senior Developer, HR Manager"
                        />
                        <InputError v-if="$page.props.errors.name" :message="$page.props.errors.name" class="mt-2" />
                    </div>
                    
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" @click="showDesignationModal = false" 
                            class="btn btn-secondary">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" :disabled="designationForm.processing">
                            <span v-if="designationForm.processing">Creating...</span>
                            <span v-else>Create Designation</span>
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AppLayout>
</template>