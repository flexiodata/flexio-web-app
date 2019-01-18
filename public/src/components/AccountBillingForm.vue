<template>
  <div>
    <div class="overflow-auto">
      <pre class="f7 lh-title"><code class="db" style="white-space: pre-wrap" spellcheck="false">{{test}}</code></pre>
    </div>
    <div class="bb-b--black-10 mv4"></div>
    <Card
      class="stripe-card"
      stripe="pk_test_TYooMQauvdEDq54NiTphI7jx"
      :class="{ complete }"
      :options="stripeOptions"
      @change="complete = $event.complete"
    />
    <div class="pt3">
      <el-button
        type="primary"
        class="ttu fw6 pay-with-stripe"
        @click="pay"
        :disabled="!complete"
      >
        Submit Payment
      </el-button>
    </div>
  </div>
</template>

<script>
  import api from '../api'
  import { Card, createToken } from 'vue-stripe-elements-plus'

  export default {
    components: {
      Card
    },
    data() {
      return {
        test: '',
        complete: false,
        stripeOptions: {
          // see https://stripe.com/docs/stripe.js#element-options for details
          style: {
            base: {
              color: '#32325d',
              lineHeight: '18px',
              fontFamily: '"Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif',
              fontSmoothing: 'antialiased',
              fontSize: '16px',
              '::placeholder': {
                color: '#aab7c4'
              }
            },
            invalid: {
              color: '#fa755a',
              iconColor: '#fa755a'
            }
          }
        }
      }
    },
    mounted() {
      api.v2_fetchCards().then(response => {
        this.test = JSON.stringify(response.data)
      }).catch(error => {
        this.test = JSON.stringify(error)
      })
    },
    methods: {
      pay() {
        // createToken returns a Promise which resolves in a result object with
        // either a token or an error key.
        // See https://stripe.com/docs/api#tokens for the token object.
        // See https://stripe.com/docs/api#errors for the error object.
        // More general https://stripe.com/docs/stripe.js#stripe-create-token.
        createToken().then(data => console.log(data.token))
      }
    }
  }
</script>

<style lang="stylus">
  /**
   * The CSS shown here will not be introduced in the Quickstart guide, but shows
   * how you can use CSS to style your Element's container.
   */
  .StripeElement {
    background-color: white
    height: 40px
    padding: 10px 12px
    border-radius: 4px
    border: 1px solid transparent
    box-shadow: 0 1px 3px 0 #e6ebf1
    -webkit-transition: box-shadow 150ms ease
    transition: box-shadow 150ms ease
  }

  .StripeElement--focus {
    box-shadow: 0 1px 3px 0 #cfd7df
  }

  .StripeElement--invalid {
    border-color: #fa755a
  }

  .StripeElement--webkit-autofill {
    background-color: #fefde5 !important
  }
</style>
