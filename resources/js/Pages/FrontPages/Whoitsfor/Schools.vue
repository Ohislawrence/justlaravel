<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout2 from '@/Layouts/AppLayout2.vue';
import Cta from '@/Components/Cta.vue';

const props = defineProps({
    meta: Object,
});
</script>


<template>
    <Head title="Schools & Educational Centers" />
    <AppLayout2>
        <!-- Hero Section -->
        <section class="relative bg-gradient-to-br from-emerald-50 via-white to-green-50 py-16 sm:py-24 overflow-hidden">
            <!-- Background decorative elements -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-10 left-5 w-64 h-64 bg-emerald-500 rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 right-5 w-72 h-72 bg-green-400 rounded-full blur-3xl"></div>
            </div>
            <div class="container mx-auto px-4 sm:px-6 relative z-10">
                <div class="max-w-4xl mx-auto text-center">
                    <div class="mb-6 inline-flex items-center px-4 py-2 bg-emerald-100 text-emerald-800 rounded-full text-sm font-medium border border-emerald-200">
                        🎓 For Educational Institutions
                    </div>
                    <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold mb-6 text-gray-900 leading-tight">
                        Transform Teaching & Learning <br>in Your Institution
                    </h1>
                    <p class="text-lg sm:text-xl text-gray-700 mb-10 max-w-2xl mx-auto">
                        Empower educators with smart assessment tools that save time, engage students, and improve learning outcomes.
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <a href="#features" class="px-6 sm:px-8 py-3.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition shadow-lg hover:shadow-xl text-base sm:text-lg font-medium">
                            Explore Features
                        </a>
                        <a :href="route('register')" class="px-6 sm:px-8 py-3.5 border-2 border-emerald-600 text-emerald-600 rounded-xl hover:bg-emerald-50 transition text-base sm:text-lg font-medium">
                            Get Started Free
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Education Challenges Section -->
        <section class="py-16 sm:py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-4xl mx-auto text-center mb-12 sm:mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Solving Educational Challenges</h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Common teaching and assessment pain points we help solve
                    </p>
                </div>
                <div class="grid md:grid-cols-2 gap-8 max-w-6xl mx-auto">
                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Time-Consuming Assessment Creation</h3>
                        <p class="text-gray-600">Teachers spend hours creating tests and quizzes instead of focusing on instruction.</p>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Limited Student Engagement</h3>
                        <p class="text-gray-600">Traditional paper-based assessments fail to engage digital-native students.</p>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Insufficient Data Insights</h3>
                        <p class="text-gray-600">Lack of analytics makes it difficult to identify learning gaps and measure student progress.</p>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Resource Constraints</h3>
                        <p class="text-gray-600">Schools often lack the budget for expensive assessment platforms and IT infrastructure.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Education Features Section -->
        <section id="features" class="py-16 sm:py-20 bg-emerald-50">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-4xl mx-auto text-center mb-12 sm:mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Education-Focused Features</h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Tools designed specifically for schools and educational institutions
                    </p>
                </div>
                <div class="grid md:grid-cols-2 gap-8 max-w-6xl mx-auto">
                    <!-- Feature 1 -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-emerald-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">AI-Powered Question Generation</h3>
                        <p class="text-gray-600 mb-4">Create curriculum-aligned questions in seconds. Our AI generates relevant assessments based on learning objectives.</p>
                        <div class="text-sm text-emerald-600 font-medium">✓ WAEC, JAMB, NECO aligned</div>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-emerald-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Curriculum-Aligned Assessments</h3>
                        <p class="text-gray-600 mb-4">Create tests that match Nigerian educational standards and specific curriculum requirements.</p>
                        <div class="text-sm text-emerald-600 font-medium">✓ Custom grading rubrics</div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-emerald-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Class & Student Management</h3>
                        <p class="text-gray-600 mb-4">Organize students into classes, assign assessments to groups, and track progress across your institution.</p>
                        <div class="text-sm text-emerald-600 font-medium">✓ Bulk student import</div>
                    </div>

                    <!-- Feature 4 -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-emerald-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Comprehensive Learning Analytics</h3>
                        <p class="text-gray-600 mb-4">Gain insights into student performance, identify knowledge gaps, and measure learning outcomes.</p>
                        <div class="text-sm text-emerald-600 font-medium">✓ Individual & class reports</div>
                    </div>

                    <!-- Feature 5 -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-emerald-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Secure Exam Environment</h3>
                        <p class="text-gray-600 mb-4">Maintain assessment integrity with features that prevent cheating during exams and tests.</p>
                        <div class="text-sm text-emerald-600 font-medium">✓ Browser lockdown</div>
                    </div>

                    <!-- Feature 6 -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-emerald-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Automated Grading & Feedback</h3>
                        <p class="text-gray-600 mb-4">Save time with automatic grading for objective questions and provide personalized feedback to students.</p>
                        <div class="text-sm text-emerald-600 font-medium">✓ Instant results</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Education Levels Section -->
        <section class="py-16 sm:py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-4xl mx-auto text-center mb-12 sm:mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">For All Educational Levels</h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Our platform supports institutions across the educational spectrum
                    </p>
                </div>
                <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    <div class="bg-gradient-to-br from-emerald-50 to-green-50 p-6 rounded-2xl border border-emerald-100 text-center">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-4 shadow-sm mx-auto">
                            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v6l9-5m-9 5l-9-5m9 5v-6m0 0l-9-5m9 5l9-5" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Primary Schools</h3>
                        <p class="text-gray-600">Engaging assessments for young learners with age-appropriate interfaces and question types.</p>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-50 to-green-50 p-6 rounded-2xl border border-emerald-100 text-center">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-4 shadow-sm mx-auto">
                            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-4 0H9m4 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v12m4 0h2" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Secondary Schools</h3>
                        <p class="text-gray-600">Exam preparation tools for WAEC, NECO, JAMB and other secondary school assessments.</p>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-50 to-green-50 p-6 rounded-2xl border border-emerald-100 text-center">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-4 shadow-sm mx-auto">
                            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v6l9-5m-9 5l-9-5m9 5v-6m0 0l-9-5m9 5l9-5m-9 5v-6m0 0l-9-5m9 5l9-5" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Tertiary Institutions</h3>
                        <p class="text-gray-600">Advanced assessment tools for universities, polytechnics, and colleges of education.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Use Cases Section -->
        <section class="py-16 sm:py-20 bg-emerald-50">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-4xl mx-auto text-center mb-12 sm:mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Educational Use Cases</h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        How schools are using ExamPortal to enhance teaching and learning
                    </p>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-emerald-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Classroom Assessments</h3>
                        <p class="text-gray-600">Create quizzes, tests, and exams that align with lesson plans and curriculum standards.</p>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-emerald-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Exam Preparation</h3>
                        <p class="text-gray-600">Help students prepare for external exams with practice tests and mock examinations.</p>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-emerald-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Homework Assignments</h3>
                        <p class="text-gray-600">Assign and grade homework digitally, with automatic scoring and feedback.</p>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-emerald-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Progress Tracking</h3>
                        <p class="text-gray-600">Monitor student progress over time and identify areas needing improvement.</p>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-emerald-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Group Projects</h3>
                        <p class="text-gray-600">Facilitate collaborative learning with group assessments and team-based activities.</p>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-emerald-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Parent Reporting</h3>
                        <p class="text-gray-600">Share student progress and assessment results with parents through automated reports.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonial Section -->
        <section class="py-16 sm:py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-4xl mx-auto text-center mb-12 sm:mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Trusted by Nigerian Schools</h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        See what educators are saying about ExamPortal
                    </p>
                </div>
                <div class="max-w-4xl mx-auto bg-emerald-50 p-8 rounded-2xl shadow-sm border border-emerald-100">
                    <div class="flex items-center mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-green-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                            AO
                        </div>
                        <div class="ml-4">
                            <h4 class="font-semibold text-gray-900">Amina Okafor</h4>
                            <p class="text-gray-600">Principal, Excel College Lagos</p>
                        </div>
                    </div>
                    <p class="text-lg text-gray-700 italic">
                        "ExamPortal has revolutionized assessment in our school. Our teachers save 10+ hours weekly on test creation and grading, and our students are more engaged with the interactive format. The WAEC and JAMB alignment features have significantly improved our exam preparation results."
                    </p>
                    <div class="mt-6 flex items-center">
                        <div class="flex text-yellow-400">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-700">5.0</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <Cta 
            title="Transform Your School's Assessment Process Today"
            description="Join thousands of Nigerian educational institutions that trust ExamPortal for their assessment needs."
            :primaryAction="{ label: 'Start Free Trial', url: route('register') }"
            :secondaryAction="{ label: 'Schedule Demo', url: route('contact') }"
        />
    </AppLayout2>
</template>