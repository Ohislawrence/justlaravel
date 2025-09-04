<!-- resources/js/Pages/Home.vue (or similar) -->
<template>
    <!-- Use the new layout component -->
     
    <AppLayout2>
        <Head title="Home" />
      <!-- All your existing homepage-specific content goes here -->
      <!-- Hero Section -->
      <section class="container mx-auto px-4 sm:px-6 py-16 md:py-28 relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0 opacity-5">
          <div class="absolute top-10 left-5 w-40 h-40 bg-emerald-500 rounded-full blur-3xl"></div>
          <div class="absolute bottom-10 right-5 w-52 h-52 bg-teal-400 rounded-full blur-3xl"></div>
        </div>
        <div class="max-w-4xl mx-auto text-center relative z-10">
          <div class="mb-6 inline-flex items-center px-4 py-2 bg-emerald-50 text-emerald-700 rounded-full text-sm font-medium border border-emerald-100">
            🇳🇬 Made for Nigerian
          </div>
          <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold mb-6 bg-gradient-to-r from-gray-900 via-emerald-700 to-teal-500 bg-clip-text text-transparent leading-tight">
            Revolutionize Assessment <br> with AI-Powered Quiz/Test Creation
          </h1>
          <p class="text-lg sm:text-xl text-gray-600 mb-10 max-w-3xl mx-auto">
            The complete quiz platform designed for Nigerian educational institutions, corporations, and training organizations. Create, customize, and deploy intelligent assessments that drive real learning outcomes.
          </p>
          <div class="flex flex-col sm:flex-row justify-center gap-4 mb-8">
            <Link :href="route('register')" class="px-6 sm:px-8 py-3.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition shadow-lg hover:shadow-xl text-base sm:text-lg font-medium transform hover:scale-[1.02] duration-300">
              Get stated for FREE
            </Link>
            <Link :href="route('login')" class="px-6 sm:px-8 py-3.5 border-2 border-gray-200 rounded-xl hover:border-emerald-300 transition text-base sm:text-lg font-medium hover:bg-emerald-50">
              Sign in
            </Link>
          </div>
          <div class="text-sm text-gray-500">
            No payment required to start
          </div>
        </div>
      </section>
  
      <!-- Stats Section -->
      <section class="bg-emerald-50 py-12 sm:py-16">
        <div class="container mx-auto px-4 sm:px-6">
          <div class="grid grid-cols-2 md:grid-cols-4 gap-6 sm:gap-8 text-center">
            <div class="p-4">
              <div class="text-2xl sm:text-3xl font-bold text-emerald-600 mb-1">5,000+</div>
              <div class="text-sm sm:text-base text-gray-600">Active organizations</div>
            </div>
            <div class="p-4">
              <div class="text-2xl sm:text-3xl font-bold text-emerald-600 mb-1">25,200+</div>
              <div class="text-sm sm:text-base text-gray-600">Students</div>
            </div>
            <div class="p-4">
              <div class="text-2xl sm:text-3xl font-bold text-emerald-600 mb-1">50K+</div>
              <div class="text-sm sm:text-base text-gray-600">Quizzes Created</div>
            </div>
            <div class="p-4">
              <div class="text-2xl sm:text-3xl font-bold text-emerald-600 mb-1">36</div>
              <div class="text-sm sm:text-base text-gray-600">States Covered</div>
            </div>
          </div>
        </div>
      </section>
  
      <!-- Key Features -->
      <section id="features" class="container mx-auto px-4 sm:px-6 py-16 sm:py-20">
        <div class="max-w-4xl mx-auto text-center mb-12 sm:mb-16">
          <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">The Smart Assessment Platform Built for Nigeria</h2>
          <p class="text-lg sm:text-xl text-gray-600">Everything you need to create, manage, and analyze assessments that work in the educational context.</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 max-w-6xl mx-auto">
          <!-- Feature Cards - Copied directly -->
          <div class="group bg-white p-6 sm:p-8 rounded-2xl shadow-sm hover:shadow-lg transition border border-gray-100 hover:border-emerald-200 duration-300">
            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-100 transition">
              <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
            </div>
            <h3 class="text-lg sm:text-xl font-semibold mb-3">AI Question Generation</h3>
            <p class="text-gray-600 mb-4 text-sm sm:text-base">Automatically create quiz questions from any topic or article using advanced AI technology across all academic fields and subjects.</p>
            <div class="text-xs sm:text-sm text-emerald-600 font-medium">✓ AI-powered question creation</div>
          </div>
          <div class="group bg-white p-6 sm:p-8 rounded-2xl shadow-sm hover:shadow-lg transition border border-gray-100 hover:border-emerald-200 duration-300">
            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-100 transition">
              <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
            <h3 class="text-lg sm:text-xl font-semibold mb-3">Real-Time Analytics</h3>
            <p class="text-gray-600 mb-4 text-sm sm:text-base">Track student performance, identify learning gaps, and generate detailed reports for parents and administrators.</p>
            <div class="text-xs sm:text-sm text-emerald-600 font-medium">✓ Performance insights</div>
          </div>
          <div class="group bg-white p-6 sm:p-8 rounded-2xl shadow-sm hover:shadow-lg transition border border-gray-100 hover:border-emerald-200 duration-300">
            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-100 transition">
              <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path> </svg>
            </div>
            <h3 class="text-lg sm:text-xl font-semibold mb-3">Nigerian Education Support</h3> 
            <p class="text-gray-600 mb-4 text-sm sm:text-base">Designed specifically for Nigeria's educational standards, supporting WAEC, etc and custom grading systems alongside modern assessment methods.</p> 
            <div class="text-xs sm:text-sm text-emerald-600 font-medium">✓ WAEC grading compliance</div>
          </div>
          <div class="group bg-white p-6 sm:p-8 rounded-2xl shadow-sm hover:shadow-lg transition border border-gray-100 hover:border-emerald-200 duration-300">
            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-100 transition">
              <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
            </div>
            <h3 class="text-lg sm:text-xl font-semibold mb-3">Advanced Exam Proctoring</h3>
            <p class="text-gray-600 mb-4 text-sm sm:text-base">Our proprietary monitoring system ensures exam integrity with real-time student behavior tracking and AI-powered anomaly detection.</p>
            <div class="text-xs sm:text-sm text-emerald-600 font-medium">✓ AI integrity monitoring</div>
          </div>
          <div class="group bg-white p-6 sm:p-8 rounded-2xl shadow-sm hover:shadow-lg transition border border-gray-100 hover:border-emerald-200 duration-300">
            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-100 transition">
              <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path></svg>
            </div>
            <h3 class="text-lg sm:text-xl font-semibold mb-3">Affordable Pricing</h3>
            <p class="text-gray-600 mb-4 text-sm sm:text-base">Flexible payment options including bank transfer, mobile money, and installment plans for schools.</p>
            <div class="text-xs sm:text-sm text-emerald-600 font-medium">✓ Pay in Naira</div>
          </div>
          <div class="group bg-white p-6 sm:p-8 rounded-2xl shadow-sm hover:shadow-lg transition border border-gray-100 hover:border-emerald-200 duration-300">
            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-100 transition">
              <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-lg sm:text-xl font-semibold mb-3">24/7 Local Support</h3>
            <p class="text-gray-600 mb-4 text-sm sm:text-base">Get help in English or local languages via WhatsApp, phone, or email from our Lagos-based support team.</p>
            <div class="text-xs sm:text-sm text-emerald-600 font-medium">✓ Nigerian support team</div>
          </div>
        </div>
      </section>
  
      <!-- Testimonials -->
      <section class="bg-gray-50 py-16 sm:py-20">
        <div class="container mx-auto px-4 sm:px-6">
          <div class="max-w-4xl mx-auto text-center mb-12 sm:mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Trusted by Nigerian organizations</h2>
            <p class="text-lg sm:text-xl text-gray-600">See what businesses and institutions are saying about QuizPortal Nigeria.</p>
          </div>
          <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 max-w-6xl mx-auto">
            <!-- Testimonial Cards - Copied directly -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
              <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mr-4">
                  <span class="text-emerald-600 font-semibold">AD</span>
                </div>
                <div>
                  <div class="font-semibold text-gray-900">Mr. Adunni Olatunji</div>
                  <div class="text-xs sm:text-sm text-gray-500">Teacher</div>
                </div>
              </div>
              <p class="text-gray-600 mb-4 text-sm sm:text-base">"QuizPortal has revolutionized how we conduct assessments. The AI question generation saves me hours of work every week."</p>
              <div class="flex text-amber-400">⭐⭐⭐⭐⭐</div>
            </div>
            <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
              <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mr-4">
                  <span class="text-emerald-600 font-semibold">IO</span>
                </div>
                <div>
                  <div class="font-semibold text-gray-900">Ibrahim Okafor</div>
                  <div class="text-xs sm:text-sm text-gray-500">HR</div>
                </div>
              </div>
              <p class="text-gray-600 mb-4 text-sm sm:text-base">"The features in QuizPortal is a game-changer. Our staffs can now take text even at home even on weekend."</p>
              <div class="flex text-amber-400">⭐⭐⭐⭐⭐</div>
            </div>
            <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
              <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mr-4">
                  <span class="text-emerald-600 font-semibold">EN</span>
                </div>
                <div>
                  <div class="font-semibold text-gray-900">Mrs. Emeka Nwosu</div>
                  <div class="text-xs sm:text-sm text-gray-500">Head of School</div>
                </div>
              </div>
              <p class="text-gray-600 mb-4 text-sm sm:text-base">"The analytics help me identify struggling students early. Reports are detailed and professional."</p>
              <div class="flex text-amber-400">⭐⭐⭐⭐⭐</div>
            </div>
          </div>
        </div>
      </section>
  
      <!-- Pricing Section -->
      <section id="pricing" class="container mx-auto px-4 sm:px-6 py-16 sm:py-20">
        <div class="max-w-4xl mx-auto text-center mb-12 sm:mb-16">
          <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Simple, Transparent Pricing</h2>
          <p class="text-lg sm:text-xl text-gray-600">Choose the plan that fits your needs. All prices in Nigerian Naira.</p>
        </div>
        <!-- Pricing Toggle -->
        <div class="max-w-2xl mx-auto text-center mb-12 sm:mb-16">
                    <div class="inline-flex bg-gray-100 rounded-full p-1 mb-6">
                        <button
                            v-for="cycle in cycles"
                            :key="cycle.value"
                            @click="activeCycle = cycle.value"
                            class="px-4 py-2 text-sm font-medium rounded-full transition-colors"
                            :class="{
                                'bg-white text-emerald-600 shadow-sm': activeCycle === cycle.value,
                                'text-gray-600 hover:text-gray-800': activeCycle !== cycle.value
                            }"
                        >
                            {{ cycle.label }}
                        </button>
                    </div>
                    <p class="text-gray-600">
                        Have questions before choosing? <a :href="route('contact')" class="text-emerald-600 font-medium hover:underline">Contact our sales team</a> or check out our <a href="#" class="text-emerald-600 font-medium hover:underline">FAQs</a>.
                    </p>
                </div>

                <!-- Pricing Cards -->
                <div class="grid md:grid-cols-3 gap-6 sm:gap-8 max-w-6xl mx-auto">
                    <!-- Loop through plans -->
                    <div v-for="(plan, index) in plans" :key="plan.id" 
                         class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-200 hover:shadow-md transition"
                         :class="{
                            'shadow-lg border-2 border-emerald-600 transform hover:scale-[1.02] transition duration-300': plan.is_default,
                            'hover:shadow-md': !plan.is_default
                         }">
                        
                        <!-- Popular badge for default plan -->
                        <div v-if="plan.is_default" class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-emerald-600 text-white px-4 py-1 rounded-full text-xs font-medium">
                            Most Popular
                        </div>

                        <div class="text-center mb-8" :class="{ 'mt-4': plan.is_default }">
                            <h3 class="text-lg sm:text-xl font-semibold mb-2">{{ plan.name }}</h3>
                            <div v-if="plan.slug !== 'enterprise'" class="text-2xl sm:text-3xl font-bold mb-2">
                                {{ getCyclePrice(plan) }}<span class="text-sm font-normal text-gray-500">{{ getPriceSuffix() }}</span>
                            </div>
                            <div v-else class="text-2xl sm:text-3xl font-bold mb-2">
                                Custom
                            </div>
                            <p class="text-gray-600 text-sm sm:text-base">{{ plan.description }}</p>
                        </div>
                        <ul class="space-y-3 mb-8">
                            <li v-if="getFeatureValue(plan, 'users_limit') !== 'Not included'" class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-emerald-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                {{ getFeatureValue(plan, 'users_limit') === 'unlimited' ? 'Unlimited' : 'Up to ' + getFeatureValue(plan, 'users_limit') }} students
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-emerald-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                {{ getFeatureValue(plan, 'ai_question_generation') ? 'AI Question Generation' : 'Basic Question Creation' }}
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-emerald-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                {{ getFeatureValue(plan, 'quiz_attempts_limit') === 'unlimited' ? 'Unlimited quizzes' : (getFeatureValue(plan, 'quiz_attempts_limit') + ' quizzes') }}
                            </li>
                            <li v-if="getFeatureValue(plan, 'question_pool_limit') !== 'Not included'" class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-emerald-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                {{ getFeatureValue(plan, 'question_pool_limit') === 'unlimited' ? 'Unlimited question pool' : (getFeatureValue(plan, 'question_pool_limit') + ' question pool') }}
                            </li>
                            <li v-if="getFeatureValue(plan, 'storage_space') !== 'Not included'" class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-emerald-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                {{ getFeatureValue(plan, 'storage_space') === 'unlimited' ? 'Unlimited storage' : (getFeatureValue(plan, 'storage_space') + ' storage') }}
                            </li>
                            <li class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-emerald-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                {{ plan.slug === 'enterprise' ? 'Dedicated support' : (plan.slug === 'school' ? 'Priority support' : 'WhatsApp support') }}
                            </li>
                        </ul>
                        <Link v-if="plan.slug !== 'enterprise'" :href="route('register')" 
                              class="w-full py-3 text-center rounded-lg transition font-medium text-sm block"
                              :class="{
                                'bg-emerald-600 text-white hover:bg-emerald-700': plan.is_default,
                                'border-2 border-emerald-600 text-emerald-600 hover:bg-emerald-50': !plan.is_default
                              }">
                            Start Free Trial
                        </Link>
                        <a v-else href="mailto:sales@quizportal.online" class="w-full py-3 text-center border-2 border-emerald-600 text-emerald-600 rounded-lg hover:bg-emerald-50 transition font-medium text-sm block">
                            Contact Sales
                        </a>
                    </div>
                </div>
      </section>
  
      <!-- Blog Section -->
      <section id="blog" class="bg-gradient-to-b from-white to-gray-50 py-16 sm:py-20">
            <div class="container mx-auto px-4 sm:px-6">
                <!-- Section Header -->
                <div class="max-w-4xl mx-auto text-center mb-12 sm:mb-16">
                    <div class="mb-4 inline-flex items-center px-4 py-2 bg-emerald-50 text-emerald-700 rounded-full text-sm font-medium border border-emerald-100">
                        📚 Educational Insights
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                        Latest from Our <span class="bg-gradient-to-r from-emerald-600 to-teal-500 bg-clip-text text-transparent">Education Blog</span>
                    </h2>
                    <p class="text-lg sm:text-xl text-gray-600 max-w-3xl mx-auto">
                        Tips, insights, and best practices to enhance learning outcomes and student/staff engagement.
                    </p>
                </div>

                

                <!-- Blog Grid -->
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 max-w-6xl mx-auto">
                    <!-- Blog Post -->
                    <article v-for="blog in recentBlogs" :key="blog.id" class="group bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 hover:border-emerald-200 overflow-hidden">
                        <Link :href="route('blogs.show', blog.slug)" class="block">
                          <div class="aspect-video relative overflow-hidden">
                            <!-- Featured Image -->
                            <img 
                                v-if="blog.featured_image" 
                                :src="'/storage/' + blog.featured_image" 
                                :alt="blog.title"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            >
                            
                            <!-- Fallback Gradient Background -->
                            <div 
                                v-else
                                class="w-full h-full bg-gradient-to-br from-emerald-50 to-teal-50 flex items-center justify-center"
                            >
                                <div class="absolute inset-0 opacity-10">
                                    <div class="absolute top-2 left-2 w-16 h-16 bg-emerald-500 rounded-full blur-xl"></div>
                                    <div class="absolute bottom-2 right-2 w-20 h-20 bg-teal-400 rounded-full blur-xl"></div>
                                </div>
                                <div class="relative z-10">
                                    <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center shadow-md">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <div class="p-6">
                                <div class="flex items-center gap-2 mb-3">
                                    <span v-for="category in blog.categories.slice(0, 1)" :key="category.id" class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                        {{ category.name }}
                                    </span>
                                    <span class="text-xs text-gray-500">{{ formatDate(blog.created_at) }}</span>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-3 group-hover:text-emerald-600 transition-colors">
                                    {{ blog.title }}
                                </h3>
                                <p class="text-sm text-gray-600 mb-4 line-clamp-3">
                                  {{ blog.excerpt || stripHtml(blog.content).substring(0, 150) + '...' }}
                                </p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                                            <span class="text-white text-xs font-semibold">
                                                QP
                                            </span>
                                        </div>
                                        <span class="text-xs text-gray-600">Quiz Portal</span>
                                    </div>
                                    <span class="text-xs text-gray-500">3 min read</span>
                                </div>
                            </div>
                        </Link>
                    </article>
                </div>

                <!-- View All Blog Posts -->
                <div class="text-center mt-12">
                    <Link :href="route('blogs.index')" class="inline-flex items-center px-8 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-all duration-200 hover:scale-[1.02] font-medium shadow-lg hover:shadow-xl">
                        View All Articles
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </Link>
                </div>
            </div>
        </section>
  
       <!-- CTA Section -->
      <Cta />
      <!-- End of Homepage Specific Content -->
    </AppLayout2>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { Head,Link } from '@inertiajs/vue3'
  import AppLayout2 from '@/Layouts/AppLayout2.vue'; 
  import { defineProps } from 'vue'; 
  import Cta from '@/Components/Cta.vue';
  

  const props = defineProps({
    recentBlogs: Array,
    plans: Array,
    meta: Object,
    });


    const formatDate = (dateString) => {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    };
    const activeCycle = ref('monthly');
    const cycles = [
        { value: 'monthly', label: 'Monthly' },
        { value: 'quarterly', label: 'Quarterly' },
        { value: 'yearly', label: 'Yearly' },
    ];

    // Helper function to format price
    const formatPrice = (price) => {
    const numericPrice = typeof price === 'string' ? parseFloat(price) : price;
    
    if (isNaN(numericPrice)) return 'Invalid price';
    if (numericPrice === 0) return 'Free';
    
    // Check if it's a whole number
    const isWholeNumber = numericPrice % 1 === 0;
    
    return `₦${numericPrice.toLocaleString('en-NG', {
        minimumFractionDigits: isWholeNumber ? 0 : 2,
        maximumFractionDigits: isWholeNumber ? 0 : 2
    })}`;
};

