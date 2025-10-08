<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import Checkbox from '@/Components/Checkbox.vue';
import SelectInput from '@/Components/SelectInput.vue';
import DateTimePicker from '@/Components/DateTimePicker.vue';
import RichtextEditorNormal from '@/Components/RichtextEditorNormal.vue';

const props = defineProps({
    quiz: Object,
    quizTypes: Object,
    industries: Object,
    certificateTemplates: {
        type: Array, 
        default: () => []
    },
    gradingSystems: {
        type: Array,
        default: () => []
    }
});

const form = useForm({
    title: props.quiz.title,
    description: props.quiz.description || '',
    instructions: props.quiz.instructions,
    grading_system_id: props.quiz.grading_system_id || null,
    quiz_type: props.quiz.quiz_type,
    industry: props.quiz.industry || '',
    is_published: props.quiz.is_published,
    is_public: props.quiz.is_public,
    is_proctored: props.quiz.is_proctored,
    randomize_questions: props.quiz.randomize_questions,
    randomize_answers: props.quiz.randomize_answers,
    show_correct_answers: props.quiz.show_correct_answers,
    show_leaderboard: props.quiz.show_leaderboard,
    enable_discussions: props.quiz.enable_discussions,
    max_attempts: props.quiz.max_attempts,
    max_participants: props.quiz.max_participants,
    time_limit: props.quiz.time_limit,
    passing_score: props.quiz.passing_score,
    starts_at: props.quiz.starts_at || null,
    ends_at: props.quiz.ends_at || null,
    settings: props.quiz.settings,
    survey_thank_you_message: props.quiz.survey_thank_you_message,
    enable_certificates: props.quiz.enable_certificates || false,
    certificate_template_id: props.quiz.certificate_template_id || null,
    certificate_pass_percentage: props.quiz.certificate_pass_percentage,
    certificate_expiry_days: props.quiz.certificate_expiry_days,
    settings: {
        show_scores: props.quiz.settings?.show_scores || false,
        review_questions: props.quiz.settings?.review_questions || false,
        per_question_timing: props.quiz.settings?.per_question_timing || false,
        default_time_per_question: props.quiz.settings?.default_time_per_question,
    }
});

const submit = () => {
    form.put(route('examiner.quizzes.update', props.quiz.id));
};

const selectedGradingSystem = computed(() => {
    if (!form.grading_system_id) return null;
    return props.gradingSystems.find(s => s.id == form.grading_system_id);
});
</script>

