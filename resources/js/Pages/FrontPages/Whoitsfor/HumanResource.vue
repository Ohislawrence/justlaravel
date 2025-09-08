
<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout2 from '@/Layouts/AppLayout2.vue';
import Cta from '@/Components/Cta.vue';
const props = defineProps({
    meta: Object,
});
</script>

<template>
    <Head title="HR Solutions" />
    <AppLayout2>
        <!-- Hero Section -->
        <section class="relative bg-gradient-to-br from-green-50 via-white to-emerald-50 py-16 sm:py-24 overflow-hidden">
            <!-- Background decorative elements -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-10 left-5 w-64 h-64 bg-green-500 rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 right-5 w-72 h-72 bg-emerald-400 rounded-full blur-3xl"></div>
            </div>
            <div class="container mx-auto px-4 sm:px-6 relative z-10">
                <div class="max-w-4xl mx-auto text-center">
                    <div class="mb-6 inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-medium border border-green-200">
                        👥 For HR Professionals
                    </div>
                    <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold mb-6 text-gray-900 leading-tight">
                        Transform Your HR <br>Assessment Process
                    </h1>
                    <p class="text-lg sm:text-xl text-gray-700 mb-10 max-w-2xl mx-auto">
                        Streamline hiring, training, and employee development with our powerful assessment platform designed specifically for HR teams.
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <a href="#features" class="px-6 sm:px-8 py-3.5 bg-green-600 text-white rounded-xl hover:bg-green-700 transition shadow-lg hover:shadow-xl text-base sm:text-lg font-medium">
                            Explore Features
                        </a>
                        <a :href="route('register')" class="px-6 sm:px-8 py-3.5 border-2 border-green-600 text-green-600 rounded-xl hover:bg-green-50 transition text-base sm:text-lg font-medium">
                            Get Started Free
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- HR Challenges Section -->
        <section class="py-16 sm:py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-4xl mx-auto text-center mb-12 sm:mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Solving HR Challenges</h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Common pain points we help HR professionals overcome
                    </p>
                </div>
                <div class="grid md:grid-cols-2 gap-8 max-w-6xl mx-auto">
                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Time-Consuming Hiring Process</h3>
                        <p class="text-gray-600">Manual screening and assessment creation takes valuable time away from strategic HR initiatives.</p>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Inconsistent Evaluation</h3>
                        <p class="text-gray-600">Lack of standardized assessment methods leads to biased or inconsistent candidate evaluation.</p>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Limited Training Insights</h3>
                        <p class="text-gray-600">Difficulty measuring the effectiveness of employee training and development programs.</p>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Poor Data Analytics</h3>
                        <p class="text-gray-600">Lack of comprehensive analytics to make data-driven decisions about talent acquisition and development.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- HR Features Section -->
        <section id="features" class="py-16 sm:py-20 bg-gray-50">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-4xl mx-auto text-center mb-12 sm:mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">HR-Focused Features</h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Tools designed specifically for human resources professionals
                    </p>
                </div>
                <div class="grid md:grid-cols-2 gap-8 max-w-6xl mx-auto">
                    <!-- Feature 1 -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">AI-Powered Candidate Screening</h3>
                        <p class="text-gray-600 mb-4">Generate role-specific assessments in seconds. Our AI creates relevant questions based on job descriptions and requirements.</p>
                        <div class="text-sm text-green-600 font-medium">✓ Role-specific assessments</div>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Customizable Skill Assessments</h3>
                        <p class="text-gray-600 mb-4">Create tailored tests for specific skills, competencies, and cultural fit. Use our templates or build your own from scratch.</p>
                        <div class="text-sm text-green-600 font-medium">✓ Custom scoring rubrics</div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Bulk Candidate Management</h3>
                        <p class="text-gray-600 mb-4">Import multiple candidates at once, assign assessments to groups, and track progress across your entire recruitment pipeline.</p>
                        <div class="text-sm text-green-600 font-medium">✓ CSV import/export</div>
                    </div>

                    <!-- Feature 4 -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Comprehensive Analytics</h3>
                        <p class="text-gray-600 mb-4">Gain deep insights into candidate performance, identify skill gaps, and make data-driven hiring decisions with detailed reports.</p>
                        <div class="text-sm text-green-600 font-medium">✓ Comparative analysis</div>
                    </div>

                    <!-- Feature 5 -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Secure Online Proctoring</h3>
                        <p class="text-gray-600 mb-4">Ensure assessment integrity with our proctoring features that detect suspicious behavior during remote assessments.</p>
                        <div class="text-sm text-green-600 font-medium">✓ Browser lockdown</div>
                    </div>

                    <!-- Feature 6 -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Automated Certificates</h3>
                        <p class="text-gray-600 mb-4">Issue professional certificates automatically to successful candidates, complete with your company branding and security features.</p>
                        <div class="text-sm text-green-600 font-medium">✓ Branded certificates</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Use Cases Section -->
        <section class="py-16 sm:py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-4xl mx-auto text-center mb-12 sm:mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">HR Use Cases</h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        How HR teams are using QuizPortal to transform their processes
                    </p>
                </div>
                <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-6 rounded-2xl border border-green-100">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mb-4 shadow-sm">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Candidate Screening</h3>
                        <p class="text-gray-600">Filter applicants efficiently with standardized assessments that evaluate both technical skills and cultural fit.</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-6 rounded-2xl border border-green-100">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mb-4 shadow-sm">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Employee Training</h3>
                        <p class="text-gray-600">Measure training effectiveness and knowledge retention with pre- and post-training assessments.</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-6 rounded-2xl border border-green-100">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mb-4 shadow-sm">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Compliance Training</h3>
                        <p class="text-gray-600">Ensure regulatory compliance with mandatory training and certification programs that track completion and comprehension.</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-6 rounded-2xl border border-green-100">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mb-4 shadow-sm">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Performance Reviews</h3>
                        <p class="text-gray-600">Supplement performance evaluations with objective data from skill assessments and competency tests.</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-6 rounded-2xl border border-green-100">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mb-4 shadow-sm">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Promotion Readiness</h3>
                        <p class="text-gray-600">Objectively evaluate employees for promotion opportunities with standardized assessment criteria.</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-6 rounded-2xl border border-green-100">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mb-4 shadow-sm">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Talent Development</h3>
                        <p class="text-gray-600">Identify high-potential employees and create personalized development plans based on assessment results.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonial Section -->
        <section class="py-16 sm:py-20 bg-gray-50">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-4xl mx-auto text-center mb-12 sm:mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Trusted by HR Professionals</h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        See what HR teams are saying about QuizPortal
                    </p>
                </div>
                <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                            AS
                        </div>
                        <div class="ml-4">
                            <h4 class="font-semibold text-gray-900">Amanda Stevens</h4>
                            <p class="text-gray-600">HR Director, TechCorp International</p>
                        </div>
                    </div>
                    <p class="text-lg text-gray-700 italic">
                        "QuizPortal has revolutionized our hiring process. We've reduced time-to-hire by 40% and improved candidate quality significantly. The AI-generated questions are remarkably relevant, and the analytics help us make data-driven decisions."
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
            title="Transform Your HR Processes Today"
            description="Join thousands of HR professionals who trust QuizPortal for their assessment needs."
            :primaryAction="{ label: 'Start Free Trial', url: route('register') }"
            :secondaryAction="{ label: 'Schedule Demo', url: route('contact') }"
        />
    </AppLayout2>
</template>