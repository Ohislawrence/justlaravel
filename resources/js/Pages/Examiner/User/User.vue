<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router, Link  } from '@inertiajs/vue3';
import { defineProps } from 'vue';

defineProps({
    users: Object
});



const deleteUser = (id) => {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(route('examiner.user.destroy', id));
    }
};
</script>

<template>
    <AppLayout title="Users">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">User Management</h5>
                    <Link :href="route('examiner.user.create')" class="btn btn-primary">Add User</Link>
                </div>

                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
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
                                    {{ user.roles[0]?.name || 'No role' }}
                                </td>
                                <td>{{ new Date(user.created_at).toLocaleDateString() }}</td>
                                <td>
                                    <span class="badge bg-label-primary me-1">Active</span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button
                                            type="button"
                                            class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown"
                                        >
                                        
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <Link :href="route('examiner.user.edit', user.id)">
                                                <i class="bx bx-edit-alt me-1"></i> Edit</Link>
                                            <a href="javascript:void(0);" class="dropdown-item" @click="deleteUser(user.id)">
                                                <i class="bx bx-trash me-1"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="users.data.length === 0">
                                <td colspan="5" class="text-center">No users found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer">
                    <div class="d-flex justify-content-end">
                        <nav v-if="users.links.length > 3">
                            <ul class="pagination mb-0">
                                <li
                                    v-for="(link, key) in users.links"
                                    :key="key"
                                    class="page-item"
                                    :class="{ active: link.active, disabled: !link.url }"
                                >
                                    <ResponsiveNavLink
                                        class="page-link"
                                        :href="link.url || '#'"
                                        v-html="link.label"
                                    />
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
