<template>
  <el-button @click="show_file_chooser_dialog = true">
    <slot>Browse</slot>

    <!-- file chooser dialog -->
    <el-dialog
      custom-class="el-dialog--compressed-body"
      title="Choose file"
      width="60vw"
      top="4vh"
      :append-to-body="true"
      :visible.sync="show_file_chooser_dialog"
    >
      <FileChooser
        ref="file-chooser"
        style="max-height: 60vh"
        :connection="active_connection"
        :selected-items.sync="files"
        v-bind="file_chooser_opts"
        v-if="show_file_chooser_dialog"
      />
      <span slot="footer" class="dialog-footer">
        <div class="flex flex-row items-center">
          <el-button
            class="ttu fw6"
            @click="show_new_connection_dialog = true"
            v-require-rights:connection.create
          >
            New Connection
          </el-button>
          <div class="flex-fill"></div>
          <el-button
            class="ttu fw6"
            @click="show_file_chooser_dialog = false"
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
        </div>
      </span>
    </el-dialog>

    <!-- new connection dialog -->
    <el-dialog
      custom-class="el-dialog--no-header el-dialog--no-footer"
      width="46rem"
      top="4vh"
      :modal-append-to-body="false"
      :append-to-body="true"
      :close-on-click-modal="false"
      :visible.sync="show_new_connection_dialog"
    >
      <ConnectionEditPanel
        @close="show_new_connection_dialog = false"
        @cancel="show_new_connection_dialog = false"
        @update-connection="onUpdateConnection"
        v-if="show_new_connection_dialog"
      />
    </el-dialog>
  </el-button>
</template>

<script>
  import FileChooser from '@/components/FileChooser'
  import ConnectionEditPanel from '@/components/ConnectionEditPanel'

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
        active_connection: undefined,
        show_file_chooser_dialog: false,
        show_new_connection_dialog: false
      }
    },
    components: {
      FileChooser,
      ConnectionEditPanel
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
        this.show_file_chooser_dialog = false
        this.files = []
      },
      onUpdateConnection(connection) {
        this.show_new_connection_dialog = false
        this.active_connection = connection
      },
    }
  }
</script>
