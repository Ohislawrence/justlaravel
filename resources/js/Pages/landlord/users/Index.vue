<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { defineProps } from 'vue';

defineProps({
    organization: Object,
    users: Object,
    designations: Array,
});

const deleteUser = (userId) => {
    if (confirm('Are you sure you want to delete this examiner? This action cannot be undone.')) {
        router.delete(`/landlord/organizations/${props.organization.id}/users/${userId}`, {
            preserveScroll: true,
            onSuccess: () => {
                // Optional: Show success message
            },
        });
    }
};
</script>

<template>
    <AppLayout title="Organization Examiners">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Examiners in {{ organization.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium">Manage Examiners</h3>
                            <Link 
                                :href="route('landlord.organizations.users.create', organization.id)"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Add New Examiner
                            </Link>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Designation
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="user in users.data" :key="user.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full" :src="user.profile_photo_url" alt="">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ user.name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        ID: {{ user.organization_member.unique_code || 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ user.email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span v-if="user.organization_member.designation" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ user.organization_member.designation.name }}
                                            </span>
                                            <span v-else class="text-sm text-gray-500">Not assigned</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                                :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                                {{ user.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <Link 
                                                :href="route('landlord.organizations.users.show', { organization: organization.id, user: user.id })"
                                                class="text-blue-600 hover:text-blue-900 mr-3"
                                            >
                                                View
                                            </Link>
                                            <Link 
                                                :href="route('landlord.organizations.users.edit', { organization: organization.id, user: user.id })"
                                                class="text-indigo-600 hover:text-indigo-900 mr-3"
                                            >
                                                Edit
                                            </Link>
                                            <button 
                                                @click="deleteUser(user.id)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="users.data.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No examiners found in this organization.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6" v-if="users.links.length > 3">
                            <nav class="flex items-center justify-between">
                                <div class="flex-1 flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-700">
                                            Showing
                                            <span class="font-medium">{{ users.from }}</span>
                                            to
                                            <span class="font-medium">{{ users.to }}</span>
                                            of
                                            <span class="font-medium">{{ users.total }}</span>
                                            results
                                        </p>
                                    </div>
                                    <div>
                                        <ul class="flex items-center space-x-2">
                                            <li v-for="(link, key) in users.links" :key="key">
                                                <Link 
                                                    :href="link.url || '#'" 
                                                    class="px-3 py-1 rounded-md text-sm font-medium"
                                                    :class="{
                                                        'bg-blue-50 text-blue-600': link.active,
                                                        'text-gray-500 hover:text-gray-700': !link.active && link.url,
                                                        'text-gray-400 cursor-not-allowed': !link.url
                                                    }"
                                                    v-html="link.label"
                                                />
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>