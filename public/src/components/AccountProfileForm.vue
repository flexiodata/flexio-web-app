<template>
  <div>
    <form
      novalidate
      @submit.prevent="submit"
    >
      <ui-textbox
        autocomplete="off"
        label="First Name"
        floating-label
        help=" "
        v-model="first_name"
      >
      </ui-textbox>
      <ui-textbox
        autocomplete="off"
        label="Last Name"
        floating-label
        help=" "
        v-model="last_name"
      >
      </ui-textbox>
      <ui-textbox
        autocomplete="off"
        disabled
        label="Username"
        floating-label
        help=" "
        v-model="username"
      >
      </ui-textbox>
      <ui-textbox
        type="email"
        autocomplete="off"
        disabled
        label="Email Address"
        floating-label
        help=" "
        v-model="email"
      >
      </ui-textbox>
      <btn
        btn-lg
        btn-primary
        class="b ttu"
        @click="saveChanges"
      >Save Changes</btn>
    </form>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import Btn from './Btn.vue'

  export default {
    components: {
      Btn
    },
    data() {
      return {
        first_name: ' ',
        last_name: ' '
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
      username() {
        return _.get(this.active_user, 'user_name', ' ')
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
      },
      saveChanges() {
        var eid = this.active_user_eid
        var attrs = _.pick(this.$data, ['first_name', 'last_name'])
        this.$store.dispatch('updateUser', { eid, attrs })
      }
    }
  }
</script>
