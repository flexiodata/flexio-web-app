<template>
  <div>
    <div
      class="tl pb3"
      v-if="title.length > 0"
    >
      <h3 class="fw6 f3 mid-gray mt0 mb2">{{title}}</h3>
    </div>
    <div
      class="pb3 mid-gray marked"
      v-html="description"
      v-show="show_description"
    >
    </div>
    <h4 class="mid-gray">1. Choose connection</h4>
    <BuilderComponentConnectionChooser
      class="mb3"
      :connection-identifier.sync="connection_identifier"
      :show-result="has_available_connection"
    >
      <el-button
        slot="buttons"
        plain
        size="tiny"
        class="ttu b"
        @click="clearConnection"
      >
        Use Different Connection
      </el-button>
    </BuilderComponentConnectionChooser>
    <template v-if="has_available_connection">
      <h4 class="mid-gray">2. Choose files</h4>
      <div class="mb3" v-show="paths.length > 0">
        <div class="bt b--black-10"></div>
        <div class="overflow-y-auto" style="max-height: 260px">
          <div
            class="flex flex-row items-center no-select cursor-default darken-05 css-item"
            :key="path"
            v-for="(path, index) in paths"
          >
            <div class="flex-fill ph2 f7 lh-copy">{{path}}</div>
            <div
              class="pointer black-30 hover-black-60 ph1 css-remove"
              @click="removeFile(index)"
            >
              <i class="db material-icons md-24">close</i>
            </div>
          </div>
        </div>
        <div class="bt b--black-10"></div>
      </div>
      <div>
        <el-button
          class="ttu b"
          size="small"
          @click="show_file_chooser_dialog = true"
        >
          Add files
        </el-button>
      </div>
    </template>

    <!-- file chooser dialog -->
    <el-dialog
      custom-class="el-dialog--compressed-body"
      title="Choose files"
      width="51rem"
      top="8vh"
      :modal-append-to-body="false"
      :visible.sync="show_file_chooser_dialog"
    >
      <BuilderComponentFileChooser
        ref="file-chooser"
        :connection-identifier="connection_identifier"
        :show-result="false"
        v-if="show_file_chooser_dialog"
      />
      <span slot="footer" class="dialog-footer">
        <el-button
          class="ttu b"
          @click="show_file_chooser_dialog = false"
        >
          Cancel
        </el-button>
        <el-button
          class="ttu b"
          type="primary"
          @click="addFiles"
        >
          Add files
        </el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
  import marked from 'marked'
  import { mapGetters } from 'vuex'
  import { CONNECTION_STATUS_AVAILABLE } from '../constants/connection-status'
  import BuilderComponentConnectionChooser from './BuilderComponentConnectionChooser.vue'
  import BuilderComponentFileChooser from './BuilderComponentFileChooser.vue'
  import MixinConnection from './mixins/connection'

  const getDefaultValues = () => {
    return {
      op: 'read',
      path: []
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
      paths() {
        var paths = _.get(this.edit_values, 'path', [])
        if (!_.isArray(paths)) {
          return [paths]
        }
        return paths
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
          if (_.isArray(path)) {
            path = _.get(path, '[0]', '')
          }

          path = path.substring(1)
          var cid = path.substring(0, path.indexOf('/'))
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
    }
  }
</script>

<style lang="stylus">
  .css-item
    padding: 1px 0
    .css-remove
      visibility: hidden
    &:hover
      .css-remove
        visibility: visible
</style>