<template>
    <AppLayout :title="`Edit ${quiz.title}`">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Edit Quiz: {{ quiz.title }}
                </h2>
                <Link :href="route('examiner.quizzes.show', quiz.id)" class="text-sm text-gray-600 hover:text-gray-900">
                    Back to Quiz
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 gap-6">
                                <!-- Basic Information -->
                                <div class="space-y-6">
                                    <h3 class="text-lg font-medium">Basic Information</h3>
                                    
                                    <div>
                                        <InputLabel for="title" value="Quiz Title *" />
                                        <TextInput
                                            id="title"
                                            v-model="form.title"
                                            type="text"
                                            class="mt-1 block w-full"
                                            required
                                            autofocus
                                        />
                                        <InputError class="mt-2" :message="form.errors.title" />
                                    </div>

                                    <div>
                                        <InputLabel for="description" value="Description" />
                                        <RichtextEditorNormal 
                                            id="description"
                                            v-model="form.description" 
                                            :error="form.errors.description"
                                            :content="form.description" 
                                            class="mt-1 block w-full"
                                        />
                                        <InputError class="mt-2" :message="form.errors.description" />
                                    </div>

                                    <div>
                                        <InputLabel for="instructions" value="Instructions" />
                                        <RichtextEditorNormal 
                                            id="instructions"
                                            v-model="form.instructions"
                                            :content="form.instructions" 
                                            :error="form.errors.instructions"
                                            class="mt-1 block w-full"
                                        />
                                        <InputError class="mt-2" :message="form.errors.instructions" />
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <InputLabel for="quiz_type" value="Quiz Type *" />
                                            <SelectInput
                                                id="quiz_type"
                                                v-model="form.quiz_type"
                                                class="mt-1 block w-full"
                                                required
                                            >
                                                <option 
                                                    v-for="(label, value) in quizTypes" 
                                                    :key="value" 
                                                    :value="value"
                                                    :selected="form.quiz_type === value"
                                                >
                                                    {{ label }}
                                                </option>
                                            </SelectInput>
                                            <InputError class="mt-2" :message="form.errors.quiz_type" />
                                        </div>

                                        <div>
                                            <InputLabel for="industry" value="Industry" />
                                            <SelectInput
                                                id="industry"
                                                v-model="form.industry"
                                                class="mt-1 block w-full"
                                            >
                                                <option :value="null">Select Industry</option>
                                                <option 
                                                    v-for="(label, value) in industries" 
                                                    :key="value" 
                                                    :value="value"
                                                    :selected="form.industry === value"
                                                >
                                                    {{ label }}
                                                </option>
                                            </SelectInput>
                                            <InputError class="mt-2" :message="form.errors.industry" />
                                        </div>

                                        <div v-if="form.quiz_type === 'survey'">
                                        <InputLabel for="survey_thank_you_message" value="Survey Thank You Message" />
                                        <Textarea
                                            id="survey_thank_you_message"
                                            v-model="form.survey_thank_you_message"
                                            class="mt-1 block w-full"
                                            rows="3"
                                            placeholder="Thank you for completing our survey!"
                                        />
                                        <InputError class="mt-2" :message="form.errors['survey_thank_you_message']" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Settings -->
                                <div class="space-y-6">
                                    <h3 class="text-lg font-medium">Quiz Settings</h3>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        <div class="flex items-center">
                                            <Checkbox id="is_published" v-model:checked="form.is_published" />
                                            <InputLabel for="is_published" value="Publish" class="ml-2" />
                                        </div>

                                        <div class="flex items-center">
                                            <Checkbox id="is_proctored" v-model:checked="form.is_proctored" />
                                            <InputLabel for="is_proctored" value="Use Proctor" class="ml-2" />
                                        </div>

                                        <div class="flex items-center">
                                            <Checkbox id="randomize_questions" v-model:checked="form.randomize_questions" />
                                            <InputLabel for="randomize_questions" value="Randomize Questions" class="ml-2" />
                                        </div>

                                        <div class="flex items-center">
                                            <Checkbox id="randomize_answers" v-model:checked="form.randomize_answers" />
                                            <InputLabel for="randomize_answers" value="Randomize Answers" class="ml-2" />
                                        </div>

                                        <div class="flex items-center">
                                            <Checkbox id="show_scores" v-model:checked="form.settings.show_scores" />
                                            <InputLabel for="show_scores" value="Show scores summary when done" class="ml-2" />
                                        </div>

                                        <div class="flex items-center">
                                            <Checkbox id="review_questions" v-model:checked="form.settings.review_questions" />
                                            <InputLabel for="review_questions" value="Show questions & answers when done" class="ml-2" />
                                        </div>

                                        <div class="flex items-center">
                                            <Checkbox id="per_question_timing" v-model:checked="form.settings.per_question_timing" />
                                            <InputLabel for="per_question_timing" value="Timed per question" class="ml-2" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Restrictions -->
                                <div class="space-y-6">
                                    <h3 class="text-lg font-medium">Access Restrictions</h3>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <InputLabel for="max_attempts" value="Max Attempts (0 for unlimited)" />
                                            <TextInput
                                                id="max_attempts"
                                                v-model="form.max_attempts"
                                                type="number"
                                                min="0"
                                                class="mt-1 block w-full"
                                            />
                                            <InputError class="mt-2" :message="form.errors.max_attempts" />
                                        </div>

                                        <div>
                                            <InputLabel for="max_participants" value="Max Participants (0 for unlimited)" />
                                            <TextInput
                                                id="max_participants"
                                                v-model="form.max_participants"
                                                type="number"
                                                min="0"
                                                class="mt-1 block w-full"
                                            />
                                            <InputError class="mt-2" :message="form.errors.max_participants" />
                                        </div>

                                        <div v-if="form.settings.per_question_timing">
                                            <InputLabel for="time_per_question" value="Default Time per question (seconds)" />
                                            <TextInput
                                                v-model="form.settings.default_time_per_question"
                                                type="number"
                                                id="default_time_per_question"
                                                min="5"
                                                max="300"
                                                class="mt-1 block w-full"
                                                placeholder="60"
                                            />
                                            <InputError class="mt-2" :message="form.errors['settings.default_time_per_question']" />
                                            <p class="text-xs text-gray-500 mt-1">
                                            Each question will have a countdown timer. If a question does not have it own time, this will be used. Previous button is disabled.
                                            
                                            </p>
                                        </div>
                                        <div v-else>
                                            <InputLabel for="time_limit" value="Time Limit (minutes)" />
                                            <TextInput
                                                id="time_limit"
                                                v-model="form.time_limit"
                                                type="number"
                                                min="1"
                                                class="mt-1 block w-full"
                                            />
                                            <InputError class="mt-2" :message="form.errors.time_limit" />
                                            <p class="text-xs text-gray-500 mt-1">
                                            Previous button is enabled.
                                            </p>
                                        </div>

                                        <div>
                                            <InputLabel for="passing_score" value="Passing Score (%)" />
                                            <TextInput
                                                id="passing_score"
                                                v-model="form.passing_score"
                                                type="number"
                                                min="0"
                                                max="100"
                                                class="mt-1 block w-full"
                                            />
                                            <InputError class="mt-2" :message="form.errors.passing_score" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Schedule -->
                                <div class="space-y-6">
                                    <h3 class="text-lg font-medium">Schedule</h3>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="relative">
                                            <InputLabel for="starts_at" value="Start Date & Time" />
                                            <DateTimePicker
                                                id="starts_at"
                                                v-model="form.starts_at"
                                                timezone="UTC"
                                                class="mt-1 block w-full"
                                            />
                                            <InputError class="mt-2" :message="form.errors.starts_at" />
                                        </div>

                                        <div class="relative">
                                            <InputLabel for="ends_at" value="End Date & Time" />
                                            <DateTimePicker
                                                id="ends_at"
                                                v-model="form.ends_at"
                                                timezone="UTC"
                                                class="mt-1 block w-full"
                                            />
                                            <InputError class="mt-2" :message="form.errors.ends_at" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Grading System -->
                                <div class="space-y-6">
                                    <h3 class="text-lg font-medium">Grading System</h3>
                                    
                                    <div>
                                        <InputLabel for="grading_system_id" value="Select Grading System" />
                                        <SelectInput
                                            id="grading_system_id"
                                            v-model="form.grading_system_id"
                                            class="mt-1 block w-full"
                                        >
                                            <option :value="null">Default (Pass/Fail)</option>
                                            <option 
                                                v-for="system in gradingSystems" 
                                                :key="system.id" 
                                                :value="system.id"
                                                :selected="form.grading_system_id == system.id"
                                            >
                                                {{ system.name }} ({{ system.type }})
                                            </option>
                                        </SelectInput>
                                        <InputError class="mt-2" :message="form.errors.grading_system_id" />
                                        
                                        <div v-if="selectedGradingSystem" class="mt-3 p-3 bg-green-50 rounded">
                                            <h4 class="font-medium">{{ selectedGradingSystem.name }} Grade Ranges:</h4>
                                            <div class="mt-2">
                                                <div 
                                                    v-for="(range, index) in Array.isArray(selectedGradingSystem.grade_ranges) ? selectedGradingSystem.grade_ranges : []" 
                                                    :key="index" 
                                                    class="text-sm"
                                                >
                                                    {{ range.min }}% - {{ range.max }}%: {{ range.grade }} 
                                                    <span v-if="range.points">({{ range.points }} points)</span>
                                                    <span v-if="range.interpretation">- {{ range.interpretation }}</span>
                                                </div>
                                                
                                                <!-- Show message if no grade ranges exist -->
                                                <div v-if="!Array.isArray(selectedGradingSystem.grade_ranges) || selectedGradingSystem.grade_ranges.length === 0" class="text-sm text-gray-500">
                                                    No grade ranges defined for this system.
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>

                                <!-- Certificate Settings -->
                                <div class="space-y-6">
                                    <h3 class="text-lg font-medium">Certificate Settings</h3>
                                    
                                    <div class="card mt-4">
                                        <div class="card-body">
                                            <div class="form-check form-switch mb-3">
                                                <Checkbox 
                                                    id="enable_certificates" 
                                                    v-model:checked="form.enable_certificates" 
                                                />
                                                <InputLabel for="enable_certificates" value="Enable certificates for this quiz" class="ml-2" />
                                                <InputError class="mt-2" :message="form.errors.enable_certificates" />
                                            </div>

                                            <div v-if="form.enable_certificates" class="mt-3">
                                                <div class="mb-3">
                                                    <InputLabel for="certificate_template_id" value="Certificate Template" />
                                                    <SelectInput
                                                        id="certificate_template_id"
                                                        v-model="form.certificate_template_id"
                                                        class="mt-1 block w-full"
                                                    >
                                                        <option :value="null">Use Default Template</option>
                                                        <option 
                                                            v-for="template in certificateTemplates" 
                                                            :key="template.id" 
                                                            :value="template.id"
                                                            :selected="form.certificate_template_id === template.id"
                                                        >
                                                            {{ template.name }}
                                                        </option>
                                                    </SelectInput>
                                                    <InputError class="mt-2" :message="form.errors.certificate_template_id" />
                                                </div>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <InputLabel for="certificate_pass_percentage" value="Passing Percentage" />
                                                        <TextInput
                                                            id="certificate_pass_percentage"
                                                            v-model="form.certificate_pass_percentage"
                                                            type="number"
                                                            min="0"
                                                            max="100"
                                                            class="mt-1 block w-full"
                                                        />
                                                        <InputError class="mt-2" :message="form.errors.certificate_pass_percentage" />
                                                    </div>
                                                    <div>
                                                        <InputLabel for="certificate_expiry_days" value="Expiry Days (leave blank for no expiry)" />
                                                        <TextInput
                                                            id="certificate_expiry_days"
                                                            v-model="form.certificate_expiry_days"
                                                            type="number"
                                                            min="0"
                                                            class="mt-1 block w-full"
                                                        />
                                                        <InputError class="mt-2" :message="form.errors.certificate_expiry_days" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="flex items-center justify-end mt-6">
                                    <button 
                                        type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                        :class="{ 'opacity-25': form.processing }" 
                                        :disabled="form.processing"
                                    >
                                        Update Quiz
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Custom styles to enhance the green theme */
.bg-green-50 {
    background-color: #ecfdf5;
}

