<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

const isMenuOpen = ref(false);
const page = usePage();
const showingNavigationDropdown = ref(false);

defineProps({
    title: String,
});

const isLandlord = computed(() => page.props.auth.user.role === 'landlord');

const currentOrganizationName = computed(() =>
    page.props.auth.user.current_organization?.name ||
    page.props.organization?.name ||
    'My Organization'
);

const userRole = computed(() => page.props.auth.user.role);
const showOrgIcon = computed(() => !isLandlord.value);
const showRoleBadge = computed(() => page.props.auth.user?.role);

const roleBadgeClasses = computed(() => ({
    'bg-blue-100 text-blue-800': userRole.value === 'examiner',
    'bg-purple-100 text-purple-800': userRole.value === 'landlord',
    'bg-gray-100 text-gray-800': !['examiner', 'landlord'].includes(userRole.value)
}));

const logout = () => {
    router.post(route('logout'));
};

// Mobile menu toggle
const toggleMobileMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
};
</script>

<style scoped>
.app-brand-text {
    @apply truncate max-w-[180px] md:max-w-[220px] transition-all duration-300;
}

/* Mobile menu overlay */
.layout-overlay {
    @apply fixed inset-0 z-40 bg-black bg-opacity-50 transition-opacity lg:hidden;
}

/* Sidebar styling for mobile */
@media (max-width: 1024px) {
    .layout-menu {
        @apply fixed inset-y-0 left-0 z-50 w-64 transform bg-white shadow-xl transition-transform duration-300 ease-in-out;
    }
    
    .layout-menu.closed {
        @apply -translate-x-full;
    }
    
    .layout-menu.open {
        @apply translate-x-0;
    }
    
    .layout-page {
        @apply ml-0;
    }
}
</style>

