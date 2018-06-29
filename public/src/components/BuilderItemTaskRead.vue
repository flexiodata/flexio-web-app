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
      :connection-eid.sync="connection_eid"
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
      <BuilderComponentFileChooser
        :connection-eid="connection_eid"
        :show-result="!is_active"
        @open-folder="updateFolder"
        @selection-change="updateFiles"
      />
    </template>

  </div>
</template>

<script>
  import marked from 'marked'
  import { mapGetters } from 'vuex'
  import { CONNECTION_STATUS_AVAILABLE } from '../constants/connection-status'
  import BuilderComponentConnectionChooser from './BuilderComponentConnectionChooser.vue'
  import BuilderComponentFileChooser from './BuilderComponentFileChooser.vue'

  const getDefaultValues = () => {
    return {
      op: 'connect',
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
      connection_eid() {
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
        connection_eid: '',
        orig_values: getDefaultValues(),
        edit_values: getDefaultValues()
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
      is_active() {
        return this.index == this.activeItemIdx
      },
      store_connection() {
        return _.find(this.getAvailableConnections(), { eid: this.connection_eid }, null)
      },
      has_available_connection() {
        return _.get(this.store_connection, 'connection_status', '') == CONNECTION_STATUS_AVAILABLE
      }
    },
    methods: {
      ...mapGetters([
        'getAvailableConnections'
      ]),
      initSelf() {
        var form_values = _.get(this.item, 'form_values', {})

        // set connection eid
        var path = _.get(form_values, 'path', '')
        if (path.length == 0) {
          this.connection_eid = ''
        } else {
          if (_.isArray(path)) {
            path = _.get(path, '[0]', '')
          }

          path = path.substring(1)
          var ceid = path.substring(0, path.indexOf('/'))
          this.connection_eid = ceid
        }

        this.orig_values = _.assign(getDefaultValues(), form_values)
        this.edit_values = _.assign(getDefaultValues(), form_values)

        this.$emit('update:isNextAllowed', this.has_available_connection)
      },
      clearConnection() {
        this.connection_eid = ''
      },
      updateFolder(files, path) {
        this.edit_values = _.assign({}, this.edit_values, { path: '' })
      },
      updateFiles(files, path) {
        var file_paths = _.map(files, (f) => { return f.path })
        this.edit_values = _.assign({}, this.edit_values, { path: file_paths })
      },
      onEditValuesChange() {
        if (_.isEqual(this.edit_values, this.orig_values)) {
          return
        }

        this.$emit('active-item-change', this.index)
        this.$emit('item-change', this.edit_values, this.index)
      }
    }
  }
</script>
