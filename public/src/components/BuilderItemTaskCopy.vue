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
      <div>
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
            class="ttu fw6"
            @click="clearConnection"
          >
            Use Different Connection
          </el-button>
        </BuilderComponentConnectionChooser>
      </div>
      <template v-if="has_available_source_connection">
        <label class="el-form-item__label">Source path</label>
        <el-input
          v-model="source_path"
          spellcheck="false"
        >
          <el-button
            slot="append"
            class="ttu fw6"
            type="primary"
            size="small"
            @click="showSourceFileChooser"
          >
            Browse
          </el-button>
        </el-input>
      </template>

      <div v-if="has_available_source_connection">
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
            class="ttu fw6"
            @click="clearConnection"
          >
            Use Different Connection
          </el-button>
        </BuilderComponentConnectionChooser>
      </div>
      <template v-if="has_available_destination_connection">
        <label class="el-form-item__label">Destination path</label>
        <el-input
          v-model="destination_path"
          spellcheck="false"
        >
          <el-button
            slot="append"
            class="ttu fw6"
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
      top="4vh"
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
          Done
        </el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
  import marked from 'marked'
  import { afterNth } from '@/utils'
  import { mapGetters } from 'vuex'
  import { CONNECTION_STATUS_AVAILABLE } from '../constants/connection-status'
  import BuilderComponentConnectionChooser from '@/components/BuilderComponentConnectionChooser'
  import BuilderComponentFileChooser from '@/components/BuilderComponentFileChooser'
  import MixinConnection from '@/components/mixins/connection'

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
        this.source_path = ''
        this.$emit('active-item-change', this.index)
      },
      destination_connection_identifier() {
        this.destination_path = ''
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
      source_path: {
        get() {
          var after_path = afterNth(this.edit_values.from, '/', 2)
          return '/' + after_path
        },
        set(value) {
          if (this.source_connection_identifier.length == 0) {
            return this.edit_values.from = ''
          }

          var path = value
          if (path.indexOf('/') != 0) {
            path = '/' + path
          }

          this.edit_values.from = '/' + this.source_connection_identifier + path
        }
      },
      destination_path: {
        get() {
          var after_path = afterNth(this.edit_values.to, '/', 2)
          return '/' + after_path
        },
        set(value) {
          if (this.destination_connection_identifier.length == 0) {
            return this.edit_values.to = ''
          }

          var path = value
          if (path.indexOf('/') != 0) {
            path = '/' + path
          }

          this.edit_values.to = '/' + this.destination_connection_identifier + path
        }
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
