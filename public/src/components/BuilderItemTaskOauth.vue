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
      :filter-by="filter_by"
      :connection-identifier.sync="edit_values.connection"
      :show-result="has_available_connection"
      v-on="$listeners"
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
    <el-form
      class="el-form--compact el-form__label-tiny"
      label-position="top"
      :model="edit_values"
      v-if="has_available_connection"
    >
      <el-form-item
        key="alias"
        prop="alias"
        label="What langauge would you like to output?"
      >
        <el-select v-model="edit_values.lang">
          <el-option
            :label="option.label"
            :value="option.val"
            :key="option.val"
            v-for="option in lang_options"
          />
        </el-select>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
  import marked from 'marked'
  import { mapGetters } from 'vuex'
  import util from '../utils'
  import { CONNECTION_STATUS_AVAILABLE } from '../constants/connection-status'
  import BuilderComponentConnectionChooser from './BuilderComponentConnectionChooser.vue'
  import MixinConnection from './mixins/connection'

  const getDefaultValues = () => {
    return {
      op: 'oauth',
      lang: 'python'
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
        edit_values: getDefaultValues(),
        lang_options: [
          { label: 'Python',  val: 'python' },
          { label: 'Node.js', val: 'nodejs' }
          //{ label: 'Javascript', val: 'javascript' }
        ]
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
      },
      filter_by() {
        return (item) => {
          return this.$_Connection_isOauth(item)
        }
      },
      lang() {
        return _.get(this.edit_values, 'lang', 'python')
      },
      base64_nodejs() {
        var alias = this.$_Connection_getConnectionIdentifier(this.store_connection)
        var code = `// This function returns the OAuth access token from the specified connection.
// Click the "Test" button to echo back your OAuth token.

exports.flex_handler = function(flex) {

  // The connection identifier can be either the alias that you specified
  // or the eid of the connection.
  var connection_identifier = '${alias}'
  //var auth_token = flex.connections[connection_identifier].get_access_token()

  // We're simply echoing the OAuth token here. You can use this token
  // for native API calls to the connected service.
  flex.end('auth_token')
}
`
        return util.btoaUnicode(code)
      },
      base64_python() {
        var alias = this.$_Connection_getConnectionIdentifier(this.store_connection)
        var code = `# This function returns the OAuth access token from the specified connection.
# Click the "Test" button to echo back your OAuth token.

import requests
import json

def flex_handler(flex):

    # The connection identifier can be either the alias that you specified
    # or the eid of the connection.
    connection_identifier = '${alias}'
    auth_token = flex.connections[connection_identifier].get_access_token()

    # We're simply echoing the OAuth token here. You can use this token
    # for native API calls to the connected service.
    flex.end(auth_token)
`

        return util.btoaUnicode(code)
      },
      base64_code() {
        switch (this.lang) {
          case 'nodejs': return this.base64_nodejs
          case 'python': return this.base64_python
        }

        return ''
      },
      execute_task() {
        return {
          op: 'execute',
          lang: this.lang,
          code: this.base64_code
        }
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
        this.$emit('item-change', this.execute_task, this.index)
      }
    }
  }
</script>
