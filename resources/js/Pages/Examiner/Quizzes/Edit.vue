<script setup>
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
import RichTextArea from '@/Components/RichTextArea.vue';

const props = defineProps({
    quiz: Object,
    quizTypes: Object,
    industries: Object,
});

const form = useForm({
    title: props.quiz.title,
    description: props.quiz.description,
    instructions: props.quiz.instructions,
    quiz_type: props.quiz.quiz_type,
    industry: props.quiz.industry,
    is_published: props.quiz.is_published,
    is_public: props.quiz.is_public,
    randomize_questions: props.quiz.randomize_questions,
    randomize_answers: props.quiz.randomize_answers,
    show_correct_answers: props.quiz.show_correct_answers,
    show_leaderboard: props.quiz.show_leaderboard,
    enable_discussions: props.quiz.enable_discussions,
    max_attempts: props.quiz.max_attempts,
    max_participants: props.quiz.max_participants,
    time_limit: props.quiz.time_limit,
    passing_score: props.quiz.passing_score,
    starts_at: props.quiz.starts_at,
    ends_at: props.quiz.ends_at,
    settings: props.quiz.settings,
});

const submit = () => {
    form.put(route('examiner.quizzes.update', props.quiz.id));
};
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
                                            />
                                            <InputError class="mt-2" :message="form.errors.starts_at" />
                                        </div>

                                        <div class="relative">
                                            <InputLabel for="ends_at" value="End Date & Time" />
                                            <DateTimePicker
                                                id="ends_at"
                                                v-model="form.ends_at"
                                                class="mt-1 block w-full"
                                            />
                                            <InputError class="mt-2" :message="form.errors.ends_at" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-end mt-6">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Update Quiz
                                </PrimaryButton>
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