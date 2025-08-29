<!-- resources/js/Pages/HelpCenter/Article.vue -->
<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout2.vue';
import { ref, onMounted, onUnmounted } from 'vue';

defineProps({
    article: Object,
    relatedArticles: Array,
    categories: Array,
});

const isMobile = ref(false);

const checkScreenSize = () => {
    isMobile.value = window.innerWidth < 768;
};

onMounted(() => {
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
});

onUnmounted(() => {
    window.removeEventListener('resize', checkScreenSize);
});
</script>

<template>
    <Head :title="article.title" />
    <AppLayout>
        <div class="py-12 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Mobile: Main content first -->
                <div class="md:hidden">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                        <nav class="flex mb-6" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                                <li>
                                    <Link :href="route('help.index')" class="text-emerald-600 hover:text-emerald-800">
                                        Help Center
                                    </Link>
                                </li>
                                <li>
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                        <Link 
                                            v-if="article.category"
                                            :href="route('help.category', article.category.slug)" 
                                            class="ml-1 text-emerald-600 hover:text-emerald-800 md:ml-2"
                                        >
                                            {{ article.category.name }}
                                        </Link>
                                    </div>
                                </li>
                                <li aria-current="page">
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                        <span class="ml-1 text-gray-500 md:ml-2">{{ article.title }}</span>
                                    </div>
                                </li>
                            </ol>
                        </nav>

                        <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ article.title }}</h1>
                        
                        <div class="prose max-w-none" v-html="article.content"></div>

                        <div class="mt-12 pt-6 border-t border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Was this article helpful?</h3>
                            <div class="flex space-x-4">
                                <button class="px-4 py-2 bg-emerald-100 text-emerald-800 rounded-md hover:bg-emerald-200">
                                    Yes
                                </button>
                                <button class="px-4 py-2 bg-gray-100 text-gray-800 rounded-md hover:bg-gray-200">
                                    No
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Categories card after main content on mobile -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                        <h3 class="font-bold text-gray-900 mb-4">Help Categories</h3>
                        <ul class="space-y-2">
                            <li v-for="category in categories" :key="category.id">
                                <Link :href="route('help.category', category.slug)" 
                                      class="text-gray-600 hover:text-emerald-600">
                                    {{ category.name }}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <div v-if="relatedArticles.length" class="mt-4">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Related Articles</h3>
                        <div class="grid gap-4">
                            <Link v-for="related in relatedArticles" :key="related.id"
                                  :href="route('help.article', [related.category.slug, related.slug])"
                                  class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition border border-gray-200">
                                <h4 class="font-medium text-emerald-600">{{ related.title }}</h4>
                                <p class="text-sm text-gray-500 mt-1">{{ related.category.name }}</p>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Desktop: Standard layout -->
                <div class="hidden md:grid md:grid-cols-4 gap-8">
                    <!-- Sidebar -->
                    <div class="md:col-span-1">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-6">
                            <h3 class="font-bold text-gray-900 mb-4">Help Categories</h3>
                            <ul class="space-y-2">
                                <li v-for="category in categories" :key="category.id">
                                    <Link :href="route('help.category', category.slug)" 
                                          class="text-gray-600 hover:text-emerald-600">
                                        {{ category.name }}
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="md:col-span-3">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <nav class="flex mb-6" aria-label="Breadcrumb">
                                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                                    <li>
                                        <Link :href="route('help.index')" class="text-emerald-600 hover:text-emerald-800">
                                            Help Center
                                        </Link>
                                    </li>
                                    <li>
                                        <div class="flex items-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                            <Link 
                                                v-if="article.category"
                                                :href="route('help.category', article.category.slug)" 
                                                class="ml-1 text-emerald-600 hover:text-emerald-800 md:ml-2"
                                            >
                                                {{ article.category.name }}
                                            </Link>
                                        </div>
                                    </li>
                                    <li aria-current="page">
                                        <div class="flex items-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                            <span class="ml-1 text-gray-500 md:ml-2">{{ article.title }}</span>
                                        </div>
                                    </li>
                                </ol>
                            </nav>

                            <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ article.title }}</h1>
                            
                            <div class="prose max-w-none" v-html="article.content"></div>

                            <div class="mt-12 pt-6 border-t border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Was this article helpful?</h3>
                                <div class="flex space-x-4">
                                    <button class="px-4 py-2 bg-emerald-100 text-emerald-800 rounded-md hover:bg-emerald-200">
                                        Yes
                                    </button>
                                    <button class="px-4 py-2 bg-gray-100 text-gray-800 rounded-md hover:bg-gray-200">
                                        No
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div v-if="relatedArticles.length" class="mt-8">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">Related Articles</h3>
                            <div class="grid md:grid-cols-2 gap-4">
                                <Link v-for="related in relatedArticles" :key="related.id"
                                      :href="route('help.article', [related.category.slug, related.slug])"
                                      class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition border border-gray-200">
                                    <h4 class="font-medium text-emerald-600">{{ related.title }}</h4>
                                    <p class="text-sm text-gray-500 mt-1">{{ related.category.name }}</p>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>