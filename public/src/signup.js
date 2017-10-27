import FlexioModal from './components/FlexioModal.vue'

// expose component to global scope
if (typeof window !== 'undefined' && window.Vue) {
  Vue.component('flexio-modal', FlexioModal)
}

export { FlexioModal }

export default FlexioModal
