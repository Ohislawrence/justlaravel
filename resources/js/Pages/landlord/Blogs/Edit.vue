<!-- resources/js/Pages/Landlord/Blogs/Edit.vue -->
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const props = defineProps({
    blog: Object,
    categories: Array,
});

const form = useForm({
    title: props.blog.title,
    content: props.blog.content,
    featured_image: null,
    remove_image: false,
    is_published: props.blog.is_published,
    category_ids: props.blog.categories.map(c => c.id),
});

const fileInput = ref(null);

const handleFileChange = (e) => {
    form.featured_image = e.target.files[0];
};

const submit = () => {
    form.put(route('landlord.blogs.update', props.blog.id), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <AppLayout title="Edit Blog Post">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Blog Post
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                                    Title
                                </label>
                                <input
                                    id="title"
                                    v-model="form.title"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    type="text"
                                    placeholder="Blog post title"
                                />
                                <p v-if="form.errors.title" class="text-red-500 text-xs italic">{{ form.errors.title }}</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Categories</label>
                                <div class="flex flex-wrap gap-2">
                                    <label v-for="category in categories" :key="category.id" class="inline-flex items-center">
                                        <input 
                                            type="checkbox" 
                                            v-model="form.category_ids"
                                            :value="category.id"
                                            class="form-checkbox h-5 w-5 text-blue-600"
                                        >
                                        <span class="ml-2 text-gray-700">{{ category.name }}</span>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Content
                                </label>
                                <QuillEditor
                                    v-model:content="form.content"
                                    contentType="html"
                                    theme="snow"
                                    toolbar="full"
                                    class="h-64 mb-2"
                                />
                                <p v-if="form.errors.content" class="text-red-500 text-xs italic">{{ form.errors.content }}</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Current Featured Image
                                </label>
                                <img 
                                    v-if="blog.featured_image && !form.remove_image" 
                                    :src="'/storage/' + blog.featured_image" 
                                    class="h-32 w-auto mb-2"
                                />
                                <div v-else class="text-gray-500 text-sm mb-2">
                                    No featured image
                                </div>
                                
                                <div class="flex items-center space-x-4">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2" for="featured_image">
                                            Change Image
                                        </label>
                                        <input
                                            id="featured_image"
                                            ref="fileInput"
                                            type="file"
                                            class="hidden"
                                            @change="handleFileChange"
                                        />
                                        <button
                                            type="button"
                                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded"
                                            @click="fileInput.click()"
                                        >
                                            Choose New Image
                                        </button>
                                        <span v-if="form.featured_image" class="ml-2 text-sm text-gray-600">
                                            {{ form.featured_image.name }}
                                        </span>
                                    </div>
                                    
                                    <div v-if="blog.featured_image" class="mt-6">
                                        <label class="inline-flex items-center">
                                            <input
                                                v-model="form.remove_image"
                                                type="checkbox"
                                                class="form-checkbox h-5 w-5 text-red-600"
                                            />
                                            <span class="ml-2 text-gray-700">Remove Image</span>
                                        </label>
                                    </div>
                                </div>
                                <p v-if="form.errors.featured_image" class="text-red-500 text-xs italic">{{ form.errors.featured_image }}</p>
                            </div>

                            <div class="mb-4">
                                <label class="inline-flex items-center">
                                    <input
                                        v-model="form.is_published"
                                        type="checkbox"
                                        class="form-checkbox h-5 w-5 text-blue-600"
                                    />
                                    <span class="ml-2 text-gray-700">Publish this post</span>
                                </label>
                            </div>

                            <div class="flex items-center justify-between">
                                <button
                                    type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                    :disabled="form.processing"
                                >
                                    Update Post
                                </button>
                                <Link
                                    :href="route('landlord.blogs.index')"
                                    class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800"
                                >
                                    Cancel
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>