<template>
  <div>
    <div>
      <div class="flex flex-column flex-row-ns">
        <value-select
          class="w-25-ns min-w4-ns"
          placeholder="Method"
          label="Method"
          floating-label
          :options="method_options"
          v-model="method"
        />
        <ui-textbox
          class="flex-fill ml4-ns"
          autocomplete="off"
          placeholder="URL"
          label="URL"
          floating-label
          help=" "
          v-model="url"
        />
      </div>
    </div>

    <div class="mv3">
      <ui-tabs>
        <ui-tab id="authorization" title="Authorization">
          <div class="ma3 mw6">
            <value-select
              placeholder="Authorization Type"
              label="Authorization Type"
              floating-label
              :options="auth_options"
              v-model="auth"
            />
            <ui-textbox
              autocomplete="off"
              placeholder="Username"
              label="Username"
              floating-label
              help=" "
              v-model="username"
              v-if="auth == 'basic'"
            />
            <ui-textbox
              type="password"
              autocomplete="off"
              placeholder="Password"
              label="Password"
              floating-label
              help=" "
              v-model="password"
              v-if="auth == 'basic'"
            />
            <ui-textbox
              type="password"
              autocomplete="off"
              placeholder="Token"
              label="Token"
              floating-label
              help=" "
              v-model="token"
              v-if="auth == 'bearer'"
            />
            <ui-textbox
              type="password"
              autocomplete="off"
              placeholder="Access Token"
              label="Access Token"
              floating-label
              help=" "
              v-model="access_token"
              v-if="auth == 'oauth2'"
            />
            <ui-textbox
              type="password"
              autocomplete="off"
              placeholder="Refresh Token"
              label="Refresh Token"
              help=" "
              v-model="refresh_token"
              v-if="auth == 'oauth2'"
            />
            <ui-textbox
              autocomplete="off"
              placeholder="Expires"
              label="Expires"
              floating-label
              help=" "
              v-model="expires"
              v-if="auth == 'oauth2'"
            />
          </div>
        </ui-tab>

        <ui-tab id="form-data" title="Form Data">
          <div class="ma3">
            <keypair-item
              :item="{ key: 'Key', val: 'Value' }"
              :is-static="true"
              v-if="false"
            />
            <keypair-item
              v-for="(item, index) in data"
              :key="index"
              :item="item"
              :index="index"
              :count="data.length"
              @change="onFormDataItemChange"
              @delete="onFormDataItemDelete"
            />
          </div>
        </ui-tab>

        <ui-tab id="headers" title="Headers">
          <div class="ma3">
            <keypair-item
              :item="{ key: 'Key', val: 'Value' }"
              :is-static="true"
              v-if="false"
            />
            <keypair-item
              v-for="(item, index) in headers"
              :key="index"
              :item="item"
              :index="index"
              :count="headers.length"
              @change="onHeaderItemChange"
              @delete="onHeaderItemDelete"
            />
          </div>
        </ui-tab>
      </ui-tabs>
    </div>
  </div>
</template>

<script>
  import { CONNECTION_TYPE_HTTP } from '../constants/connection-type'
  import Btn from './Btn.vue'
  import ServiceIcon from './ServiceIcon.vue'
  import ValueSelect from './ValueSelect.vue'
  import KeypairItem from './KeypairItem.vue'

  const newKeypairItem = (key, val) => {
    key = _.defaultTo(key, '')
    val = _.defaultTo(val, '')

    return {
      key,
      val
    }
  }

  const method_options = [
    { val: '',        label: 'Default (none)' },
    { val: 'GET',     label: 'GET'            },
    { val: 'POST',    label: 'POST'           },
    { val: 'PUT',     label: 'PUT'            },
    { val: 'PATCH',   label: 'PATCH'          },
    { val: 'DELETE',  label: 'DELETE'         },
    { val: 'HEAD',    label: 'HEAD'           },
    { val: 'OPTIONS', label: 'OPTIONS'        }
  ]

  const auth_options = [
    { val: 'none',   label: 'No Auth'      },
    { val: 'basic',  label: 'Basic Auth'   },
    { val: 'bearer', label: 'Bearer Token' }/*,
    { val: 'oauth2', label: 'OAuth 2.0'    }*/
  ]

  export default {
    props: {
      'connection': {
        type: Object,
        required: true
      }
    },
    components: {
      Btn,
      ServiceIcon,
      ValueSelect,
      KeypairItem
    },
    watch: {
      connection_info() {
        this.$emit('update:connection', this.getConnection())
      }
    },
    data() {
      return {
        method_options,
        auth_options,
        method: '',
        url: '',
        auth: 'none',
        username: '',
        password: '',
        token: '',
        access_token: '',
        refresh_token: '',
        expires: '',
        headers: [],
        data: []
      }
    },
    computed: {
      connection_info() {
        return _.pick(this.$data, ['method', 'url', 'auth', 'username', 'password', 'token', 'access_token', 'refresh_token', 'expires', 'headers', 'data'])
      }
    },
    mounted() {
      this.$nextTick(() => {
        this.reset()
      })
    },
    methods: {
      reset() {
        _.each(_.get(this.connection, 'connection_info', {}), (val, key) => {
          if (_.isString(val))
            this[key] = val

          if (_.isPlainObject(val))
          {
            _.each(val, (val2, key2) => {
              this[key] = [].concat(this[key]).concat(newKeypairItem(key2, val2))
            })
          }
        })

        // add "ghost" items
        this.data = [].concat(this.data).concat(newKeypairItem())
        this.headers = [].concat(this.headers).concat(newKeypairItem())
      },
      getConnection() {
        var connection_info = _.cloneDeep(this.connection_info)

        var data = _.keyBy(this.$data.data, 'key')
        data = _.pickBy(data, (val, key) => { return key.length > 0 })
        data = _.mapValues(data, 'val')

        var headers = _.keyBy(this.$data.headers, 'key')
        headers = _.pickBy(headers, (val, key) => { return key.length > 0 })
        headers = _.mapValues(headers, 'val')

        connection_info.data = data
        connection_info.headers = headers

        return _.assign({}, this.connection, { connection_info })
      },
      onFormDataItemChange(item, index) {
        if (index == _.size(this.data) - 1)
          this.data = [].concat(this.data).concat(newKeypairItem())

        var arr = [].concat(this.data)
        arr[index] = _.assign({}, item)
        this.data = [].concat(arr)
      },
      onFormDataItemDelete(item, index) {
        var tmp = this.data
        _.pullAt(tmp, [index])
        this.data = []
        this.$nextTick(() => { this.data = [].concat(tmp) })
      },
      onHeaderItemChange(item, index) {
        if (index == _.size(this.headers) - 1)
          this.headers = [].concat(this.headers).concat(newKeypairItem())

        var arr = [].concat(this.headers)
        arr[index] = _.assign({}, item)
        this.headers = [].concat(arr)
      },
      onHeaderItemDelete(item, index) {
        var tmp = this.headers
        _.pullAt(tmp, [index])
        this.headers = []
        this.$nextTick(() => { this.headers = [].concat(tmp) })
      }
    }
  }
</script>
