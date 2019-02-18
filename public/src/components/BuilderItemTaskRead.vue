<template>
  <div>
    <div
      class="tl pb3"
      v-if="title.length > 0"
    >
      <h3 class="fw6 f3 mt0 mb2">{{title}}</h3>
    </div>
    <div
      class="pb3 marked"
      v-html="description"
      v-show="show_description"
    >
    </div>
    <h4 class="fw6">1. Choose connection</h4>
    <BuilderComponentConnectionChooser
      class="mb3"
      :filter-by="filter_by"
      :connection-identifier.sync="connection_identifier"
      :show-result="has_available_connection"
    >
      <el-button
        slot="buttons"
        plain
        size="tiny"
        class="ttu fw6"
        @click="clearConnection"
      >
        Use Different Connection
      </el-button>
    </BuilderComponentConnectionChooser>
    <template v-if="has_available_connection">
      <h4 class="fw6">2. Choose file</h4>
      <el-input
        v-model="read_path"
        spellcheck="false"
      >
        <el-button
          slot="append"
          class="ttu fw6"
          type="primary"
          size="small"
          @click="show_file_chooser_dialog = true"
        >
          Browse
        </el-button>
      </el-input>
    </template>

    <!-- file chooser dialog -->
    <el-dialog
      custom-class="el-dialog--compressed-body"
      title="Choose file"
      width="51rem"
      top="8vh"
      :modal-append-to-body="false"
      :visible.sync="show_file_chooser_dialog"
    >
      <BuilderComponentFileChooser
        ref="file-chooser"
        :connection-identifier="connection_identifier"
        :allow-multiple="false"
        :allow-folders="false"
        :show-result="false"
        v-if="show_file_chooser_dialog"
      />
      <span slot="footer" class="dialog-footer">
        <el-button
          class="ttu fw6"
          @click="show_file_chooser_dialog = false"
        >
          Cancel
        </el-button>
        <el-button
          class="ttu fw6"
          type="primary"
          @click="addFile"
        >
          Choose file
        </el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
  import marked from 'marked'
  import { afterFirst } from '../utils'
  import { mapGetters } from 'vuex'
  import { CONNECTION_STATUS_AVAILABLE } from '../constants/connection-status'
  import BuilderComponentConnectionChooser from './BuilderComponentConnectionChooser.vue'
  import BuilderComponentFileChooser from './BuilderComponentFileChooser.vue'
  import MixinConnection from './mixins/connection'

  const getDefaultValues = () => {
    return {
      op: 'read',
      path: ''
    }
  }

  export default {
    props: {
      item: {
        type: Object,
        required: true
      },
      index: {
        type: Number,
        required: true
      },
      activeItemIdx: {
        type: Number,
        required: true
      },
      isNextAllowed: {
        type: Boolean,
        required: true
      }
    },
    mixins: [MixinConnection],
    components: {
      BuilderComponentConnectionChooser,
      BuilderComponentFileChooser
    },
    watch: {
      item: {
        handler: 'initSelf',
        immediate: true,
        deep: true
      },
      has_available_connection() {
        this.$emit('update:isNextAllowed', this.has_available_connection)
      },
      connection_identifier() {
        this.read_path = ''
        this.$emit('active-item-change', this.index)
      },
      edit_values: {
        handler: 'onEditValuesChange',
        immediate: true,
        deep: true
      }
    },
    data() {
      return {
        connection_identifier: '',
        orig_values: getDefaultValues(),
        edit_values: getDefaultValues(),
        show_file_chooser_dialog: false
      }
    },
    computed: {
      show_description() {
        return this.description.length > 0
      },
      title() {
        return _.get(this.item, 'title', 'Read')
      },
      description() {
        return marked(_.get(this.item, 'description', ''))
      },
      filter_by() {
        return (item) => {
          return this.$_Connection_isStorage(item)
        }
      },
      read_path: {
        get() {
          var path = afterFirst(this.edit_values.path, ':')
          if (path.indexOf('/') != 0) {
            path = '/' + path
          }
          return path
        },
        set(value) {
          if (this.connection_identifier.length == 0) {
            return this.edit_values.path = ''
          }

          var path = value
          if (path.indexOf('/') != 0) {
            path = '/' + path
          }

          this.edit_values.path = this.connection_identifier + ':' + path
        }
      },
      store_connection() {
        return this.$_Connection_getConnectionByIdentifier(this.connection_identifier)
      },
      has_available_connection() {
        return _.get(this.store_connection, 'connection_status', '') == CONNECTION_STATUS_AVAILABLE
      }
    },
    methods: {
      initSelf() {
        var form_values = _.get(this.item, 'form_values', {})

        // set connection identifier
        var path = _.get(form_values, 'path', '')
        if (path.length == 0) {
          this.connection_identifier = ''
        } else {
          var cid = path.substring(0, path.indexOf(':'))
          this.connection_identifier = cid
        }

        var values = _.assign({}, getDefaultValues(), form_values)
        this.orig_values = _.cloneDeep(values)
        this.edit_values = _.cloneDeep(values)

        this.$emit('update:isNextAllowed', this.has_available_connection)
      },
      clearConnection() {
        this.connection_identifier = ''
        this.edit_values = _.assign({}, this.edit_values, { path: [] })
      },
      onEditValuesChange() {
        if (_.isEqual(this.edit_values, this.orig_values)) {
          return
        }

        this.$emit('active-item-change', this.index)
        this.$emit('item-change', this.edit_values, this.index)
      },
      addFile() {
        var files = this.$refs['file-chooser'].getSelectedFiles()
        files = _.get(files, '[0].full_path', '')
        this.edit_values = _.assign({}, this.edit_values, { path: files })
        this.show_file_chooser_dialog = false
      },
      /*
      addFiles() {
        var files = this.$refs['file-chooser'].getSelectedFiles()
        files = _.map(files, (f) => { return f.path })
        var existing_files = _.get(this.edit_values, 'path', [])
        if (!_.isArray(existing_files)) {
          existing_files = [existing_files]
        }
        files = existing_files.concat(files)
        this.edit_values = _.assign({}, this.edit_values, { path: files })
        this.show_file_chooser_dialog = false
      },
      removeFile(idx) {
        var files = _.get(this.edit_values, 'path', [])
        files.splice(idx, 1)
        this.edit_values = _.assign({}, this.edit_values, { path: files })
      }
      */
    }
  }
</script>
