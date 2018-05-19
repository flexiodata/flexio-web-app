<template>
  <div>
    <div class="tc mb3">
      <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGEAAAAcCAYAAABxutG8AAAACXBIWXMAAAWJAAAFiQFtaJ36AAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAARoSURBVHja7FpLbtswEFUMo13aN7BuYK+7sXqCKCeIcoI4QPeW9104J7B8gsoniLzpNsoJqtzAXrYo4JLFY8swMySlyAochMDAkEyKnzfz5iMFAd1GQlZCfgg5aHIn5FrIIHhvrbUz4t6lkMwxbickEbLB9VhILiTEf5GQh1M9lK/fp/IMlkKGQkq5ny+ftvtjzdczrqceAARYXA7AAowJjf9OFYAB9jPErQkACboCYVZzfKEtVG/hCbPDhLgXdglCTPQpcT82rERqx+MbpOgSlEop29FBGDF9InC/lCtcV0LSt+gkwf2RBkZ2bDoy/cGBiIZ82oGQ9+bZ+q849xgaN9E4t4AGyt89ETZT3Lz1mGdIRHetRm/CoY+wnxC/itokaxTCwh58QtQpwX3y+rOnJfiEv2qeVFsoFwJLClg41qf82MYCQEGAYBsjD/RA0NQZ03eM/cSOM/pL4eI5a5djPna7xqFEHiGw3Ni9lhhuGT+UWZLHjAAgswFQU/vnWuDiatJCMjHmDmHwExAGOJyEGTg3ZNpwzfMGDm4C0NSiF9i0CVjGzDchIp9ZSwCsGgYnUgELHYg+EqvIgp45UerBwxwFUbSTw1SH0KiQAEKOvdGopDQ0PIYi3Wo0RM2XEL6mqQUkFiottbUPGeXKFdWfNYhkUoOrfXzCD+JwlRmbucbKYpWPltLKDptThxASieit5yGzPgEOuGIOf2Zyvuh/DgaggopE9u8xD2yznRMLqGB9VLJ3xZQ9dBpZE30ULaXEfIUvAJ5KSAEQUU5X3NtAOUruWT1oY8502mEDutQFLWYmt9HCjOFSk1oqos+M2EPcolJRVhrbQlAkgKR1S0vpIV6+YDZegrd0WTdwRGZzFfgeCaUwnezewsut+wFQCxWUVOKQnT4SIFEBxKSLEDVkLOzgkAnj4M1EzRahLNsKR1+gUK4aVNR1nnCMtrBQZBf1rV2NvuQ63wII84AvNaensIHXqh01PZyKKEukDgdftEhJFUNRixrJ5zO/2wUIVJmiaJDwmW3gyccZLKUN50xFkJHMHYTj9Xm3QgY/XdBR/gJLuHQ43ZCYi8ofWnndiginYoB2RVbXDG3mrwVChMzYpuUrbG7FJIAJ4SATyI6Yb35EKpXW8M0szBkAUHWzTOYQXZWy58ziCyyuAF2MtYQrNPrF6DMiakcqKdxoIOUMJ/9LqvofPupfVSx///p54ypb4P97ht/Vm7gi+P/VSWzpG0oQunLMS2YxUeAua+v9NgFdnjbzgQ3uzQirlGvYCwDGBo3MxL1KAOFT3kgC+j3FEHP6VGoT9RlNVyGq/t62aalgA4uKiIglZWijIhLHpXZgPiUWzjdENXOEJ7SJmlLneYICos47hRKauw74cjhXluDKGgnoqrL4r8rl1wBE2CBjflbo6xsIFR4hmW86zgFxo1FFRFCUWkdmUEzC+CxbqLvFPKaGJ4J2LgT9JIZPuNUsQn1RWHA1KtDJBcrb3H4qtR+uxvRHgAEAAbWMsfVFqtEAAAAASUVORK5CYII=" alt="Flex.io">
    </div>
    <h1 class="b f3 tc ph0 mh0 mt0 mb3 black-80 w-100">Reset Password</h1>
    <div v-if="is_sent">
      <div class="mv3 lh-copy">
        <p>Your password has been successfully reset. To continue using Flex.io, you will need to sign in again.</p>
        <router-link to="/signin" class="link ph4 pv2a b lh-title white bg-blue b--blue darken-10 ttu tc db">Sign in</router-link>
      </div>
    </div>
    <div v-else>
      <div class="mv3 lh-copy">
        <p>Please enter a new password for your account.</p>
      </div>
      <div v-if="error_msg" class="mv3 ph3 pv2a lh-title fw6 br1 white bg-dark-red">
        {{error_msg}}
      </div>
      <div v-show="false">
        <input v-model="email" type="hidden">
        <input v-model="verify_code" type="hidden">
      </div>

      <el-form
        ref="form"
        class="mt3 el-form-cozy el-form__label-tiny"
        :model="$data"
        :rules="rules"
      >
        <el-form-item
          key="password"
          prop="password"
        >
          <el-input
            type="password"
            placeholder="Password"
            autocomplete="off"
            spellcheck="false"
            :autofocus="true"
            v-model="password"
          />
        </el-form-item>

        <el-form-item
          key="password2"
          prop="password2"
        >
          <el-input
            type="password"
            placeholder="Retype Password"
            autocomplete="off"
            spellcheck="false"
            v-model="password2"
          />
        </el-form-item>
      </el-form>
      <div class="mt3">
        <el-button type="primary" class="ttu b w-100" @click="tryChangePassword">
          <span v-if="is_submitting">Submitting...</span>
          <span v-else>Change password</span>
        </el-button>
      </div>
    </div>
  </div>
</template>

<script>
  import _ from 'lodash'
  import api from '../api'
  import Validation from './mixins/validation'

  export default {
    mixins: [Validation],
    data() {
      return {
        email: _.get(this.$route, 'query.email', ''),
        verify_code: _.get(this.$route, 'query.verify_code', ''),
        password: '',
        password2: '',
        is_submitting: false,
        is_sent: false,
        error_msg: '',
        rules: {
          password: [
            { required: true, message: 'Please input your current password', trigger: 'blur' }
          ],
          password2: [
            { required: true, message: 'Please input your new password', trigger: 'blur' },
            { validator: this.formValidatePassword },
            { validator: this.checkPasswordMatch, trigger: 'blur' }
          ]
        }
      }
    },
    methods: {
      getAttrs() {
        // assemble non-empty values for submitting to the backend
        var attrs = _.assign({}, this.$data)
        attrs = _.pick(attrs, ['email', 'verify_code', 'password'])
        return _.omitBy(attrs, _.isEmpty)
      },
      checkPasswordMatch(rule, value, callback) {
        if (this.password != this.password2) {
          callback(new Error('The password confirmation does not match'))
        } else {
          callback()
        }
      },
      formValidatePassword(rule, value, callback) {
        var key = rule.field
        this.validatePassword(key, value, (response, errors) => {
          var message = _.get(errors, key + '.message', '')
          if (message.length > 0) {
            callback(new Error(message))
          } else {
            callback()
          }
        })
      },
      tryChangePassword() {
        this.$refs.form.validate((valid) => {
          if (!valid)
            return

          var attrs = this.getAttrs()

          this.is_submitting = true

          api.resetPassword({ attrs }).then(response => {
            // success callback
            this.is_submitting = false
            this.is_sent = true
          }, response => {
            // error callback
            this.is_submitting = false
            this.error_msg = _.get(response, 'data.error.message', '')
          })
        })
      }
    }
  }
</script>
