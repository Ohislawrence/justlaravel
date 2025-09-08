<!-- resources/js/Pages/Certifications.vue -->
<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout2 from '@/Layouts/AppLayout2.vue';
import Cta from '@/Components/Cta.vue';

const props = defineProps({
    meta: Object,
});
</script>

<template>
    <Head title="Certifications" />
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
                        🏆 Professional Certifications
                    </div>
                    <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold mb-6 text-gray-900 leading-tight">
                        Streamline Your Certification <br>Programs
                    </h1>
                    <p class="text-lg sm:text-xl text-gray-700 mb-10 max-w-2xl mx-auto">
                        Create, deliver, and manage professional certification exams with our secure, scalable platform.
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

        <!-- Certification Challenges Section -->
        <section class="py-16 sm:py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-4xl mx-auto text-center mb-12 sm:mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Solving Certification Challenges</h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Common certification program pain points we help solve
                    </p>
                </div>
                <div class="grid md:grid-cols-2 gap-8 max-w-6xl mx-auto">
                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Security Concerns</h3>
                        <p class="text-gray-600">Preventing cheating and maintaining exam integrity is challenging with remote certification programs.</p>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Manual Processes</h3>
                        <p class="text-gray-600">Manual grading, certificate issuance, and record-keeping are time-consuming and error-prone.</p>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Scalability Issues</h3>
                        <p class="text-gray-600">Traditional certification methods don't scale well for growing numbers of candidates across different locations.</p>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Limited Analytics</h3>
                        <p class="text-gray-600">Lack of comprehensive data makes it difficult to improve certification programs and demonstrate value.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Certification Features Section -->
        <section id="features" class="py-16 sm:py-20 bg-emerald-50">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-4xl mx-auto text-center mb-12 sm:mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Certification-Focused Features</h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Tools designed specifically for certification programs
                    </p>
                </div>
                <div class="grid md:grid-cols-2 gap-8 max-w-6xl mx-auto">
                    <!-- Feature 1 -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-emerald-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Secure Exam Proctoring</h3>
                        <p class="text-gray-600 mb-4">Maintain exam integrity with advanced proctoring features including browser lockdown, webcam monitoring, and activity tracking.</p>
                        <div class="text-sm text-emerald-600 font-medium">✓ AI-powered cheating detection</div>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-emerald-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Automated Certificate Generation</h3>
                        <p class="text-gray-600 mb-4">Issue professional certificates instantly upon successful completion, with customizable templates and secure verification.</p>
                        <div class="text-sm text-emerald-600 font-medium">✓ Digital credential verification</div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-emerald-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 16a1 1 0 01-1 1h-2a1 1 01-1-1v-6z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Question Pool Management</h3>
                        <p class="text-gray-600 mb-4">Create large question banks and randomize questions for each candidate, ensuring unique exam experiences.</p>
                        <div class="text-sm text-emerald-600 font-medium">✓ Dynamic question selection</div>
                    </div>

                    <!-- Feature 4 -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-emerald-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2 2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Comprehensive Analytics</h3>
                        <p class="text-gray-600 mb-4">Gain insights into exam performance, pass rates, question difficulty, and candidate demographics.</p>
                        <div class="text-sm text-emerald-600 font-medium">✓ Performance tracking</div>
                    </div>

                    <!-- Feature 5 -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-emerald-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Flexible Grading System</h3>
                        <p class="text-gray-600 mb-4">Create custom grading rubrics, set passing scores, and implement weighted scoring for different question types.</p>
                        <div class="text-sm text-emerald-600 font-medium">✓ Custom scoring rules</div>
                    </div>

                    <!-- Feature 6 -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-emerald-100">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="current" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Bulk Candidate Management</h3>
                        <p class="text-gray-600 mb-4">Import multiple candidates, schedule exams, and manage certification renewals efficiently.</p>
                        <div class="text-sm text-emerald-600 font-medium">✓ CSV import/export</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Certification Types Section -->
        <section class="py-16 sm:py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-4xl mx-auto text-center mb-12 sm=mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Types of Certifications</h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Our platform supports various certification programs
                    </p>
                </div>
                <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    <div class="bg-gradient-to-br from-emerald-50 to-green-50 p-6 rounded-2xl border border-emerald-100">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mb-4 shadow-sm">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2 2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Professional Certifications</h3>
                        <p class="text-gray-600">Industry-specific credentials that validate expertise and knowledge in specialized fields.</p>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-50 to-green-50 p-6 rounded-2xl border border-emerald-100">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mb-4 shadow-sm">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semi mb-3 text-gray-900">Compliance Training</h3>
                        <p class="text-gray-600">Mandatory certifications for regulatory requirements, safety protocols, and industry standards.</p>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-50 to-green-50 p-6 rounded-2xl border border-emerald-100">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mb-4 shadow-sm">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Skill Badges</h3>
                        <p class="text-gray-600">Micro-credentials that validate specific skills or competencies, often used in upskilling programs.</p>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-50 to-green-50 p-6 rounded-2xl border border-emerald-100">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mb-4 shadow-sm">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-20-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Corporate Training</h3>
                        <p class="text-gray-600">Internal certification programs for employee development, promotion readiness, and career paths.</p>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-50 to-green-50 p-6 rounded-2xl border border-emerald-100">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mb-4 shadow-sm">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Academic Certifications</h3>
                        <p class="text-gray-600">Formal educational credentials, continuing education units, and specialized academic programs.</p>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-50 to-green-50 p-6 rounded-2xl border border-emerald-100">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mb-4 shadow-sm">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 810M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H52 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-900">Product Certifications</h3>
                        <p class="text-gray-600">Vendor-specific credentials that validate proficiency with particular tools, software, or platforms.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonial Section -->
        <section class="py-16 sm:py-20 bg-emerald-50">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-4xl mx-auto text-center mb-12 sm:mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Trusted by Certification Bodies</h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        See what certification organizations are saying about QuizPortal
                    </p>
                </div>
                <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-emerald-100">
                    <div class="flex items-center mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-green-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                            CK
                        </div>
                        <div class="ml-4">
                            <h4 class="font-semibold text-gray-900">Chinedu Okoro</h4>
                            <p class="text-gray-600">Certification Director, Nigerian IT Professionals Board</p>
                        </div>
                    </div>
                    <p class="text-lg text-gray-700 italic">
                        "QuizPortal has transformed our certification programs. The proctoring features ensure exam integrity, and the automated certificate generation has saved us countless hours. We've been able to scale our programs across West Africa without compromising quality."
                    </p>
                    <div class="mt-6 flex items-center">
                        <div class="flex text-yellow-400">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.1181.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.693.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
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
            title="Launch Your Certification Program Today"
            description="Join leading certification bodies that use QuizPortal for secure, scalable exam delivery."
            :primaryAction="{ label: 'Start Free Trial', url: route('register') }"
            :secondaryAction="{ label: 'Schedule Demo', url: route('contact') }"
        />
    </AppLayout2>
</template>