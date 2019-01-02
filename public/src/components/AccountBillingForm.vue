<template>
  <div>
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
  import { Card, createToken } from 'vue-stripe-elements-plus'

  export default {
    components: {
      Card
    },
    data() {
      return {
        complete: false,
        stripeOptions: {
          // see https://stripe.com/docs/stripe.js#element-options for details
          style: {
            base: {
              color: '#32325d',
              lineHeight: '18px',
              fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
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
    methods: {
      pay() {

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
