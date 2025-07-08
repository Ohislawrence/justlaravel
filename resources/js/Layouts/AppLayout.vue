<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import FlashMessages from '@/Components/FlashMessages.vue';

const isMenuOpen = ref(false);
const page = usePage();
const showingNavigationDropdown = ref(false);

defineProps({
    title: String,
});

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <Head :title="title" />

    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <ResponsiveNavLink :href="route('dashboard')">
                        <span class="app-brand-text demo menu-text fw-bolder ms-2">Tracklia</span>
                    </ResponsiveNavLink>

                    <a href="javascript:void(0)" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1" v-if="$page.props.auth.user.role === 'landlord'">
                    <!-- Dashboard -->
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('landlord.dashboard')" :active="route().current('landlord.dashboard')">
                            <div>Dashboard</div>
                        </ResponsiveNavLink>
                    </li>
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('landlord.users.index')" :active="route().current('landlord.users.index')">
                            <div>Users</div>
                        </ResponsiveNavLink>
                    </li>
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('landlord.plans.index')" :active="route().current('landlord.plans.index')">
                            <div>Plans</div>
                        </ResponsiveNavLink>
                    </li>
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('landlord.settings.index')" :active="route().current('landlord.settings.index')">
                            <div>Settings</div>
                        </ResponsiveNavLink>
                    </li>
                </ul>

                <!-- Examiner -->
                <ul class="menu-inner py-1" v-if="$page.props.auth.user.role === 'examiner'">
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('examiner.dashboard')" :active="route().current('examiner.dashboard')">
                            <div>Dashboard</div>
                        </ResponsiveNavLink>
                    </li>
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('examiner.quizzes.index')" :active="route().current('examiner.quizzes.index')">
                            <div>Quiz</div>
                        </ResponsiveNavLink>
                    </li>
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('examiner.question-pools.index')" :active="route().current('examiner.question-pools.index')">
                            <div>Pool</div>
                        </ResponsiveNavLink>
                    </li>
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('examiner.user.index')" :active="route().current('examiner.user.index')">
                            <div>Users</div>
                        </ResponsiveNavLink>
                    </li>
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('examiner.groups.index')" :active="route().current('examiner.groups.index')">
                            <div>Groups</div>
                        </ResponsiveNavLink>
                    </li>
                    <li class="menu-item">
                        <ResponsiveNavLink :href="route('examiner.settings.index')" :active="route().current('examiner.settings.index')">
                            <div>Settings</div>
                        </ResponsiveNavLink>
                    </li>
                </ul>
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)" @click="isMenuOpen = !isMenuOpen">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <!-- Search -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center">
                                <i class="bx bx-search fs-4 lh-0"></i>
                                <input
                                    type="text"
                                    class="form-control border-0 shadow-none"
                                    placeholder="Search..."
                                    aria-label="Search..."
                                />
                            </div>
                        </div>
                        <!-- /Search -->

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0)" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img :src="'/assets/img/avatars/1.png'" alt class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0)">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img :src="'/assets/img/avatars/1.png'" alt class="w-px-40 h-auto rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-semibold d-block">{{ $page.props.auth.user.name }}</span>
                                                    <small class="text-muted">{{ $page.props.auth.user.email }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">
                                            <i class="bx bx-user me-2"></i>
                                            <span class="align-middle">My Profile</span>
                                        </ResponsiveNavLink>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0)">
                                            <i class="bx bx-cog me-2"></i>
                                            <span class="align-middle">Settings</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0)">
                                            <span class="d-flex align-items-center align-middle">
                                                <i class="flex-shrink-0 bx bx-credit-card me-2"></i>
                                                <span class="flex-grow-1 align-middle">Billing</span>
                                                <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20">4</span>
                                            </span>
                                        </a>
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
                        <header>
                            <slot name="header" />
                        </header>
                        <!-- Show success message -->
                        <div v-if="$page.props.flash.message" class="bg-green-100 border-t-4 border-green-500 rounded-b text-green-900 px-4 py-3 shadow-md my-5" role="alert">
                        <div class="flex">
                            <div>
                            {{ $page.props.flash.message }}
                            </div>
                        </div>
                        </div>
                        <!-- Content -->
                        <slot />
                    </div>
                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">
                                ©2025
                                <a href="https://tracklia.com" target="_blank" class="footer-link fw-bolder">Tracklia.com</a>
                            </div>
                            <div>
                                <a href="javascript:void(0)" class="footer-link me-4">License</a>
                                <a href="javascript:void(0)" class="footer-link me-4">Contact Us</a>
                                <a href="javascript:void(0)" class="footer-link me-4">Support</a>
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
        <div class="layout-overlay layout-menu-toggle" @click="isMenuOpen = false"></div>
    </div>
    <!-- / Layout wrapper -->
</template>