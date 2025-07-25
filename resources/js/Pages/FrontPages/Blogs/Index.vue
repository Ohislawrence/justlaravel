<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout2 from '@/Layouts/AppLayout2.vue';
import { defineProps } from 'vue';

defineProps({
    blogs: Object,
});
</script>

<template>
    <Head title="Blog - QuizPortal NG" />
    <AppLayout2>
        <div class="bg-gradient-to-b from-white to-gray-50 py-16 sm:py-20">
            <div class="container mx-auto px-4 sm:px-6">
                <!-- Section Header -->
                <div class="max-w-4xl mx-auto text-center mb-12 sm:mb-16">
                    <div class="mb-4 inline-flex items-center px-4 py-2 bg-emerald-50 text-emerald-700 rounded-full text-sm font-medium border border-emerald-100">
                        📚 Educational Insights
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                        Latest from Our <span class="bg-gradient-to-r from-emerald-600 to-teal-500 bg-clip-text text-transparent">Education Blog</span>
                    </h1>
                    <p class="text-lg sm:text-xl text-gray-600 max-w-3xl mx-auto">
                        Tips, insights, and best practices for Nigerian educators to enhance learning outcomes and student engagement.
                    </p>
                </div>

                <!-- Blog Grid/List -->
                <div v-if="blogs && blogs.data && blogs.data.length > 0" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 max-w-6xl mx-auto">
                    <article v-for="blog in blogs.data" :key="blog.id" class="group bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 hover:border-emerald-200 overflow-hidden">
                        <!-- Placeholder Image or Dynamic Image -->
                        <div class="aspect-video relative overflow-hidden">
                            <!-- Featured Image -->
                            <img 
                                v-if="blog.featured_image" 
                                :src="'/storage/' + blog.featured_image" 
                                :alt="blog.title"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            >
                            
                            <!-- Fallback Gradient Background -->
                            <div 
                                v-else
                                class="w-full h-full bg-gradient-to-br from-emerald-50 to-teal-50 flex items-center justify-center"
                            >
                                <div class="absolute inset-0 opacity-10">
                                    <div class="absolute top-2 left-2 w-16 h-16 bg-emerald-500 rounded-full blur-xl"></div>
                                    <div class="absolute bottom-2 right-2 w-20 h-20 bg-teal-400 rounded-full blur-xl"></div>
                                </div>
                                <div class="relative z-10">
                                    <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center shadow-md">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <!-- Category Tag -->
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span v-for="category in blog.categories" :key="category.id" class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                    {{ category.name }}
                                </span>
                                <span v-if="blog.published_at" class="text-xs text-gray-500">
                                    {{ new Date(blog.published_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) }}
                                </span>
                            </div>
                            <!-- Post Title -->
                            <h2 class="text-lg font-semibold text-gray-900 mb-3 group-hover:text-emerald-600 transition-colors line-clamp-2">
                                <Link :href="route('blogs.show', blog.slug)" class="focus:outline-none focus:underline">
                                    {{ blog.title }}
                                </Link>
                            </h2>
                            <!-- Post Excerpt -->
                            <p v-if="blog.excerpt" class="text-sm text-gray-600 mb-4 line-clamp-3">
                                {{ blog.excerpt }}
                            </p>
                            <!-- Author Info -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <div v-if="blog.user" class="w-6 h-6 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                                        <span class="text-white text-xs font-semibold">
                                            {{ blog.user.name.split(' ').map(n => n[0]).join('').toUpperCase() }}
                                        </span>
                                    </div>
                                    <span v-if="blog.user" class="text-xs text-gray-600">{{ blog.user.name }}</span>
                                </div>
                                <!-- Read More Link -->
                                <Link :href="route('blogs.show', blog.slug)" class="text-xs font-medium text-emerald-600 hover:text-emerald-700 transition-colors group">
                                    Read More
                                    <svg class="w-3 h-3 ml-1 inline-block group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No posts found</h3>
                    <p class="mt-1 text-sm text-gray-500">Check back later for new articles.</p>
                </div>

                <!-- Pagination -->
                <div v-if="blogs.links && blogs.links.length > 3" class="mt-8 flex items-center justify-center">
                    <div class="flex gap-1">
                        <template v-for="(link, index) in blogs.links" :key="index">
                            <Link 
                                :href="link.url || '#'" 
                                class="px-3 py-1 rounded-md"
                                :class="{
                                    'bg-emerald-600 text-white': link.active,
                                    'text-gray-700 hover:bg-gray-100': !link.active && link.url,
                                    'text-gray-400 cursor-not-allowed': !link.url
                                }"
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout2>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>