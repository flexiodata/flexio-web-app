<template>
  <div>
    <div class="f4">General</div>
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
          required
          v-model="ename"
        />
      </div>

      <ui-textbox
        autocomplete="off"
        placeholder="URL"
        help=" "
        required
        v-model="url"
      />
    </div>

    <div class="f4">Authorization</div>
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
        required
        v-model="username"
        v-if="auth == 'basic'"
      />
      <ui-textbox
        autocomplete="off"
        placeholder="Password"
        help=" "
        required
        v-model="password"
        v-if="auth == 'basic'"
      />
      <ui-textbox
        autocomplete="off"
        placeholder="Token"
        help=" "
        required
        v-model="token"
        v-if="auth == 'bearer'"
      />
      <ui-textbox
        autocomplete="off"
        placeholder="Access Token"
        help=" "
        required
        v-model="access_token"
        v-if="auth == 'oauth2'"
      />
      <ui-textbox
        autocomplete="off"
        placeholder="Refresh Token"
        help=" "
        required
        v-model="refresh_token"
        v-if="auth == 'oauth2'"
      />
      <ui-textbox
        autocomplete="off"
        placeholder="Expires"
        help=" "
        required
        v-model="expires"
        v-if="auth == 'oauth2'"
      />
    </div>

    <div class="mb3 f4">Form Data</div>
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
        @change="onItemChange"
        @delete="onItemDelete"
      />
    </div>

    <div class="mb3 f4">Headers</div>
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
        @change="onItemChange"
        @delete="onItemDelete"
      />
    </div>

    <div class="flex flex-row justify-end pa3 bt b--black-05 bg-near-white">
      <btn btn-md class="b ttu blue mr2" :disabled="!isNew && !is_changed" @click="onCancel">Cancel</btn>
      <btn btn-md btn-primary class="ttu b" :disabled="!isNew && !is_changed" @click="onSave">
        <span v-if="isNew">Create Connection</span>
        <span v-else>Save Changes</span>
      </btn>
    </div>
  </div>
</template>

<script>
  import Btn from './Btn.vue'
  import ValueSelect from './ValueSelect.vue'
  import KeypairItem from './KeypairItem.vue'

  const newItem = () => {
    return {
      key: '',
      val: ''
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
        headers: [newItem()],
        form_data: [newItem()],
        name: '',
        ename: '',
        description: '',
        url: '',
        auth: 'none',
        username: '',
        password: '',
        token: '',
        access_token: '',
        refresh_token: '',
        expires: ''
      }
    },
    computed: {
      is_changed() {
        return !_.isEqual(this.headers, this.original_headers)
      }
    },
    methods: {
      onItemChange(item, index) {
        if (index == _.size(this.headers) - 1)
          this.headers = [].concat(this.headers).concat(newItem())
      },
      onItemDelete(item, index) {
        _.pullAt(this.headers, [index])
        this.$nextTick(() => { this.headers = [].concat(this.headers) })
      },
      onCancel() {
        this.headers = []
        this.$nextTick(() => { this.headers = [].concat(this.original_headers) })

        this.$emit('cancel', this.connection)
      },
      onSave() {
        this.$emit('submit', this.connection)
      }
    }
  }
</script>
