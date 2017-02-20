<template>
  <ui-modal
    ref="dialog"
    remove-close-button
    @hide="onHide"
  >
    <div slot="header" class="f4">Email Flex.io Support</div>

    <div v-if="is_open">
      <div v-if="sent">
        <p class="lh-copy">Thanks for taking the time to submit a support request! We received your request and will be in touch with you shortly via email.</p>
      </div>
      <form v-else novalidate @submit.prevent="submit">
        <ui-textbox
          autocomplete="off"
          label="Subject"
          floating-label
          help=" "
          required
          v-deferred-focus
          :error="errors.first('subject')"
          :invalid="errors.has('subject')"
          v-model="email.subject"
          v-validate
          data-vv-name="subject"
          data-vv-value-path="email.subject"
          data-vv-rules="required"
        ></ui-textbox>
        <ui-textbox
          autocomplete="off"
          label="Message"
          floating-label
          help=" "
          required
          v-deferred-focus
          :error="errors.first('message')"
          :invalid="errors.has('message')"
          v-model="email.message"
          v-validate
          data-vv-name="message"
          data-vv-value-path="email.message"
          data-vv-rules="required"
        ></ui-textbox>
      </form>
    </div>

    <div slot="footer" class="flex flex-row items-end">
      <btn v-if="!sent" btn-md class="b ttu blue mr2" @click="close()">Cancel</btn>
      <btn v-if="!sent" btn-md class="b ttu blue" @click="submit()">Send Request</btn>
      <btn v-if="sent" btn-md class="b ttu blue" @click="close()">Close</btn>
    </div>
  </ui-modal>
</template>

<script>
  import Btn from './Btn.vue'

  const DEFAULT_ATTRS = {
    subject: '',
    message: ''
  }

  export default {
    components: {
      Btn
    },
    data() {
      return {
        is_open: false,
        sent: false,
        email: _.extend({}, DEFAULT_ATTRS),
      }
    },
    methods: {
      open(attrs) {
        this.reset(attrs)
        this.is_open = true
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

          this.$emit('submit', this.email, this)
        })
      },
      reset(attrs) {
        this.sent = false
        this.email = _.extend({}, DEFAULT_ATTRS, attrs)
      },
      success() {
        this.sent = true
      },
      onHide() {
        this.reset()
        this.is_open = false
      }
    }
  }
</script>
