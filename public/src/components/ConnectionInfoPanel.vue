<template>
  <div v-if="!force_render">
    <el-form
      ref="form"
      class="el-form--compact el-form__label-tiny"
      :model="form_values"
      :rules="rules"
      @validate="onValidateItem"
      @submit.prevent.native
    >
      <el-form-item
        key="url"
        label="Method &amp; URL"
        prop="url"
      >
        <el-input
          auto-complete="off"
          spellcheck="false"
          placeholder="URL"
          v-model="form_values.url"
        >
          <el-select
            style="width: 120px"
            placeholder="Method"
            slot="prepend"
            v-model="form_values.method"
          >
            <el-option
              :label="option.label"
              :value="option.val"
              :key="option.val"
              v-for="option in method_options"
            />
          </el-select>
        </el-input>
      </el-form-item>
    </el-form>

    <div class="mt4 ba b--black-10 br2">
      <el-tabs class="bg-white br2 ph3 pt1 pb3" v-model="active_tab_name">
        <el-tab-pane name="authorization">
          <div slot="label" class="tc" style="min-width: 3rem">Authorization</div>
          <div class="mv2 mh3 mw6">
            <el-form
              class="el-form--compact el-form__label-tiny"
              label-position="top"
              label-width="10rem"
              :model="form_values"
            >
              <el-form-item
                label="Authorization Type"
                key="auth"
                prop="auth"
              >
                <el-select
                  placeholder="Authorization Type"
                  v-model="form_values.auth"
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
                v-if="form_values.auth == 'basic'"
              >
                <el-input
                  placeholder="Username"
                  spellcheck="false"
                  v-model="form_values.username"
                />
              </el-form-item>
              <el-form-item
                label="Password"
                key="password"
                prop="password"
                v-if="form_values.auth == 'basic'"
              >
                <el-input
                  type="password"
                  placeholder="Password"
                  spellcheck="false"
                  v-model="form_values.password"
                />
              </el-form-item>
              <el-form-item
                label="Token"
                key="token"
                prop="token"
                v-if="form_values.auth == 'bearer'"
              >
                <el-input
                  type="password"
                  placeholder="Token"
                  spellcheck="false"
                  v-model="form_values.token"
                />
              </el-form-item>
              <el-form-item
                label="Access Token"
                key="access_token"
                prop="access_token"
                v-if="form_values.auth == 'oauth2'"
              >
                <el-input
                  type="password"
                  placeholder="Access Token"
                  spellcheck="false"
                  v-model="form_values.access_token"
                />
              </el-form-item>
              <el-form-item
                label="Refresh Token"
                key="refresh_token"
                prop="refresh_token"
                v-if="form_values.auth == 'oauth2'"
              >
                <el-input
                  type="password"
                  placeholder="Refresh Token"
                  spellcheck="false"
                  v-model="form_values.refresh_token"
                />
              </el-form-item>
              <el-form-item
                label="Expires"
                key="expires"
                prop="expires"
                v-if="form_values.auth == 'oauth2'"
              >
                <el-input
                  type="password"
                  placeholder="Expires"
                  spellcheck="false"
                  v-model="form_values.expires"
                />
              </el-form-item>
            </el-form>
          </div>
        </el-tab-pane>

        <el-tab-pane name="form-data">
          <div slot="label" class="tc" style="min-width: 3rem">Form Data</div>
          <div class="mv2 mh3">
            <KeypairList
              ref="data-list"
              :header="{ key: 'Key', val: 'Value' }"
              v-model="form_values.data"
            />
          </div>
        </el-tab-pane>

        <el-tab-pane name="headers">
          <div slot="label" class="tc" style="min-width: 3rem">Headers</div>
          <div class="mv2 mh3">
            <KeypairList
              ref="headers-list"
              :header="{ key: 'Key', val: 'Value' }"
              v-model="form_values.headers"
            />
          </div>
        </el-tab-pane>
      </el-tabs>
    </div>
  </div>
</template>

<script>
  import ServiceIcon from '@/components/ServiceIcon'
  import KeypairList from '@/components/KeypairList'

  const newKeypairItem = (key, val) => {
    key = _.defaultTo(key, '')
    val = _.defaultTo(val, '')

    return {
      key,
      val
    }
  }

  const getDefaultInfo = () => {
    return {
      method: 'GET',
      url: '',
      auth: 'none',
      username: '',
      password: '',
      token: '',
      access_token: '',
      refresh_token: '',
      expires: '',
      headers: {},
      data: {}
    }
  }

  const method_options = [
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
      connectionInfo: {
        type: Object,
        required: true
      },
      formErrors: {
        type: Object
      }
    },
    components: {
      ServiceIcon,
      KeypairList
    },
    watch: {
      connectionInfo: {
        handler: 'initSelf',
        immediate: true,
        deep: true
      },
      form_values: {
        handler: 'emitUpdate',
        deep: true
      },
      form_errors(val) {
        this.$emit('update:formErrors', val)
      }
    },
    data() {
      return {
        active_tab_name: 'authorization',
        force_render: false,
        is_emitting_update: false,
        method_options,
        auth_options,
        form_values: getDefaultInfo(),
        form_errors: {},
        rules: {
          url: [
            { required: true, message: 'Please input a URL' },
            { type: 'url', message: 'Please input a valid URL', trigger: 'blur' }
          ]
        }
      }
    },
    methods: {
      initSelf() {
        if (this.is_emitting_update) {
          return
        }

        this.form_values = getDefaultInfo()

        // fill out the info from the connection info
        _.each(this.connectionInfo, (val, key) => {
          if (_.isString(val)) {
            this.form_values[key] = val
          }

          if (_.isPlainObject(val)) {
            _.each(val, (val2, key2) => {
              this.form_values[key][key2] = val2
            })
          }
        })

        this.forceRender()
      },
      forceRender() {
        this.force_render = true
        this.$nextTick(() => { this.force_render = false })
      },
      emitUpdate() {
        var connection_info = _.cloneDeep(this.form_values)

        // make sure our update below doesn't trigger another call to 'initSelf'
        this.is_emitting_update = true
        this.$emit('update:connectionInfo', connection_info)
        this.$nextTick(() => { this.is_emitting_update = false })
      },
      validate(callback) {
        if (this.$refs.form) {
          this.$refs.form.validate(callback)
        } else {
          callback(true)
        }
      },
      onValidateItem(key, valid) {
        var errors = _.assign({}, this.form_errors)
        if (valid) {
          errors = _.omit(errors, [key])
        } else {
          errors[key] = true
        }
        this.form_errors = _.assign({}, errors)
      },
    }
  }
</script>
