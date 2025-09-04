<!-- resources/js/Pages/CookiePolicy.vue -->
<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout2 from '@/Layouts/AppLayout2.vue';
</script>

<template>
    <Head title="Cookie Policy" />
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Cookies & Tracking
                    </div>
                    <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold mb-6 text-gray-900 leading-tight">
                        Cookie Policy
                    </h1>
                    <p class="text-lg sm:text-xl text-gray-700 mb-10 max-w-2xl mx-auto">
                        Learn how we use cookies and similar technologies to enhance your experience on ExamPortal.
                    </p>
                </div>
            </div>
        </section>

        <!-- Cookie Policy Content -->
        <section class="py-16 sm:py-20 bg-white">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="max-w-4xl mx-auto">
                    <div class="prose prose-emerald max-w-none text-gray-700">
                        <p class="lead text-lg font-medium text-gray-800 mb-6">
                            Last Updated: January 1, 2024
                        </p>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">1. Introduction</h2>
                        <p>
                            ExamPortal Nigeria ("we," "us," or "our") uses cookies and similar tracking technologies on our website <a :href="route('home')" class="text-emerald-600 hover:underline">examportal.online</a> (the "Site") and in our services (the "Services"). This Cookie Policy explains what cookies are, how we use them, the types of cookies we use, your choices regarding cookies, and how you can manage your cookie preferences.
                        </p>
                        <p>
                            By using our Site or Services, you consent to the use of cookies in accordance with this Cookie Policy. If you do not agree to the use of cookies, you should adjust your browser settings accordingly or refrain from using our Site and Services.
                        </p>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">2. What Are Cookies?</h2>
                        <p>
                            Cookies are small text files that are stored on your device (computer, tablet, smartphone, etc.) when you visit a website. They are widely used to make websites work more efficiently, as well as to provide information to the owners of the site.
                        </p>
                        <p>
                            Cookies can be "persistent" or "session" cookies. Persistent cookies remain on your device for a specified period of time or until you delete them manually. Session cookies are temporary and are deleted when you close your web browser.
                        </p>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">3. How We Use Cookies</h2>
                        <p>We use cookies for various purposes, including:</p>
                        <ul class="list-disc pl-5 space-y-2">
                            <li><strong>Essential Cookies:</strong> These cookies are necessary for the website to function and cannot be switched off in our systems. They are usually only set in response to actions made by you which amount to a request for services, such as setting your privacy preferences, logging in, or filling in forms. You can set your browser to block or alert you about these cookies, but some parts of the site will not then work.</li>
                            <li><strong>Performance Cookies:</strong> These cookies allow us to count visits and traffic sources so we can measure and improve the performance of our site. They help us to know which pages are the most and least popular and see how visitors move around the site. All information these cookies collect is aggregated and therefore anonymous. If you do not allow these cookies, we will not know when you have visited our site.</li>
                            <li><strong>Functionality Cookies:</strong> These cookies enable the website to provide enhanced functionality and personalization. They may be set by us or by third-party providers whose services we have added to our pages. If you do not allow these cookies, then some or all of these services may not function properly.</li>
                            <li><strong>Targeting Cookies:</strong> These cookies may be set through our site by our advertising partners. They may be used by those companies to build a profile of your interests and show you relevant adverts on other sites. They do not store personal information directly, but are based on uniquely identifying your browser and internet device. If you do not allow these cookies, you will experience less targeted advertising.</li>
                            <li><strong>Analytics Cookies:</strong> These cookies help us understand how visitors interact with our website and services. We use this information to compile reports and help us improve the site. The cookies collect information in a way that does not directly identify anyone, including the number of visitors to the website and blog, where visitors have come to the website from, and the pages they visited.</li>
                        </ul>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">4. Types of Cookies We Use</h2>
                        <div class="overflow-x-auto mt-6">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cookie Type</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Examples</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Essential</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">Enable core website functionality</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">Login session, CSRF protection</td>
                                    </tr>
                                    <tr class="bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Performance</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">Analyze website performance</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">Page load times, error rates</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Functionality</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">Remember choices and preferences</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">Language preference, region</td>
                                    </tr>
                                    <tr class="bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Targeting</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">Deliver relevant advertising</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">Ad personalization, retargeting</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Analytics</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">Understand user behavior</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">Google Analytics, Matomo</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">5. Third-Party Cookies</h2>
                        <p>
                            In some special cases, we also use cookies provided by trusted third parties. The following section details which third-party cookies you might encounter through our Site:
                        </p>
                        <ul class="list-disc pl-5 space-y-2 mt-4">
                            <li><strong>Google Analytics:</strong> This site uses Google Analytics which is one of the most widespread and trusted analytics solutions on the web for helping us to understand how you use the site and ways that we can improve your experience. These cookies may track things such as how long you spend on the site and the pages that you visit so we can continue to produce engaging content.</li>
                            <li><strong>Social Media Platforms:</strong> Our site may feature social media sharing buttons and/or links. These sites may set cookies through their own scripts. We have no control over the cookies used by these platforms. You should check their individual privacy policies to understand how they use cookies.</li>
                        </ul>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">6. Managing Cookies</h2>
                        <p>
                            You can control and/or delete cookies as you wish. You can delete all cookies that are already on your device and you can set most browsers to prevent them from being placed. However, if you do this, you may have to manually adjust some preferences every time you visit a site and some services and functionalities may not work.
                        </p>
                        <p class="mt-4">
                            Most web browsers automatically accept cookies, but you can modify your browser setting to decline cookies if you prefer. If you choose to decline cookies, you may not be able to fully experience the interactive features of the ExamPortal Services or websites you visit.
                        </p>
                        <p class="mt-4">
                            To find information relating to other browsers, visit the browser developer's website.
                        </p>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">7. Changes to This Cookie Policy</h2>
                        <p>
                            We may update our Cookie Policy from time to time. We will notify you of any changes by posting the new Cookie Policy on this page and updating the "Last Updated" date.
                        </p>
                        <p class="mt-4">
                            You are advised to review this Cookie Policy periodically for any changes. Changes to this Cookie Policy are effective when they are posted on this page.
                        </p>

                        <h2 class="text-2xl font-bold text-gray-900 mt-10 mb-4">8. Contact Us</h2>
                        <p>
                            If you have any questions about this Cookie Policy, please contact us at:
                        </p>
                        <p class="mt-4">
                            <strong>Email:</strong> <a href="mailto:privacy@examportal.online" class="text-emerald-600 hover:underline">privacy@examportal.online</a><br>
                            <strong>Address:</strong> Lagos, Nigeria
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section (Optional) -->
        <section class="py-16 sm:py-20 bg-gradient-to-r from-emerald-600 to-teal-600 text-white">
            <div class="container mx-auto px-4 sm:px-6 text-center">
                <h2 class="text-3xl sm:text-4xl font-bold mb-6">Have Questions About Our Cookies?</h2>
                <p class="text-lg sm:text-xl mb-8 max-w-2xl mx-auto text-emerald-100">
                    We're here to help. Contact our support team for any cookie-related inquiries.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="mailto:privacy@examportal.online" class="px-6 sm:px-8 py-3.5 bg-white text-emerald-600 rounded-xl hover:bg-gray-100 transition shadow-lg hover:shadow-xl text-base sm:text-lg font-medium">
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
/* Ensure Tailwind's prose styles are included or add custom styles for headings, lists, links, tables, etc. */
</style>