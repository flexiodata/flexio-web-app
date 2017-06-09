<template>
  <form
    novalidate
    :class="invite_info.users_str.length > 0 ? 'pa3 mb3 ba br1 b--black-05' : ''"
    @submit.prevent="submit"
  >
    <div class="flex flex-row">
      <ui-textbox
        autocomplete="off"
        class="flex-fill"
        label="Invite people"
        help=" "
        placeholder="Email Addresses"
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
    </div>
    <ui-textbox
      autocomplete="off"
      label="Add a message"
      help=" "
      placeholder="(optional)"
      :multi-line="true"
      :rows="1"
      v-model="invite_info.message"
      v-if="invite_info.users_str.length > 0"
    ></ui-textbox>
    <div
      class="flex flex-row"
      v-if="invite_info.users_str.length > 0"
    >
      <div class="flex-fill"></div>
      <btn btn-sm class="b ttu blue mr2" @click="reset">Cancel</btn>
      <btn btn-sm btn-primary class="b ttu" @click="submit">Send Invites</btn>
    </div>
  </form>
</template>

<script>
  import * as types from '../constants/action-type'
  import Btn from './Btn.vue'

  const defaultAttrs = () => {
    return {
      users_str: '',
      message: ''
    }
  }

  export default {
    props: {
      'object-eid': {
        type: String,
        default: ''
      }
    },
    components: {
      Btn
    },
    data() {
      return {
        invite_info: _.extend({}, defaultAttrs()),
        show_message: false
      }
    },
    computed: {
      users() {
        return this.invite_info.users_str.replace(/\s/g,'').split(',')
      }
    },
    methods: {
      submit() {
        this.$validator.validateAll().then(success => {
          // handle error
          if (!success)
            return

          /*
          var attrs = {
            users: this.users,
            message: this.invite_info.message
          }
          */

          var rights = _.map(this.users, (u) => {
            return {
              'object_eid': this.objectEid,
              'access_code': u,
              'actions': [
                types.ACTION_TYPE_READ,
                types.ACTION_TYPE_EXECUTE,
                types.ACTION_TYPE_WRITE,
                types.ACTION_TYPE_DELETE
              ]
            }
          })

          this.$store.dispatch('createRights', { attrs: { rights } }).then(response => {
            this.reset()
          })
        })
      },
      reset(attrs) {
        this.invite_info = _.extend({}, defaultAttrs(), attrs)
      }
    }
  }
</script>
