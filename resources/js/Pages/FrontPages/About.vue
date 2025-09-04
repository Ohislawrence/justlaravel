<!-- resources/js/Pages/About.vue -->
<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout2 from '@/Layouts/AppLayout2.vue';
defineProps({
    meta: Object,
});
</script>

<template>
    <Head title="About Us">
        <meta name="description" :content="meta.description">
        <meta property="og:title" :content="meta.title">
        <meta property="og:description" :content="meta.description">
        <meta property="og:image" :content="meta.image">
        <meta property="og:url" :content="meta.url">
        <meta property="og:type" content="website">

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" :content="meta.title">
        <meta name="twitter:description" :content="meta.description">
        <meta name="twitter:image" :content="meta.image">
    </Head> 
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
                        🇳🇬 Proudly Nigerian
                    </div>
                    <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold mb-6 text-gray-900 leading-tight">
                        Empowering Nigerian Educators <br> with Smart Technology
                    </h1>
                    <p class="text-lg sm:text-xl text-gray-700 mb-10 max-w-2xl mx-auto">
                        We are on a mission to revolutionize education in Nigeria by providing innovative, accessible, and culturally relevant assessment tools.
                    </p>
                </div>
            </div>
        </section>

        <!-- Our Story Section -->
        <section class="py-16 sm:py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-4xl mx-auto">
                    <div class="text-center mb-12 sm:mb-16">
                        <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Our Story</h2>
                        <div class="w-20 h-1 bg-gradient-to-r from-emerald-500 to-teal-500 mx-auto rounded-full"></div>
                    </div>
                    <div class="prose prose-emerald max-w-none text-gray-700 text-lg">
                        <p class="mb-6">
                            ExamPortal Nigeria was born out of a deep understanding of the unique challenges faced by educators across our great nation. Our founders, a team of passionate educators and technologists, saw firsthand how traditional assessment methods were holding back both teachers and students.
                        </p>
                        <p class="mb-6">
                            In 2020, we set out to build a platform that wasn't just another quiz tool, but a comprehensive solution designed specifically for the Nigerian educational context. We focused on understanding local curricula, infrastructure limitations, and pedagogical needs.
                        </p>
                        <p class="mb-6">
                            Today, ExamPortal is trusted by thousands of teachers and hundreds of schools across all 36 states. We are proud to be part of the movement driving educational excellence in Nigeria, one quiz at a time.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mission & Vision Section -->
        <section class="py-16 sm:py-20 bg-gray-50">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-6xl mx-auto">
                    <div class="grid md:grid-cols-2 gap-10 sm:gap-16">
                        <!-- Mission -->
                        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                            <div class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center mb-6">
                                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Our Mission</h3>
                            <p class="text-gray-700 text-lg">
                                To empower Nigerian educators with smart, accessible technology that enhances teaching effectiveness, engages students, and improves learning outcomes across all educational levels.
                            </p>
                        </div>
                        <!-- Vision -->
                        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                            <div class="w-14 h-14 bg-teal-100 rounded-xl flex items-center justify-center mb-6">
                                <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Our Vision</h3>
                            <p class="text-gray-700 text-lg">
                                To be the leading force in transforming Nigerian education through technology, fostering a generation of confident learners and innovative educators equipped for the future.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Core Values Section -->
        <section class="py-16 sm:py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-4xl mx-auto text-center mb-12 sm:mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Our Core Values</h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        These principles guide everything we do at ExamPortal Nigeria.
                    </p>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 max-w-6xl mx-auto">
                    <!-- Value 1 -->
                    <div class="group bg-gray-50 p-6 sm:p-8 rounded-2xl border border-gray-100 hover:border-emerald-200 hover:shadow-sm transition duration-300">
                        <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-100 transition">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold mb-3 text-gray-900">Educator First</h3>
                        <p class="text-gray-600 text-sm sm:text-base">
                            We listen, learn, and build with Nigerian educators at the heart of every decision.
                        </p>
                    </div>
                    <!-- Value 2 -->
                    <div class="group bg-gray-50 p-6 sm:p-8 rounded-2xl border border-gray-100 hover:border-emerald-200 hover:shadow-sm transition duration-300">
                        <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-100 transition">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold mb-3 text-gray-900">Cultural Relevance</h3>
                        <p class="text-gray-600 text-sm sm:text-base">
                            Our solutions are deeply rooted in the Nigerian educational context and cultural landscape.
                        </p>
                    </div>
                    <!-- Value 3 -->
                    <div class="group bg-gray-50 p-6 sm:p-8 rounded-2xl border border-gray-100 hover:border-emerald-200 hover:shadow-sm transition duration-300">
                        <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-100 transition">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold mb-3 text-gray-900">Innovation</h3>
                        <p class="text-gray-600 text-sm sm:text-base">
                            We constantly push boundaries to deliver cutting-edge tools that solve real classroom problems.
                        </p>
                    </div>
                    <!-- Value 4 -->
                    <div class="group bg-gray-50 p-6 sm:p-8 rounded-2xl border border-gray-100 hover:border-emerald-200 hover:shadow-sm transition duration-300">
                        <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-100 transition">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold mb-3 text-gray-900">Accessibility</h3>
                        <p class="text-gray-600 text-sm sm:text-base">
                            We strive to make quality educational technology available and usable for everyone, everywhere.
                        </p>
                    </div>
                    <!-- Value 5 -->
                    <div class="group bg-gray-50 p-6 sm:p-8 rounded-2xl border border-gray-100 hover:border-emerald-200 hover:shadow-sm transition duration-300">
                        <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-100 transition">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold mb-3 text-gray-900">Security & Privacy</h3>
                        <p class="text-gray-600 text-sm sm:text-base">
                            Protecting student and educator data is paramount. We build trust through robust security.
                        </p>
                    </div>
                    <!-- Value 6 -->
                    <div class="group bg-gray-50 p-6 sm:p-8 rounded-2xl border border-gray-100 hover:border-emerald-200 hover:shadow-sm transition duration-300">
                        <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-100 transition">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-semibold mb-3 text-gray-900">Community</h3>
                        <p class="text-gray-600 text-sm sm:text-base">
                            We foster a collaborative environment where educators connect, share, and grow together.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Team Section (Placeholder) -->
        <!--
        <section class="py-16 sm:py-20 bg-gray-50">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-4xl mx-auto text-center mb-12 sm:mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Meet Our Team</h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        The passionate individuals driving ExamPortal Nigeria forward.
                    </p>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden text-center">
                        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 h-48 flex items-center justify-center">
                            <div class="w-24 h-24 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                                JD
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900">John Doe</h3>
                            <p class="text-emerald-600 mb-3">Co-Founder & CEO</p>
                            <p class="text-gray-600 text-sm">
                                Former teacher turned tech entrepreneur, passionate about EdTech in Africa.
                            </p>
                        </div>
                    </div>
                     Add more team members here
                </div>
            </div>
        </section>
        -->

        <!-- CTA Section -->
        <section class="py-16 sm:py-20 bg-gradient-to-r from-emerald-600 to-teal-600 text-white">
            <div class="container mx-auto px-4 sm:px-6 text-center">
                <h2 class="text-3xl sm:text-4xl font-bold mb-6">Join Us in Transforming Education</h2>
                <p class="text-lg sm:text-xl mb-8 max-w-2xl mx-auto text-emerald-100">
                    Whether you're an educator, partner, or investor, we'd love to hear from you.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="/contact" class="px-6 sm:px-8 py-3.5 bg-white text-emerald-600 rounded-xl hover:bg-gray-100 transition shadow-lg hover:shadow-xl text-base sm:text-lg font-medium">
                        Contact Us
                    </a>
                    <a :href="route('register')" class="px-6 sm:px-8 py-3.5 border-2 border-white text-white rounded-xl hover:bg-white/10 transition text-base sm:text-lg font-medium">
                        Get Started
                    </a>
                </div>
            </div>
        </section>
    </AppLayout2>
</template>

<style scoped>
/* If you are not using @tailwindcss/typography plugin, you might need to style the prose content manually */
</style>