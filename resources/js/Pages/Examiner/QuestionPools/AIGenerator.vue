<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { useToast } from 'vue-toastification';

const toast = useToast();

const props = defineProps({
    questionPool: Object,
});

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
    number_of_questions: 5,
    language: 'English',
    pool_id: props.questionPool.id,
});

const generateQuestions = async () => {
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
            alert('No questions generated. Please try again.');
        }
    } catch (error) {
        console.error('Error generating questions:', error);
        alert(error.response?.data?.error || 'Failed to generate questions.');
    } finally {
        loading.value = false;
    }
};

const saveQuestions = () => {
    // Create a new form instance with all required data
    const submissionForm = useForm({
        questions: generatedQuestions.value.map(question => ({
            question: question.question,
            type: question.type,
            options: question.options || [],
            correct_answers: question.correct_answers || [],
            points: 1, // Default points
            is_required: true
        })),
        pool_id: props.questionPool.id
    });

    submissionForm.post(route('examiner.question-pools.store-ai-questions'), {
        preserveScroll: true,
        onSuccess: () => {
            generatedQuestions.value = [];
            showPreview.value = false;
            // Show success message
            toast.success('Questions saved successfully!');
        },
        onError: (errors) => {
            console.error('Error saving questions:', errors);
            // Show error message
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

// Add a delete all function
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
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
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
                                        class="mt-1 block w-full"
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
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        placeholder="Paste your article or text here..."
                                    ></textarea>
                                    <InputError class="mt-2" :message="form.errors.article" />
                                </div>

                                <div class="mb-4">
                                    <InputLabel for="question_type" value="Question Type" />
                                    <select
                                        id="question_type"
                                        v-model="form.question_type"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
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
                                        max="20"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError class="mt-2" :message="form.errors.number_of_questions" />
                                </div>

                                <div class="mb-4">
                                    <InputLabel for="difficulty" value="Difficulty Level" />
                                    <select
                                        id="difficulty"
                                        v-model="form.difficulty"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
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
                                        class="mt-1 block w-full"
                                        placeholder="Language for questions"
                                    />
                                    <InputError class="mt-2" :message="form.errors.language" />
                                </div>

                                <PrimaryButton @click="generateQuestions" :disabled="form.processing || loading">
                                    <span v-if="loading">Generating...</span>
                                    <span v-else>Generate Questions</span>
                                </PrimaryButton>
                            </div>

                            <div v-if="showPreview && generatedQuestions.length > 0">
                                <h4 class="text-lg font-medium mb-4">Generated Questions Preview</h4>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-500">
                                        {{ generatedQuestions.length }} question(s) generated
                                    </span>
                                    <button 
                                        @click="deleteAllQuestions"
                                        class="text-sm text-red-600 hover:text-red-800"
                                    >
                                        Delete All
                                    </button>
                                </div>

                                <div class="space-y-4 max-h-[500px] overflow-y-auto">
                                    <div v-for="(question, index) in generatedQuestions" :key="index" 
                                        class="p-4 border rounded-lg relative group">

                                        <!-- Delete button for individual question -->
                                        <button
                                            @click="deleteQuestion(index)"
                                            class="absolute top-2 right-2 text-gray-400 hover:text-red-600"
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
                                                        class="text-green-600 ml-1">✓</span>
                                                </li>
                                            </ul>
                                        </div>

                                        <!-- True/False -->
                                        <div v-else-if="question.type === 'true_false'" class="mt-2">
                                            <p class="text-sm text-gray-600">Correct Answer: 
                                                <span class="font-medium">
                                                    {{ question.correct_answers[0] ? question.correct_answers[0] : 'False' }}
                                                </span>
                                            </p>
                                        </div>

                                        <!-- Other Types -->
                                        <div v-else-if="question.type !== 'matching'" class="mt-2">
                                            <p class="text-sm text-gray-600">Correct Answer:
                                                <span class="font-medium">
                                                    {{ question.correct_answers.join(', ') }}
                                                </span>
                                            </p>
                                        </div>

                                        <p class="text-sm mt-2">
                                            Type: 
                                            <span class="capitalize">{{ question.type ? question.type.replace('_', ' ') : '' }}</span>
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-4 flex justify-end space-x-2">
                                    <button 
                                        @click="showPreview = false" 
                                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
                                    >
                                        Regenerate
                                    </button>
                                    <PrimaryButton @click="saveQuestions" :disabled="form.processing">
                                        Save to Pool
                                    </PrimaryButton>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>