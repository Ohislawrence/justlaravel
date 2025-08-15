<!-- resources/js/Pages/Contact.vue -->
<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout2 from '@/Layouts/AppLayout2.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'; // Or use your own button style
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue'; // Make sure you have this component, or use a standard textarea
import Cta from '@/Components/Cta.vue';

// Define props for success message or pre-filled data if needed
const props = defineProps({
    status: String, // For success message
    // You could also pass pre-filled data like user name/email if logged in
    // userData: Object
});

const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: '',
});

const submit = () => {
    form.post(route('contact.submit'), { // Adjust route name as needed
        onFinish: () => {
            // Optionally reset the form or show a success message
             if (!form.hasErrors) {
                 form.reset(); // Reset only if submission was successful
             }
        },
    });
};
</script>

<template>
    <Head title="Contact Us - QuizPortal NG" />
    <AppLayout2>
        <!-- Hero Section -->
        <section class="relative bg-gradient-to-br from-emerald-50 via-white to-teal-50 py-16 sm:py-24 overflow-hidden">
            <!-- Background decorative elements -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-10 left-5 w-64 h-64 bg-emerald-500 rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 right-5 w-72 h-72 bg-teal-400 rounded-full blur-3xl"></div>
            </div>
            <div class="container mx-auto px-4 sm:px-6 relative z-10">
                <div class="max-w-3xl mx-auto text-center">
                    <div class="mb-6 inline-flex items-center px-4 py-2 bg-emerald-100 text-emerald-800 rounded-full text-sm font-medium border border-emerald-200">
                        <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Get In Touch
                    </div>
                    <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold mb-6 text-gray-900 leading-tight">
                        We'd Love to Hear <br>from You
                    </h1>
                    <p class="text-lg sm:text-xl text-gray-700 mb-10 max-w-2xl mx-auto">
                        Have questions, feedback, or need support? Reach out to us using the form below or through our contact details.
                    </p>
                </div>
            </div>
        </section>

        <!-- Contact Content -->
        <section class="py-16 sm:py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16">
                    <!-- Contact Form -->
                    <div class="bg-gray-50 p-6 sm:p-8 rounded-2xl border border-gray-100">
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">Send us a Message</h2>

                        <!-- Status Message -->
                        <div v-if="status" class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700">
                            {{ status }}
                        </div>
                        <div v-if="form.recentlySuccessful" class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700">
                            Thank you for your message! We'll get back to you soon.
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Name Input -->
                            <div>
                                <InputLabel for="name" value="Your Name" class="block text-sm font-medium text-gray-700 mb-1" />
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-sm"
                                    required
                                    autocomplete="name"
                                />
                                <InputError :message="form.errors.name" class="mt-1 text-sm text-red-600" />
                            </div>

                            <!-- Email Input -->
                            <div>
                                <InputLabel for="email" value="Your Email" class="block text-sm font-medium text-gray-700 mb-1" />
                                <TextInput
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-sm"
                                    required
                                    autocomplete="email"
                                />
                                <InputError :message="form.errors.email" class="mt-1 text-sm text-red-600" />
                            </div>

                             <!-- Subject Input -->
                            <div>
                                <InputLabel for="subject" value="Subject" class="block text-sm font-medium text-gray-700 mb-1" />
                                <TextInput
                                    id="subject"
                                    v-model="form.subject"
                                    type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-sm"
                                    required
                                />
                                <InputError :message="form.errors.subject" class="mt-1 text-sm text-red-600" />
                            </div>

                            <!-- Message Textarea -->
                            <div>
                                <InputLabel for="message" value="Your Message" class="block text-sm font-medium text-gray-700 mb-1" />
                                <!-- If you don't have a TextArea component, replace <TextArea> with <textarea> -->
                                <TextArea
                                    id="message"
                                    v-model="form.message"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-sm min-h-[150px]"
                                    required
                                />
                                <!--
                                <textarea
                                    id="message"
                                    v-model="form.message"
                                    rows="5"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-sm"
                                    required
                                ></textarea>
                                -->
                                <InputError :message="form.errors.message" class="mt-1 text-sm text-red-600" />
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-4">
                                <PrimaryButton
                                    type="submit"
                                    :disabled="form.processing"
                                    :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                                    class="w-full py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition shadow-sm hover:shadow font-medium"
                                >
                                    <span v-if="form.processing">Sending...</span>
                                    <span v-else>Send Message</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>

                    <!-- Contact Information -->
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">Contact Information</h2>
                        <p class="text-gray-600 mb-8">
                            Prefer to reach us directly? Use the information below.
                        </p>

                        <div class="space-y-6">
                            <!-- Address -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Our Office</h3>
                                    <p class="text-gray-600">
                                        QuizPortal Nigeria<br>
                                        Lagos, Nigeria<br>
                                        <!-- Add full address if available -->
                                    </p>
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Phone</h3>
                                    <p class="text-gray-600">
                                        <!-- Add phone number -->
                                        <!-- <a href="tel:+234XXXXXXXXXX" class="text-emerald-600 hover:underline">+234 XXX XXX XXXX</a> -->
                                        Coming Soon
                                    </p>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Email</h3>
                                    <p class="text-gray-600">
                                        <a href="mailto:support@quizportal.ng" class="text-emerald-600 hover:underline">support@quizportal.ng</a>
                                    </p>
                                    <p class="text-gray-600 mt-1">
                                        For general inquiries: <a href="mailto:info@quizportal.ng" class="text-emerald-600 hover:underline">info@quizportal.ng</a>
                                    </p>
                                </div>
                            </div>

                            <!-- Social Media -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Connect With Us</h3>
                                    <div class="flex space-x-4">
                                        <a href="#" target="_blank" class="text-gray-500 hover:text-emerald-600 transition">
                                            <span class="sr-only">Twitter</span>
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                            </svg>
                                        </a>
                                        <a href="#" target="_blank" class="text-gray-500 hover:text-emerald-600 transition">
                                            <span class="sr-only">Facebook</span>
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/>
                                            </svg>
                                        </a>
                                        <a href="#" target="_blank" class="text-gray-500 hover:text-emerald-600 transition">
                                            <span class="sr-only">LinkedIn</span>
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

         <!-- FAQ Section (Optional Placeholder) -->
        <!--
        <section class="py-16 sm:py-20 bg-gray-50">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-4xl mx-auto text-center mb-12 sm:mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
                    <p class="text-lg text-gray-600">
                        Find quick answers to common questions.
                    </p>
                </div>
                <div class="max-w-3xl mx-auto space-y-4">
                     FAQ Item 1
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <button class="flex justify-between items-center w-full p-6 text-left focus:outline-none">
                            <span class="text-lg font-medium text-gray-900">How do I reset my password?</span>
                            <svg class="w-5 h-5 text-gray-500 transform transition-transform" :class="{ 'rotate-180': isOpen1 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div v-show="isOpen1" class="px-6 pb-6 text-gray-600">
                            You can reset your password by clicking the "Forgot Password" link on the login page and following the instructions sent to your email.
                        </div>
                    </div>
                     Add more FAQ items here
                </div>
            </div>
        </section>
        -->

        <!-- Map Section (Optional Placeholder) -->
        <!--
        <section class="py-16 sm:py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-4xl mx-auto text-center mb-12 sm:mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Find Us</h2>
                    <p class="text-lg text-gray-600">
                        Visit our office in the heart of Lagos.
                    </p>
                </div>
                <div class="rounded-2xl overflow-hidden shadow-lg h-96 bg-gray-200">
                     Replace with actual map embed
                    <div class="w-full h-full flex items-center justify-center text-gray-500">
                        Interactive Map Placeholder
                    </div>
                </div>
            </div>
        </section>
        -->

        <!-- CTA Section -->
      <Cta />
    </AppLayout2>
</template>

<style scoped>
/* Add any specific styles for the contact page content if needed */
</style>