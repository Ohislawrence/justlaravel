<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout2.vue';

defineProps({
    category: Object,
    articles: Array,
    categories: Array,
});
</script>

<template>
    <Head :title="`${category.name} - Help Center`" />
    <AppLayout>
        <div class="py-12 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-4 gap-8">
                    <!-- Sidebar -->
                    <div class="md:col-span-1">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-6">
                            <h3 class="font-bold text-gray-900 mb-4">Help Categories</h3>
                            <ul class="space-y-2">
                                <li v-for="cat in categories" :key="cat.id">
                                    <Link 
                                        :href="route('help.category', cat.slug)" 
                                        class="text-gray-600 hover:text-emerald-600"
                                        :class="{ 'text-emerald-600 font-medium': cat.id === category.id }"
                                    >
                                        {{ cat.name }}
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="md:col-span-3">
                        <nav class="flex mb-6" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                                <li>
                                    <Link :href="route('help.index')" class="text-emerald-600 hover:text-emerald-800">
                                        Help Center
                                    </Link>
                                </li>
                                <li aria-current="page">
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="ml-1 text-gray-500 md:ml-2">{{ category.name }}</span>
                                    </div>
                                </li>
                            </ol>
                        </nav>

                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                            <div class="p-6 border-b border-gray-200">
                                <h1 class="text-2xl font-bold text-gray-900">{{ category.name }}</h1>
                                <p v-if="category.description" class="text-gray-600 mt-2">{{ category.description }}</p>
                            </div>

                            <div v-if="articles.length > 0" class="divide-y divide-gray-200">
                                <Link 
                                    v-for="article in articles" 
                                    :key="article.id"
                                    :href="route('help.article', [category.slug, article.slug])"
                                    class="block px-6 py-4 hover:bg-gray-50 transition"
                                >
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-900 font-medium">{{ article.title }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <p v-if="article.excerpt" class="text-gray-600 text-sm mt-1">{{ article.excerpt }}</p>
                                </Link>
                            </div>

                            <div v-else class="p-6 text-center text-gray-500">
                                No articles found in this category.
                            </div>
                        </div>

                        <div class="mt-6 text-center">
                            <Link 
                                :href="route('help.index')" 
                                class="inline-flex items-center text-emerald-600 hover:text-emerald-800"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                                </svg>
                                Back to Help Center
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>