<!-- resources/js/Pages/PrivacyPolicy.vue -->
<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout2 from '@/Layouts/AppLayout2.vue';
</script>

<template>
    <Head title="Privacy Policy - QuizPortal NG" />
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Privacy & Security
                    </div>
                    <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold mb-6 text-gray-900 leading-tight">
                        Privacy Policy
                    </h1>
                    <p class="text-lg sm:text-xl text-gray-700 mb-10 max-w-2xl mx-auto">
                        Your privacy is important to us. This policy explains how we collect, use, and protect your information.
                    </p>
                </div>
            </div>
        </section>

        <!-- Privacy Policy Content -->
        <section class="py-16 sm:py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-4xl mx-auto">
                    <div class="prose prose-emerald max-w-none text-gray-700">
                        <p class="lead text-lg font-medium text-gray-800 mb-6">
                            Last Updated: January 1, 2024
                        </p>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">Introduction</h2>
                        <p>
                            QuizPortal Nigeria ("we," "us," or "our") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website <a :href="route('home')" class="text-emerald-600 hover:underline">quizportal.ng</a> (the "Site") and use our services (the "Services"). Please read this Privacy Policy carefully. If you do not agree with the terms of this Privacy Policy, please do not access the Site or use the Services.
                        </p>
                        <p>
                            We reserve the right to change this Privacy Policy at any time and will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date. Your continued use of the Site or Services after we post any such changes constitutes acceptance of those changes.
                        </p>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">Information We Collect</h2>
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">Personal Information You Provide</h3>
                        <p>We collect personal information that you voluntarily provide to us when you:</p>
                        <ul class="list-disc pl-5 space-y-2">
                            <li><strong>Register for an account:</strong> Name, email address, phone number, organization name.</li>
                            <li><strong>Use our Services:</strong> Information related to quizzes, student data (if you are an educator), responses, and performance metrics.</li>
                            <li><strong>Contact us:</strong> Name, email address, phone number, and the contents of your message.</li>
                            <li><strong>Participate in surveys or promotions:</strong> Information you provide in response to surveys or contests.</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">Information Automatically Collected</h3>
                        <p>We automatically collect certain information about your device and your use of the Site and Services, including:</p>
                        <ul class="list-disc pl-5 space-y-2">
                            <li><strong>Device Information:</strong> IP address, browser type, operating system, referring/exit pages, and clickstream data.</li>
                            <li><strong>Usage Information:</strong> Pages visited, time spent on pages, features used, access times, and anonymized statistics.</li>
                            <li><strong>Log Information:</strong> Details of how you used our Services, including search queries and error reports.</li>
                        </ul>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">How We Use Your Information</h2>
                        <p>We use the information we collect for various purposes, including:</p>
                        <ul class="list-disc pl-5 space-y-2">
                            <li>To provide, operate, and maintain our Services.</li>
                            <li>To improve, personalize, and expand our Services.</li>
                            <li>To understand and analyze how you use our Site and Services.</li>
                            <li>To develop new products, services, features, and functionality.</li>
                            <li>To communicate with you, either directly or through one of our partners, for customer service, to provide you with updates and other information relating to the Site and Services, and for marketing and promotional purposes.</li>
                            <li>To process your transactions and manage your account.</li>
                            <li>To find and prevent fraud.</li>
                            <li>To comply with legal obligations.</li>
                        </ul>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">Sharing Your Information</h2>
                        <p>We may share information we collect about you in the following situations:</p>
                        <ul class="list-disc pl-5 space-y-2">
                            <li><strong>With Service Providers:</strong> We may share your information with third-party vendors, service providers, contractors, or agents who perform services for us or on our behalf and require access to such information to do that work.</li>
                            <li><strong>For Business Transfers:</strong> We may share or transfer your information in connection with, or during negotiations of, any merger, sale of company assets, financing, or acquisition of all or a portion of our business to another company.</li>
                            <li><strong>With Affiliates:</strong> We may share your information with our affiliates, in which case we will require those affiliates to honor this Privacy Policy.</li>
                            <li><strong>With Business Partners:</strong> We may share your information with our business partners to offer you certain products, services, or promotions.</li>
                            <li><strong>With Your Consent:</strong> We may disclose your information for any other purpose with your consent.</li>
                            <li><strong>For Legal Reasons:</strong> We may disclose your information if required to do so by law or in response to valid requests by public authorities (e.g., a court or a government agency).</li>
                        </ul>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">Data Security</h2>
                        <p>
                            We use administrative, technical, and physical security measures to help protect your personal information. While we have taken reasonable steps to secure the personal information you provide to us, please be aware that despite our efforts, no security measures are perfect or impenetrable, and no method of data transmission can be guaranteed against any interception or other type of misuse.
                        </p>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">Data Retention</h2>
                        <p>
                            We will retain your information for as long as necessary to fulfill the purposes outlined in this Privacy Policy, unless a longer retention period is required or permitted by law.
                        </p>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">Your Data Protection Rights</h2>
                        <p>
                            Depending on your location, you may have the following rights regarding your personal information:
                        </p>
                        <ul class="list-disc pl-5 space-y-2">
                            <li><strong>The right to access:</strong> You have the right to request copies of your personal data.</li>
                            <li><strong>The right to rectification:</strong> You have the right to request that we correct any information you believe is inaccurate or complete information you believe is incomplete.</li>
                            <li><strong>The right to erasure:</strong> You have the right to request that we erase your personal data, under certain conditions.</li>
                            <li><strong>The right to restrict processing:</strong> You have the right to request that we restrict the processing of your personal data, under certain conditions.</li>
                            <li><strong>The right to object to processing:</strong> You have the right to object to our processing of your personal data, under certain conditions.</li>
                            <li><strong>The right to data portability:</strong> You have the right to request that we transfer the data that we have collected to another organization, or directly to you, under certain conditions.</li>
                        </ul>
                        <p class="mt-4">
                            If you make a request, we have one month to respond to you. If you would like to exercise any of these rights, please contact us using the contact information provided below.
                        </p>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">Children's Privacy</h2>
                        <p>
                            Our Services do not address anyone under the age of 13. We do not knowingly collect personally identifiable information from anyone under the age of 13. If you are a parent or guardian and you are aware that your child has provided us with personal information, please contact us. If we become aware that we have collected personal information from anyone under the age of 13 without verification of parental consent, we take steps to remove that information from our servers.
                        </p>

                         <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">International Data Transfers</h2>
                        <p>
                            Your information, including Personal Data, may be transferred to — and maintained on — computers located outside of your state, province, country, or other governmental jurisdiction where the data protection laws may differ from those of your jurisdiction. If you are located outside Nigeria and choose to provide information to us, please note that we transfer the data, including Personal Data, to Nigeria and process it there. Your consent to this Privacy Policy followed by your submission of such information represents your agreement to that transfer.
                        </p>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">Contact Us</h2>
                        <p>
                            If you have questions or comments about this Privacy Policy, please contact us at:
                        </p>
                        <p class="mt-4">
                            <strong>Email:</strong> <a href="mailto:privacy@quizportal.ng" class="text-emerald-600 hover:underline">privacy@quizportal.ng</a><br>
                            <strong>Address:</strong> Lagos, Nigeria
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section (Optional) -->
        <section class="py-16 sm:py-20 bg-gradient-to-r from-emerald-600 to-teal-600 text-white">
            <div class="container mx-auto px-4 sm:px-6 text-center">
                <h2 class="text-3xl sm:text-4xl font-bold mb-6">Have Questions About Your Privacy?</h2>
                <p class="text-lg sm:text-xl mb-8 max-w-2xl mx-auto text-emerald-100">
                    We're here to help. Contact our support team for any privacy-related inquiries.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="mailto:privacy@quizportal.ng" class="px-6 sm:px-8 py-3.5 bg-white text-emerald-600 rounded-xl hover:bg-gray-100 transition shadow-lg hover:shadow-xl text-base sm:text-lg font-medium">
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