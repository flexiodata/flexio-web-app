<template>
  <flexio-modal
    title="Create Your Free Account"
    container-cls="relative"
    container-style="width: 32rem"
    :show-header="false"
    :show-footer="false"
    @cancel="$emit('cancel')"
    @submit="$emit('submit')"
  >
    <div class="pointer f3 lh-solid b child black-30 hover-black-60 mt2 mr3 absolute top-0 right-0" @click="$emit('cancel')">&times;</div>
    <div class="pv3 ph2">
      <FlexioEntryForm
        :initial-view="initialView"
      />
    </div>
  </flexio-modal>
</template>

<script>
  import FlexioModal from '@comp/FlexioModal'
  import FlexioEntryForm from '@comp/FlexioEntryForm'

  export default {
    props: {
      'initial-view': {
        type: String,
        default: 'signup'
      }
    },
    components: {
      FlexioModal,
      FlexioEntryForm
    },
    data() {
      return {
        view: this.initialView // 'signin', 'signup' or 'forgotpassword'
      }
    }
  }
</script>
