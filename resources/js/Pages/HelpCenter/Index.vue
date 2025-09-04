<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout2.vue';
import { router } from '@inertiajs/vue3';

defineProps({
    categories: Array,
    featuredArticles: Array,
    meta: Object,
});

const searchQuery = ref('');

const performSearch = () => {
    if (searchQuery.value.trim()) {
        router.get(route('help.search'), { 
            query: searchQuery.value 
        });
    }
};
</script>

<template>
    <Head title="Help Center" />
    <AppLayout>
        <div class="py-12 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">How can we help?</h1>
                    <div class="max-w-2xl mx-auto">
                        <div class="relative">
                            <input type="text" v-model="searchQuery" @keyup.enter="performSearch" 
                                placeholder="Search help articles..." 
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <button class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div v-if="featuredArticles.length" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Featured Articles</h2>
                    <div class="grid md:grid-cols-3 gap-6">
                        <Link v-for="article in featuredArticles" :key="article.id" 
                              :href="route('help.article', [article.category.slug, article.slug])"
                              class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition border border-gray-200">
                            <h3 class="text-lg font-semibold text-emerald-600 mb-2">{{ article.title }}</h3>
                            <p class="text-gray-600 text-sm">{{ article.category.name }}</p>
                        </Link>
                    </div>
                </div>

                <div class="space-y-8">
                    <div v-for="category in categories" :key="category.id">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ category.name }}</h2>
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 divide-y divide-gray-200">
                            <Link v-for="article in category.articles" :key="article.id" 
                                  :href="route('help.article', [category.slug, article.slug])"
                                  class="block px-6 py-4 hover:bg-gray-50 transition">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-900 font-medium">{{ article.title }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>