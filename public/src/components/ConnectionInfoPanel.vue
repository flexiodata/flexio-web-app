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
            <el-form
              class="el-form-cozy"
              label-width="10rem"
              :model="$data"
              :rules="rules"
              :status-icon="true"
            >
              <el-form-item
                label="Authorization Type"
                key="auth"
                prop="auth"
              >
                <el-select
                  placeholder="Authorization Type"
                  v-model="auth"
                >
                  <el-option
                    :label="option.label"
                    :value="option.val"
                    :key="option.val"
                    v-for="option in auth_options"
                  />
                </el-select>
              </el-form-item>
              <el-form-item
                label="Username"
                key="username"
                prop="username"
                v-if="auth == 'basic'"
              >
                <el-input
                  placeholder="Username"
                  spellcheck="false"
                  v-model="username"
                />
              </el-form-item>
              <el-form-item
                label="Password"
                key="password"
                prop="password"
                v-if="auth == 'basic'"
              >
                <el-input
                  type="password"
                  placeholder="Password"
                  spellcheck="false"
                  v-model="password"
                />
              </el-form-item>
              <el-form-item
                label="Token"
                key="token"
                prop="token"
                v-if="auth == 'bearer'"
              >
                <el-input
                  type="password"
                  placeholder="Token"
                  spellcheck="false"
                  v-model="token"
                />
              </el-form-item>
              <el-form-item
                label="Access Token"
                key="access_token"
                prop="access_token"
                v-if="auth == 'oauth2'"
              >
                <el-input
                  type="password"
                  placeholder="Access Token"
                  spellcheck="false"
                  v-model="access_token"
                />
              </el-form-item>
              <el-form-item
                label="Refresh Token"
                key="refresh_token"
                prop="refresh_token"
                v-if="auth == 'oauth2'"
              >
                <el-input
                  type="password"
                  placeholder="Refresh Token"
                  spellcheck="false"
                  v-model="refresh_token"
                />
              </el-form-item>
              <el-form-item
                label="Expires"
                key="expires"
                prop="expires"
                v-if="auth == 'oauth2'"
              >
                <el-input
                  type="password"
                  placeholder="Expires"
                  spellcheck="false"
                  v-model="expires"
                />
              </el-form-item>
            </el-form>
          </div>
        </ui-tab>

        <ui-tab id="form-data" title="Form Data">
          <div class="ma3">
            <KeypairItem
              :item="{ key: 'Key', val: 'Value' }"
              :is-static="true"
              v-if="false"
            />
            <KeypairItem
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
            <KeypairItem
              :item="{ key: 'Key', val: 'Value' }"
              :is-static="true"
              v-if="false"
            />
            <KeypairItem
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
