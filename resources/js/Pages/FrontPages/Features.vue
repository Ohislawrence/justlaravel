<!-- resources/js/Pages/Features.vue -->
<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout2 from '@/Layouts/AppLayout2.vue';
import Cta from '@/Components/Cta.vue';
defineProps({
    meta: Object,
});
</script>

<template>
    <Head title="Features - QuizPortal NG" />
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        Powerful & Intuitive
                    </div>
                    <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold mb-6 text-gray-900 leading-tight">
                        Everything You Need <br>for Smarter Assessments
                    </h1>
                    <p class="text-lg sm:text-xl text-gray-700 mb-10 max-w-2xl mx-auto">
                        Discover the comprehensive set of tools designed to make creating, delivering, and analyzing quizzes effortless for Nigerian educators.
                    </p>
                </div>
            </div>
        </section>

        <!-- Core Features Section -->
        <section id="core-features" class="py-16 sm:py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-4xl mx-auto text-center mb-12 sm:mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Core Features</h2>
                    <p class="text-lg sm:text-xl text-gray-600">Essential tools to enhance your teaching and assessment workflow.</p>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 max-w-6xl mx-auto">
                    <!-- Feature 1: AI Question Generation -->
                    <div class="group bg-gray-50 p-6 sm:p-8 rounded-2xl border border-gray-100 hover:border-emerald-200 hover:shadow-sm transition duration-300">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-200 transition">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold mb-3 text-gray-900">AI Question Generation</h3>
                        <p class="text-gray-600 mb-4 text-sm sm:text-base">Automatically create diverse quiz questions from curriculum topics, saving you hours of prep time.</p>
                        <div class="text-xs sm:text-sm text-emerald-600 font-medium">✓ Supports WAEC, JAMB, NECO</div>
                    </div>

                    <!-- Feature 2: Real-Time Analytics -->
                    <div class="group bg-gray-50 p-6 sm:p-8 rounded-2xl border border-gray-100 hover:border-emerald-200 hover:shadow-sm transition duration-300">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-200 transition">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold mb-3 text-gray-900">Real-Time Analytics</h3>
                        <p class="text-gray-600 mb-4 text-sm sm:text-base">Track student performance instantly, identify knowledge gaps, and generate insightful reports.</p>
                        <div class="text-xs sm:text-sm text-emerald-600 font-medium">✓ Class & Individual Reports</div>
                    </div>

                    <!-- Feature 3: Mobile-First Design -->
                    <div class="group bg-gray-50 p-6 sm:p-8 rounded-2xl border border-gray-100 hover:border-emerald-200 hover:shadow-sm transition duration-300">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-200 transition">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold mb-3 text-gray-900">Mobile-First Design</h3>
                        <p class="text-gray-600 mb-4 text-sm sm:text-base">Seamlessly create and take quizzes on any device. Optimized for low-bandwidth environments.</p>
                        <div class="text-xs sm:text-sm text-emerald-600 font-medium">✓ Quiz on moblie device</div>
                    </div>

                    <!-- Feature 4: Local Language Support -->
                    <div class="group bg-gray-50 p-6 sm:p-8 rounded-2xl border border-gray-100 hover:border-emerald-200 hover:shadow-sm transition duration-300">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-200 transition">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold mb-3 text-gray-900">Local Language Support</h3>
                        <p class="text-gray-600 mb-4 text-sm sm:text-base">Create and deliver quizzes in English, Hausa, Yoruba, and Igbo for inclusive learning.</p>
                        <div class="text-xs sm:text-sm text-emerald-600 font-medium">✓ Multi-language Interface</div>
                    </div>

                    <!-- Feature 5: Collaboration Tools -->
                    <div class="group bg-gray-50 p-6 sm:p-8 rounded-2xl border border-gray-100 hover:border-emerald-200 hover:shadow-sm transition duration-300">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-200 transition">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold mb-3 text-gray-900">Collaboration Tools</h3>
                        <p class="text-gray-600 mb-4 text-sm sm:text-base">Share quizzes, question banks, and results with colleagues. Perfect for departmental teamwork.</p>
                        <div class="text-xs sm:text-sm text-emerald-600 font-medium">✓ Share & Co-Author</div>
                    </div>

                    <!-- Feature 6: Assessment Types -->
                    <div class="group bg-gray-50 p-6 sm:p-8 rounded-2xl border border-gray-100 hover:border-emerald-200 hover:shadow-sm transition duration-300">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-200 transition">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold mb-3 text-gray-900">Diverse Assessment Types</h3>
                        <p class="text-gray-600 mb-4 text-sm sm:text-base">Create multiple choice, true/false, short answer, and essay questions to suit any subject.</p>
                        <div class="text-xs sm:text-sm text-emerald-600 font-medium">✓ Auto & Manual Grading</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Advanced Features Section -->
        <section id="advanced-features" class="py-16 sm:py-20 bg-gray-50">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-4xl mx-auto text-center mb-12 sm:mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Advanced Capabilities</h2>
                    <p class="text-lg sm:text-xl text-gray-600">Powerful features for scaling and deeper insights.</p>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 max-w-6xl mx-auto">
                    <!-- Feature 7: Curriculum Alignment -->
                    <div class="group bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-emerald-200 hover:shadow-md transition duration-300">
                        <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-teal-200 transition">
                            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold mb-3 text-gray-900">Curriculum Alignment</h3>
                        <p class="text-gray-600 mb-4 text-sm sm:text-base">Map quizzes directly to Nigerian curriculum standards (WAEC, NECO, JAMB) for precise targeting.</p>
                        <div class="text-xs sm:text-sm text-teal-600 font-medium">✓ Outcome Tracking</div>
                    </div>

                    <!-- Feature 8: Security & Privacy -->
                    <div class="group bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-emerald-200 hover:shadow-md transition duration-300">
                        <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-teal-200 transition">
                            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold mb-3 text-gray-900">Robust Security</h3>
                        <p class="text-gray-600 mb-4 text-sm sm:text-base">Enterprise-grade security to protect student data and ensure assessment integrity.</p>
                        <div class="text-xs sm:text-sm text-teal-600 font-medium">✓ Secure Exam Mode</div>
                    </div>

                    <!-- Feature 9: Integrations -->
                    <div class="group bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-emerald-200 hover:shadow-md transition duration-300">
                        <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-teal-200 transition">
                            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold mb-3 text-gray-900">Easy Integrations</h3>
                        <p class="text-gray-600 mb-4 text-sm sm:text-base">Connect with popular LMS platforms and educational tools for a seamless workflow.</p>
                        <div class="text-xs sm:text-sm text-teal-600 font-medium">✓ API Access</div>
                    </div>

                     <!-- Feature 10: Custom Branding -->
                    <div class="group bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-emerald-200 hover:shadow-md transition duration-300">
                        <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-teal-200 transition">
                            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold mb-3 text-gray-900">Custom Branding</h3>
                        <p class="text-gray-600 mb-4 text-sm sm:text-base">Add your organization's logo, colors, and custom messages to create a personalized experience.</p>
                        <div class="text-xs sm:text-sm text-teal-600 font-medium">✓ White-label Options</div>
                    </div>

                    <!-- Feature 11: Automated Feedback -->
                    <div class="group bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-emerald-200 hover:shadow-md transition duration-300">
                        <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-teal-200 transition">
                            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold mb-3 text-gray-900">Automated Feedback</h3>
                        <p class="text-gray-600 mb-4 text-sm sm:text-base">Provide instant, personalized feedback to students based on their quiz responses.</p>
                        <div class="text-xs sm:text-sm text-teal-600 font-medium">✓ Adaptive Learning Paths</div>
                    </div>

                    <!-- Feature 12: Bulk Operations -->
                    <div class="group bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-emerald-200 hover:shadow-md transition duration-300">
                        <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-teal-200 transition">
                            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold mb-3 text-gray-900">Bulk Operations</h3>
                        <p class="text-gray-600 mb-4 text-sm sm:text-base">Import students, export results, and manage large classes efficiently with bulk tools.</p>
                        <div class="text-xs sm:text-sm text-teal-600 font-medium">✓ CSV Import/Export</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Feature Comparison (Optional) -->
        <!--
        <section class="py-16 sm:py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-4xl mx-auto text-center mb-12 sm:mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Feature Comparison</h2>
                    <p class="text-lg sm:text-xl text-gray-600">See which features are included in each plan.</p>
                </div>
                <div class="overflow-x-auto">
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">AI Question Generation</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">✓</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">✓</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">✓</td>
                            </tr>
                             Add more rows for other features
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        -->

        <!-- CTA Section -->
      <Cta />
    </AppLayout2>
</template>

<style scoped>
/* Add any specific styles for the features page content if needed */
</style>