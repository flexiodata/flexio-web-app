<template>
  <ui-modal
    ref="dialog"
    title="Which connection would you like to use?"
  >
    <div class="flex flex-row">
      <div class="flex-fill">
        <h5 class="ma0 mb2 f6 fw6 ttu silver">Existing Connections</h5>
        <connection-chooser-list
          :connection-type-filter="connection_type"
          :show-default-connections="false"
          @item-activate="submitExisting"
        ></connection-chooser-list>
      </div>
      <div class="flex-none">
        <h5 class="ma0 mb2 f6 fw6 ttu silver">Create a New Connection</h5>
        <service-list
          list-type="input"
          :filter-items="connection_type"
          @item-activate="submitNew"
        ></service-list>
      </div>
    </div>
  </ui-modal>
</template>

<script>
  import Btn from './Btn.vue'
  import ConnectionChooserList from './ConnectionChooserList.vue'
  import ServiceList from './ServiceList.vue'

  export default {
    components: {
      Btn,
      ConnectionChooserList,
      ServiceList
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
        this.$nextTick(() => { this.close() })
      },
      submitNew(item) {
        this.$emit('choose-new', item, this)
        this.$nextTick(() => { this.close() })
      }
    }
  }
</script>
