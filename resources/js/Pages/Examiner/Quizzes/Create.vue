<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import Checkbox from '@/Components/Checkbox.vue';
import SelectInput from '@/Components/SelectInput.vue';
import DateTimePicker from '@/Components/DateTimePicker.vue';
import RichTextArea from '@/Components/RichTextArea.vue';
import Multiselect from '@vueform/multiselect';
import { format, parseISO } from 'date-fns';

const props = defineProps({
    organization: Object,
    availablePools: Array,
    quizTypes: Object,
    industries: Object,
    availableQuestions: Array,
    quiz: {
        type: Object,
        default: () => ({ pools: [] })
    }
});

const poolManager = ref(null);

const { flash } = usePage().props;

const form = useForm({
    title: '',
    description: '',
    instructions: '',
    quiz_type: 'test',
    industry: null,
    is_published: false,
    is_public: false,
    randomize_questions: false,
    randomize_answers: false,
    show_correct_answers: false,
    show_leaderboard: false,
    enable_discussions: false,
    max_attempts: null,
    max_participants: '',
    time_limit: '',
    passing_score: '',
    starts_at: '',
    ends_at: '',
    settings: {},
    pools: props.quiz.pools || [],
    survey_thank_you_message: '',
});

const isValid = computed(() => {
    return form.title && 
           (!poolManager.value || 
            poolManager.value.getPoolData().every(p => p.questions.length > 0));
});

const submit = () => {
    
    // Prepare the form data for submission
    const formData = {
        ...form.data(),
        max_attempts: form.max_attempts !== null ? parseInt(form.max_attempts) : null,
        max_participants: form.max_participants !== null ? parseInt(form.max_participants) : null,
        time_limit: form.time_limit !== null ? parseInt(form.time_limit) : null,
        passing_score: form.passing_score !== null ? parseInt(form.passing_score) : null,
        starts_at: form.starts_at ? form.starts_at : null,
        ends_at: form.ends_at ? form.ends_at : null,
        
    };
    
    form.transform(() => formData)
        .post(route('examiner.quizzes.store', props.organization.id), {
            onSuccess: () => {
                // Reset form on success
                form.reset();
            },
            onError: (errors) => {
                console.error('Validation errors:', errors);
            }
        });
};

const selectedPools = ref([]);

const addSelectedPools = () => {
    if (selectedPools.value.length > 0) {
        selectedPools.value.forEach(poolId => {
            const pool = props.availablePools.find(p => p.id === poolId);
            if (pool && !pools.value.some(p => p.id === poolId)) {
                pools.value.push({
                    id: pool.id,
                    name: pool.name,
                    description: pool.description,
                    questions_to_show: pool.questions_to_show,
                    questions: pool.questions.map(q => q.id)
                });
            }
        });
        selectedPools.value = [];
    }
};

const formatForDisplay = (isoString) => {
  if (!isoString) return '';
  return format(parseISO(isoString), 'MMM d, yyyy h:mm a');
};

const formatDateTime = (dateTime) => {
    if (!dateTime) return '';
    try {
        // Attempt to parse the date string provided by DateTimePicker
        const date = new Date(dateTime);

        // Check if date is valid
        if (isNaN(date.getTime())) {
            console.warn('formatDateTime received an unparsable date string:', dateTime);
            return dateTime; // Return original string if parsing fails
        }

        // Format the date and time for display (adjust options as needed)
        const options = {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        };
        return date.toLocaleString('en-US', options);
    } catch (error) {
        console.error('Error formatting date for display:', error, 'Input:', dateTime);
        return dateTime; // Fallback to original input on error
    }
};

</script>

<template>
    <AppLayout title="Create New Quiz">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create New Quiz for {{ organization.name }}
            </h2>
        </template>

        <!-- Success Message -->
        <div v-if="flash.success" class="mb-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-green-100 border border-green-400 text-green-2 px-4 py-3 rounded">
                {{ flash.success }}
            </div>
        </div>

        <!-- Error Message -->
        <div v-if="flash.error" class="mb-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ flash.error }}
            </div>
        </div>

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
                                        <Textarea
                                            id="description"
                                            v-model="form.description"
                                            class="mt-1 block w-full"
                                            rows="3"
                                        />
                                        <InputError class="mt-2" :message="form.errors.description" />
                                    </div>

                                    <div>
                                        <InputLabel for="instructions" value="Instructions" />
                                        <RichTextArea
                                            id="instructions"
                                            v-model="form.instructions"
                                            label="Instructions"
                                            :error="form.errors.instructions"
                                        />
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
                                                <option v-for="(label, value) in quizTypes" :key="value" :value="value">
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
                                                <option v-for="(label, value) in industries" :key="value" :value="value">
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
                                            <InputLabel for="is_published" value="Publish Immediately" class="ml-2" />
                                        </div>

                                        <div class="flex items-center">
                                            <Checkbox id="is_public" v-model:checked="form.is_public" />
                                            <InputLabel for="is_public" value="Make Public" class="ml-2" />
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
                                            <Checkbox id="show_correct_answers" v-model:checked="form.show_correct_answers" />
                                            <InputLabel for="show_correct_answers" value="Show Correct Answers" class="ml-2" />
                                        </div>

                                        <div class="flex items-center">
                                            <Checkbox id="show_leaderboard" v-model:checked="form.show_leaderboard" />
                                            <InputLabel for="show_leaderboard" value="Show Leaderboard" class="ml-2" />
                                        </div>

                                        <div class="flex items-center">
                                            <Checkbox id="enable_discussions" v-model:checked="form.enable_discussions" />
                                            <InputLabel for="enable_discussions" value="Enable Discussions" class="ml-2" />
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
                                                @input="e => form.max_attempts = e.target.value === '' ? null : parseInt(e.target.value)"
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

                                        <div>
                                            <InputLabel for="time_limit" value="Time Limit (minutes)" />
                                            <TextInput
                                                id="time_limit"
                                                v-model="form.time_limit"
                                                type="number"
                                                min="1"
                                                class="mt-1 block w-full"
                                            />
                                            <InputError class="mt-2" :message="form.errors.time_limit" />
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
                                                    class="mt-1 block w-full"
                                                    :display-format="'MMM d, yyyy h:mm a'"
                                                    :model-value="form.starts_at"
                                                    @update:modelValue="(val) => form.starts_at = val"
                                                    label="Start Date & Time"
                                                />
                                                <InputError class="mt-2" :message="form.errors.starts_at" />
                                            </div>

                                            <div class="relative">
                                                <InputLabel for="ends_at" value="End Date & Time" />
                                                <DateTimePicker
                                                    id="ends_at"
                                                    v-model="form.ends_at"
                                                    class="mt-1 block w-full"
                                                    :display-format="'MMM d, yyyy h:mm a'"
                                                    :model-value="form.ends_at"
                                                    @update:modelValue="(val) => form.ends_at = val"
                                                    label="Start Date & Time"   
                                                />
                                                <InputError class="mt-2" :message="form.errors.ends_at" />
                                            </div>
                                        </div>
                                    </div>

                                <!-- Submit Button -->
                                <div class="flex items-center justify-end">
                                    <PrimaryButton 
                                        :class="{ 'opacity-25': form.processing }" 
                                        :disabled="form.processing || !isValid"
                                    >
                                        Create Quiz
                                    </PrimaryButton>
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
/* Custom styles for date picker dropdowns */
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