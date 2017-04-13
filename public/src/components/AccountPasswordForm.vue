<template>
  <div>
    <ui-alert @dismiss="show_success = false" type="success" :dismissible="false" v-show="show_success">
      Your password was updated successfully.
    </ui-alert>
    <ui-alert @dismiss="show_error = false" type="error" v-show="show_error">
      {{error_msg}}
    </ui-alert>
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
        :error="new_password_error"
        :invalid="new_password_error.length > 0"
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
        btn-md
        btn-primary
        class="b ttu"
        @click="trySaveChanges"
      >Save Changes</btn>
    </form>
  </div>
</template>

<script>
  import api from '../api'
  import { mapState } from 'vuex'
  import Btn from './Btn.vue'

  export default {
    components: {
      Btn
    },
    watch: {
      new_password: function(val, old_val) { this.validateForm('new_password') }
    },
    data() {
      return {
        old_password: '',
        new_password: '',
        new_password2: '',
        error_msg: '',
        ss_errors: {},
        show_success: false,
        show_error: false
      }
    },
    computed: {
      ...mapState([
        'active_user_eid'
      ]),
      new_password_error() {
        return _.get(this.ss_errors, 'new_password.message', '')
      }
    },
    methods: {
      validateForm: _.debounce(function(validate_key, callback) {
        var validate_attrs = [{
          key: 'new_password',
          value: this.new_password,
          type: 'password'
        }]

        // if a validation key is provided; only run validation on that key
        if (!_.isNil(validate_key))
        {
          validate_attrs = _.filter(validate_attrs, (attr) => {
            return attr.key == validate_key || _.has(this.ss_errors, attr.key)
          })
        }

        api.validate({ attrs: validate_attrs }).then((response) => {
          this.ss_errors = _.keyBy(response.body, 'key')

          if (_.isFunction(callback))
            callback.call(this)
        }, (response) => {
          // error callback
        })
      }, 300),
      resetForm() {
        this.old_password = ''
        this.new_password = ''
        this.new_password2 = ''
        this.error_msg = ''
        this.ss_errors = _.assign({})
      },
      trySaveChanges() {
        // this will show errors below each input
        this.validateForm(null, () => {
          if (this.new_password_error.length > 0)
            return

          var eid = this.active_user_eid
          var attrs = _.pick(this.$data, ['old_password', 'new_password', 'new_password2'])
          this.$store.dispatch('changePassword', { eid, attrs }).then(response => {
            if (response.ok)
            {
              this.show_success = true
              this.show_error = false
              this.resetForm()
              setTimeout(() => { this.show_success = false }, 3000)
            }
             else
            {
              this.show_success = false
              this.show_error = true
              this.showErrors(_.get(response, 'data.errors'))
            }
          })
        })
      },
      showErrors: function(errors) {
        if (_.isArray(errors) && errors.length > 0)
          this.error_msg = errors[0].message
      }
    }
  }
</script>
