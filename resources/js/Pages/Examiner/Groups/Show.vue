<script setup>
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    organization: Object,
    group: Object,
    availableUsers: Array,
    availableQuizzes: Array,
});

const addMembers = () => {
    const userIds = Array.from(
        document.querySelectorAll('input[name="user_ids[]"]:checked')
    ).map(el => el.value);

    router.post(route('examiner.groups.addMembers', {
        organization: props.organization.slug,
        group: props.group.id
    }), { user_ids: userIds });
};

const assignQuizzes = () => {
    const quizIds = Array.from(
        document.querySelectorAll('input[name="quiz_ids[]"]:checked')
    ).map(el => el.value);

    router.post(route('examiner.groups.assignQuizzes', {
        organization: props.organization.slug,
        group: props.group.id
    }), { quiz_ids: quizIds });
};

const removeMember = (userId) => {
    router.delete(route('examiner.groups.removeMember', {
        organization: props.organization.slug,
        group: props.group.id,
        user: userId
    }));
};

const removeQuiz = (quizId) => {
    router.delete(route('examiner.groups.removeQuiz', {
        organization: props.organization.slug,
        group: props.group.id,
        quiz: quizId
    }));
};
</script>

<template>
    <AppLayout title="Manage Group" >
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manage Group: {{ group.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Group Info -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium mb-2">Group Information</h3>
                            <form @submit.prevent="router.put(route('examiner.groups.update', {
                                organization: organization.slug,
                                group: group.id
                            }), group)">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Name</label>
                                        <input v-model="group.name" type="text" class="mt-1 block w-full">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Description</label>
                                        <input v-model="group.description" type="text" class="mt-1 block w-full">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mt-4">
                                    Update Group
                                </button>
                            </form>
                        </div>

                        <!-- Members Section -->
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium">Group Members</h3>
                                <button @click="addMembers" class="btn btn-primary">
                                    Add Selected Members
                                </button>
                            </div>

                            <!-- Available Members -->
                            <div class="mb-6">
                                <h4 class="font-medium mb-2">Available Members</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                    <div v-for="user in availableUsers" :key="user.id" class="flex items-center">
                                        <input type="checkbox" name="user_ids[]" :value="user.id" class="mr-2">
                                        <span>{{ user.name }}</span>
                                    </div>
                                    <p v-if="availableUsers.length === 0" class="text-gray-500">
                                        No available members to add
                                    </p>
                                </div>
                            </div>

                            <!-- Current Members -->
                            <div>
                                <h4 class="font-medium mb-2">Current Members</h4>
                                <div class="space-y-2">
                                    <div v-for="member in group.members" :key="member.id" 
                                         class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                        <span>{{ member.name }}</span>
                                        <button @click="removeMember(member.id)" 
                                                class="text-red-600 hover:text-red-800">
                                            Remove
                                        </button>
                                    </div>
                                    <p v-if="group.members.length === 0" class="text-gray-500">
                                        No members in this group
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Quizzes Section -->
                        <div>
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium">Assigned Quizzes</h3>
                                <button @click="assignQuizzes" class="btn btn-primary">
                                    Assign Selected Quizzes
                                </button>
                            </div>

                            <!-- Available Quizzes -->
                            <div class="mb-6">
                                <h4 class="font-medium mb-2">Available Quizzes</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                    <div v-for="quiz in availableQuizzes" :key="quiz.id" class="flex items-center">
                                        <input type="checkbox" name="quiz_ids[]" :value="quiz.id" class="mr-2">
                                        <span>{{ quiz.title }}</span>
                                    </div>
                                    <p v-if="availableQuizzes.length === 0" class="text-gray-500">
                                        No available quizzes to assign
                                    </p>
                                </div>
                            </div>

                            <!-- Current Quizzes -->
                            <div>
                                <h4 class="font-medium mb-2">Assigned Quizzes</h4>
                                <div class="space-y-2">
                                    <div v-for="quiz in group.quizzes" :key="quiz.id" 
                                         class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                        <span>{{ quiz.title }}</span>
                                        <button @click="removeQuiz(quiz.id)" 
                                                class="text-red-600 hover:text-red-800">
                                            Remove
                                        </button>
                                    </div>
                                    <p v-if="group.quizzes.length === 0" class="text-gray-500">
                                        No quizzes assigned to this group
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>