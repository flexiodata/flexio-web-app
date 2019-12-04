<template>
  <div>
    <el-form
      class="el-form--cozy el-form__label-tiny"
      ref="form"
      size="small"
      :model="$data"
      @submit.prevent.native
      v-if="editMode == 'all' || editMode == 'contact'"
    >
      <h4 class="mb3 pb1 silver ttu f7 fw6 bb b--black-10">Billing contact</h4>

      <el-form-item
        class="w-50-ns pr1-ns"
        style="max-width: 30rem"
        label="Name"
        key="billing_name"
        prop="billing_name"
      >
        <el-input
          type="text"
          v-model="billing_name"
        />
      </el-form-item>

      <el-form-item
        class="w-50-ns pr1-ns"
        style="max-width: 30rem"
        label="Company"
        key="billing_company"
        prop="billing_company"
      >
        <el-input
          type="text"
          v-model="billing_company"
        />
      </el-form-item>

      <el-form-item
        class="w-50-ns pr1-ns"
        style="max-width: 30rem"
        label="Email"
        key="billing_email"
        prop="billing_email"
      >
        <el-input
          type="email"
          v-model="billing_email"
        />
        <div class="nt1 f8 i light-silver">
          We will send invoices to this email
        </div>
      </el-form-item>
    </el-form>

    <el-form
      class="el-form--cozy el-form__label-tiny"
      ref="form"
      size="small"
      :model="$data"
      @submit.prevent.native
      v-if="editMode == 'all' || editMode == 'address'"
    >
      <h4 class="mt4 mb3 pb1 silver ttu f7 fw6 bb b--black-10">Billing address</h4>

      <el-form-item
        class="w-50-ns pr1-ns"
        style="max-width: 30rem"
        label="Address"
        key="billing_address1"
        prop="billing_address1"
      >
        <el-input
          type="text"
          placeholder="Street"
          v-model="billing_address1"
        />
      </el-form-item>

      <el-form-item
        class="w-50-ns pr1-ns"
        style="max-width: 30rem"
        key="billing_address2"
        prop="billing_address2"
      >
        <el-input
          type="text"
          placeholder="Apt/Suite"
          v-model="billing_address2"
        />
      </el-form-item>

      <el-form-item
        class="w-50-ns el-form-item__label-clear"
        style="max-width: 30rem"
        label="Country"
        key="billing_country"
        prop="billing_country"
      >
        <CountrySelect
          v-model="billing_country"
        />
      </el-form-item>

      <div class="flex flex-column flex-row-ns flex-wrap-ns nl1 nr1">
        <div class="w-50-ns ph1">
          <el-form-item
            label="City"
            key="billing_city"
            prop="billing_city"
          >
            <el-input
              type="text"
              v-model="billing_city"
            />
          </el-form-item>
        </div>
        <div class="w-25-ns ph1">
          <el-form-item
            label="State"
            key="billing_state"
            prop="billing_state"
          >
            <el-input
              type="text"
              v-model="billing_state"
            />
          </el-form-item>
        </div>
        <div class="w-25-ns ph1">
          <el-form-item
            label="Zip/Postal"
            key="billing_postal_code"
            prop="billing_postal_code"
          >
            <el-input
              type="text"
              v-model="billing_postal_code"
            />
          </el-form-item>
        </div>
      </div>

      <el-form-item
        label="Other Billing Information"
        key="billing_other"
        prop="billing_other"
      >
        <el-input
          type="textarea"
          rows="4"
          v-model="billing_other"
        />
        <div class="nt1 f8 i light-silver">
          If you need to add a tax ID, VAT information or anything else to your invoice, you can do so here
        </div>
      </el-form-item>
    </el-form>

    <el-form
      class="el-form--cozy el-form__label-tiny"
      ref="form"
      size="small"
      :model="$data"
      @submit.prevent.native
      v-if="editMode == 'all' || editMode == 'card'"
    >
      <h4 class="mt4 mb3 pb1 silver ttu f7 fw6 bb b--black-10">Payment method</h4>

      <el-form-item
        class="w-50-ns pr1-ns"
        style="max-width: 30rem"
        label="Cardholder Name"
        key="card_name"
        prop="card_name"
      >
        <el-input
          type="text"
          placeholder="Full name"
          v-model="card_name"
        />
      </el-form-item>

      <div
        class="el-form-item el-form-item--small w-50-ns pr1-ns"
        style="max-width: 30rem"
      >
        <label class="db f8 mb1" for="card-number">
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

      <div class="el-form-item el-form-item--small">
        <div class="flex flex-column flex-row-ns flex-wrap-ns nl1 nr1">
          <div class="w-20-ns ph1" style="min-width: 10rem">
            <label class="db f8 mb1" for="card-expiry">
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
          <div class="w-20-ns ph1" style="min-width: 10rem">
            <label class="db f8 mb1" for="card-cvc">
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
    </el-form>

    <ButtonBar
      class="mt4"
      :submit-button-text="'Save changes'"
      :submit-button-disabled="!is_submit_button_enabled"
      @cancel-click="$emit('cancel-click')"
      @submit-click="onSubmit"
    />
  </div>
