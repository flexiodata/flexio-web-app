<template>
  <div>
    <!-- header -->
    <HeaderBar
      class="mb4"
      :title="title"
      @close-click="onClose"
      v-show="showHeader"
    />

    <!-- body -->
    <el-form
      ref="form"
      class="el-form--cozy el-form__label-tiny"
      label-position="top"
      :model="$data"
      :rules="rules"
      @submit.prevent.native
    >
      <p class="f5">Enter the email addresses of the people you would like to invite to your team. New team members will get an email with a link to accept the invitation.</p>
      <el-form-item
        key="our_emails"
        prop="our_emails"
        label="Send invites to the following email addresses"
      >
        <el-select
          ref="email-invite-select"
          multiple
          filterable
          allow-create
          default-first-option
          popper-class="dn"
          class="w-100"
          spellcheck="false"
          placeholder="Enter email addresses"
          v-model="our_emails"
          v-tag-input
        >
          <el-option
            :label="item.label"
            :value="item.value"
            :key="item.value"
            v-for="item in []"
          />
        </el-select>
      </el-form-item>
    </el-form>

    <!-- footer -->
    <ButtonBar
      class="mt4"
      :submit-button-disabled="has_errors == true"
      :submit-button-text="'Send Invites'"
      @cancel-click="onCancel"
      @submit-click="onSubmit"
      v-show="showFooter"
    />
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import { isValidEmail } from '@/utils'
  import HeaderBar from '@/components/HeaderBar'
  import ButtonBar from '@/components/ButtonBar'

  const getDefaultState = (component) => {
    return {
      rules: {
        our_emails: [
          { type: 'array', validator: component.formValidateEmailArray }
        ],
      },
      has_errors: false,
      is_emitting_update: false,

      // edit values
      our_emails: []
    }
  }

  export default {
    props: {
      title: {
        type: String,
        default: 'Add Team Members'
      },
      showHeader: {
        type: Boolean,
        default: true
      },
      showFooter: {
        type: Boolean,
        default: true
      },
      emails: {
        type: Array,
        default: () => []
      }
    },
    components: {
      HeaderBar,
      ButtonBar
    },
    watch: {
      emails: {
        handler: 'initSelf',
        immediate: true,
        deep: true
      },
      our_emails: {
        handler: 'emitChange',
        deep: true
      }
    },
    data() {
      return getDefaultState(this)
    },
    computed: {
      ...mapState({
        active_team_name: state => state.teams.active_team_name,
      }),
    },
    mounted() {
      this.initSelf()
    },
    methods: {
      initSelf() {
        if (this.is_emitting_update === true ) {
          return
        }

        // reset our local component data
        _.assign(this.$data, getDefaultState(this))

        // reset local objects
        this.our_emails = [].concat(this.emails)

        // reset the form and focus the select element
        this.$nextTick(() => {
          if (this.$refs.form) {
            this.$refs.form.resetFields()
            this.$refs['email-invite-select'].focus()
          }
        })
      },
      formValidateEmailArray(rule, value, callback) {
        var has_errors = false
        _.each(value, v => {
          has_errors = has_errors || !isValidEmail(v)
        })

        if (value.length == 0) {
          this.has_errors = true
          callback(new Error('Please input at least one email address'))
        } else if (has_errors) {
          this.has_errors = true
          callback(new Error('One or more of the email addresses entered is invalid'))
        } else {
          this.has_errors = false
          callback()
        }
      },
      sendInvites() {
        var timeout = 1

        // quick hack to allow multiple users to be added until the API supports it
        _.forEach(this.our_emails, email => {
          setTimeout(() => { this.sendInvite(email) }, timeout)
          timeout += 50
        })

        this.$emit('submit', this.our_emails)
      },
      sendInvite(member) {
        var team_name = this.active_team_name
        var attrs = { member }
        this.$store.dispatch('members/create', { team_name, attrs })
      },
      emitChange() {
        if (!this.has_errors) {
          // make sure our update below doesn't trigger another call to 'initSelf'
          this.is_emitting_update = true
          this.$emit('update:emails', this.our_emails)
          this.$nextTick(() => { this.is_emitting_update = false })
        }
      },
      onClose() {
        this.initSelf()
        this.$emit('close')
      },
      onCancel() {
        this.initSelf()
        this.$emit('cancel')
      },
      onSubmit() {
        this.sendInvites()
      },
    }
  }
</script>
