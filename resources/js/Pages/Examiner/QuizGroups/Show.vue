<script setup>
import { Link,useForm  } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref } from 'vue';

const props = defineProps({
    organization: Object,
    group: Object,
    quizzes: Object,
    childGroups: Array,
    availableQuizzes: Array,
});

const selectedQuiz = ref(null);
const showAddQuizModal = ref(false);

const form = useForm({
    quiz_id: null,  
    order: 0,
});

const addQuizToGroup = () => {
    form.post(route('examiner.quiz-groups.quizzes.store', {
        quiz_group: props.group.id
    }), {
        preserveScroll: true,
        onSuccess: () => {
            showAddQuizModal.value = false;
            form.reset();
        },
        onFinish: () => {
            form.processing = false;
        }
    });
};
</script>

<template>
    <AppLayout :title="group.name">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ group.name }}
                </h2>
                <div class="space-x-2">
                    <Link
                        :href="route('examiner.quiz-groups.edit', { organization: organization.id, quiz_group: group.id })"
                        class="btn btn-sm btn-primary"
                    >
                        Edit
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="md:col-span-2 space-y-6">
                        <!-- Group Details -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h3 class="text-lg font-medium mb-4">Group Details</h3>
                                
                                <div v-if="group.description" class="mb-4">
                                    <p class="text-sm text-gray-500">Description</p>
                                    <p class="mt-1">{{ group.description }}</p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Parent Group</p>
                                        <p class="font-medium">
                                            <template v-if="group.parent">
                                                <Link
                                                    :href="route('examiner.quiz-groups.show', { organization: organization.id, quiz_group: group.parent.id })"
                                                    class="text-blue-600 hover:text-blue-800"
                                                >
                                                    {{ group.parent.name }}
                                                </Link>
                                            </template>
                                            <template v-else>None</template>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Status</p>
                                        <p class="font-medium">
                                            <span :class="{
                                                'text-green-600': group.is_active,
                                                'text-red-600': !group.is_active
                                            }">
                                                {{ group.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </p>
                                    </div>
                                    <div v-if="group.price">
                                        <p class="text-sm text-gray-500">Price</p>
                                        <p class="font-medium">${{ group.price.toFixed(2) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quizzes in this group -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium">Quizzes in this Group</h3>
                                    <button
                                        @click="showAddQuizModal = true"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition"
                                    >
                                        Add Quiz
                                    </button>
                                </div>

                                <div v-if="quizzes.data.length > 0" class="space-y-4">
                                    <div v-for="quiz in quizzes.data" :key="quiz.id" class="border rounded-lg p-4 hover:bg-gray-50">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <Link
                                                    :href="route('examiner.quizzes.show', quiz.id)"
                                                    class="font-medium text-blue-600 hover:text-blue-800"
                                                >
                                                    {{ quiz.title }}
                                                </Link>
                                                <p class="text-sm text-gray-500 mt-1">{{ quiz.description }}</p>
                                            </div>
                                            <button
                                                @click="selectedQuiz = quiz"
                                                class="text-red-600 hover:text-red-800 text-sm"
                                            >
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="text-center py-8 text-gray-500">
                                    <p>No quizzes added to this group yet.</p>
                                </div>

                                <Pagination :links="quizzes.links" class="mt-4" />
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Subgroups -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium">Subgroups</h3>
                                    <Link
                                        :href="route('examiner.quiz-groups.create', organization.id)"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition"
                                    >
                                        Create Subgroup
                                    </Link>
                                </div>

                                <div v-if="childGroups.length > 0" class="space-y-4">
                                    <div v-for="child in childGroups" :key="child.id" class="border rounded-lg p-4 hover:bg-gray-50">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <Link
                                                    :href="route('examiner.quiz-groups.show', { organization: organization.id, quiz_group: child.id })"
                                                    class="font-medium text-blue-600 hover:text-blue-800"
                                                >
                                                    {{ child.name }}
                                                </Link>
                                                <p class="text-sm text-gray-500 mt-1">{{ child.description }}</p>
                                                <div class="flex flex-wrap gap-2 mt-2">
                                                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">
                                                        {{ child.quizzes_count }} quizzes
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="text-center py-4 text-gray-500">
                                    <p>No subgroups created yet.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Quiz Modal -->
        <div v-if="showAddQuizModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">Add Quiz to Group</h3>
                    <button @click="showAddQuizModal = false" class="text-gray-500 hover:text-gray-700">
                        &times;
                    </button>
                </div>
                
                <form @submit.prevent="addQuizToGroup">
                    <div class="space-y-4">
                        <div>
                            <InputLabel for="quiz_id" value="Select Quiz *" />
                            <select 
                                id="quiz_id"
                                v-model="form.quiz_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required
                            >
                                <option :value="null" disabled>Select a quiz to add</option>
                                <option 
                                    v-for="quiz in availableQuizzes" 
                                    :key="quiz.id" 
                                    :value="quiz.id"
                                >
                                    {{ quiz.title }} ({{ quiz.quiz_type }})
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.quiz_id" />
                        </div>

                        <div>
                            <InputLabel for="order" value="Display Order" />
                            <TextInput
                                id="order"
                                v-model="form.order"
                                type="number"
                                min="0"
                                class="mt-1 block w-full"
                            />
                            <InputError class="mt-2" :message="form.errors.order" />
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button
                                type="button"
                                @click="showAddQuizModal = false"
                                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing">Adding...</span>
                                <span v-else>Add Quiz</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>