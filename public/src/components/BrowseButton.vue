<template>
  <el-button @click="show_dialog = true">
    <slot>Browse</slot>

    <!-- file chooser dialog -->
    <el-dialog
      custom-class="el-dialog--compressed-body"
      title="Choose file"
      width="60vw"
      top="4vh"
      :append-to-body="true"
      :visible.sync="show_dialog"
    >
      <FileChooser
        ref="file-chooser"
        style="max-height: 60vh"
        :selected-items.sync="files"
        :allow-multiple="false"
        :allow-folders="false"
        :show-connection-list="true"
        v-if="show_dialog"
      />
      <span slot="footer" class="dialog-footer">
        <el-button
          class="ttu fw6"
          @click="show_dialog = false"
        >
          Cancel
        </el-button>
        <el-button
          class="ttu fw6"
          type="primary"
          @click="addFiles"
        >
          Done
        </el-button>
      </span>
    </el-dialog>
  </el-button>
</template>

<script>
  import FileChooser from '@/components/FileChooser'

  export default {
    data() {
      return {
        files: [],
        show_dialog: false
      }
    },
    components: {
      FileChooser
    },
    methods: {
      addFiles() {
        var files = this.files
        files = _.map(files, (f) => { return f.full_path })
        this.$emit('path-selected', _.get(files, '[0]', ''))
        this.show_dialog = false
        this.files = []
      }
    }
  }
</script>
