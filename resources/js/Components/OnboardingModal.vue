<template>
    <!-- Modal Backdrop -->
    <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <!-- Semi-transparent green backdrop with blur -->
        <div 
            class="fixed inset-0 bg-green-100 bg-opacity-70 backdrop-blur-sm transition-opacity" 
            @click="closeModal"
        ></div>

        <!-- Smaller, centered modal -->
        <div class="relative w-full max-w-xl transform overflow-hidden rounded-2xl bg-white/95 backdrop-blur-md shadow-2xl transition-all">

            <!-- Close Button -->
            <button
                @click="closeModal"
                class="absolute top-3 right-3 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-white/70 text-gray-500 hover:bg-white hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-400"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Modal Content -->
            <div class="p-6 text-center">

                <!-- Step 1: Welcome -->
                <div v-if="currentStep === 1">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
                        <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l-9 5m9-5v6" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Welcome to ExamPortal!</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Get started with your role as
                        <span class="font-semibold text-green-600">{{ userRoleLabel }}</span>.
                        This quick tour will help you get started.
                    </p>
                    <div class="mt-4">
                        <img src="https://blisspot.com/blogs/wp-content/uploads/blog/5e/96/01/4239f3d51d092f69d529f372a37b4601.jpg" alt="" class="h-15 w-full rounded-t-lg object-cover" />
                    </div>
                </div>

                <!-- Step 2: Create & Manage Exams -->
                <div v-else-if="currentStep === 2">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-100">
                        <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Create & Manage Exams</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        As a {{ userRoleLabel }}, you can:
                    </p>
                    <ul class="mt-3 space-y-1 text-left max-w-xs mx-auto text-xs text-gray-500 list-disc list-inside bg-gray-50 p-3 rounded-lg">
                        <li v-if="userRole === 'examiner'">Create exam groups and individual quizzes</li>
                        <li v-if="userRole === 'examiner'">Manage question pools</li>
                        <li v-if="userRole === 'examiner'">Generate questions with Ai</li>
                        <li v-if="userRole === 'examiner'">Add examinee(students/staffs) and instructors</li>
                        <li v-if="userRole === 'examinee'">Take assigned exams</li>
                        <li v-if="userRole === 'examinee'">Track your certificates</li>
                        <li v-if="userRole === 'instructor'">Create and assign exams</li>
                        <li v-if="userRole === 'landlord'">Manage all organizations</li>
                    </ul>
                    <div class="mt-4">
                        <img src="https://t3.ftcdn.net/jpg/03/73/70/74/360_F_373707453_eIkvklrZ0Odgkyot5kfFd4UyRzxxfmWA.jpg" alt="Create Exams" class="rounded-lg w-full" />
                    </div>
                </div>

                <!-- Step 3: Track Performance -->
                <div v-else-if="currentStep === 3">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-purple-100">
                        <svg class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Track Performance</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Monitor results, view analytics, and generate reports.
                        {{ userRole === 'examiner' || userRole === 'landlord' ? 'Track user progress and identify areas for improvement.' : 'Review your performance over time.' }}
                    </p>
                    <div class="mt-4">
                        <img src="/assets/img/dashboard.png" alt="Analytics Dashboard" class="rounded-lg w-full" />
                    </div>
                </div>

                <!-- Step 4: Customize Experience -->
                <div v-else-if="currentStep === 4">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-amber-100">
                        <svg class="h-8 w-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Customize Your Experience</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Configure settings, manage your profile, and tailor the platform to your needs.
                    </p>
                    <p class="mt-3 text-xs font-semibold text-green-600 bg-green-50 py-2 px-3 rounded-lg inline-block">
                        🎉 You can always restart this tutorial from your profile menu!
                    </p>
                    <div class="mt-4">
                        <img src="/assets/img/restart.png" alt="Custom Settings" class="rounded-lg w-full" />
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="mt-6 flex flex-col sm:flex-row sm:justify-between gap-2">
                    <button
                        v-if="currentStep > 1"
                        type="button"
                        class="px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-400"
                        @click="prevStep"
                    >
                        ← Previous
                    </button>
                    <button
                        v-if="currentStep < 4"
                        type="button"
                        class="flex-1 py-2 text-xs font-semibold text-white bg-green-600 rounded-lg hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500"
                        @click="nextStep"
                    >
                        Next →
                    </button>
                    <button
                        v-else
                        type="button"
                        class="flex-1 py-2 text-xs font-semibold text-white bg-green-600 rounded-lg hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500"
                        @click="completeOnboarding"
                    >
                        🚀 Get Started
                    </button>
                </div>

                <!-- Progress Dots -->
                <div class="mt-5 flex justify-center space-x-2">
                    <div
                        v-for="step in 4"
                        :key="step"
                        class="h-2 w-2 rounded-full transition-all duration-300"
                        :class="currentStep >= step ? 'bg-green-600 w-6' : 'bg-gray-300'"
                    ></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    isOpen: {
        type: Boolean,
        required: true
    }
});

const emit = defineEmits(['close', 'completed']);

const currentStep = ref(1);
const page = usePage();

const userRole = computed(() => page.props.auth.user.role);
const userRoleLabel = computed(() => {
    const roles = {
        examiner: 'Examiner',
        examinee: 'Examinee',
        instructor: 'Instructor',
        landlord: 'Administrator'
    };
    return roles[userRole.value] || 'User';
});

const closeModal = () => {
    emit('close');
};

const nextStep = () => {
    if (currentStep.value < 4) {
        currentStep.value++;
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

const completeOnboarding = () => {
    window.axios.post(route('examiner.onboarding.complete'))
        .then(() => {
            emit('completed');
            closeModal();
        })
        .catch(error => {
            console.error('Failed to mark onboarding as complete:', error);
            closeModal();
        });
};
</script>