<template>
    <Head :title="title" />
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Mobile menu toggle overlay -->
            <div 
                v-if="isMenuOpen" 
                class="layout-overlay lg:hidden" 
                @click="toggleMobileMenu"
            ></div>

            <!-- Menu -->
            <aside 
                id="layout-menu" 
                class="layout-menu menu-vertical menu bg-white"
                :class="{ 'open': isMenuOpen, 'closed': !isMenuOpen }"
            >
                <div class="app-brand demo">
                    <ResponsiveNavLink :href="route('dashboard')" class="group">
                        <div class="flex items-center">
                            <!-- Logo/Icon (optional) -->
                            <svg v-if="isLandlord" class="w-5 h-5 mr-2 text-indigo-500" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 14a6 6 0 110-12 6 6 0 010 12z" />
                            </svg>
                            <!-- Main Brand Text -->
                            <span
                                class="app-brand-text font-bold text-gray-800 dark:text-blue group-hover:text-indigo-600 transition-colors duration-200">
                                <span v-if="isLandlord">QuizPortal</span>
                                <template v-else>
                                    <!-- Organization name with optional icon -->
                                    <svg v-if="showOrgIcon" class="w-4 h-4 mr-1 inline opacity-70" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    {{ currentOrganizationName }}
                                </template>
                            </span>
                        </div>
                    </ResponsiveNavLink>
                    <a href="javascript:void(0)"
                        class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none"
                        @click="toggleMobileMenu">
                        <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                </div>
                <div class="menu-inner-shadow"></div>
                <ul class="menu-inner py-1" v-if="$page.props.auth.user.role === 'landlord'">
                    <!-- Dashboard -->
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('landlord.dashboard')"
                            :active="route().current('landlord.dashboard')">
                            <div>Dashboard</div>
                        </ResponsiveNavLink>
                    </li>
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('landlord.users.index')"
                            :active="route().current('landlord.users.index')">
                            <div>Users</div>
                        </ResponsiveNavLink>
                    </li>
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('landlord.organizations.index')"
                            :active="route().current('landlord.organizations.index')">
                            <div>Organizations</div>
                        </ResponsiveNavLink>
                    </li>
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('landlord.plans.index')"
                            :active="route().current('landlord.plans.index')">
                            <div>Plans</div>
                        </ResponsiveNavLink>
                    </li>
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('landlord.subscriptions.index')"
                            :active="route().current('landlord.subscriptions.index')">
                            <div>Subscriptions</div>
                        </ResponsiveNavLink>
                    </li>
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('landlord.blogs.index')"
                            :active="route().current('landlord.blogs.index')">
                            <div>Blogs</div>
                        </ResponsiveNavLink>
                    </li>
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('landlord.settings.index')"
                            :active="route().current('landlord.settings.index')">
                            <div>Settings</div>
                        </ResponsiveNavLink>
                    </li>
                </ul>
                <!-- Examiner -->
                <ul class="menu-inner py-1" v-if="$page.props.auth.user.role === 'examiner'">
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('examiner.dashboard')"
                            :active="route().current('examiner.dashboard')">
                            <div>Dashboard</div>
                        </ResponsiveNavLink>
                    </li>
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('examiner.quiz-groups.index')"
                            :active="route().current('examiner.quiz-groups.index')">
                            <div>Quiz Group</div>
                        </ResponsiveNavLink>
                    </li>
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('examiner.quizzes.index')"
                            :active="route().current('examiner.quizzes.index')">
                            <div>Quiz</div>
                        </ResponsiveNavLink>
                    </li>
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('examiner.question-pools.index')"
                            :active="route().current('examiner.question-pools.index')">
                            <div>Question Pool</div>
                        </ResponsiveNavLink>
                    </li>
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('examiner.user.index')"
                            :active="route().current('examiner.user.index')">
                            <div>Users</div>
                        </ResponsiveNavLink>
                    </li>
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('examiner.groups.index')"
                            :active="route().current('examiner.groups.index')">
                            <div>User Group</div>
                        </ResponsiveNavLink>
                    </li>
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('examiner.subscription.plans')"
                            :active="route().current('examiner.subscription.plans')">
                            <div>Subscription</div>
                        </ResponsiveNavLink>
                    </li>
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('examiner.settings.index')"
                            :active="route().current('examiner.settings.index')">
                            <div>Settings</div>
                        </ResponsiveNavLink>
                    </li>
                </ul>
                <div class="menu-inner-shadow"></div>
                <ul class="menu-inner py-1" v-if="$page.props.auth.user.role === 'examinee'">
                    <!-- Dashboard -->
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('examinee.dashboard')"
                            :active="route().current('examinee.dashboard')">
                            <div>My Dashboard</div>
                        </ResponsiveNavLink>
                    </li>
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('examinee.quizzes.index')"
                            :active="route().current('examinee.quizzes.index')">
                            <div>My Quiz</div>
                        </ResponsiveNavLink>
                    </li>
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('examinee.history')"
                            :active="route().current('examinee.history')">
                            <div>My History</div>
                        </ResponsiveNavLink>
                    </li>
                </ul>
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <nav
                    class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)"
                            @click="toggleMobileMenu">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>
                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <!-- Search -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center">
                                <i class="bx bx-search fs-4 lh-0"></i>
                                <input type="text" class="form-control border-0 shadow-none"
                                    placeholder="Search..." aria-label="Search..." />
                            </div>
                        </div>
                        <!-- /Search -->
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow flex items-center space-x-2"
                                    href="javascript:void(0)" data-bs-toggle="dropdown">
                                    <div
                                        class="flex items-center justify-center w-10 h-10 bg-blue-500 rounded-full text-white font-semibold uppercase">
                                        <!-- 5. Use Tailwind classes for avatar size -->
                                        {{ $page.props.auth.user.name
                                            .split(' ')
                                            .map(n => n[0])
                                            .join('')
                                            .substring(0, 2) }}
                                    </div>
                                    <span class="hidden md:inline font-medium text-gray-800">
                                        {{ $page.props.auth.user.name }}
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item py-2 px-3 hover:bg-gray-100 rounded-md transition-colors duration-150"
                                            href="javascript:void(0)">
                                            <div class="d-flex align-items-center">
                                                <!-- Avatar -->
                                                <div class="flex-shrink-0 me-3">
                                                    <!-- 5. Use Tailwind classes for avatar size -->
                                                    <div
                                                        class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white fw-bold w-10 h-10">
                                                        {{ $page.props.auth.user.name
                                                            .split(' ')
                                                            .map(n => n[0])
                                                            .join('')
                                                            .substring(0, 2)
                                                            .toUpperCase() }}
                                                    </div>
                                                </div>
                                                <!-- User Info -->
                                                <div class="flex-grow-1">
                                                    <span class="fw-semibold d-block text-dark">{{
                                                        $page.props.auth.user.name }}</span>
                                                    <small class="text-muted d-block">{{ userRole }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <ResponsiveNavLink :href="route('profile.show')"
                                            :active="route().current('profile.show')">
                                            <i class="bx bx-user me-2"></i>
                                            <span class="align-middle">My Profile</span>
                                        </ResponsiveNavLink>
                                    </li>
                                    <li>
                                        <ResponsiveNavLink class="dropdown-item" :href="route('examiner.settings.index')"
                                            :active="route().current('examiner.settings.index')">
                                            <i class="bx bx-cog me-2"></i>
                                            <span class="align-middle">Settings</span>
                                        </ResponsiveNavLink>
                                    </li>
                                    <li v-if="userRole == 'examiner'">
                                        <ResponsiveNavLink class="dropdown-item" :href="route('examiner.subscription.plans')"
                                            :active="route().current('examiner.subscription.plans')">
                                            <span class="d-flex align-items-center align-middle">
                                                <i class="flex-shrink-0 bx bx-credit-card me-2"></i>
                                                <span class="flex-grow-1 align-middle">Subscription</span>
                                            </span>
                                        </ResponsiveNavLink>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <form method="POST" @submit.prevent="logout">
                                            <ResponsiveNavLink as="button">
                                                <i class="bx bx-power-off me-2"></i>
                                                <span class="align-middle">Log Out</span>
                                            </ResponsiveNavLink>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                            <!--/ User -->
                        </ul>
                    </div>
                </nav>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- 1. Standardized Header Slot Container -->
                        <header class="w-full">
                            <slot name="header" />
                        </header>

                        <!-- 2. Standardized Flash Messages (Success) -->
                        <div v-if="$page.props.flash.success" class="mb-4">
                            <div
                                class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                                <span class="block sm:inline">{{ $page.props.flash.success }}</span>
                                <button @click="$page.props.flash.success = null"
                                    class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                    <span class="sr-only">Close</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- 2. Standardized Flash Messages (Error) -->
                        <div v-if="$page.props.flash.error" class="mb-4">
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                                <span class="block sm:inline">{{ $page.props.flash.error }}</span>
                                <button @click="$page.props.flash.error = null"
                                    class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                    <span class="sr-only">Close</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Content -->
                        <slot />
                    </div>

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div
                            class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">
                                ©2025
                                <a href="https://quizportal.online  " target="_blank" class="footer-link fw-bolder">QuizPortal
                                    Online</a>
                            </div>
                            <div>
                                <a href="javascript:void(0)" class="footer-link me-4">License</a>
                                <Link :href="route('contact')" class="footer-link me-4">Contact Us</Link>
                                <Link :href="route('blogs.index')" class="footer-link me-4">Blogs</Link>
                            </div>
                        </div>
                    </footer>
                    <!-- / Footer -->
                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle" @click="toggleMobileMenu"></div>
    </div>
    <!-- / Layout wrapper -->
</template>