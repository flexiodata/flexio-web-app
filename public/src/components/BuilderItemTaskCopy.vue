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

    <div class="el-form el-form--cozy el-form__label-tiny">
      <h4 class="fw6">1. Choose source</h4>
      <BuilderComponentConnectionChooser
        class="mb3"
        :connection-identifier.sync="source_connection_identifier"
        :show-result="has_available_source_connection"
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
      <template v-if="has_available_source_connection">
        <label class="el-form-item__label">Source path</label>
        <el-input
          v-model="edit_values.from"
        >
          <el-button
            slot="append"
            class="ttu b"
            type="primary"
            size="small"
            @click="showSourceFileChooser"
          >
            Browse
          </el-button>
        </el-input>
      </template>

      <h4 class="fw6 mt4">2. Choose destination</h4>
      <BuilderComponentConnectionChooser
        class="mb3"
        :connection-identifier.sync="destination_connection_identifier"
        :show-result="has_available_destination_connection"
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
      <template v-if="has_available_destination_connection">
        <label class="el-form-item__label">Destination path</label>
        <el-input
          v-model="edit_values.to"
        >
          <el-button
            slot="append"
            class="ttu b"
            type="primary"
            size="small"
            @click="showDestinationFileChooser"
          >
            Browse
          </el-button>
        </el-input>
      </template>
    </div>

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
        :connection-identifier="file_chooser_connection_identifier"
        :allow-multiple="false"
        :allow-folders="false"
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
          @click="addFile"
        >
          Done
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
      op: 'copy',
      from: '',
      to: ''
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
      has_available_connections() {
        this.$emit('update:isNextAllowed', this.has_available_connections)
      },
      source_connection_identifier() {
        this.$emit('active-item-change', this.index)
      },
      destination_connection_identifier() {
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
        source_connection_identifier: '',
        destination_connection_identifier: '',
        orig_values: getDefaultValues(),
        edit_values: getDefaultValues(),
        show_file_chooser_dialog: false,
        choosing_source: false
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
      source_connection() {
        return this.$_Connection_getConnectionByIdentifier(this.source_connection_identifier)
      },
      destination_connection() {
        return this.$_Connection_getConnectionByIdentifier(this.destination_connection_identifier)
      },
      file_chooser_connection_identifier() {
        return this.choosing_source ? this.source_connection_identifier : this.destination_connection_identifier
      },
      has_available_source_connection() {
        return _.get(this.source_connection, 'connection_status', '') == CONNECTION_STATUS_AVAILABLE
      },
      has_available_destination_connection() {
        return _.get(this.destination_connection, 'connection_status', '') == CONNECTION_STATUS_AVAILABLE
      },
      has_available_connections() {
        return this.has_available_source_connection && this.has_available_destination_connection
      }
    },
    methods: {
      initSelf() {
        var form_values = _.get(this.item, 'form_values', {})

        // set source connection identifier
        var path = _.get(form_values, 'from', '')
        if (path.length == 0) {
          this.source_connection_identifier = ''
        } else {
          if (_.isArray(path)) {
            path = _.get(path, '[0]', '')
          }

          path = path.substring(1)
          var cid = path.substring(0, path.indexOf('/'))
          this.source_connection_identifier = cid
        }

        // set destination connection identifier
        var path = _.get(form_values, 'to', '')
        if (path.length == 0) {
          this.destination_connection_identifier = ''
        } else {
          if (_.isArray(path)) {
            path = _.get(path, '[0]', '')
          }

          path = path.substring(1)
          var cid = path.substring(0, path.indexOf('/'))
          this.destination_connection_identifier = cid
        }

        var values = _.assign({}, getDefaultValues(), form_values)
        this.orig_values = _.cloneDeep(values)
        this.edit_values = _.cloneDeep(values)

        this.$emit('update:isNextAllowed', this.has_available_connections)
      },
      clearConnection() {
        this.source_connection_identifier = ''
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
        files = _.get(files, '[0].path', '')

        if (this.choosing_source) {
          this.edit_values = _.assign({}, this.edit_values, { from: files })
        } else {
          this.edit_values = _.assign({}, this.edit_values, { to: files })
        }

        this.show_file_chooser_dialog = false
      },
      showSourceFileChooser() {
        this.choosing_source = true
        this.show_file_chooser_dialog = true
      },
      showDestinationFileChooser() {
        this.choosing_source = false
        this.show_file_chooser_dialog = true
      }
    }
  }
</script>
