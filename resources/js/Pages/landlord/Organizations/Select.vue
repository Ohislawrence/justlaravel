<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { defineProps } from 'vue';

defineProps({
    organizations: Array,
});
</script>

<template>
    <AppLayout title="Select Organization">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h2 class="text-xl font-semibold mb-6">Select an Organization</h2>
                        
                        <div class="grid md:grid-cols-3 gap-4">
                            <div 
                                v-for="org in organizations" 
                                :key="org.id"
                                class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer"
                            >
                                <Link 
                                    :href="route('organizations.switch')"
                                    method="post"
                                    :data="{ organization_id: org.id }"
                                    as="button"
                                    class="w-full text-left"
                                >
                                    <h3 class="font-medium">{{ org.name }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ org.description || 'No description' }}
                                    </p>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>