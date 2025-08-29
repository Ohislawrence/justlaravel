<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout2.vue';

defineProps({
    results: Array,
    query: String,
    categories: Array,
});
</script>

<template>
    <Head title="Search Results" />
    <AppLayout>
        <div class="py-12 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Mobile layout: main content first -->
                <div class="md:hidden">
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-gray-900 mb-6">Search Results for "{{ query }}"</h1>
                        
                        <div v-if="results.length">
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 divide-y divide-gray-200">
                                <Link v-for="result in results" :key="result.id"
                                      :href="route('help.article', [result.category.slug, result.slug])"
                                      class="block px-6 py-4 hover:bg-gray-50 transition">
                                    <h3 class="text-lg font-medium text-emerald-600">{{ result.title }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ result.category.name }}</p>
                                    <p class="text-gray-600 mt-2 line-clamp-2" v-html="result.excerpt"></p>
                                </Link>
                            </div>
                        </div>

                        <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                            <p class="text-gray-600">No results found for "{{ query }}"</p>
                            <Link :href="route('help.index')" class="mt-4 inline-flex items-center text-emerald-600 hover:text-emerald-800">
                                Back to Help Center
                            </Link>
                        </div>
                    </div>
                    
                    <!-- Categories card after main content on mobile -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
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

                <!-- Desktop layout: standard sidebar -->
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
                        <h1 class="text-2xl font-bold text-gray-900 mb-6">Search Results for "{{ query }}"</h1>
                        
                        <div v-if="results.length">
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 divide-y divide-gray-200">
                                <Link v-for="result in results" :key="result.id"
                                      :href="route('help.article', [result.category.slug, result.slug])"
                                      class="block px-6 py-4 hover:bg-gray-50 transition">
                                    <h3 class="text-lg font-medium text-emerald-600">{{ result.title }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ result.category.name }}</p>
                                    <p class="text-gray-600 mt-2 line-clamp-2" v-html="result.excerpt"></p>
                                </Link>
                            </div>
                        </div>

                        <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                            <p class="text-gray-600">No results found for "{{ query }}"</p>
                            <Link :href="route('help.index')" class="mt-4 inline-flex items-center text-emerald-600 hover:text-emerald-800">
                                Back to Help Center
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>