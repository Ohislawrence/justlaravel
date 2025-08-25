<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { useToast } from 'vue-toastification';
import FeatureLimitModal from '@/Components/FeatureLimitModal.vue'

const toast = useToast();

const props = defineProps({
    questionPool: Object,
    canGenAiQue: Boolean,
});

const showModal = ref(false)
const modalFeature = ref('')
const modalMessage = ref('')

function openFeatureModal(featureName, message) {
  modalFeature.value = featureName
  modalMessage.value = message
  showModal.value = true
}

const loading = ref(false);
const generatedQuestions = ref([]);
const showPreview = ref(false);

const questionTypes = [
    { value: 'random', label: 'Random' },
    { value: 'multiple_choice', label: 'Multiple Choice' },
    { value: 'true_false', label: 'True/False' },
    { value: 'short_answer', label: 'Short Answer' },
    { value: 'fill_blank', label: 'Fill in the Blank' },
    { value: 'matching', label: 'Matching' },
    { value: 'ordering', label: 'Ordering' },
];

const form = useForm({
    source_type: 'topic',
    topic: '',
    article: '',
    question_type: 'random',
    difficulty: 'medium',
    number_of_questions: '',
    language: 'English',
    pool_id: props.questionPool.id,
});

const generateQuestions = async () => {
    if (props.canGenAiQue == false) {
        openFeatureModal(
        'Generating AI Question',
        `You have reached the AI generating limit. Upgrade your plan to generate more questions this period.`
        )
        return
    }
    loading.value = true;
    try {
        const response = await axios.post(
            route('examiner.question-pools.generate-ai-questions'),
            form.data()
        );

        if (response.data.questions && response.data.questions.length > 0) {
            generatedQuestions.value = response.data.questions;
            showPreview.value = true;
        } else {
            toast.error('No questions generated. Please try again.');
        }
    } catch (error) {
        console.error('Error generating questions:', error);
        toast.error(error.response?.data?.error || 'Failed to generate questions.');
    } finally {
        loading.value = false;
    }
};

const saveQuestions = () => {
    const submissionForm = useForm({
        questions: generatedQuestions.value.map(question => ({
            question: question.question,
            type: question.type,
            options: question.options || [],
            correct_answers: question.correct_answers || [],
            points: 1,
            is_required: true
        })),
        pool_id: props.questionPool.id
    });

    submissionForm.post(route('examiner.question-pools.store-ai-questions'), {
        preserveScroll: true,
        onSuccess: () => {
            generatedQuestions.value = [];
            showPreview.value = false;
            toast.success('Questions saved successfully!');
        },
        onError: (errors) => {
            console.error('Error saving questions:', errors);
            toast.error('Failed to save questions. Please try again.');
        }
    });
};

const deleteQuestion = (index) => {
    generatedQuestions.value.splice(index, 1);
    if (generatedQuestions.value.length === 0) {
        showPreview.value = false;
    }
};

const deleteAllQuestions = () => {
    generatedQuestions.value = [];
    showPreview.value = false;
};
</script>

<style scoped>
.delete-btn {
    @apply opacity-0 group-hover:opacity-100 transition-opacity duration-200;
}

@media (hover: none) {
    .delete-btn {
        @apply opacity-100;
    }
}

/* Enhanced green-themed styling */
.bg-green-600 {
    background-color: #10B981;
}

.bg-green-700:hover {
    background-color: #059669;
}

.focus\:ring-green-500:focus {
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.5);
}

/* Green-themed select */
select:focus {
    border-color: #10B981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.5);
}

