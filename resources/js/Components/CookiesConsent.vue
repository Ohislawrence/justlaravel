<script setup>
import { ref, onMounted } from 'vue';

const showConsent = ref(false);

onMounted(() => {
    const consent = localStorage.getItem('cookie_consent');

    // Only hide if user actually clicked Accept/Reject
    if (consent !== 'accepted' && consent !== 'necessary_only') {
        showConsent.value = true;
    }
});

const acceptAll = () => {
    localStorage.setItem('cookie_consent', 'accepted');
    showConsent.value = false;
    enableAnalytics();
};

const acceptNecessary = () => {
    localStorage.setItem('cookie_consent', 'necessary_only');
    showConsent.value = false;
    enableEssentialCookies();
};

const customizePreferences = () => {
    // expand later if needed
    acceptAll();
};

const enableEssentialCookies = () => {
    document.cookie = "essential_cookies=enabled; path=/; SameSite=Lax";
};

const enableAnalytics = () => {
    enableEssentialCookies();
    document.cookie = "analytics_enabled=true; path=/; SameSite=Lax";
    if (typeof gtag !== 'undefined') {
        gtag('consent', 'update', { 'analytics_storage': 'granted' });
    }
};
</script>
