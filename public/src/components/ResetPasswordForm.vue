<template>
  <div>
    <div class="tc" style="margin-top: -76px">
      <img src="../assets/logo-square-80x80.png" alt="Flex.io" class="br-100 ba bw1 b--white" style="width: 84px; box-shadow: 0 0 3px rgba(0,0,0,0.4)">
    </div>
    <h1 class="fw6 tc mb4">Reset Password</h1>
    <template v-if="is_sent">
      <p>Your password has been successfully reset. To continue using Flex.io, you will need to sign in again.</p>
      <div class="pv2">
        <router-link
          to="/signin"
          class='link db tc border-box no-select ttu fw6 w-100 ph4 pv2a lh-title white bg-blue br2 darken-10'
        >
          Sign in
        </router-link>
      </div>
    </template>
    <template v-else>
      <p>Please enter a new password for your account.</p>
      <div class="mb3" v-if="error_msg">
        <div class="el-alert el-alert--error is-light">
          {{error_msg}}
        </div>
      </div>
      <div v-show="false">
        <input v-model="email" type="hidden">
        <input v-model="verify_code" type="hidden">
      </div>

      <el-form
        ref="form"
        class="mt3"
        :model="$data"
        :rules="rules"
        @submit.prevent.native
      >
        <el-form-item
          key="password"
          prop="password"
        >
          <el-input
            type="password"
            placeholder="Password"
            auto-complete="off"
            spellcheck="false"
            :autofocus="true"
            v-model="password"
          />
        </el-form-item>

        <el-form-item
          key="password2"
          prop="password2"
        >
          <el-input
            type="password"
            placeholder="Retype Password"
            auto-complete="off"
            spellcheck="false"
            v-model="password2"
          />
        </el-form-item>
      </el-form>
      <div class="pb2 mt3">
        <el-button type="primary" class="ttu fw6 w-100" @click="tryChangePassword">
          <span v-if="is_submitting">Submitting...</span>
          <span v-else>Change password</span>
        </el-button>
      </div>
    </template>
  </div>
</template>

<script>
  import api from '@/api'
  import MixinValidation from '@/components/mixins/validation'

  export default {
    mixins: [MixinValidation],
    data() {
      return {
        email: _.get(this.$route, 'query.email', ''),
        verify_code: _.get(this.$route, 'query.verify_code', ''),
        password: '',
        password2: '',
        is_submitting: false,
        is_sent: false,
        error_msg: '',
        rules: {
          password: [
            { required: true, message: 'Please input your new password', trigger: 'blur' },
            { validator: this.formValidatePassword }
          ],
          password2: [
            { required: true, message: 'Please confirm your new password', trigger: 'blur' },
            { validator: this.checkPasswordMatch, trigger: 'blur' }
          ]
        }
      }
    },
    methods: {
      getAttrs() {
        // assemble non-empty values for submitting to the backend
        var attrs = _.assign({}, this.$data)
        attrs = _.pick(attrs, ['email', 'verify_code', 'password'])
        return _.omitBy(attrs, _.isEmpty)
      },
      checkPasswordMatch(rule, value, callback) {
        if (this.password != this.password2) {
          callback(new Error('The password confirmation does not match'))
        } else {
          callback()
        }
      },
      formValidatePassword(rule, value, callback) {
        var key = rule.field
        this.$_Validation_validatePassword(key, value, (response, errors) => {
          var message = _.get(errors, key + '.message', '')
          if (message.length > 0) {
            callback(new Error(message))
          } else {
            callback()
          }
        })
      },
      tryChangePassword() {
        this.$refs.form.validate(valid => {
          if (!valid)
            return

          var attrs = this.getAttrs()

          this.is_submitting = true

          api.resetPassword(attrs).then(response => {
            this.is_submitting = false
            this.is_sent = true
            this.error_msg = ''
          }).catch(error => {
            this.is_submitting = false
            this.error_msg = _.get(error, 'response.data.error.message', '')
          })
        })
      }
    }
  }
</script>
