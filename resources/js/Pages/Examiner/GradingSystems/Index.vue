<script setup>
import { usePage, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

const { props } = usePage();
const { gradingSystems, defaultSystems } = props;

const deleteSystem = (system) => {
    if (confirm(`Are you sure you want to delete "${system.name}"? This action cannot be undone.`)) {
        useForm({}).delete(route('examiner.grading-systems.destroy', system.id));
    }
};

const setDefault = (system) => {
    useForm({}).post(route('examiner.grading-systems.set-default', system.id));
};
</script>

<template>
    <AppLayout title="Grading Systems">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Grading Systems
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium">Manage Grading Systems</h3>
                            <Link 
                                :href="route('examiner.grading-systems.create')" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Create New System
                            </Link>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Default Systems -->
                            <div>
                                <h4 class="font-medium text-gray-900 mb-4">Default Systems</h4>
                                <div class="space-y-3">
                                    <div v-for="system in defaultSystems" :key="system.id" class="p-4 border border-gray-200 rounded-lg bg-gray-50 shadow-sm">
                                        <h5 class="font-medium text-gray-900">{{ system.name }}</h5>
                                        <p class="text-sm text-gray-600">{{ system.type }}</p>
                                        <div class="mt-2 text-xs text-gray-700">
                                            <div v-for="(range, index) in system.grade_ranges" :key="index" class="flex justify-between py-1 border-b border-gray-100 last:border-b-0">
                                                <span>{{ range.min }}%-{{ range.max }}%</span>
                                                <span class="font-medium">{{ range.grade }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Custom Systems -->
                            <div>
                                <h4 class="font-medium text-gray-900 mb-4">Your Custom Systems</h4>
                                <div v-if="gradingSystems.filter(s => !s.is_default).length === 0" class="text-gray-500 text-center py-8">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p>No custom grading systems created yet.</p>
                                </div>
                                <div v-else class="space-y-3">
                                    <div v-for="system in gradingSystems.filter(s => !s.is_default)" :key="system.id" class="p-4 border border-gray-200 rounded-lg bg-white shadow-sm hover:shadow-md transition-shadow duration-200">
                                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                                            <div class="mb-3 sm:mb-0">
                                                <h5 class="font-medium text-gray-900">{{ system.name }}</h5>
                                                <p class="text-sm text-gray-600">{{ system.type }}</p>
                                                <p class="text-xs text-gray-500">Created: {{ new Date(system.created_at).toLocaleDateString() }}</p>
                                            </div>
                                            <div class="flex flex-wrap gap-2">
                                                <Link 
                                                    :href="route('examiner.grading-systems.edit', system.id)" 
                                                    class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition-colors duration-150"
                                                >
                                                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Edit
                                                </Link>
                                                <button 
                                                    @click="deleteSystem(system)" 
                                                    class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition-colors duration-150"
                                                >
                                                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Delete
                                                </button>
                                                <button 
                                                    @click="setDefault(system)" 
                                                    class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition-colors duration-150"
                                                >
                                                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Set Default
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mt-3 text-xs grid grid-cols-2 gap-2">
                                            <div v-for="(range, index) in system.grade_ranges" :key="index" class="bg-gray-50 px-2 py-1 rounded text-center border border-gray-100">
                                                <div class="font-medium text-gray-900">{{ range.min }}-{{ range.max }}%</div>
                                                <div class="text-green-600 font-medium">{{ range.grade }}</div>
                                                <div v-if="range.points" class="text-blue-600 text-xs mt-1">{{ range.points }} pts</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Enhanced green-themed styling */
.bg-green-600 {
    background-color: #10B981;
}

.bg-green-700:hover {
    background-color: #059669;
}

/* Green-themed text */
.text-green-600 {
    color: #10B981;
}

/* Border styling */
.border-gray-100 {
    border-color: #f3f4f6;
}

/* Transition effects */
.transition-colors {
    transition: all 0.2s ease;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .grid {
        grid-template-columns: 1fr;
    }
    
    .gap-6 > *:not(:last-child) {
        margin-bottom: 1.5rem;
    }
}
</style>