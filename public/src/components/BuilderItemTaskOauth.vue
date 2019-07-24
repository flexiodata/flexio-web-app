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
        key="lang"
        prop="lang"
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
  import { btoaUnicode } from '@/utils'
  import { CONNECTION_STATUS_AVAILABLE } from '@/constants/connection-status'
  import BuilderComponentConnectionChooser from '@/components/BuilderComponentConnectionChooser'
  import MixinConnection from '@/components/mixins/connection'

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
        var cname = this.$_Connection_getConnectionIdentifier(this.store_connection)
        var code = `// this function returns an OAuth access token to access the service specified
// in a connection; click the "Test" button to echo the OAuth token

exports.flex_handler = function(flex) {

  // get the OAuth access token for the connection from the connection
  // identifier, which can be the connection name or connection eid
  var connection_identifier = '${cname}'
  var auth_token = flex.connections[connection_identifier].getAccessToken()

  // echo the OAuth token; use this token to make authenticated API calls
  // to the service defined in the connection
  flex.end(auth_token)
}
`
        return btoaUnicode(code)
      },
      base64_python() {
        var cname = this.$_Connection_getConnectionIdentifier(this.store_connection)
        var code = `# this function returns an OAuth access token to access the service specified
# in a connection; click the "Test" button to echo the OAuth token

def flex_handler(flex):

    # get the OAuth access token for the connection from the connection
    # identifier, which can be the connection name or connection eid
    connection_identifier = '${cname}'
    auth_token = flex.connections[connection_identifier].get_access_token()

    # echo the OAuth token; use this token to make authenticated API calls
    # to the service defined in the connection
    flex.end(auth_token)
`

        return btoaUnicode(code)
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