</template>

<script>
  import api from '@/api'
  import { isProduction } from '@/utils'
  import { CardNumber, CardExpiry, CardCvc, createToken } from 'vue-stripe-elements-plus'
  import CountrySelect from '@/components/CountrySelect'
  import ButtonBar from '@/components/ButtonBar'

  export default {
    props: {
      editMode: {
        type: String,
        default: 'all' // 'all', 'contact', 'address' or 'card'
      },
      stripe: {
        type: String,
        required: true
      }
    },
    components: {
      CardNumber,
      CardExpiry,
      CardCvc,
      CountrySelect,
      ButtonBar
    },
    watch: {
      number() { this.update() },
      expiry() { this.update() },
      cvc() { this.update() }
    },
    data() {
      return {
        billing_name: '',
        billing_company: '',
        billing_email: '',
        billing_address1: '',
        billing_address2: '',
        billing_city: '',
        billing_state: '',
        billing_postal_code: '',
        billing_country: '',
        billing_other: '',
        card_name: '',
        card_number: '',
        card_exp: '',
        card_cvc: '',

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
              fontSize: '13px',
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
    computed: {
      is_submit_button_enabled() {
        // Stripe card form is not complete; bail out
        if (this.editMode == 'all' || this.editMode == 'card') {
          if (!this.complete) {
            return false
          }
        }

        return true
      }
    },
    methods: {
      update() {
        this.complete = this.number && this.expiry && this.cvc

        // field completed, find field to focus next
        if (this.number) {
          if (!this.expiry) {
            this.$refs.cardExpiry.focus()
          } else if (!this.cvc) {
            this.$refs.cardCvc.focus()
          }
        } else if (this.expiry) {
          if (!this.cvc) {
            this.$refs.cardCvc.focus()
          } else if (!this.number) {
            this.$refs.cardNumber.focus()
          }
        }

        // no focus magic for the CVC field as it gets complete with three
        // numbers, but can also have four
      },
      onSubmit() {
        if (this.editMode == 'all' || this.editMode == 'contact') {
          // do billing contact API call
        }

        if (this.editMode == 'all' || this.editMode == 'address') {
          // do billing address API call
        }

        if (this.editMode == 'all' || this.editMode == 'card') {
          // createToken returns a Promise which resolves in a result object with
          // either a token or an error key.
          // See https://stripe.com/docs/api#tokens for the token object.
          // See https://stripe.com/docs/api#errors for the error object.
          // More general https://stripe.com/docs/stripe.js#stripe-create-token.
          createToken().then(data => {
            var token_id = data.token.id

            api.createCard('me', { token: token_id }).then(card_data => {
              this.$emit('create-card', card_data)
            })
          })
        }
      },
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
    height: 32px
    padding: 7px 12px
    border-radius: 4px
    border: 1px solid #dcdfe6
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
