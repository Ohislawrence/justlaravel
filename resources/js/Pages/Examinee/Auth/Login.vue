<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const form = ref({
    unique_code: '',
    password: '',
    remember: false,
});

const submit = () => {
    router.post(route('examinee.login.post'), form.value);
};
</script>

<template>
    <AppLayout title="Examinee Login">
        <div class="flex items-center justify-center min-h-screen bg-gray-100">
            <div class="w-full max-w-md p-8 space-y-8 bg-white rounded-lg shadow">
                <div class="text-center">
                    <h2 class="text-3xl font-extrabold text-gray-900">Examinee Login</h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Use your unique code and password to access your exams
                    </p>
                </div>
                
                <form class="mt-8 space-y-6" @submit.prevent="submit">
                    <div class="space-y-4 rounded-md shadow-sm">
                        <div>
                            <label for="unique_code" class="block text-sm font-medium text-gray-700">
                                Unique Code
                            </label>
                            <input
                                id="unique_code"
                                v-model="form.unique_code"
                                type="text"
                                required
                                class="block w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            />
                        </div>
                        
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Password
                            </label>
                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                required
                                class="block w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            />
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input
                                    id="remember"
                                    v-model="form.remember"
                                    type="checkbox"
                                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                />
                                <label for="remember" class="block ml-2 text-sm text-gray-900">
                                    Remember me
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <button
                            type="submit"
                            class="flex justify-center w-full px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Sign in
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>