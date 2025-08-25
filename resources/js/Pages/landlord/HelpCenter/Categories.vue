<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';

defineProps({
    categories: Array
});

const form = useForm({
    name: '',
    description: '',
    order: 0
});

const editingCategory = ref(null);

const editCategory = (category) => {
    editingCategory.value = category;
    form.name = category.name;
    form.description = category.description;
    form.order = category.order;
};

const submitForm = () => {
    if (editingCategory.value) {
        form.put(route('landlord.categories.update', editingCategory.value.id), {
            onSuccess: () => {
                editingCategory.value = null;
                form.reset();
            }
        });
    } else {
        form.post(route('landlord.categories.store'), {
            onSuccess: () => form.reset()
        });
    }
};

const deleteCategory = (id) => {
    if (confirm('Are you sure you want to delete this category?')) {
        Inertia.delete(route('landlord.categories.destroy', id));
    }
};
</script>

<template>
    <Head title="Manage Help Categories" />
    <AdminLayout>
        <div class="py-6 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-2xl font-bold text-gray-900">Help Center Categories</h1>
                    <Link :href="route('landlord.articles.index')" class="text-emerald-600 hover:text-emerald-800">
                        Manage Articles →
                    </Link>
                </div>

                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ editingCategory ? 'Edit Category' : 'Add New Category' }}
                        </h2>
                        <form @submit.prevent="submitForm" class="mt-4 space-y-4">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                    <input v-model="form.name" type="text" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="order" class="block text-sm font-medium text-gray-700">Order</label>
                                    <input v-model.number="form.order" type="number" id="order" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                </div>
                            </div>
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea v-model="form.description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"></textarea>
                            </div>
                            <div class="flex justify-end space-x-3">
                                <button v-if="editingCategory" @click="editingCategory = null; form.reset()" type="button" class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                                    Cancel
                                </button>
                                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-emerald-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2" :disabled="form.processing">
                                    {{ editingCategory ? 'Update' : 'Create' }} Category
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="divide-y divide-gray-200">
                        <div v-for="category in categories" :key="category.id" class="p-4 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ category.name }}</h3>
                                <p v-if="category.description" class="text-sm text-gray-500 mt-1">{{ category.description }}</p>
                                <div class="mt-2 flex space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Order: {{ category.order }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Articles: {{ category.articles_count || 0 }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button @click="editCategory(category)" class="text-emerald-600 hover:text-emerald-900">
                                    Edit
                                </button>
                                <button @click="deleteCategory(category.id)" class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>