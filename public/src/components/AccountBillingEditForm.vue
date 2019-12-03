<template>
  <el-form
    size="small"
  >
    <h4 class="mb4 pb1 silver ttu f7 fw6 bb b--black-10">Please enter your card information</h4>

    <div class="nt2">
      <div class="mt3 form-item required">
        <label class="db fw6 f7 mb1" for="card-number">
          Card Number
        </label>
        <div>
          <CardNumber class="stripe-element card-number"
            ref="cardNumber"
            :stripe="stripe"
            :options="stripe_opts"
            @change="number = $event.complete"
          />
        </div>
      </div>
    </div>

    <div class="flex flex-column flex-row-ns flex-wrap nl1 nr1">
      <div class="w-30-ns ph1" style="min-width: 150px">
        <div class="mt3 form-item required">
          <label class="db fw6 f7 mb1" for="card-expiry">
            Expiration Date
          </label>
          <div>
            <CardExpiry class="stripe-element card-expiry"
              ref="cardExpiry"
              :stripe="stripe"
              :options="stripe_opts"
              @change="expiry = $event.complete"
            />
          </div>
        </div>
      </div>
      <div class="w-30-ns ph1" style="min-width: 150px">
        <div class="mt3 form-item required">
          <label class="db fw6 f7 mb1" for="card-cvc">
            CVC
          </label>
          <div>
            <CardCvc class="stripe-element card-cvc"
              ref="cardCvc"
              :stripe="stripe"
              :options="stripe_opts"
              @change="cvc = $event.complete"
            />
          </div>
        </div>
      </div>
    </div>

    <h4 class="mv4 pb1 silver ttu f7 fw6 bb b--black-10">Please enter your billing address</h4>

    <div class="nt2">
      <div class="mt3 form-item required">
        <label class="db fw6 f7 mb1" for="name">Name</label>
        <el-input
          type="text"
          name="name"
          maxlength="255"
          required
          style="max-width: 30rem"
        />
      </div>
    </div>

    <div>
      <div class="mt3 form-item">
        <label class="db fw6 f7 mb1" for="address">Address</label>
        <el-input
          type="text"
          name="address"
          maxlength="255"
        />
      </div>
    </div>

    <div class="flex flex-column flex-row-ns flex-wrap nl1 nr1">
      <div class="w-50-ns ph1">
        <div class="mt3 form-item">
          <label class="db fw6 f7 mb1" for="city">City</label>
          <el-input
              type="text"
            id="city"
            name="city"
            maxlength="80"
          />
        </div>
      </div>
      <div class="w-25-ns ph1">
        <div class="mt3 form-item">
          <label class="db fw6 f7 mb1" for="state">State</label>
          <el-input
              type="text"
            id="state"
            name="state"
            maxlength="80"
          />
        </div>
      </div>
      <div class="w-25-ns ph1">
        <div class="mt3 form-item required">
          <label class="db fw6 f7 mb1" for="postal_code">Zip</label>
          <el-input
            type="text"
            name="postal_code"
            maxlength="20"
            required
          />
        </div>
      </div>
    </div>

    <div>
      <div class="mt3 form-item">
        <label class="db fw6 f7 mb1" for="country">Country</label>
        <el-input
          type="text"
          name="country"
          maxlength="80"
          style="max-width: 30rem"
        />
      </div>
    </div>

    <div>
      <div class="mt3 form-item required">
        <label class="db fw6 f7 mb1" for="email">Email</label>
        <el-input
          type="email"
          name="email"
          maxlength="80"
          required
          style="max-width: 30rem"
        />
      </div>
    </div>

    <!-- Used to display Element errors. -->
    <div class="error mt2" role="alert">
      <span class="message"></span>
    </div>

    <!-- Used to display Element success. -->
    <div class="success">
      <h3 class="title"></h3>
      <p class="message"></p>
    </div>

    <ButtonBar
      class="mt4"
      :submit-button-text="'Save changes'"
      :submit-button-disabled="!complete"
      @cancel-click="$emit('cancel-click')"
      @submit-click="onSubmit"
    />
  </el-form>
</template>

<script>
  import api from '@/api'
  import { isProduction } from '@/utils'
  import { CardNumber, CardExpiry, CardCvc } from 'vue-stripe-elements-plus'
  import ButtonBar from '@/components/ButtonBar'

  export default {
    props: {
      stripe: {
        type: String,
        required: true
      }
    },
    components: {
      CardNumber,
      CardExpiry,
      CardCvc,
      ButtonBar
    },
    data() {
      return {
        complete: false,
        number: false,
        expiry: false,
        cvc: false,
        stripe_opts: {
          // see https://stripe.com/docs/stripe.js#element-options for details
          style: {
            base: {
              color: '#606266',
              lineHeight: '18px',
              fontFamily: '"Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif',
              fontSmoothing: 'antialiased',
              fontSize: '16px',
              '::placeholder': {
                color: '#c0c4cc'
              }
            },
            invalid: {
              color: '#f56c6c',
              iconColor: '#f56c6c'
            }
          }
        }
      }
    },
    methods: {
      onSubmit() {
        // do stuff
        this.$emit('submit-click')
      }
    }
  }
</script>

<style lang="stylus">
  @import '../stylesheets/variables.styl'

  /**
   * The CSS shown here will not be introduced in the Quickstart guide, but shows
   * how you can use CSS to style your Element's container.
   */
  .StripeElement {
    background-color: white
    height: 40px
    padding: 10px 12px
    border-radius: 4px
    border: 1px solid rgba(0,0,0,0.1)
    transition: all 150ms ease
  }

  .StripeElement--focus {
    border-color: $blue
  }

  .StripeElement--invalid {
    border-color: #fa755a
  }

  .StripeElement--webkit-autofill {
    background-color: #fefde5 !important
  }
</style>
