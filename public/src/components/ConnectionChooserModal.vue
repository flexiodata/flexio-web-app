<template>
  <ui-modal
    ref="dialog"
    title="Which connection would you like to use?"
  >
    <connection-chooser-list
      :connection-type-filter="connection_type"
      :show-default-connections="false"
      @item-activate="submitExisting"
    ></connection-chooser-list>
  </ui-modal>
</template>

<script>
  import Btn from './Btn.vue'
  import ConnectionChooserList from './ConnectionChooserList.vue'

  export default {
    props: {
      'connection-type-filter': {
        type: String,
        default: ''
      }
    },
    components: {
      Btn,
      ConnectionChooserList
    },
    data() {
      return {
        ss_errors: {},
        connection_type: ''
      }
    },
    methods: {
      open(connection_type) {
        this.reset(connection_type)
        this.$refs['dialog'].open()
      },
      close() {
        this.$refs['dialog'].close()
      },
      reset(connection_type) {
        this.ss_errors = {}
        this.connection_type = connection_type
      },
      submitExisting(item) {
        this.$emit('choose-existing', item, this)
      }
    }
  }
</script>