// Helper function to get feature value
const getFeatureValue = (plan, featureSlug) => {
    return plan.features[featureSlug] ?? 'Not included';
};

// Helper to get price for active cycle
const getCyclePrice = (plan) => {
    const price = plan[`${activeCycle.value}_price`];
    if (plan.slug === 'enterprise') return 'Custom';
    return formatPrice(price);
};

// Helper to get price suffix
const getPriceSuffix = () => {
    switch(activeCycle.value) {
        case 'monthly': return '/month';
        case 'quarterly': return '/quarter';
        case 'yearly': return '/year';
        default: return '/month';
    }
};

    const stripHtml = (html) => {
  return html.replace(/<\/?[^>]+(>|$)/g, "");
};
  </script>
  
  <style scoped>
  /* Move the blob animation styles here or ensure they are global */
  @keyframes blob {
    0% {
      transform: translate(0px, 0px) scale(1);
    }
    33% {
      transform: translate(30px, -50px) scale(1.1);
    }
    66% {
      transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
      transform: translate(0px, 0px) scale(1);
    }
  }
  .animate-blob {
    animation: blob 7s infinite;
  }
  .animation-delay-2000 {
    animation-delay: 2s;
  }
  .animation-delay-4000 {
    animation-delay: 4s;
  }
  </style>