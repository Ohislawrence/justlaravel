<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import RichtextEditorNormal from '@/Components/RichtextEditorNormal.vue';

defineProps({
    articles: Array,
    categories: Array
});

const form = useForm({
    title: '',
    slug: '',
    content: '',
    category_id: '',
    is_featured: false,
    is_published: true
});

const editingArticle = ref(null);

const editArticle = (article) => {
    editingArticle.value = article;
    form.title = article.title;
    form.content = article.content;
    form.category_id = article.category_id;
    form.is_featured = article.is_featured;
    form.is_published = article.is_published;
};

const submitForm = () => {
    if (editingArticle.value) {
        form.put(route('landlord.articles.update', editingArticle.value.id), {
            onSuccess: () => {
                editingArticle.value = null;
                form.reset();
            }
        });
    } else {
        form.post(route('landlord.articles.store'), {
            onSuccess: () => form.reset()
        });
    }
};

const deleteArticle = (id) => {
    if (confirm('Are you sure you want to delete this article?')) {
        Inertia.delete(route('landlord.articles.destroy', id));
    }
};
</script>

<template>
    <Head title="Manage Help Articles" />
    <AdminLayout>
        <div class="py-6 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-2xl font-bold text-gray-900">Help Center Articles</h1>
                    <Link :href="route('landlord.categories.index')" class="text-emerald-600 hover:text-emerald-800">
                        Manage Categories →
                    </Link>
                </div>

                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ editingArticle ? 'Edit Article' : 'Add New Article' }}
                        </h2>
                        <form @submit.prevent="submitForm" class="mt-4 space-y-4">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                                    <input v-model="form.title" type="text" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                                    <select v-model="form.category_id" id="category_id" class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-emerald-500 focus:outline-none focus:ring-emerald-500 sm:text-sm">
                                        <option value="">Select a category</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                </select>
                                </div>
                            </div>
                            <div>
                            <label class="block text-sm font-medium text-gray-700">Content</label>
                            <RichtextEditorNormal 
                                v-model="form.content" 
                                :error="form.errors.content"
                                class="mt-1"
                            />
                            </div>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="flex items-center">
                                    <input v-model="form.is_featured" type="checkbox" id="is_featured" class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                    <label for="is_featured" class="ml-2 block text-sm text-gray-900">Featured Article</label>
                                </div>
                                <div class="flex items-center">
                                    <input v-model="form.is_published" type="checkbox" id="is_published" class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                    <label for="is_published" class="ml-2 block text-sm text-gray-900">Published</label>
                                </div>
                            </div>
                            <div class="flex justify-end space-x-3">
                                <button v-if="editingArticle" @click="editingArticle = null; form.reset()" type="button" class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                                    Cancel
                                </button>
                                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-emerald-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2" :disabled="form.processing">
                                    {{ editingArticle ? 'Update' : 'Create' }} Article
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="divide-y divide-gray-200">
                        <div v-for="article in articles" :key="article.id" class="p-4 flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ article.title }}</h3>
                                <div class="mt-1 flex space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ article.category.name }}
                                    </span>
                                    <span v-if="article.is_featured" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        Featured
                                    </span>
                                    <span v-if="!article.is_published" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Draft
                                    </span>
                                </div>
                                <p class="mt-2 text-sm text-gray-600 line-clamp-2">{{ article.content }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <button @click="editArticle(article)" class="text-emerald-600 hover:text-emerald-900">
                                    Edit
                                </button>
                                <button @click="deleteArticle(article.id)" class="text-red-600 hover:text-red-900">
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