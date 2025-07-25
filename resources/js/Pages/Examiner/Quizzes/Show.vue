<script setup>
import { ref, onMounted, watch, computed } from 'vue'; 
import { router, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    quiz: Object,
    stats: Object,
    availablePools: Array,
    quizPools: Object,
});

const shareLink = ref('');
const copySuccess = ref(false);
const shareLinkInput = ref(null);
const selectedPool = ref('');
const questionsToShow = ref(1);

const { flash } = usePage().props;

const togglePublish = () => {
    router.post(route('examiner.quizzes.toggle-publish', props.quiz.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            if (props.quiz.is_published) {
                generateShareLink();
            }
        }
    });
};

const generateShareLink = async () => {
    try {
        const response = await axios.get(route('examiner.quizzes.share-link', props.quiz.id));
        shareLink.value = response.data.link;
    } catch (error) {
        console.error('Error generating share link:', error);
        shareLink.value = 'Error: Could not generate link';
    }
};

const copyShareLink = async () => {
    if (!shareLink.value) return;
    
    try {
        await navigator.clipboard.writeText(shareLink.value);
        copySuccess.value = true;
        setTimeout(() => copySuccess.value = false, 2000);
    } catch (error) {
        // Fallback for older browsers
        shareLinkInput.value.select();
        document.execCommand('copy');
        copySuccess.value = true;
        setTimeout(() => copySuccess.value = false, 2000);
    }
};

onMounted(() => {
    if (props.quiz.is_published) {
        generateShareLink();
    }
});

watch(() => props.quiz.is_published, (isPublished) => {
    if (isPublished) {
        generateShareLink();
    } else {
        shareLink.value = '';
    }
});

const questionTypeLabel = (type) => {
    const types = {
        'multiple_choice': 'Multiple Choice',
        'true_false': 'True/False',
        'short_answer': 'Short Answer',
        'essay': 'Essay',
        'fill_in_blank': 'Fill in Blank',
        'matching': 'Matching',
        'ordering': 'Ordering'
    };
    return types[type] || type;
};

const formatFillInBlankText = (text, answers) => {
    if (!text || !answers) return text;
    const answersCopy = [...answers];
    return text.replace(/\[blank\]/g, () => {
        return `[${answersCopy.shift() || 'answer'}]`;
    });
};

const confirmDeleteQuestion = (question) => {
    if (confirm(`Are you sure you want to delete this question?\n"${question.question}"`)) {
        router.delete(route('examiner.quizzes.questions.destroy', {
            quiz: props.quiz.id,
            question: question.id
        }));
    }
};

// Add pool management methods
const attachPool = () => {
    router.post(route('examiner.quizzes.attach-pool', {
        quiz: props.quiz.id,
        pool: selectedPool.value
    }), {
        questions_to_show: questionsToShow.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            selectedPool.value = '';
            questionsToShow.value = 1;
        }
    });
};

const detachPool = (poolId) => {
    router.delete(route('examiner.quizzes.detach-pool', {
        quiz: props.quiz.id,
        pool: poolId
    }), {
        preserveScroll: true,
    });
};

const availablePoolsList = computed(() => {
    return props.availablePools.filter(pool =>
        !props.quizPools?.some(qp => qp.id === pool.id)
    );
});
</script>

