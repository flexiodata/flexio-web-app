<template>
  <div>
    <el-form
      class="mb3 pb1 el-form--cozy el-form__label-tiny"
      ref="billingContactForm"
      size="small"
      :model="billing_info"
      :rules="billing_contact_form_rules"
      @validate="onValidateItem"
      @submit.prevent.native
      v-if="editMode == 'all' || editMode == 'contact'"
    >
      <h4 class="mb3 pb1 f7 ttu fw6 bb b--black-10">Billing contact</h4>

      <el-form-item
        class="w-50-l pr1-l"
        style="max-width: 30rem"
        label="Name"
        key="billing_name"
        prop="billing_name"
      >
        <el-input
          type="text"
          ref="billingNameInput"
          v-model.trim="billing_info.billing_name"
        />
      </el-form-item>

      <el-form-item
        class="w-50-l pr1-l"
        style="max-width: 30rem"
        label="Company"
        key="billing_company"
        prop="billing_company"
      >
        <el-input
          type="text"
          v-model.trim="billing_info.billing_company"
        />
      </el-form-item>

      <el-form-item
        class="w-50-l pr1-l"
        style="max-width: 30rem"
        label="Email"
        key="billing_email"
        prop="billing_email"
      >
        <el-input
          type="email"
          placeholder="accounting@example.com"
          v-model.trim="billing_info.billing_email"
        />
        <div class="f8 i light-silver lh-copy" style="margin-top: 2px">
          All future invoices will be sent to this email address
        </div>
      </el-form-item>
    </el-form>

    <el-form
      class="mb3 pb1 el-form--cozy el-form__label-tiny"
      ref="billingAddressForm"
      size="small"
      :model="billing_info"
      :rules="billing_address_form_rules"
      @validate="onValidateItem"
      @submit.prevent.native
      v-if="editMode == 'all' || editMode == 'address'"
    >
      <h4 class="mb3 pb1 f7 ttu fw6 bb b--black-10">Billing address</h4>

      <el-form-item
        class="w-50-l pr1-l"
        style="max-width: 30rem"
        label="Address"
        key="billing_address1"
        prop="billing_address1"
      >
        <el-input
          type="text"
          placeholder="Street"
          ref="billingAddress1Input"
          v-model.trim="billing_info.billing_address1"
        />
      </el-form-item>

      <el-form-item
        class="w-50-l pr1-l"
        style="max-width: 30rem"
        key="billing_address2"
        prop="billing_address2"
      >
        <el-input
          type="text"
          placeholder="Apt/Suite"
          v-model.trim="billing_info.billing_address2"
        />
      </el-form-item>

      <el-form-item
        class="w-50-l el-form-item__label-clear"
        style="max-width: 30rem"
        label="Country"
        key="billing_country"
        prop="billing_country"
      >
        <CountrySelect
          v-model.trim="billing_info.billing_country"
        />
      </el-form-item>

      <div class="flex flex-column flex-row-l flex-wrap-l nl1 nr1">
        <div class="w-50-l ph1">
          <el-form-item
            label="City"
            key="billing_city"
            prop="billing_city"
          >
            <el-input
              type="text"
              v-model.trim="billing_info.billing_city"
            />
          </el-form-item>
        </div>
        <div class="w-25-l ph1">
          <el-form-item
            label="State"
            key="billing_state"
            prop="billing_state"
          >
            <el-input
              type="text"
              v-model.trim="billing_info.billing_state"
            />
          </el-form-item>
        </div>
        <div class="w-25-l ph1">
          <el-form-item
            label="Zip/Postal"
            key="billing_postal_code"
            prop="billing_postal_code"
          >
            <el-input
              type="text"
              v-model.trim="billing_info.billing_postal_code"
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
          v-model.trim="billing_info.billing_other"
        />
        <div class="f8 i light-silver lh-copy" style="margin-top: 2px">
          If you need to add a tax ID, VAT information or anything else to your invoice, you can do so here
        </div>
      </el-form-item>
    </el-form>

    <el-form
      class="mb3 pb1 el-form--cozy el-form__label-tiny"
      ref="billingPaymentMethodForm"
      size="small"
      :model="billing_info"
      @submit.prevent.native
      v-if="editMode == 'all' || editMode == 'card'"
    >
      <h4 class="mb3 pb1 f7 ttu fw6 bb b--black-10">Payment method</h4>

      <div
        class="el-form-item el-form-item--small w-50-l pr1-l"
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

      <div class="flex flex-column flex-row-l flex-wrap-l nl1 nr1">
        <div class="w-20-l ph1" style="min-width: 10rem">
          <div class="el-form-item el-form-item--small">
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
        </div>
        <div class="w-20-l ph1" style="min-width: 10rem">
          <div class="el-form-item el-form-item--small">
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
      :cancel-button-visible="has_payment_method"
      :submit-button-text="!has_payment_method ? 'Set up billing' : 'Save changes'"
      :submit-button-disabled="has_errors"
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

  const getDefaultBillingInfo = () => {
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

      card_exp_month: '',
      card_exp_years: '',
      card_id: '',
      card_last4: '',
      card_type: '',

      customer_id: '',
    }
  }

  const getDefaultState = () => {
    return {
      billing_info: getDefaultBillingInfo(),
      billing_contact_form_rules: {
        billing_name: [
          { required: true, message: 'Please enter your name', trigger: 'blur' }
        ],
        billing_email: [
          { required: true, message: 'Please enter your email address', trigger: 'blur' },
          { type: 'email', message: 'Please enter a valid email address', trigger: 'blur' }
        ]
      },
      billing_address_form_rules: {
        billing_address1: [
          { required: true, message: 'Please enter an address', trigger: 'blur' }
        ],
        billing_city: [
          { required: true, message: 'Please enter a city', trigger: 'blur' }
        ],
        billing_state: [
          { required: true, message: 'Please enter a state', trigger: 'blur' }
        ],
        billing_postal_code: [
          { required: true, message: 'Please enter a postal code', trigger: 'blur' }
        ],
        billing_country: [
          { required: true, message: 'Please select a country' }
        ],
      },
      form_errors: {},
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
  }

  export default {
    props: {
      editMode: {
        type: String,
        default: 'all' // 'all', 'contact', 'address' or 'card'
      },
      billingInfo: {
        type: Object,
        default: () => {}
      },
      stripe: {
        type: String,
        required: true
      },
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
      cvc() { this.update() },
    },
    data() {
      return getDefaultState()
    },
    computed: {
      has_payment_method() {
        return _.get(this.billingInfo, 'card_id', '').length > 0
      },
      has_errors() {
        // Stripe card form is not complete; bail out
        if (this.editMode == 'all' || this.editMode == 'card') {
          if (!this.complete) {
            return true
          }
        }

        return _.keys(this.form_errors).length > 0
      }
    },
    mounted() {
      this.billing_info = _.assign({}, this.billing_info, this.billingInfo)
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
      onValidateItem(key, valid) {
        var errors = _.assign({}, this.form_errors)
        if (valid) {
          errors = _.omit(errors, [key])
        } else {
          errors[key] = true
        }
        this.form_errors = _.assign({}, errors)
      },
      onSubmit() {
        var payload = _.omit(this.billing_info, ['card_exp_month', 'card_exp_years', 'card_id', 'card_last4', 'card_type', 'customer_id'])

        switch (this.editMode) {
          case 'contact':
            payload = _.pick(payload, ['billing_name', 'billing_company', 'billing_email'])
            break
          case 'address':
            payload = _.pick(payload, ['billing_address1', 'billing_address2', 'billing_city', 'billing_state', 'billing_postal_code', 'billing_country', 'billing_other'])
            break
          case 'card':
            // we'll add the token (created below) to this
            payload = {}
            break
        }

        this.form_errors = {}

        if (this.$refs.billingContactForm) {
          this.$refs.billingContactForm.validate((valid, errors) => {
            this.form_errors = _.assign({}, this.form_errors, errors)
          })
        }

        if (this.$refs.billingAddressForm) {
          this.$refs.billingAddressForm.validate((valid, errors) => {
            this.form_errors = _.assign({}, this.form_errors, errors)
          })
        }

        // we've got some issues to resolve; bail out
        if (this.has_errors) {
          return
        }

        if (this.editMode == 'all' || this.editMode == 'card') {
          // createToken returns a Promise which resolves in a result object with
          // either a token or an error key.
          // See https://stripe.com/docs/api#tokens for the token object.
          // See https://stripe.com/docs/api#errors for the error object.
          // More general https://stripe.com/docs/stripe.js#stripe-create-token.
          createToken().then(data => {
            payload.token = data.token.id

            api.updateBilling('me', payload).then(response => {
              this.$emit('billing-updated', response.data)
            })
          })
        } else {
          api.updateBilling('me', payload).then(response => {
            this.$emit('billing-updated', response.data)
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
