<template>
  <div :class="disabled ? 'o-40 no-pointer-events': ''">
    <ConnectionListItem
      v-for="(item, key) in items"
      :key="key"
      :item="item"
      :selected-item="selectedItem"
      @activate="onItemActivate"
      @delete="onItemDelete"
      v-bind="$attrs"
    />
    <div class="pa2" v-if="showAddButton">
      <el-button
        size="small"
        type="primary"
        class="ttu fw6 w-100 connection-new-button"
        @click="show_connection_dialog = true"
        v-require-rights:connection.update
      >
        New Connection
      </el-button>
    </div>

    <!-- connection dialog -->
    <el-dialog
      custom-class="el-dialog--no-header el-dialog--no-footer"
      width="46rem"
      top="4vh"
      :modal-append-to-body="false"
      :close-on-click-modal="false"
      :visible.sync="show_connection_dialog"
    >
      <ConnectionEditPanel
        @close="show_connection_dialog = false"
        @cancel="show_connection_dialog = false"
        @update-connection="onUpdateConnection"
        v-if="show_connection_dialog"
      />
    </el-dialog>
  </div>
</template>

<script>
  import ConnectionListItem from '@/components/ConnectionListItem'
  import ConnectionEditPanel from '@/components/ConnectionEditPanel'

  export default {
    inheritAttrs: false,
    props: {
      items: {
        type: Array,
        default: () => []
      },
      selectedItem: {
        type: Object,
        default: () => {}
      },
      disabled: {
        type: Boolean,
        default: false
      },
      showAddButton: {
        type: Boolean,
        default: false
      }
    },
    components: {
      ConnectionListItem,
      ConnectionEditPanel
    },
    data() {
      return {
        show_connection_dialog: false
      }
    },
    methods: {
      onUpdateConnection(connection) {
        this.$emit('update:selectedItem', connection)
        this.show_connection_dialog = false
      },
      onItemActivate(item) {
        this.$emit('item-activate', item)
      },
      onItemDelete(item) {
        this.$emit('item-delete', item)
      }
    }
  }
</script>

<style lang="stylus" scoped>
  @import '../stylesheets/variables.styl'

  .connection-new-button
    background-color: $nearer-white
    border: 1px dashed rgba(0,0,0,0.075)
    color: #bbb

    &:hover
      background-color: $blue
      border: 1px solid $blue
      color: #fff
</style>