<template>
    <AppLayout :title="quiz.title">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ quiz.title }}
                </h2>
                <div class="space-x-2">
                    <button @click="togglePublish" class="btn btn-sm" 
                        :class="{
                            'btn-success': !quiz.is_published,
                            'btn-outline': quiz.is_published
                        }">
                        {{ quiz.is_published ? 'Unpublish' : 'Publish' }}
                    </button>
                    <Link :href="route('examiner.quizzes.edit', quiz.id)" class="btn btn-sm btn-primary">
                        Edit
                    </Link>
                </div>
            </div>
        </template>
        <!-- Flash Messages -->
        <div v-if="flash.success" class="mb-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ flash.success }}
            </div>
        </div>

        <div v-if="flash.error" class="mb-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ flash.error }}
            </div>
        </div>

        <div v-if="flash.info" class="mb-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
                {{ flash.info }}
            </div>
        </div>

        <div v-if="flash.warning" class="mb-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                {{ flash.warning }}
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Quiz Details -->
                    <div class="md:col-span-2 space-y-6">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h3 class="text-lg font-medium mb-4">Quiz Details</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Type</p>
                                        <p class="font-medium">{{ quiz.quiz_type }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Industry</p>
                                        <p class="font-medium">{{ quiz.industry || 'N/A' }}</p>
                                    </div>
                                </div>

                                <div v-if="quiz.description" class="mb-4">
                                    <p class="text-sm text-gray-500 mb-2">Description</p>
                                    <div 
                                        class="prose prose-sm max-w-none"
                                        v-html="quiz.description"
                                    ></div>
                                </div>

                                <div v-if="quiz.instructions" class="mb-4">
                                    <p class="text-sm text-gray-500 mb-2">Instructions</p>
                                    <div 
                                        class="prose prose-sm max-w-none bg-blue-50 p-3 rounded-md border-l-4 border-blue-400"
                                        v-html="quiz.instructions"
                                    ></div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Time Limit</p>
                                        <p class="font-medium">{{ quiz.time_limit ? `${quiz.time_limit} minutes` : 'No limit' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Passing Score</p>
                                        <p class="font-medium">{{ quiz.passing_score ? `${quiz.passing_score}%` : 'Not set' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Questions</p>
                                        <p class="font-medium">{{ quiz.questions?.length || 0 }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Questions Section -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium">Questions</h3>
                                    <Link 
                                        :href="route('examiner.quizzes.questions.create', quiz.id)"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition"
                                    >
                                        Add Question
                                    </Link>
                                </div>

                                <div v-if="quiz.questions && quiz.questions.length > 0" class="space-y-4">
                                    <div v-for="(question, index) in quiz.questions" :key="question.id" class="border rounded-lg p-4 hover:bg-gray-50">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <h4 class="font-medium mb-2">
                                                    {{ index + 1 }}. <span v-html="question.question"></span>
                                                </h4>
                                                <div class="flex flex-wrap gap-2 text-sm text-gray-500 mb-2">
                                                    <span class="bg-gray-100 px-2 py-1 rounded">
                                                        {{ questionTypeLabel(question.type) }}
                                                    </span>
                                                    <span class="bg-blue-100 px-2 py-1 rounded">
                                                        {{ question.points }} points
                                                    </span>
                                                    <span class="px-2 py-1 rounded" :class="{
                                                        'bg-red-100 text-red-700': question.is_required,
                                                        'bg-green-100 text-green-700': !question.is_required
                                                    }">
                                                        {{ question.is_required ? 'Required' : 'Optional' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex space-x-2 ml-4">
                                                <Link 
                                                    :href="route('examiner.quizzes.questions.edit', { quiz: quiz.id, question: question.id })"
                                                    class="text-blue-600 hover:text-blue-800 text-sm"
                                                >
                                                    Edit
                                                </Link>
                                                <button 
                                                    @click="confirmDeleteQuestion(question)"
                                                    class="text-red-600 hover:text-red-800 text-sm"
                                                >
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Show question content based on type -->
                                        <div class="mt-3 text-sm">
                                            <div v-if="question.type === 'multiple_choice'" class="bg-gray-50 p-3 rounded">
                                                <p class="font-medium mb-2">Options:</p>
                                                <ul class="space-y-1">
                                                    <li v-for="(option, optIndex) in question.options" :key="optIndex" class="flex items-center">
                                                        <span class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center mr-2 text-xs">
                                                            {{ String.fromCharCode(65 + optIndex) }}
                                                        </span>
                                                        <span v-html="option"></span>
                                                        <span v-if="question.correct_answers && question.correct_answers.includes(option)" class="ml-2 text-green-600 font-medium">
                                                            ✓
                                                        </span>
                                                    </li>
                                                </ul>
                                            </div>
                                            
                                            <div v-else-if="question.type === 'true_false'" class="bg-gray-50 p-3 rounded">
                                                <p class="font-medium">Correct Answer: 
                                                    <span class="text-green-600">
                                                        {{ question.correct_answers && question.correct_answers[0] === true ? 'True' : 'False' }}
                                                    </span>
                                                </p>
                                            </div>
                                            
                                            <div v-else-if="question.type === 'short_answer'" class="bg-gray-50 p-3 rounded">
                                                <p class="font-medium mb-2">Accepted Answers:</p>
                                                <ul class="list-disc pl-5 space-y-1">
                                                    <li v-for="(answer, ansIndex) in question.correct_answers" :key="ansIndex" class="text-green-600">
                                                        {{ answer }}
                                                    </li>
                                                </ul>
                                            </div>
                                            
                                            <div v-else-if="question.type === 'fill_in_blank'" class="bg-gray-50 p-3 rounded">
                                                <p class="font-medium mb-2">With Answers:</p>
                                                <div class="whitespace-pre-line" v-html="formatFillInBlankText(question.question, question.correct_answers)"></div>
                                            </div>
                                            
                                            <div v-else-if="question.type === 'matching'" class="bg-gray-50 p-3 rounded">
                                                <p class="font-medium mb-2">Correct Matches:</p>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                                    <div v-for="(pair, index) in question.options" :key="index" class="flex items-center">
                                                        <span class="font-medium" v-html="pair.left"></span>
                                                        <span class="mx-2">→</span>
                                                        <span class="text-green-600 font-medium" v-html="pair.right"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div v-else-if="question.type === 'ordering'" class="bg-gray-50 p-3 rounded">
                                                <p class="font-medium mb-2">Correct Order:</p>
                                                <ol class="list-decimal pl-5 space-y-1">
                                                    <li v-for="(item, idx) in question.correct_answers" :key="idx" class="text-green-600">
                                                        <span v-html="item"></span>
                                                    </li>
                                                </ol>
                                            </div>
                                            
                                            <div v-else-if="question.type === 'essay'" class="bg-gray-50 p-3 rounded">
                                                <p class="font-medium text-gray-600">Essay Question - Manual Grading Required</p>
                                                <p v-if="question.max_words" class="text-sm text-gray-500 mt-1">
                                                    Max words: {{ question.max_words }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div v-else class="text-center py-8 text-gray-500">
                                    <p class="mb-4">No questions added yet.</p>
                                    <Link 
                                        :href="route('examiner.quizzes.questions.create', quiz.id)"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition"
                                    >
                                        Add First Question
                                    </Link>
                                </div>
                            </div>
                        </div>
                        <!-- Question Pools Section -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium">Question Pools</h3>
                                    <Link 
                                        :href="route('examiner.question-pools.create')"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition"
                                    >
                                        Create New Pool
                                    </Link>
                                </div>

                                <!-- Current Pools -->
                                <div v-if="quizPools?.length" class="space-y-4 mb-6">
                                    <div v-for="pool in quizPools" :key="pool.id" class="border rounded-lg p-4 hover:bg-gray-50">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <h4 class="font-medium mb-1">{{ pool.name }}</h4>
                                                <p class="text-sm text-gray-500 mb-2">{{ pool.description || 'No description' }}</p>
                                                <div class="flex flex-wrap gap-2 text-sm text-gray-500">
                                                    <span class="bg-gray-100 px-2 py-1 rounded">
                                                        {{ pool.questions_count }} questions
                                                    </span>
                                                    <span class="bg-blue-100 px-2 py-1 rounded">
                                                        Show {{ pool.pivot?.questions_to_show || 0 }} questions
                                                    </span>
                                                </div>
                                            </div>
                                            <button 
                                                @click="detachPool(pool.id)"
                                                class="text-red-600 hover:text-red-800 text-sm"
                                            >
                                                Remove
                                            </button>
                                        </div>
                                        <Link 
                                            :href="route('examiner.question-pools.manage-questions', pool.id)"
                                            class="mt-3 inline-block text-sm text-blue-600 hover:text-blue-800"
                                        >
                                            Manage Questions
                                        </Link>
                                    </div>
                                </div>
                                <div v-else class="text-center py-4 text-gray-500">
                                    <p>No question pools added yet.</p>
                                </div>

                                <!-- Add Pool Form -->
                                <div v-if="availablePoolsList.length > 0" class="mt-6">
                                    <h4 class="font-medium mb-3">Add Existing Pool</h4>
                                    <div class="space-y-3">
                                        <select 
                                            v-model="selectedPool"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        >
                                            <option value="" disabled>Select a pool to add</option>
                                            <option 
                                                v-for="pool in availablePoolsList" 
                                                :key="pool.id" 
                                                :value="pool.id"
                                            >
                                                {{ pool.name }} ({{ pool.questions_count }} questions)
                                            </option>
                                        </select>
                                        
                                        <div v-if="selectedPool">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Questions to show from this pool
                                            </label>
                                            <input
                                                v-model="questionsToShow"
                                                type="number"
                                                min="1"
                                                :max="availablePoolsList.find(p => p.id === selectedPool)?.questions_count || 1"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            >
                                        </div>
                                        
                                        <button
                                            @click="attachPool"
                                            :disabled="!selectedPool"
                                            class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium"
                                        >
                                            Add Pool
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats and Actions -->
                    <div class="space-y-6">
                        <!-- Status Card -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h3 class="text-lg font-medium mb-4">Status</h3>
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Published</p>
                                        <p class="font-medium">
                                            <span :class="{
                                                'text-green-600': quiz.is_published,
                                                'text-red-600': !quiz.is_published
                                            }">
                                                {{ quiz.is_published ? 'Yes' : 'No' }}
                                            </span>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Created</p>
                                        <p class="font-medium">{{ new Date(quiz.created_at).toLocaleDateString() }}</p>
                                    </div>
                                    <div v-if="quiz.starts_at">
                                        <p class="text-sm text-gray-500">Starts</p>
                                        <p class="font-medium">{{ new Date(quiz.starts_at).toLocaleString() }}</p>
                                    </div>
                                    <div v-if="quiz.ends_at">
                                        <p class="text-sm text-gray-500">Ends</p>
                                        <p class="font-medium">{{ new Date(quiz.ends_at).toLocaleString() }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Visibility</p>
                                        <p class="font-medium">{{ quiz.is_public ? 'Public' : 'Private' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Card -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h3 class="text-lg font-medium mb-4">Statistics</h3>
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Total Attempts</p>
                                        <p class="font-medium">{{ stats?.attempts_count || 0 }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Average Score</p>
                                        <p class="font-medium">{{ Math.round(stats?.average_score || 0) }}%</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Completion Rate</p>
                                        <p class="font-medium">{{ Math.round(stats?.completion_rate || 0) }}%</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Share Quiz Card -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h3 class="text-lg font-medium mb-4">Share Quiz</h3>
                                
                                <div v-if="quiz.is_published">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <input
                                            ref="shareLinkInput"
                                            type="text"
                                            :value="shareLink"
                                            readonly
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            placeholder="Generating link..."
                                        >
                                        <button
                                            @click="copyShareLink"
                                            :disabled="!shareLink"
                                            class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium"
                                        >
                                            Copy
                                        </button>
                                    </div>
                                    <p v-if="copySuccess" class="text-green-600 text-sm flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Link copied to clipboard!
                                    </p>
                                    
                                    <div class="mt-4 p-3 bg-blue-50 rounded-md">
                                        <p class="text-sm text-blue-700">
                                            Share this link with students to allow them to take the quiz.
                                        </p>
                                    </div>
                                </div>
                                
                                <div v-else class="text-center py-4">
                                    <p class="text-sm text-gray-600 mb-3">Publish the quiz to generate a shareable link.</p>
                                    <button 
                                        @click="togglePublish" 
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium"
                                    >
                                        Publish Quiz
                                    </button>
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
.prose {
    color: inherit;
}

.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
    color: inherit;
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
}

.prose p {
    margin-top: 0.25rem;
    margin-bottom: 0.25rem;
}

.prose ul, .prose ol {
    margin-top: 0.25rem;
    margin-bottom: 0.25rem;
}

.prose li {
    margin-top: 0.125rem;
    margin-bottom: 0.125rem;
}
</style>