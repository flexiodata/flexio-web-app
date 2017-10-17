<template>
  <div>
    <div class="flex flex-row items-center mb4">
      <div class="f3 fw6">
        <span v-if="isNew">New Connection</span>
        <span v-else>{{connection.name}}</span>
      </div>
      <div class="code light-silver ml2 ml3-ns" v-if="identifier.length > 0">({{identifier}})</div>
    </div>

    <div class="mv3">
      <div class="flex flex-column flex-row-ns">
        <ui-textbox
          class="flex-fill mr4-ns"
          autocomplete="off"
          placeholder="Name"
          help=" "
          required
          v-model="name"
        />
        <ui-textbox
          class="flex-fill"
          autocomplete="off"
          placeholder="Alias"
          help=" "
          v-model="ename"
        />
      </div>

      <ui-textbox
        autocomplete="off"
        placeholder="URL"
        help=" "
        v-model="url"
      />
    </div>

    <div class="f4 mt3 mt4-ns">Authorization</div>
    <div class="mv3 mw6">
      <value-select
        class="cf"
        placeholder="Authorization Type"
        :options="auth_options"
        v-model="auth"
      />
      <ui-textbox
        autocomplete="off"
        placeholder="Username"
        help=" "
        v-model="username"
        v-if="auth == 'basic'"
      />
      <ui-textbox
        type="password"
        autocomplete="off"
        placeholder="Password"
        help=" "
        v-model="password"
        v-if="auth == 'basic'"
      />
      <ui-textbox
        type="password"
        autocomplete="off"
        placeholder="Token"
        help=" "
        v-model="token"
        v-if="auth == 'bearer'"
      />
      <ui-textbox
        type="password"
        autocomplete="off"
        placeholder="Access Token"
        help=" "
        v-model="access_token"
        v-if="auth == 'oauth2'"
      />
      <ui-textbox
        type="password"
        autocomplete="off"
        placeholder="Refresh Token"
        help=" "
        v-model="refresh_token"
        v-if="auth == 'oauth2'"
      />
      <ui-textbox
        autocomplete="off"
        placeholder="Expires"
        help=" "
        v-model="expires"
        v-if="auth == 'oauth2'"
      />
    </div>

    <div class="f4 mt3 mt4-ns">Form Data</div>
    <div class="mv3">
      <keypair-item
        :item="{ key: 'Key', val: 'Value' }"
        :is-static="true"
        v-if="false"
      />
      <keypair-item
        v-for="(item, index) in form_data"
        :key="index"
        :item="item"
        :index="index"
        :count="form_data.length"
        @change="onFormDataItemChange"
        @delete="onFormDataItemDelete"
      />
    </div>

    <div class="f4 mt3 mt4-ns">Headers</div>
    <div class="mv3">
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

    <div class="flex flex-row justify-end mt3 mt4-ns pa3 bt b--black-05 bg-near-white">
      <btn btn-md class="b ttu blue mr2" :disabled="!isNew && !is_changed" @click="onCancel">Cancel</btn>
      <btn btn-md btn-primary class="ttu b" :disabled="!isNew && !is_changed" @click="onSave">
        <span v-if="isNew">Create Connection</span>
        <span v-else>Save Changes</span>
      </btn>
    </div>
  </div>
</template>

<script>
  import { CONNECTION_TYPE_HTTP } from '../constants/connection-type'
  import Btn from './Btn.vue'
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

  const auth_options = [
    { val: 'none',   label: 'No Auth'      },
    { val: 'basic',  label: 'Basic Auth'   },
    { val: 'bearer', label: 'Bearer Token' },
    { val: 'oauth2', label: 'OAuth 2.0'    }
  ]

  export default {
    props: {
      'connection': {
        type: Object,
        required: true
      },
      'is-new': {
        type: Boolean,
        default: false
      }
    },
    components: {
      Btn,
      ValueSelect,
      KeypairItem
    },
    data() {
      return {
        auth_options,
        name: 'My Connection',
        ename: '',
        description: '',
        connection_type: CONNECTION_TYPE_HTTP,
        url: '',
        auth: 'none',
        username: '',
        password: '',
        token: '',
        access_token: '',
        refresh_token: '',
        expires: '',
        headers: [],
        form_data: []
      }
    },
    computed: {
      eid() {
        return _.get(this.connection, 'eid', '')
      },
      identifier() {
        var cid = _.get(this.connection, 'ename', '')
        return cid.length > 0 ? cid : _.get(this.connection, 'eid', '')
      },
      is_changed() {
        return true
      }
    },
    mounted() {
      this.$nextTick(() => { this.reset() })
    },
    methods: {
      reset() {
        this.name = _.get(this.connection, 'name', '')
        this.ename = _.get(this.connection, 'ename', '')
        this.description = _.get(this.connection, 'description', '')

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
        this.form_data = [].concat(this.form_data).concat(newKeypairItem())
        this.headers = [].concat(this.headers).concat(newKeypairItem())
      },
      getConnection() {
        var eid = this.eid
        var connection = _.pick(this.$data, ['name', 'ename', 'description', 'connection_type'])
        var connection_info = _.pick(this.$data, ['url', 'auth', 'username', 'password', 'token', 'access_token', 'refresh_token', 'expires', 'headers', 'form_data'])

        var form_data = _.keyBy(this.$data.form_data, 'key')
        form_data = _.pickBy(form_data, (val, key) => { return key.length > 0 })
        form_data = _.mapValues(form_data, 'val')

        var headers = _.keyBy(this.$data.headers, 'key')
        headers = _.pickBy(headers, (val, key) => { return key.length > 0 })
        headers = _.mapValues(headers, 'val')

        connection_info.form_data = form_data
        connection_info.headers = headers

        return _.assign({}, connection, { eid, connection_info })
      },
      onFormDataItemChange(item, index) {
        if (index == _.size(this.form_data) - 1)
          this.form_data = [].concat(this.form_data).concat(newKeypairItem())

        var arr = [].concat(this.form_data)
        arr[index] = _.assign({}, item)
        this.form_data = [].concat(arr)
      },
      onFormDataItemDelete(item, index) {
        var tmp = this.form_data
        _.pullAt(tmp, [index])
        this.form_data = []
        this.$nextTick(() => { this.form_data = [].concat(tmp) })
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
      },
      onCancel() {
        this.reset()
        this.$emit('cancel', this.getConnection(), this.connection)
      },
      onSave() {
        this.$emit('submit', this.getConnection())
      }
    }
  }
</script>
