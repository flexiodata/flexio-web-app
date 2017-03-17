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
        label="Username"
        floating-label
        help=" "
        v-model="user_name"
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
        @click="trySaveChanges"
      >Save Changes</btn>
    </form>

    <!-- username confirm modal -->
    <confirm-modal
      ref="modal-confirm"
      title="Really change your username?"
      submit-label="Change my username"
      cancel-label="Cancel"
      @confirm="onConfirmModalClose"
      @hide="show_confirm_modal = false"
      v-if="show_confirm_modal"
    >
      <div class="lh-copy">Changing your username can have unintended effects. Are you sure you want to change your username?</div>
    </confirm-modal>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import Btn from './Btn.vue'
  import ConfirmModal from './ConfirmModal.vue'

  export default {
    components: {
      Btn,
      ConfirmModal
    },
    data() {
      return {
        first_name: ' ',
        last_name: ' ',
        user_name: ' ',
        show_confirm_modal: false
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
        this.user_name = _.get(this.active_user, 'user_name', ' ')
      },
      trySaveChanges() {
        var old_username = _.get(this.active_user, 'user_name', ' ')
        var new_username = _.get(this.$data, 'user_name')

        if (new_username == old_username)
          this.saveChanges()
           else
          this.openConfirmModal()
      },
      saveChanges() {
        var eid = this.active_user_eid
        var attrs = _.pick(this.$data, ['first_name', 'last_name', 'user_name'])
        this.$store.dispatch('updateUser', { eid, attrs })
      },
      openConfirmModal() {
        this.show_confirm_modal = true
        this.$nextTick(() => { this.$refs['modal-confirm'].open() })
      },
      onConfirmModalClose(modal) {
        this.saveChanges()
        modal.close()
      }
    }
  }
</script>
