<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    description: '',
    website: '',
    industry: '',
    is_active: true,
});

const submitForm = () => {
    form.post(route('landlord.organizations.store'));
};
</script>

<template>
    <AppLayout title="Create Organization">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create New Organization
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
                                    Organization Name *
                                </label>
                                <input
                                    v-model="form.name"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="name"
                                    type="text"
                                    required
                                    :class="{ 'border-red-500': form.errors.name }"
                                >
                                <p v-if="form.errors.name" class="text-red-500 text-xs italic">
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                                    Description
                                </label>
                                <textarea
                                    v-model="form.description"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="description"
                                    rows="3"
                                ></textarea>
                            </div>

                            <!-- Website -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="website">
                                    Website
                                </label>
                                <input
                                    v-model="form.website"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="website"
                                    type="url"
                                    placeholder="https://example.com"
                                >
                            </div>

                            <!-- Industry -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="industry">
                                    Industry
                                </label>
                                <input
                                    v-model="form.industry"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="industry"
                                    type="text"
                                    placeholder="Education, Technology, Healthcare, etc."
                                >
                            </div>

                            <!-- Status -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Status
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
                                        Active Organization
                                    </label>
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-6 pt-6 border-t border-gray-200">
                                <Link 
                                    :href="route('landlord.organizations.index')"
                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    class="ml-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                    :disabled="form.processing"
                                >
                                    <span v-if="form.processing">Creating...</span>
                                    <span v-else>Create Organization</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>