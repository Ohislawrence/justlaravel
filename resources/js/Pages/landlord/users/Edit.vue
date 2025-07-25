<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    organization: Object,
    user: Object,
    designations: Array,
    groups: Array,
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    designation_id: props.user.organization_member.designation_id,
    unique_code: props.user.organization_member.unique_code,
    groups: props.user.groups.map(group => group.id),
    is_active: props.user.is_active,
});

const submitForm = () => {
    form.put(`/landlord/organizations/${props.organization.id}/users/${props.user.id}`);
};
</script>

<template>
    <AppLayout :title="`Edit Examiner - ${user.name}`">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Examiner - {{ user.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submitForm">
                            <!-- Name -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                                    Full Name
                                </label>
                                <input
                                    v-model="form.name"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="name"
                                    type="text"
                                    :class="{ 'border-red-500': form.errors.name }"
                                >
                                <p v-if="form.errors.name" class="text-red-500 text-xs italic">
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                                    Email
                                </label>
                                <input
                                    v-model="form.email"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="email"
                                    type="email"
                                    :class="{ 'border-red-500': form.errors.email }"
                                >
                                <p v-if="form.errors.email" class="text-red-500 text-xs italic">
                                    {{ form.errors.email }}
                                </p>
                            </div>

                            <!-- Unique Code -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="unique_code">
                                    Employee/Staff ID
                                </label>
                                <input
                                    v-model="form.unique_code"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="unique_code"
                                    type="text"
                                    :class="{ 'border-red-500': form.errors.unique_code }"
                                >
                                <p v-if="form.errors.unique_code" class="text-red-500 text-xs italic">
                                    {{ form.errors.unique_code }}
                                </p>
                            </div>

                            <!-- Designation -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="designation_id">
                                    Designation
                                </label>
                                <select
                                    v-model="form.designation_id"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="designation_id"
                                    :class="{ 'border-red-500': form.errors.designation_id }"
                                >
                                    <option value="">Select Designation</option>
                                    <option 
                                        v-for="designation in designations" 
                                        :key="designation.id" 
                                        :value="designation.id"
                                    >
                                        {{ designation.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.designation_id" class="text-red-500 text-xs italic">
                                    {{ form.errors.designation_id }}
                                </p>
                            </div>

                            <!-- Groups -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="groups">
                                    Assign to Groups
                                </label>
                                <select
                                    v-model="form.groups"
                                    multiple
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="groups"
                                    :class="{ 'border-red-500': form.errors.groups }"
                                >
                                    <option v-for="group in groups" :key="group.id" :value="group.id">
                                        {{ group.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.groups" class="text-red-500 text-xs italic">
                                    {{ form.errors.groups }}
                                </p>
                                <p class="text-gray-600 text-xs italic mt-1">Hold Ctrl/Cmd to select multiple groups</p>
                            </div>

                            <!-- Status -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Account Status
                                </label>
                                <div class="flex items-center">
                                    <input
                                        v-model="form.is_active"
                                        id="is_active"
                                        name="is_active"
                                        type="checkbox"
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                    >
                                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                        Active Account
                                    </label>
                                </div>
                                <p v-if="form.errors.is_active" class="text-red-500 text-xs italic">
                                    {{ form.errors.is_active }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                                <Link 
                                    :href="route('landlord.organizations.users.show', { organization: organization.id, user: user.id })"
                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                    :disabled="form.processing"
                                >
                                    <span v-if="form.processing">Updating...</span>
                                    <span v-else>Update Examiner</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>