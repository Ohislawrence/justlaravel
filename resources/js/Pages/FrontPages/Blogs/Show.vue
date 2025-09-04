<!-- resources/js/Pages/Blog/Show.vue -->
<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout2 from '@/Layouts/AppLayout2.vue';
// import { defineProps } from 'vue'; // defineProps is auto-imported in <script setup>

defineProps({
    blog: Object,
    relatedBlogs: Object,
    meta: Object,
});
</script>

<template>
    <Head :title="`${blog.title} - Blog`">
        <meta name="description" :content="meta.description">
        <meta property="og:title" :content="meta.title">
        <meta property="og:description" :content="meta.description">
        <meta property="og:image" :content="meta.image">
        <meta property="og:url" :content="meta.url">
        <meta property="og:type" content="website">

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" :content="meta.title">
        <meta name="twitter:description" :content="meta.description">
        <meta name="twitter:image" :content="meta.image">
    </Head>
    <AppLayout2>
        <div class="bg-gradient-to-b from-white to-gray-50 py-16 sm:py-20">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-3xl mx-auto">
                    <!-- Back to Blog Link -->
                    <div class="mb-6">
                        <Link :href="route('blogs.index')" class="inline-flex items-center text-sm font-medium text-emerald-600 hover:text-emerald-700 transition-colors group">
                            <svg class="w-4 h-4 mr-1 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Blog
                        </Link>
                    </div>

                    <!-- Featured Image (Optional) -->
                    <div v-if="'/storage/' + blog.featured_image" class="mb-8 rounded-2xl overflow-hidden shadow-md">
                        <img :src="'/storage/' + blog.featured_image" :alt="`Featured image for ${blog.title}`" class="w-full h-auto object-cover">
                    </div>

                    <!-- Meta Information -->
                    <div class="flex flex-wrap items-center gap-4 mb-6 text-sm text-gray-500">
                        <div v-if="blog.category" class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full font-medium">
                            {{ blog.category.name }}
                        </div>
                        <span v-if="blog.published_at">
                            {{ new Date(blog.published_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'long', year: 'numeric' }) }}
                        </span>
                        <span v-if="blog.reading_time">{{ blog.reading_time }}</span>
                    </div>

                    <!-- Post Title -->
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-6 leading-tight">
                        {{ blog.title }}
                    </h1>

                    <!-- Author Information -->
                    <div v-if="blog.author" class="flex items-center mb-10 p-4 bg-white rounded-xl border border-gray-100 shadow-sm">
                        <!-- Author Avatar -->
                        <div v-if="blog.author.avatar_url" class="flex-shrink-0 mr-4">
                            <img :src="blog.author.avatar_url" :alt="`${blog.author.name}'s avatar`" class="w-12 h-12 rounded-full object-cover">
                        </div>
                        <div v-else class="flex-shrink-0 mr-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full flex items-center justify-center text-white font-bold">
                                {{ blog.author.name.split(' ').map(n => n[0]).join('').toUpperCase() }}
                            </div>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">{{ blog.author.name }}</div>
                            <div v-if="blog.author.bio" class="text-sm text-gray-600 mt-1">{{ blog.author.bio }}</div>
                        </div>
                    </div>

                    <!-- Post Content -->
                    <!-- Important: Use v-html carefully. Ensure content is sanitized on the backend. -->
                    <div class="prose prose-emerald max-w-none prose-lg prose-headings:font-bold prose-a:text-emerald-600 prose-a:no-underline hover:prose-a:underline">
                        <div v-html="blog.content"></div>
                    </div>

                    <!-- Related Blogs Section -->
                    <div v-if="relatedBlogs && relatedBlogs.length > 0" class="mt-16 pt-10 border-t border-gray-200">
                        <h3 class="text-2xl font-bold text-gray-900 mb-8 text-center">Related Articles</h3>
                        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <article v-for="relatedBlog in relatedBlogs" :key="relatedBlog.id" class="group bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 hover:border-emerald-200 overflow-hidden">
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
                                <div class="p-5">
                                    <!-- Category Tag -->
                                    <div class="flex items-center gap-2 mb-2">
                                        <!-- Example category display, adjust based on your data structure -->
                                        <span v-if="relatedBlog.category" class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                            {{ relatedBlog.category.name }}
                                        </span>
                                        <!-- Published Date -->
                                        <span v-if="relatedBlog.published_at" class="text-xs text-gray-500">
                                            {{ new Date(relatedBlog.published_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short' }) }}
                                        </span>
                                    </div>
                                    <!-- Post Title -->
                                    <h4 class="text-base font-semibold text-gray-900 mb-2 group-hover:text-emerald-600 transition-colors line-clamp-2">
                                        <Link :href="route('blogs.show', relatedBlog.slug)" class="focus:outline-none focus:underline">
                                            {{ relatedBlog.title }}
                                        </Link>
                                    </h4>
                                    <!-- Author Info -->
                                    <div class="flex items-center mt-3">
                                        <!-- Example author display, adjust based on your data structure -->
                                        <div v-if="relatedBlog.author" class="w-5 h-5 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                                            <span class="text-white text-[0.6rem] font-semibold">
                                                {{ relatedBlog.author.name.split(' ').map(n => n[0]).join('').toUpperCase() }} <!-- Initials -->
                                            </span>
                                        </div>
                                        <span v-if="relatedBlog.author" class="text-xs text-gray-600 ml-2 truncate">{{ relatedBlog.author.name }}</span>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>
                    <!-- End Related Blogs Section -->

                    <!-- Tags or Related Links (Optional Placeholder) -->
                    <!--
                    <div class="mt-12 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Tags</h3>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">Tag 1</span>
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">Tag 2</span>
                        </div>
                    </div>
                    -->

                    <!-- Next/Previous Post Navigation (Optional Placeholder) -->
                    <!--
                    <div class="mt-12 pt-6 border-t border-gray-200 flex justify-between">
                        <Link v-if="previousPost" :href="route('blog.show', previousblog.slug)" class="group max-w-xs">
                            <span class="text-sm text-gray-500 group-hover:text-emerald-600">Previous Post</span>
                            <div class="font-medium text-gray-900 group-hover:text-emerald-600 truncate">{{ previousblog.title }}</div>
                        </Link>
                        <div></div>  Empty div for spacing
                        <Link v-if="nextPost" :href="route('blog.show', nextblog.slug)" class="group max-w-xs text-right">
                            <span class="text-sm text-gray-500 group-hover:text-emerald-600">Next Post</span>
                            <div class="font-medium text-gray-900 group-hover:text-emerald-600 truncate">{{ nextblog.title }}</div>
                        </Link>
                    </div>
                    -->
                </div>
            </div>
        </div>
    </AppLayout2>
</template>

<style scoped>
/* line-clamp utility might need to be enabled in Tailwind config */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* If you are not using @tailwindcss/typography plugin, you might need to style the content manually */
/* Or ensure the plugin is installed and configured */
</style>