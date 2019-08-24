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
        v-bind="file_chooser_opts"
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
    props: {
      fileChooserOptions: {
        type: Object,
        default: () => {}
      }
    },
    data() {
      return {
        files: [],
        show_dialog: false
      }
    },
    components: {
      FileChooser
    },
    computed: {
      file_chooser_opts() {
        var default_opts = {
          allowMultiple: false,
          allowFolders: false,
          showConnectionList: true
        }

        return _.assign({}, default_opts, this.fileChooserOptions)
      }
    },
    methods: {
      addFiles() {
        var files = this.files
        var just_paths = _.map(files, (f) => { return f.full_path })

        if (this.file_chooser_opts.allowMultiple === false) {
          just_paths = _.get(just_paths, '[0]', '')
          files = _.get(files, '[0]', '')
        }

        this.$emit('paths-selected', just_paths, files)
        this.show_dialog = false
        this.files = []
      }
    }
  }
</script>