/* Green-themed input focus states */
input:focus, select:focus, textarea:focus {
    border-color: #10B981;
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
}

/* Green-themed checkbox */
input[type="checkbox"]:checked {
    background-color: #10B981;
    border-color: #10B981;
}

/* Green-themed select arrow */
select {
    background-image: url("image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2310B981' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
}

/* Green-themed button */
button[type="submit"] {
    background-color: #10B981;
    border-color: #10B981;
}

button[type="submit"]:hover {
    background-color: #059669;
    border-color: #059669;
}

button[type="submit"]:focus {
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.5);
}

/* Green-themed form labels */
label {
    color: #065f46;
}

/* Green-themed error messages */
.InputError {
    color: #047857;
}

/* Relative positioning for date picker */
.relative {
    position: relative;
}

/* Override parent container overflow for date picker dropdowns */
.relative :deep(.date-picker-dropdown),
.relative :deep(.datepicker-dropdown),
.relative :deep(.flatpickr-calendar) {
    z-index: 50;
}

/* If using a specific date picker library, adjust the selector accordingly */
.relative :deep(.date-picker-wrapper) {
    position: relative;
}

/* Ensure dropdown doesn't get clipped by parent containers */
.relative :deep(.dropdown) {
    position: absolute;
    z-index: 1000;
    width: 100%;
}
</style>