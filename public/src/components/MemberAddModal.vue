<template>
  <ui-modal
    ref="dialog"
    remove-close-button
    @hide="onHide"
  >
    <div slot="header" class="f4">Invite members</div>

    <form novalidate @submit.prevent="submit">
      <ui-textbox
        autocomplete="off"
        label="Email Addresses"
        floating-label
        help=" "
        required
        v-deferred-focus
        :error="errors.first('users_str')"
        :invalid="errors.has('users_str')"
        v-model="invite_info.users_str"
        v-validate
        data-vv-as="email addresses"
        data-vv-name="users_str"
        data-vv-value-path="invite_info.users_str"
        data-vv-rules="required"
      ></ui-textbox>
      <ui-textbox
        autocomplete="off"
        label="Message (optional)"
        floating-label
        help=" "
        v-model="invite_info.message"
      ></ui-textbox>
    </form>

    <div slot="footer" class="flex flex-row items-end">
      <btn btn-md class="b ttu blue mr2" @click="close()">Cancel</btn>
      <btn btn-md class="b ttu blue" @click="submit()">Send Invites</btn>
    </div>
  </ui-modal>
</template>

<script>
  import Btn from './Btn.vue'

  const defaultAttrs = () => {
    return {
      users_str: '',
      message: ''
    }
  }

  export default {
    components: {
      Btn
    },
    data() {
      return {
        invite_info: _.extend({}, defaultAttrs())
      }
    },
    computed: {
      users() {
        return this.invite_info.users_str.replace(/\s/g,'').split(',')
      }
    },
    methods: {
      open(attrs) {
        this.reset(attrs)
        this.$refs['dialog'].open()
      },
      close() {
        this.$refs['dialog'].close()
      },
      submit() {
        this.$validator.validateAll().then(success => {
          // handle error
          if (!success)
            return

          var attrs = {
            users: this.users,
            message: this.invite_info.message
          }

          this.$emit('submit', attrs, this)
        })
      },
      reset(attrs) {
        this.invite_info = _.extend({}, defaultAttrs(), attrs)
      },
      onHide() {
        this.$emit('hide', this)
      }
    }
  }
</script>
