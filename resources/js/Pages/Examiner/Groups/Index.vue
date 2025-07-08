<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router, Link  } from '@inertiajs/vue3';

const props = defineProps({
    organization: Object,
    groups: Object,
});

const deleteGroup = (group) => {
    if (confirm('Are you sure you want to delete this group?')) {
        router.delete(route('examiner.groups.destroy', {
            organization: props.organization.slug,
            group: group.id
        }));
    }
};
</script>

<template>
    <AppLayout title="Groups">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Groups in {{ organization.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between mb-6">
                            <h3 class="text-lg font-medium">All Groups</h3>
                            <Link :href="route('examiner.groups.create', organization.slug)"
                                  class="btn btn-primary">
                                Create New Group
                            </Link>
                        </div>

                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">Name</th>
                                    <th class="px-6 py-3 text-left">Members</th>
                                    <th class="px-6 py-3 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="group in groups.data" :key="group.id">
                                    <td class="px-6 py-4">
                                        <Link :href="route('examiner.groups.show', {
                                            organization: organization.slug,
                                            group: group.id
                                        })" class="text-blue-600 hover:underline">
                                            {{ group.name }}
                                        </Link>
                                    </td>
                                    <td class="px-6 py-4">{{ group.members_count }}</td>
                                    <td class="px-6 py-4 space-x-2">
                                        <Link :href="route('examiner.groups.show', {
                                            organization: organization.slug,
                                            group: group.id
                                        })" class="btn btn-sm btn-outline">
                                            Manage
                                        </Link>
                                        <button @click="deleteGroup(group)" 
                                                class="btn btn-sm btn-danger">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <Pagination :links="groups.links" class="mt-4" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>