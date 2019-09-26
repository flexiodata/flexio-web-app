<template>
  <div>
    <el-alert
      type="success"
      show-icon
      title="Your profile was updated successfully."
      :closable="false"
      v-if="show_success"
    />
    <el-alert
      type="error"
      show-icon
      title="There was a problem updating your profile."
      @close="show_error = false"
      v-if="show_error"
    />
    <div
      class="flex flex-row items-center lh-copy f6 pa3 br2"
      style="border: 1px solid #dcdfe6"
    >
      <img :src="gravatar_src" class="br-100"/>
      <div class="pl3">We use Gravatar, a service that associates an avatar image with your email address, to display your profile picture. <a href="https://en.gravatar.com" class="blue" target="_blank">Change your Gravatar</a>.</div>
    </div>
    <el-form
      ref="form"
      class="mt3 el-form--cozy el-form__label-tiny"
      style="max-width: 28rem"
      :model="edit_info"
      :rules="rules"
    >
      <el-form-item
        key="first_name"
        label="First Name"
        prop="first_name"
      >
        <el-input
          placeholder="First Name"
          auto-complete="off"
          spellcheck="false"
          :autofocus="true"
          v-model="edit_info.first_name"
        />
      </el-form-item>

      <el-form-item
        key="last_name"
        label="Last Name"
        prop="last_name"
      >
        <el-input
          placeholder="Last Name"
          auto-complete="off"
          spellcheck="false"
          v-model="edit_info.last_name"
        />
      </el-form-item>

      <el-form-item
        key="username"
        label="Username"
        prop="username"
      >
        <el-input
          placeholder="Username"
          auto-complete="off"
          spellcheck="false"
          v-model="edit_info.username"
        />
      </el-form-item>

      <el-form-item
        key="current_email"
        label="Email Address"
        prop="current_email"
      >
        <el-input
          placeholder="Email Address"
          auto-complete="off"
          :disabled="true"
          v-model="current_email"
        />
      </el-form-item>
    </el-form>
    <div class="mt3">
      <el-button
        type="primary"
        class="ttu fw6"
        @click="trySaveChanges"
      >
        Update profile
      </el-button>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import MixinValidation from '@/components/mixins/validation'

  const defaultInfo = () => {
    return {
      first_name: ' ',
      last_name: ' ',
      username: ' '
    }
  }

  export default {
    mixins: [MixinValidation],
    data() {
      return {
        orig_info: defaultInfo(),
        edit_info: defaultInfo(),
        show_success: false,
        show_error: false,
        rules: {
          first_name: [
            { required: true, message: 'Please input your first name', trigger: 'blur' }
          ],
          last_name: [
            { required: true, message: 'Please input your last name', trigger: 'blur' }
          ],
          username: [
            { required: true, message: 'Please input your username', trigger: 'blur' },
            { validator: this.formValidateUsername }
          ]
        }
      }
    },
    watch: {
      active_user_eid: function(val, old_val) {
        this.initFromActiveUser()
      }
    },
    computed: {
      ...mapState({
        active_user_eid: state => state.users.active_user_eid,
      }),
      current_email() {
        return _.get(this.getActiveUser(), 'email', ' ')
      },
      email_hash() {
        return _.get(this.getActiveUser(), 'email_hash', '')
      },
      gravatar_src() {
        return 'https://secure.gravatar.com/avatar/' + this.email_hash + '?d=mm&s=64'
      },
    },
    mounted() {
      this.initFromActiveUser()
    },
    methods: {
      ...mapGetters('users', {
        'getActiveUser': 'getActiveUser'
      }),
      initFromActiveUser() {
        var user = this.getActiveUser()
        var user_info = _.pick(user, ['first_name', 'last_name', 'username'])
        this.orig_info = _.assign({}, user_info)
        this.edit_info = _.assign({}, user_info)
      },
      formValidateUsername(rule, value, callback) {
        var current_username = _.get(this.getActiveUser(), 'username', ' ')

        if (value == current_username) {
          callback()
          return
        }

        this.$_Validation_validateUsername('username', value, (response, errors) => {
          var message = _.get(errors, 'username.message', '')
          if (message.length > 0) {
            callback(new Error(message))
          } else {
            callback()
          }
        })
      },
      trySaveChanges() {
        this.$refs.form.validate(valid => {
          if (!valid) {
            return
          }

          var user = this.getActiveUser()
          var old_username = _.get(user, 'username', ' ')
          var new_username = _.get(this.edit_info, 'username')

          if (new_username == old_username) {
            this.saveChanges()
          } else {
            this.openConfirmModal()
          }
        })
      },
      saveChanges() {
        this.$refs.form.validate(valid => {
          if (!valid)
            return

          var eid = this.active_user_eid
          var attrs = _.pick(this.edit_info, ['first_name', 'last_name', 'username'])
          this.$store.dispatch('users/update', { eid, attrs }).then(response => {
            this.show_success = true
            setTimeout(() => { this.show_success = false }, 4000)
          }).catch(error => {
            this.show_error = true
          })
        })
      },
      openConfirmModal() {
        this.$confirm('Changing your username can have unintended effects. Are you sure you want to change your username?', 'Really change your username?', {
          type: 'warning',
          confirmButtonClass: 'ttu fw6',
          cancelButtonClass: 'ttu fw6',
          confirmButtonText: 'Change my username',
          cancelButtonText: 'Cancel',
        }).then(() => {
          this.saveChanges()
        }).catch(error => {
          // do nothing
        })
      }
    }
  }
</script>
