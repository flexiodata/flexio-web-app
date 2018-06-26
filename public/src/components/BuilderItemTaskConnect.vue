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
    <BuilderComponentConnectionChooser
      :connection-eid.sync="edit_values.connection"
      :show-result="has_available_connection"
      v-on="$listeners"
    />
    <el-form
      class="el-form--compact"
      :model="edit_values"
      v-if="has_available_connection"
    >
      <el-form-item
        key="alias"
        prop="alias"
      >
        <p class="mv0 mr2 dib">How would you like to refer to this connection in this pipe?</p>
        <span class="dib w5">
          <el-input
            placeholder="Alias"
            v-model="edit_values['alias']"
          />
        </span>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
  import marked from 'marked'
  import { mapGetters } from 'vuex'
  import { CONNECTION_STATUS_AVAILABLE } from '../constants/connection-status'
  import BuilderComponentConnectionChooser from './BuilderComponentConnectionChooser.vue'

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
    components: {
      BuilderComponentConnectionChooser
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
      ceid() {
        return _.get(this.edit_values, 'connection', null)
      },
      store_connection() {
        return _.find(this.getAvailableConnections(), { eid: this.ceid }, null)
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
        this.orig_values = _.assign(getDefaultValues(), form_values)
        this.edit_values = _.assign(getDefaultValues(), form_values)
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
