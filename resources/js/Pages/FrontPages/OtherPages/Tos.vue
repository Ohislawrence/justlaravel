<!-- resources/js/Pages/TermsOfService.vue -->
<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout2 from '@/Layouts/AppLayout2.vue';
</script>

<template>
    <Head title="Terms of Service - QuizPortal NG" />
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Legal Agreement
                    </div>
                    <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold mb-6 text-gray-900 leading-tight">
                        Terms of Service
                    </h1>
                    <p class="text-lg sm:text-xl text-gray-700 mb-10 max-w-2xl mx-auto">
                        Please read these Terms of Service carefully before using QuizPortal Nigeria.
                    </p>
                </div>
            </div>
        </section>

        <!-- Terms of Service Content -->
        <section class="py-16 sm:py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-4xl mx-auto">
                    <div class="prose prose-emerald max-w-none text-gray-700">
                        <p class="lead text-lg font-medium text-gray-800 mb-6">
                            Last Updated: January 1, 2024
                        </p>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">1. Introduction</h2>
                        <p>
                            Welcome to QuizPortal Nigeria ("we," "us," or "our"). These Terms of Service ("Terms") govern your access to and use of our website <a :href="route('home')" class="text-emerald-600 hover:underline">quizportal.ng</a> (the "Site") and our services (the "Services").
                        </p>
                        <p>
                            By accessing or using the Site or Services, you agree to be bound by these Terms. If you disagree with any part of the terms, then you may not access the Site or use the Services.
                        </p>
                        <p>
                            We reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision is material, we will try to provide at least 30 days' notice prior to any new terms taking effect. What constitutes a material change will be determined at our sole discretion.
                        </p>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">2. Accounts</h2>
                        <p>
                            When you create an account with us, you must provide information that is accurate, complete, and current at all times. Failure to do so constitutes a breach of the Terms, which may result in immediate termination of your account on our Service.
                        </p>
                        <p>
                            You are responsible for safeguarding the password and for all activities that occur under your account. You agree not to disclose your password to any third party. You must notify us immediately upon becoming aware of any breach of security or unauthorized use of your account.
                        </p>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">3. Use of Services</h2>
                        <p>
                            By using the Services, you agree not to:
                        </p>
                        <ul class="list-disc pl-5 space-y-2">
                            <li>Use the Services for any illegal or unauthorized purpose.</li>
                            <li>Interfere with or disrupt the Services or servers or networks connected to the Services.</li>
                            <li>Transmit any worms or viruses or any code of a destructive nature.</li>
                            <li>Use the Services in any manner that could disable, overburden, damage, or impair the site or interfere with any other party's use of the Service.</li>
                            <li>Attempt to gain unauthorized access to, interfere with, damage, or disrupt any parts of the Service, the server on which the Service is stored, or any server, computer, or database connected to the Service.</li>
                        </ul>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">4. Intellectual Property</h2>
                        <p>
                            The Site and its original content, features, and functionality are and will remain the exclusive property of QuizPortal Nigeria and its licensors. The Service is protected by copyright, trademark, and other laws of both Nigeria and foreign countries.
                        </p>
                        <p>
                            Our trademarks and trade dress may not be used in connection with any product or service without the prior written consent of QuizPortal Nigeria.
                        </p>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">5. Links To Other Web Sites</h2>
                        <p>
                            Our Service may contain links to third-party web sites or services that are not owned or controlled by QuizPortal Nigeria.
                        </p>
                        <p>
                            QuizPortal Nigeria has no control over, and assumes no responsibility for, the content, privacy policies, or practices of any third-party web sites or services. You further acknowledge and agree that QuizPortal Nigeria shall not be responsible or liable, directly or indirectly, for any damage or loss caused or alleged to be caused by or in connection with the use of or reliance on any such content, goods, or services available on or through any such web sites or services.
                        </p>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">6. Termination</h2>
                        <p>
                            We may terminate or suspend access to our Service immediately, without prior notice or liability, for any reason whatsoever, including without limitation if you breach the Terms.
                        </p>
                        <p>
                            All provisions of the Terms which by their nature should survive termination shall survive termination, including, without limitation, ownership provisions, warranty disclaimers, indemnity, and limitations of liability.
                        </p>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">7. Disclaimer</h2>
                        <p>
                            Your use of the Service is at your sole risk. The Service is provided on an "AS IS" and "AS AVAILABLE" basis. The Service is provided without warranties of any kind, whether express or implied, including, but not limited to, implied warranties of merchantability, fitness for a particular purpose, non-infringement or course of performance.
                        </p>
                        <p>
                            QuizPortal Nigeria, its subsidiaries, affiliates, and its licensors do not warrant that a) the Service will function uninterrupted, secure, or available at any particular time or location; b) any errors or defects will be corrected; c) the Service is free of viruses or other harmful components; or d) the results of using the Service will meet your requirements.
                        </p>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">8. Limitation of Liability</h2>
                        <p>
                            In no event shall QuizPortal Nigeria, nor its directors, employees, partners, agents, suppliers, or affiliates, be liable for any indirect, incidental, special, consequential, or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from (i) your access to or use of or inability to access or use the Service; (ii) any conduct or content of any third party on the Service; (iii) any content obtained from the Service; and (iv) unauthorized access, use, or alteration of your transmissions or content, whether based on warranty, contract, tort (including negligence), or any other legal theory, whether or not we have been informed of the possibility of such damage.
                        </p>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">9. Governing Law</h2>
                        <p>
                            These Terms shall be governed and construed in accordance with the laws of Nigeria, without regard to its conflict of law provisions.
                        </p>
                        <p>
                            Our failure to enforce any right or provision of these Terms will not be considered a waiver of those rights. If any provision of these Terms is held to be invalid or unenforceable by a court, the remaining provisions of these Terms will remain in effect. These Terms constitute the entire agreement between us regarding our Service, and supersede and replace any prior agreements we might have between us regarding the Service.
                        </p>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">10. Contact Us</h2>
                        <p>
                            If you have any questions about these Terms, please contact us at:
                        </p>
                        <p class="mt-4">
                            <strong>Email:</strong> <a href="mailto:legal@quizportal.ng" class="text-emerald-600 hover:underline">legal@quizportal.ng</a><br>
                            <strong>Address:</strong> Lagos, Nigeria
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section (Optional) -->
        <section class="py-16 sm:py-20 bg-gradient-to-r from-emerald-600 to-teal-600 text-white">
            <div class="container mx-auto px-4 sm:px-6 text-center">
                <h2 class="text-3xl sm:text-4xl font-bold mb-6">Questions About Our Terms?</h2>
                <p class="text-lg sm:text-xl mb-8 max-w-2xl mx-auto text-emerald-100">
                    We're here to help. Contact our legal team for any inquiries.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="mailto:legal@quizportal.ng" class="px-6 sm:px-8 py-3.5 bg-white text-emerald-600 rounded-xl hover:bg-gray-100 transition shadow-lg hover:shadow-xl text-base sm:text-lg font-medium">
                        Email Us
                    </a>
                    <a :href="route('contact')" class="px-6 sm:px-8 py-3.5 border-2 border-white text-white rounded-xl hover:bg-white/10 transition text-base sm:text-lg font-medium">
                        Visit Contact Page
                    </a>
                </div>
            </div>
        </section>
    </AppLayout2>
</template>

<style scoped>
/* If you are not using @tailwindcss/typography plugin, you might need to style the prose content manually */
/* Ensure Tailwind's prose styles are included or add custom styles for headings, lists, links, etc. */
</style>