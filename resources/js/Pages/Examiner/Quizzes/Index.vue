<script setup>
import { router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref } from 'vue';
import { debounce } from 'lodash';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    organization: Object,
    quizzes: Object,
    filters: Object,
});

const { flash } = usePage().props;
const search = ref(props.filters.search);

// Loading state for delete operations
const deletingQuizId = ref(null);

const updateSearch = debounce((value) => {
    router.get(
        route('examiner.quizzes.index', props.organization.id),
        { search: value },
        { preserveState: true }
    );
}, 300);

const deleteQuiz = (quiz) => {
    if (confirm('Are you sure you want to delete this quiz?')) {
        deletingQuizId.value = quiz.id;
        router.delete(route('examiner.quizzes.destroy', quiz.id), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                deletingQuizId.value = null;
                // Flash message will be handled automatically by Inertia
            },
            onError: (errors) => {
                deletingQuizId.value = null;
                console.error('Delete error:', errors);
                // Flash message will be handled automatically by Inertia
            },
            onFinish: () => {
                deletingQuizId.value = null;
            }
        });
    }
};
</script>

<template>
    <AppLayout title="All Quizzes">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Quizzes in {{ organization.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Success Message -->
                <div v-if="flash.success" class="mb-4">
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                        <span class="block sm:inline">{{ flash.success }}</span>
                        <button
                            type="button" 
                            @click="flash.success = null"
                            class="absolute top-0 bottom-0 right-0 px-4 py-3"
                            aria-label="Close success message"
                        >
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Error Message -->
                <div v-if="flash.error" class="mb-4">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <span class="block sm:inline">{{ flash.error }}</span>
                        <button
                            type="button" 
                            @click="flash.error = null"
                            class="absolute top-0 bottom-0 right-0 px-4 py-3"
                            aria-label="Close error message"
                        >
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6 bg-white border-b border-gray-200">
                        <!-- Search and Create Button Section -->
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                            <!-- Search Bar -->
                            <div class="relative flex-grow w-full sm:w-auto">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input
                                    v-model="search"
                                    @input="updateSearch($event.target.value)"
                                    type="text"
                                    placeholder="Search quizzes..."
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white"
                                >
                                <div v-if="search" @click="search = ''" class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer">
                                    <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Create Button -->
                            <Link
                                :href="route('examiner.quizzes.create', props.organization.id)"
                                class="w-full sm:w-auto text-center bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg transition duration-150 ease-in-out flex items-center justify-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Create New Exam
                            </Link>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Title
                                        </th>
                                        <th class="hidden sm:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Type
                                        </th>
                                        <th class="hidden sm:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Questions
                                        </th>
                                        <th class="hidden sm:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Attempts
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="quiz in quizzes.data" :key="quiz.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <Link
                                                :href="route('examiner.quizzes.show', quiz.id)"
                                                class="text-green-600 hover:text-green-800 font-medium"
                                            >
                                                {{ quiz.title }}
                                            </Link>
                                        </td>
                                        <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ quiz.quiz_type }}
                                        </td>
                                        <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap">
                                            <div class="group relative inline-block">
                                                <Link
                                                    :href="route('examiner.quizzes.questions.index', { quiz: quiz.id })"
                                                    class="text-green-600 hover:text-green-800 font-medium"
                                                >
                                                    <span v-if="quiz.questions_count === quiz.total_questions">
                                                        {{ quiz.questions_count }}
                                                    </span>
                                                    <span v-else>
                                                        {{ quiz.questions_count }} ({{ quiz.total_questions }} total)
                                                    </span>
                                                    <i class="fas fa-list-ul ml-1"></i>
                                                </Link>
                                                <span v-if="quiz.questions_count !== quiz.total_questions"
                                                    class="absolute z-10 hidden group-hover:block w-64 p-2 text-sm bg-gray-800 text-white rounded shadow-lg">
                                                    {{ quiz.questions_count }} direct questions +
                                                    {{ quiz.total_questions - quiz.questions_count }} from pools
                                                </span>
                                            </div>
                                        </td>
                                        <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ quiz.attempts_count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="{
                                                'bg-green-100 text-green-800': quiz.is_published,
                                                'bg-gray-100 text-gray-800': !quiz.is_published
                                            }" class="px-2 py-1 text-xs font-medium rounded-full">
                                                {{ quiz.is_published ? 'Published' : 'Draft' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <!-- Stacked buttons on small screens -->
                                            <div class="flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0">
                                                <Link
                                                    :href="route('examiner.quizzes.edit', quiz.id)"
                                                    class="px-2.5 py-1.5 text-center text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 rounded-md transition duration-150 ease-in-out"
                                                >
                                                    Edit
                                                </Link>
                                                <Link
                                                    :href="route('examiner.quizzes.questions.index', { quiz: quiz.id })"
                                                    class="px-2.5 py-1.5 text-center text-emerald-600 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 rounded-md transition duration-150 ease-in-out"
                                                    title="Manage Questions"
                                                >
                                                    Questions
                                                </Link>
                                                <Link
                                                    :href="route('examiner.quizzes.analysis.index', { quiz: quiz.id })"
                                                    class="px-2.5 py-1.5 text-center text-teal-600 hover:text-teal-900 bg-teal-50 hover:bg-teal-100 rounded-md transition duration-150 ease-in-out"
                                                    title="Analyze Quiz"
                                                >
                                                    Analyze
                                                </Link>
                                                <button
                                                    @click="deleteQuiz(quiz)"
                                                    :disabled="deletingQuizId === quiz.id"
                                                    :class="{
                                                        'opacity-50 cursor-not-allowed': deletingQuizId === quiz.id,
                                                        'hover:text-red-900 hover:bg-red-100': deletingQuizId !== quiz.id
                                                    }"
                                                    class="px-2.5 py-1.5 text-center text-red-600 bg-red-50 rounded-md transition duration-150 ease-in-out"
                                                >
                                                    <span v-if="deletingQuizId === quiz.id" class="flex items-center justify-center">
                                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                        </svg>
                                                        Deleting...
                                                    </span>
                                                    <span v-else>Delete</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Empty State -->
                        <div v-if="quizzes.data.length === 0" class="text-center py-12">
                            <div class="text-gray-500">
                                <i class="fas fa-clipboard-list text-3xl sm:text-4xl mb-4"></i>
                                <p class="text-lg mb-2">No quizzes found</p>
                                <p class="text-sm">
                                    {{ search ? 'Try adjusting your search terms' : 'Get started by creating your first quiz' }}
                                </p>
                            </div>
                        </div>

                        <Pagination :links="quizzes.links" class="mt-6" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Enhanced search input styling */
input[type="text"] {
    transition: all 0.3s ease-in-out;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

input[type="text"]:focus {
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);
}

/* Create button hover effect */
.bg-green-600:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .flex-col .w-full {
        width: 100%;
    }
    
    .gap-4 {
        gap: 1rem;
    }
}

/* Action buttons hover effects */
.text-green-600:hover {
    transform: translateY(-1px);
}

.text-emerald-600:hover {
    transform: translateY(-1px);
}

.text-teal-600:hover {
    transform: translateY(-1px);
}
</style>