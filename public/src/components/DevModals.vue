<template>
  <div>
    <div class="flex flex-row pa4">
      <btn btn-md btn-primary class="ttu b v-mid" @hide="show_share_modal = false" @click="openShareModal">Open Pipe Share Modal</btn>
    </div>

    <!-- share modal -->
    <pipe-share-modal
      ref="modal-share"
      @hide="show_share_modal = false"
      v-if="show_share_modal"
    ></pipe-share-modal>
  </div>
</template>

<script>
  import Btn from './Btn.vue'
  import PipeShareModal from './PipeShareModal.vue'

  var example_pipe = {
    name: 'Test Pipe',
    eid: 'abcdef'
  }

  export default {
    components: {
      Btn,
      PipeShareModal
    },
    data() {
      return {
        show_share_modal: false
      }
    },
    methods: {
      openShareModal() {
        this.show_share_modal = true
        this.$nextTick(() => { this.$refs['modal-share'].open(example_pipe) })
      }
    }
  }
</script>
