import { ref } from 'vue'

export default function useFeatureLimit() {
  const showModal = ref(false)
  const modalFeature = ref('')
  const modalMessage = ref('')

  /**
   * Check feature availability and open modal if unavailable
   * @param {Boolean} condition - true if feature can be used
   * @param {String} featureName - human-friendly feature name
   * @param {String} message - error or upgrade message
   * @returns {Boolean} true if allowed, false if blocked
   */
  function checkFeature(condition, featureName, message) {
    if (!condition) {
      modalFeature.value = featureName
      modalMessage.value = message || 'This feature is not available in your current subscription plan.'
      showModal.value = true
      return false
    }
    return true
  }

  return {
    showModal,
    modalFeature,
    modalMessage,
    checkFeature,
  }
}
