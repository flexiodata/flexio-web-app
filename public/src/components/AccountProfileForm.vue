<template>
  <div>
    <ui-alert @dismiss="show_success = false" type="success" :dismissible="false" v-show="show_success">
      Your profile was updated successfully.
    </ui-alert>
    <ui-alert @dismiss="show_error = false" type="error" v-show="show_error">
      There was a problem updating your profile.
    </ui-alert>
    <form novalidate @submit.prevent="submit">
      <ui-textbox
        autocomplete="off"
        label="First Name"
        floating-label
        help=" "
        v-model="first_name"
      />
      <ui-textbox
        autocomplete="off"
        label="Last Name"
        floating-label
        help=" "
        v-model="last_name"
      />
      <ui-textbox
        autocomplete="off"
        label="Username"
        floating-label
        help=" "
        v-model="username"
      />
      <ui-textbox
        type="email"
        autocomplete="off"
        disabled
        label="Email Address"
        floating-label
        help=" "
        v-model="email"
      />
      <div class="mt3">
        <el-button type="primary" class="ttu b" @click="trySaveChanges">Save Changes</el-button>
      </div>
    </form>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'

  export default {
    data() {
      return {
        first_name: ' ',
        last_name: ' ',
        username: ' ',
        show_success: false,
        show_error: false
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
      active_user() {
        var user = this.getActiveUser()
        return user ? user : {}
      },
      email() {
        return _.get(this.active_user, 'email', ' ')
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
        this.first_name = _.get(this.active_user, 'first_name', ' ')
        this.last_name = _.get(this.active_user, 'last_name', ' ')
        this.username = _.get(this.active_user, 'username', ' ')
      },
      trySaveChanges() {
        var old_username = _.get(this.active_user, 'username', ' ')
        var new_username = _.get(this.$data, 'username')

        if (new_username == old_username)
          this.saveChanges()
           else
          this.openConfirmModal()
      },
      saveChanges() {
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
