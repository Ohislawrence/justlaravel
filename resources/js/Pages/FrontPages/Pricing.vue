<!-- resources/js/Pages/Pricing.vue -->
<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout2 from '@/Layouts/AppLayout2.vue';
import Cta from '@/Components/Cta.vue';

// Refs to manage the open state of each FAQ item
const openFaq1 = ref(false);
const openFaq2 = ref(false);
const openFaq3 = ref(false);
const openFaq4 = ref(false);

// Function to toggle FAQ state
const toggleFaq = (faqNumber) => {
  if (faqNumber === 1) {
    openFaq1.value = !openFaq1.value;
    // Close others if needed, or keep them independent
    // openFaq2.value = false; openFaq3.value = false; openFaq4.value = false;
  } else if (faqNumber === 2) {
    openFaq2.value = !openFaq2.value;
  } else if (faqNumber === 3) {
    openFaq3.value = !openFaq3.value;
  } else if (faqNumber === 4) {
    openFaq4.value = !openFaq4.value;
  }
};
</script>

<template>
    <Head title="Pricing - QuizPortal NG" />
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Simple & Transparent
                    </div>
                    <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold mb-6 text-gray-900 leading-tight">
                        Choose the Plan <br>That Fits Your Needs
                    </h1>
                    <p class="text-lg sm:text-xl text-gray-700 mb-10 max-w-2xl mx-auto">
                        Flexible pricing options designed for individual teachers, schools, and large institutions. All plans are priced in Nigerian Naira (₦).
                    </p>
                </div>
            </div>
        </section>

        <!-- Pricing Section (Main Content) -->
        <section class="py-16 sm:py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6">
                 <!-- Pricing Toggle/FAQ Intro (Optional) -->
                <div class="max-w-2xl mx-auto text-center mb-12 sm:mb-16">
                    <!-- <div class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-medium mb-4">
                        Billed Monthly
                    </div> -->
                    <p class="text-gray-600">
                        Have questions before choosing? <a :href="route('contact')" class="text-emerald-600 font-medium hover:underline">Contact our sales team</a> or check out our <a href="#" class="text-emerald-600 font-medium hover:underline">FAQs</a>.
                    </p>
                </div>

                <!-- Pricing Cards -->
                <div class="grid md:grid-cols-3 gap-6 sm:gap-8 max-w-6xl mx-auto">
                    <!-- Starter Plan -->
                    <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-200 hover:shadow-md transition">
                        <div class="text-center mb-8">
                            <h3 class="text-lg sm:text-xl font-semibold mb-2">Starter</h3>
                            <div class="text-2xl sm:text-3xl font-bold mb-2">₦5,000<span class="text-sm font-normal text-gray-500">/month</span></div>
                            <p class="text-gray-600 text-sm sm:text-base">Perfect for individual teachers</p>
                        </div>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-emerald-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Up to 100 students
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-emerald-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Unlimited quizzes
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-emerald-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Basic analytics
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-emerald-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                WhatsApp support
                            </li>
                        </ul>
                        <Link :href="route('register')" class="w-full py-3 text-center border-2 border-emerald-600 text-emerald-600 rounded-lg hover:bg-emerald-50 transition font-medium text-sm block">
                            Start Free Trial
                        </Link>
                    </div>

                    <!-- School Plan (Highlighted) -->
                    <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-lg border-2 border-emerald-600 relative transform hover:scale-[1.02] transition duration-300">
                        <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-emerald-600 text-white px-4 py-1 rounded-full text-xs font-medium">
                            Most Popular
                        </div>
                        <div class="text-center mb-8 mt-4">
                            <h3 class="text-lg sm:text-xl font-semibold mb-2">School</h3>
                            <div class="text-2xl sm:text-3xl font-bold mb-2">₦25,000<span class="text-sm font-normal text-gray-500">/month</span></div>
                            <p class="text-gray-600 text-sm sm:text-base">For schools and institutions</p>
                        </div>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-emerald-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Up to 1,000 students
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-emerald-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Multiple teachers
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-emerald-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Advanced analytics
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-emerald-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Parent portal
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-emerald-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Priority support
                            </li>
                        </ul>
                        <Link :href="route('register')" class="w-full py-3 text-center bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition font-medium text-sm block">
                            Start Free Trial
                        </Link>
                    </div>

                    <!-- Enterprise Plan -->
                    <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-200 hover:shadow-md transition">
                        <div class="text-center mb-8">
                            <h3 class="text-lg sm:text-xl font-semibold mb-2">Enterprise</h3>
                            <div class="text-2xl sm:text-3xl font-bold mb-2">Custom</div>
                            <p class="text-gray-600 text-sm sm:text-base">For large institutions</p>
                        </div>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-emerald-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Unlimited students
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-emerald-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Unlimited quizzes
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-emerald-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Premium analytics
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-emerald-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Dedicated support
                            </li>
                        </ul>
                        <a href="mailto:sales@quizportal.ng" class="w-full py-3 text-center border-2 border-emerald-600 text-emerald-600 rounded-lg hover:bg-emerald-50 transition font-medium text-sm block">
                            Contact Sales
                        </a>
                    </div>
                </div>

                <!-- Plan Comparison Table (Optional) -->
                
                <div class="mt-20 max-w-6xl mx-auto overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Feature</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Starter</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">School</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Enterprise</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Students</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Up to 100</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Up to 1,000</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Unlimited</td>
                            </tr>
                             Add more rows for other features
                        </tbody>
                    </table>
                </div>

                 <!-- FAQ Section -->
                <div class="mt-20 max-w-4xl mx-auto">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-8 text-center">Frequently Asked Questions</h2>
                    <div class="space-y-4">
                        <!-- FAQ Item 1 -->
                        <div class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden">
                            <button @click="toggleFaq(1)" class="flex justify-between items-center w-full p-6 text-left focus:outline-none">
                                <span class="text-lg font-medium text-gray-900">Is there a free trial available?</span>
                                <svg class="w-5 h-5 text-gray-500 transform transition-transform" :class="{ 'rotate-180': openFaq1 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div v-show="openFaq1" class="px-6 pb-6 text-gray-600">
                                Yes, all our paid plans come with a 30-day free trial. No credit card is required to start your trial.
                            </div>
                        </div>
                        <!-- FAQ Item 2 -->
                        <div class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden">
                            <button @click="toggleFaq(2)" class="flex justify-between items-center w-full p-6 text-left focus:outline-none">
                                <span class="text-lg font-medium text-gray-900">What payment methods do you accept?</span>
                                <svg class="w-5 h-5 text-gray-500 transform transition-transform" :class="{ 'rotate-180': openFaq2 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div v-show="openFaq2" class="px-6 pb-6 text-gray-600">
                                We accept payments via bank transfer, major credit/debit cards, and mobile money. School and Enterprise plans can also be paid via invoice.
                            </div>
                        </div>
                        <!-- FAQ Item 3 -->
                        <div class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden">
                            <button @click="toggleFaq(3)" class="flex justify-between items-center w-full p-6 text-left focus:outline-none">
                                <span class="text-lg font-medium text-gray-900">Can I change plans later?</span>
                                <svg class="w-5 h-5 text-gray-500 transform transition-transform" :class="{ 'rotate-180': openFaq3 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div v-show="openFaq3" class="px-6 pb-6 text-gray-600">
                                Yes, you can upgrade, downgrade, or cancel your plan at any time. Changes to your plan will be prorated.
                            </div>
                        </div>
                        <!-- FAQ Item 4 -->
                        <div class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden">
                            <button @click="toggleFaq(4)" class="flex justify-between items-center w-full p-6 text-left focus:outline-none">
                                <span class="text-lg font-medium text-gray-900">Do you offer discounts for NGOs or educational institutions?</span>
                                <svg class="w-5 h-5 text-gray-500 transform transition-transform" :class="{ 'rotate-180': openFaq4 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div v-show="openFaq4" class="px-6 pb-6 text-gray-600">
                                Yes, we offer special pricing for qualified non-profit organizations and educational institutions. Please contact our sales team for more information.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <Cta />
    </AppLayout2>
</template>

<style scoped>
/* Add any specific styles for the pricing page content if needed */
/* You might want to add styles for FAQ accordion behavior if implemented */
</style>