/* Hover effects */
.hover\:bg-gray-300:hover {
    background-color: #d1d5db;
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

<template>
    <AppLayout title="AI Question Generator">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                AI Question Generator for {{ questionPool.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium">Generate Questions with AI</h3>
                            <p class="text-sm text-gray-600">Create questions automatically using AI based on a topic or article.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="mb-4">
                                    <InputLabel for="source_type" value="Source Type" />
                                    <select
                                        id="source_type"
                                        v-model="form.source_type"
                                        class="mt-1 block w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm transition-colors duration-200"
                                    >
                                        <option value="topic">Topic</option>
                                        <option value="article">Article/Text</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.source_type" />
                                </div>

                                <div v-if="form.source_type === 'topic'" class="mb-4">
                                    <InputLabel for="topic" value="Topic" />
                                    <TextInput
                                        id="topic"
                                        v-model="form.topic"
                                        type="text"
                                        class="mt-1 block w-full focus:ring-green-500 focus:border-green-500 transition-colors duration-200"
                                        placeholder="e.g. World War II, Photosynthesis, JavaScript"
                                    />
                                    <InputError class="mt-2" :message="form.errors.topic" />
                                </div>

                                <div v-if="form.source_type === 'article'" class="mb-4">
                                    <InputLabel for="article" value="Article/Text" />
                                    <textarea
                                        id="article"
                                        v-model="form.article"
                                        rows="5"
                                        class="mt-1 block w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm transition-colors duration-200"
                                        placeholder="Paste your article or text here..."
                                    ></textarea>
                                    <InputError class="mt-2" :message="form.errors.article" />
                                </div>

                                <div class="mb-4">
                                    <InputLabel for="question_type" value="Question Type" />
                                    <select
                                        id="question_type"
                                        v-model="form.question_type"
                                        class="mt-1 block w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm transition-colors duration-200"
                                    >
                                        <option v-for="type in questionTypes" :value="type.value">{{ type.label }}</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.question_type" />
                                </div>

                                <div class="mb-4">
                                    <InputLabel for="number_of_questions" value="Number of Questions" />
                                    <TextInput
                                        id="number_of_questions"
                                        v-model="form.number_of_questions"
                                        type="number"
                                        min="1"
                                        max="10"
                                        class="mt-1 block w-full focus:ring-green-500 focus:border-green-500 transition-colors duration-200"
                                    />
                                    <InputError class="mt-2" :message="form.errors.number_of_questions" />
                                </div>

                                <div class="mb-4">
                                    <InputLabel for="difficulty" value="Difficulty Level" />
                                    <select
                                        id="difficulty"
                                        v-model="form.difficulty"
                                        class="mt-1 block w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm transition-colors duration-200"
                                    >
                                        <option value="easy">Easy</option>
                                        <option value="medium">Medium</option>
                                        <option value="hard">Hard</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.difficulty" />
                                </div>

                                <div class="mb-4">
                                    <InputLabel for="language" value="Language" />
                                    <TextInput
                                        id="language"
                                        v-model="form.language"
                                        type="text"
                                        class="mt-1 block w-full focus:ring-green-500 focus:border-green-500 transition-colors duration-200"
                                        placeholder="Language for questions"
                                    />
                                    <InputError class="mt-2" :message="form.errors.language" />
                                </div>

                                <button 
                                    @click="generateQuestions" 
                                    :disabled="form.processing || loading"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition-colors duration-200"
                                >
                                    <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span v-else>Generate Questions</span>
                                </button>
                            </div>

                            <div v-if="showPreview && generatedQuestions.length > 0">
                                <h4 class="text-lg font-medium mb-4">Generated Questions Preview</h4>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-500">
                                        {{ generatedQuestions.length }} question(s) generated
                                    </span>
                                    <button 
                                        @click="deleteAllQuestions"
                                        class="inline-flex items-center text-sm text-red-600 hover:text-red-800 transition-colors duration-150"
                                    >
                                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete All
                                    </button>
                                </div>

                                <div class="space-y-4 max-h-[500px] overflow-y-auto">
                                    <div v-for="(question, index) in generatedQuestions" :key="index" 
                                        class="p-4 border rounded-lg relative group hover:shadow-md transition-shadow duration-200">
                                        <!-- Delete button for individual question -->
                                        <button
                                            @click="deleteQuestion(index)"
                                            class="absolute top-2 right-2 text-gray-400 hover:text-red-600 transition-colors duration-150"
                                            title="Delete question"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                        
                                        <!-- Question text -->
                                        <p class="font-medium pr-6">{{ question.question }}</p>

                                        <!-- Multiple Choice -->
                                        <div v-if="question.type === 'multiple_choice'" class="mt-2">
                                            <p class="text-sm text-gray-600">Options:</p>
                                            <ul class="list-disc pl-5">
                                                <li v-for="(option, optIndex) in question.options" :key="optIndex">
                                                    {{ option }}
                                                    <span v-if="question.correct_answers.includes(option) || question.correct_answers.includes(optIndex)" 
                                                        class="text-green-600 ml-1 font-medium">✓</span>
                                                </li>
                                            </ul>
                                        </div>

                                        <!-- True/False -->
                                        <div v-else-if="question.type === 'true_false'" class="mt-2">
                                            <p class="text-sm text-gray-600">Correct Answer: 
                                                <span class="font-medium text-green-600">
                                                    {{ question.correct_answers[0] ? 'True' : 'False' }}
                                                </span>
                                            </p>
                                        </div>

                                        <!-- Other Types -->
                                        <div v-else-if="question.type !== 'matching'" class="mt-2">
                                            <p class="text-sm text-gray-600">Correct Answer:
                                                <span class="font-medium text-green-600">
                                                    {{ question.correct_answers.join(', ') }}
                                                </span>
                                            </p>
                                        </div>

                                        <p class="text-sm mt-2 text-gray-500">
                                            Type: 
                                            <span class="capitalize">{{ question.type ? question.type.replace('_', ' ') : '' }}</span>
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-4 flex justify-end space-x-2">
                                    <button 
                                        @click="showPreview = false" 
                                        class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors duration-150"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Regenerate
                                    </button>
                                    <button 
                                        @click="saveQuestions" 
                                        :disabled="form.processing"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition-colors duration-200"
                                    >
                                        <svg v-if="!form.processing" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span v-if="form.processing" class="flex items-center">
                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Saving...
                                        </span>
                                        <span v-else>Save to Pool</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <FeatureLimitModal 
            v-model="showModal"
            :featureName="modalFeature"
            :message="modalMessage"
        />
    </AppLayout>
</template>