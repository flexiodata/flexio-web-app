<template>
  <div>
    <el-alert
      type="success"
      show-icon
      title="Your password was updated successfully."
      :closable="false"
      v-if="show_success"
    />
    <el-alert
      type="error"
      show-icon
      :title="error_msg"
      @close="show_error = false"
      v-if="show_error"
    />
    <el-form
      ref="form"
      class="mt3 el-form--cozy el-form__label-tiny"
      :model="$data"
      :rules="rules"
    >
      <el-form-item
        key="old_password"
        label="Current Password"
        prop="old_password"
      >
        <el-input
          type="password"
          placeholder="Current Password"
          autocomplete="off"
          spellcheck="false"
          :autofocus="true"
          v-model="old_password"
        />
      </el-form-item>

      <el-form-item
        key="new_password"
        label="New Password"
        prop="new_password"
      >
        <el-input
          type="password"
          placeholder="New Password"
          autocomplete="off"
          spellcheck="false"
          v-model="new_password"
        />
      </el-form-item>

      <el-form-item
        key="new_password2"
        label="Confirm New Password"
        prop="new_password2"
      >
        <el-input
          type="password"
          placeholder="Confirm New Password"
          autocomplete="off"
          spellcheck="false"
          v-model="new_password2"
        />
      </el-form-item>
    </el-form>
    <div class="mt3">
      <el-button
        type="primary"
        class="ttu b"
        @click="saveChanges"
      >
        Update password
      </el-button>
      <router-link class="ml2 blue f6 no-underline underline-hover" to="/forgotpassword">
        I forgot my password
      </router-link>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import Validation from './mixins/validation'

  export default {
    mixins: [Validation],
    data() {
      return {
        old_password: '',
        new_password: '',
        new_password2: '',
        error_msg: '',
        show_success: false,
        show_error: false,
        rules: {
          old_password: [
            { required: true, message: 'Please input your current password', trigger: 'blur' }
          ],
          new_password: [
            { required: true, message: 'Please input your new password', trigger: 'blur' },
            { validator: this.formValidatePassword }
          ],
          new_password2: [
            { required: true, message: 'Please confirm your new password', trigger: 'blur' },
            { validator: this.checkPasswordMatch, trigger: 'blur' }
          ]
        }
      }
    },
    computed: {
      ...mapState([
        'active_user_eid'
      ])
    },
    methods: {
      checkPasswordMatch(rule, value, callback) {
        if (this.new_password != this.new_password2) {
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
      resetForm() {
        this.old_password = ''
        this.new_password = ''
        this.new_password2 = ''

        if (this.$refs.form) {
          this.$refs.form.resetFields()
        }
      },
      saveChanges() {
        this.$refs.form.validate((valid) => {
          if (!valid)
            return

          var eid = this.active_user_eid
          var attrs = _.pick(this.$data, ['old_password', 'new_password', 'new_password2'])
          this.$store.dispatch('changePassword', { eid, attrs }).then(response => {
            if (response.ok) {
              this.show_success = true
              this.show_error = false
              this.resetForm()
              setTimeout(() => { this.show_success = false }, 4000)
            } else {
              this.show_success = false
              this.show_error = true
              this.error_msg = _.get(response, 'data.error.message', '')
            }
          })
        })
      }
    }
  }
</script>
