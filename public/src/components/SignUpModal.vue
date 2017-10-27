<template>
  <flexio-modal
    title="Create Your Free Account"
    container-cls="relative"
    container-style="width: 32rem"
    :show-header="false"
    :show-footer="false"
    @cancel="show_modal = false"
    v-if="show_modal"
  >
    <div class="pointer f3 lh-solid b child black-30 hover-black-60 mt2 mr3 absolute top-0 right-0" @click="show_modal = false">&times;</div>
    <div class="pv3 ph2">
      <sign-up-form v-if="view === 'signup'" />
    </div>
  </flexio-modal>
</template>

<script>
  import FlexioModal from './FlexioModal.vue'
  import SignUpForm from './SignUpForm.vue'

  export default {
    props: {
      'initial-view': {
        type: String,
        default: 'signup'
      }
    },
    components: {
      FlexioModal,
      SignUpForm
    },
    data() {
      return {
        show_modal: false,
        view: this.initialView
      }
    },
    methods: {
      open() {
        this.show_modal = true
      },
      close() {
        this.show_modal = false
      }
    }
  }
</script>
