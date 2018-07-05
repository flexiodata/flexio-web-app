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
    <el-form
      ref="form"
      class="mt3 el-form--cozy el-form__label-tiny"
      :model="$data"
      :rules="rules"
    >
      <el-form-item
        key="first_name"
        label="First Name"
        prop="first_name"
      >
        <el-input
          placeholder="First Name"
          autocomplete="off"
          spellcheck="false"
          :autofocus="true"
          v-model="first_name"
        />
      </el-form-item>

      <el-form-item
        key="last_name"
        label="Last Name"
        prop="last_name"
      >
        <el-input
          placeholder="Last Name"
          autocomplete="off"
          spellcheck="false"
          v-model="last_name"
        />
      </el-form-item>

      <el-form-item
        key="username"
        label="Username"
        prop="username"
      >
        <el-input
          placeholder="Username"
          autocomplete="off"
          spellcheck="false"
          v-model="username"
        />
      </el-form-item>

      <el-form-item
        key="current_email"
        label="Email Address"
        prop="current_email"
      >
        <el-input
          placeholder="Email Address"
          autocomplete="off"
          :disabled="true"
          v-model="current_email"
        />
      </el-form-item>
    </el-form>
    <div class="mt3">
      <el-button type="primary" class="ttu b" @click="trySaveChanges">Update profile</el-button>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import Validation from './mixins/validation'

  export default {
    mixins: [Validation],
    data() {
      return {
        first_name: ' ',
        last_name: ' ',
        username: ' ',
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
      ...mapState([
        'active_user_eid'
      ]),
      current_email() {
        return _.get(this.getActiveUser(), 'email', ' ')
      }
    },
    mounted() {
      this.initFromActiveUser()
    },
    methods: {
      ...mapGetters([
        'getActiveUser'
      ]),
      initFromActiveUser() {
        var user = this.getActiveUser()
        this.first_name = _.get(user, 'first_name', ' ')
        this.last_name = _.get(user, 'last_name', ' ')
        this.username = _.get(user, 'username', ' ')
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
        this.$refs.form.validate((valid) => {
          if (!valid)
            return

          var user = this.getActiveUser()
          var old_username = _.get(user, 'username', ' ')
          var new_username = _.get(this.$data, 'username')

          if (new_username == old_username) {
            this.saveChanges()
          } else {
            this.openConfirmModal()
          }
        })
      },
      saveChanges() {
        this.$refs.form.validate((valid) => {
          if (!valid)
            return

          var eid = this.active_user_eid
          var attrs = _.pick(this.$data, ['first_name', 'last_name', 'username'])
          this.$store.dispatch('updateUser', { eid, attrs }).then(response => {
            if (response.ok)
            {
              this.show_success = true
              setTimeout(() => { this.show_success = false }, 4000)
            }
             else
            {
              this.show_error = true
            }
          })
        })
      },
      openConfirmModal() {
        this.$confirm('Changing your username can have unintended effects. Are you sure you want to change your username?', 'Really change your username?', {
          confirmButtonText: 'CHANGE MY USERNAME',
          cancelButtonText: 'CANCEL',
          type: 'warning'
        }).then(() => {
          this.saveChanges()
        }).catch(() => {
          // do nothing
        })
      }
    }
  }
</script>
