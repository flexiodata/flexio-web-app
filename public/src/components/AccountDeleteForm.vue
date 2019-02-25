<template>
  <div>
    <el-alert
      type="error"
      show-icon
      title="Your account and all associated data cannot be recovered once it is deleted! Please make sure you really want to continue."
      :closable="false"
    />
    <p class="lh-copy">Please enter the following information to confirm you would like to delete your account.</p>
    <el-form
      ref="form"
      class="el-form--cozy el-form__label-tiny"
      :model="edit_info"
      :rules="rules"
    >
      <el-form-item
        key="username"
        label="Your username or email address"
        prop="username"
      >
        <el-input
          autocomplete="off"
          spellcheck="false"
          :autofocus="true"
          v-model="edit_info.username"
        />
      </el-form-item>

      <el-form-item
        key="password"
        label="Confirm your password"
        prop="password"
      >
        <el-input
          type="password"
          autocomplete="off"
          spellcheck="false"
          v-model="edit_info.password"
        />
      </el-form-item>

      <el-form-item
        key="confirm_text"
        prop="confirm_text"
      >
        <template slot="label">
          Verify this is what you want to do by typing <strong>delete my account</strong> below
        </template>
        <el-input
          autocomplete="off"
          spellcheck="false"
          v-model.trim="edit_info.confirm_text"
        />
      </el-form-item>
    </el-form>
    <div class="mt4">
      <el-button
        type="danger"
        class="ttu fw6 w-100"
        :disabled="!is_delete_allowed"
        @click="deleteUser"
      >
        Yes, delete this account
      </el-button>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import { ROUTE_SIGNIN_PAGE } from '../constants/route'
  import MixinValidation from './mixins/validation'

  const defaultInfo = () => {
    return {
      username: '',
      password: '',
      confirm_text: ''
    }
  }

  export default {
    mixins: [MixinValidation],
    data() {
      return {
        edit_info: defaultInfo(),
        is_delete_allowed: false,
        rules: {
          username: [
            { required: true, message: 'Please input your username or email address', trigger: 'blur' },
            { validator: this.checkUsernameMatch, trigger: 'blur' }
          ],
          password: [
            { required: true, message: 'Please input your password', trigger: 'blur' },
            { validator: this.formValidatePassword }
          ],
          confirm_text: [
            { required: true, message: 'Please input the confirmation text', trigger: 'blur' },
            { validator: this.checkConfirmationTextMatch, trigger: 'blur' }
          ]
        }
      }
    },
    watch: {
      edit_info: {
        handler: 'doFormValidate',
        deep: true
      }
    },
    computed: {
      ...mapState([
        'active_user_eid'
      ])
    },
    methods: {
      ...mapGetters([
        'getActiveUser'
      ]),
      checkUsernameMatch(rule, value, callback) {
        var val = value.toLowerCase()
        var current_email = _.get(this.getActiveUser(), 'email').toLowerCase()
        var current_username = _.get(this.getActiveUser(), 'username').toLowerCase()

        if (val != current_email && val != current_username) {
          callback(new Error('The username or email address entered is incorrect'))
        } else {
          callback()
        }
      },
      checkConfirmationTextMatch(rule, value, callback) {
        if (value.toLowerCase() != 'delete my account') {
          callback(new Error('The confirmation text does not match'))
        } else {
          callback()
        }
      },
      formValidatePassword(rule, value, callback) {
        var key = rule.field
        this.$_Validation_validatePassword(key, value, (response, errors) => {
          var message = _.get(errors, key + '.message', '')
          if (message.length > 0) {
            callback(new Error('The password entered is incorrect'))
          } else {
            callback()
          }
        })
      },
      doFormValidate: _.debounce(function() {
        this.$refs.form.validate((valid) => {
          this.is_delete_allowed = valid
        })
      }, 500),
      deleteUser() {
        this.$refs.form.validate((valid) => {
          if (!valid) {
            return
          }

          var eid = this.active_user_eid
          var attrs = _.pick(this.edit_info, ['username', 'password'])
          this.$store.dispatch('v2_action_deleteUser', { eid, attrs }).then(response => {
            this.signOut()
          }).catch(error => {
            // TODO: add error handling?
          })
        })
      },
      signOut() {
        this.$store.dispatch('v2_action_signOut').then(response => {
          this.$router.push({ name: ROUTE_SIGNIN_PAGE })
        }).catch(error => {
          // TODO: add error handling?
        })
      }
    }
  }
</script>
