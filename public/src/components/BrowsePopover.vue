<template>
  <el-popover
    trigger="click"
    v-on="$listeners"
    v-bind="$attrs"
    v-model="is_visible"
    v-if="connection.eid"
  >
    <FileChooser
      style="min-height: 250px; height: 36vh"
      :connection="connection"
      :selected-items.sync="files"
      v-on="$listeners"
      v-bind="file_chooser_opts"
    />
    <slot name="reference" slot="reference">Browse</slot>
  </el-popover>
</template>

<script>
  import FileChooser from '@/components/FileChooser'

  export default {
    inheritAttrs: false,
    props: {
      connection: {
        type: Object,
        default: () => {}
      },
      fileChooserOptions: {
        type: Object,
        default: () => {}
      },
    },
    data() {
      return {
        is_visible: false,
        files: [],
      }
    },
    components: {
      FileChooser
    },
    computed: {
      file_chooser_opts() {
        var default_opts = {
          allowMultiple: false,
          allowFolders: false
        }

        return _.assign({}, default_opts, this.fileChooserOptions)
      }
    },
  }
</script>
