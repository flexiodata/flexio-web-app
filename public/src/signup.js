import FlexioSignUpModal from './components/SignUpModal.vue'

// expose component to global scope
if (typeof window !== 'undefined' && window.Vue) {
  Vue.component('flexio-sign-up-modal', FlexioSignUpModal)
}

export { FlexioSignUpModal }

export default FlexioSignUpModal
