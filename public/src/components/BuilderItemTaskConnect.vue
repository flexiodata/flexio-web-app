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
    <BuilderComponentConnectionChooser
      class="mb3"
      :connection-identifier.sync="edit_values.connection"
      :show-result="has_available_connection"
      v-on="$listeners"
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
    <el-form
      class="el-form--compact el-form__label-tiny"
      label-position="top"
      :model="edit_values"
      v-if="has_available_connection"
    >
      <el-form-item
        key="alias"
        prop="alias"
        label="How would you like to refer to this connection in this pipe?"
      >
        <div class="w5">
          <el-input
            placeholder="Alias"
            v-model="edit_values['alias']"
          />
        </div>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
  import marked from 'marked'
  import { mapGetters } from 'vuex'
  import { CONNECTION_STATUS_AVAILABLE } from '../constants/connection-status'
  import BuilderComponentConnectionChooser from './BuilderComponentConnectionChooser.vue'
  import MixinConnection from './mixins/connection'

  const getDefaultValues = () => {
    return {
      op: 'connect',
      connection: '',
      alias: ''
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
      BuilderComponentConnectionChooser
    },
    watch: {
      item: {
        handler: 'initSelf',
        immediate: true,
        deep: true
      },
      has_available_connection: {
        handler: 'updateNextAllowed',
        immediate: true
      },
      is_changed: {
        handler: 'onChange'
      },
      edit_values: {
        handler: 'onEditValuesChange',
        immediate: true,
        deep: true
      }
    },
    data() {
      return {
        orig_values: getDefaultValues(),
        edit_values: getDefaultValues()
      }
    },
    computed: {
      show_description() {
        return this.description.length > 0
      },
      title() {
        return _.get(this.item, 'title', 'Connect')
      },
      description() {
        return marked(_.get(this.item, 'description', ''))
      },
      is_changed() {
        return !_.isEqual(this.edit_values, this.orig_values)
      },
      cid() {
        return _.get(this.edit_values, 'connection', null)
      },
      store_connection() {
        return this.$_Connection_getConnectionByIdentifier(this.cid)
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
        this.orig_values = _.assign({}, getDefaultValues(), form_values)
        this.edit_values = _.assign({}, getDefaultValues(), form_values)
      },
      clearConnection() {
        this.edit_values = _.assign({}, this.edit_values, { connection: '' })
      },
      updateNextAllowed(value) {
        this.$emit('update:isNextAllowed', value)
      },
      onChange(val) {
        if (val) {
          this.$emit('active-item-change', this.index)
        }
      },
      onEditValuesChange() {
        this.$emit('item-change', this.edit_values, this.index)
      }
    }
  }
</script>
