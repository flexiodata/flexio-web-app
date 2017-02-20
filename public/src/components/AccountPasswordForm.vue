<template>
  <div>
    <form
      novalidate
      @submit.prevent="submit"
    >
      <ui-textbox
        type="password"
        autocomplete="off"
        label="Current Password"
        floating-label
        help=" "
        v-model="old_password"
      >
      </ui-textbox>
      <ui-textbox
        type="password"
        autocomplete="off"
        label="New Password"
        floating-label
        help=" "
        v-model="new_password"
      >
      </ui-textbox>
      <ui-textbox
        type="password"
        autocomplete="off"
        label="Confirm New Password"
        floating-label
        help=" "
        v-model="new_password2"
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
  import { mapState } from 'vuex'
  import Btn from './Btn.vue'

  export default {
    components: {
      Btn
    },
    data() {
      return {
        old_password: '',
        new_password: '',
        new_password2: ''
      }
    },
    computed: {
      ...mapState([
        'active_user_eid'
      ])
    },
    methods: {
      saveChanges() {
        var eid = this.active_user_eid
        var attrs = _.pick(this.$data, ['old_password', 'new_password', 'new_password2'])
        this.$store.dispatch('changePassword', { eid, attrs })
      }
    }
  }
</script>
