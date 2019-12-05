<template>
  <div>
    <!-- fetching -->
    <div v-if="is_fetching">
      <div class="pa1 flex flex-row items-center">
        <Spinner size="small" />
        <span class="ml2 f6">Loading...</span>
      </div>
    </div>
    <div v-else>
      <el-alert
        type="error"
        show-icon
        :title="billing_error"
        :closable="false"
        v-if="billing_error.length > 0"
      />
      <AccountBillingEditForm
        :edit-mode="edit_mode"
        :stripe="stripe_public_key"
        :billing-info="billing_info"
        @cancel-click="onCancelEditBilling"
        @billing-updated="onBillingUpdated"
        v-if="is_editing"
      />
      <div
        class="blankslate"
        v-else-if="billing_info.card_id.length == 0"
      >
        <em>No billing information is on file</em>
        <div class="mt3">
          <el-button
            type="primary"
            class="ttu fw6"
            @click="setupBilling"
          >
            Set up payment method
          </el-button>
        </div>
      </div>
      <div v-else>
        <div class="flex flex-column flex-row-l flex-wrap-l">
          <div class="w-third-l">
            <div class="mb3 f7 silver ttu fw6">Billing contact</div>
            <div class="f6 lh-copy">
              <div class="break-item">{{billing_info.billing_name}}</div>
              <div class="break-item">{{billing_info.billing_company}}</div>
              <div class="break-item">{{billing_info.billing_email}}</div>
            </div>
            <div class="mt2 f6">
              <el-button
                type="text"
                style="border: none; padding: 0"
                @click="editBillingContact()"
              >
                Update...
              </el-button>
            </div>
          </div>
          <div class="flex-fill-l mt4 mt0-l ml5-l">
            <div class="mb3 f7 silver ttu fw6">Billing address</div>
            <address class="f6 lh-copy">
              <div class="break-item">{{billing_info.billing_address1}}</div>
              <div class="break-item">{{billing_info.billing_address2}}</div>
              <div class="break-item">{{billing_info.billing_city}}, {{billing_info.billing_state}} {{billing_info.billing_postal_code}}</div>
              <div class="break-item">{{billing_country_name}}</div>
            </address>
            <div class="break-item mt2 pt2 bt b--black-10 f6 lh-copy pre overflow-y-visible overflow-x-auto">{{billing_info.billing_other}}</div>
            <div class="mt2 f6">
              <el-button
                type="text"
                style="border: none; padding: 0"
                @click="editBillingAddress()"
              >
                Update...
              </el-button>
            </div>
          </div>
        </div>
        <div class="mt4 mb3 f7 silver ttu fw6">Payment method</div>
        <div class="f6 br2 pv3 pv2-l ph3 bg-nearer-white ba b--black-05 dib flex-l flex-row-l items-center-l">
          <img :src="getCardLogo(billing_info.card_type)" class="center db dn-l mb3" style="width: 64px">
          <img :src="getCardLogo(billing_info.card_type)" class="dn db-l mr3" style="width: 36px">
          <div class="fw6 mt2 mt0-l mr5-l flex-fill">{{billing_info.card_type}} ending in {{billing_info.card_last4}}</div>
          <div class="fw6 mt2 mt0-l mr5-l">Expires: {{billing_info.card_exp_month}}/{{billing_info.card_exp_years}}</div>
          <div class="mt2 mt0-l blue pointer" @click="editCard">Update...</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import api from '@/api'
  import countries from '@/data/countries.yml'
  import { isProduction } from '@/utils'
  import Spinner from 'vue-simple-spinner'
  import AccountBillingEditForm from '@/components/AccountBillingEditForm'

  // all cards accepted by Stripe
  import visa from 'payment-icons/min/flat/visa.svg'
  import mastercard from 'payment-icons/min/flat/mastercard.svg'
  import amex from 'payment-icons/min/flat/amex.svg'
  import jcb from 'payment-icons/min/flat/jcb.svg'
  import discover from 'payment-icons/min/flat/discover.svg'
  import diners from 'payment-icons/min/flat/diners.svg'
  import unionpay from 'payment-icons/min/flat/unionpay.svg'

  // Stripe public keys
  const stripe_test_key = 'pk_test_TYooMQauvdEDq54NiTphI7jx' // Stripe public test key
  const flexio_test_key = 'pk_test_0b06VXFmtZ2ISyRiBgIfbi3O' // Flex.io public test key
  const flexio_prod_key = 'pk_live_0VQaMv9XVFoZcAC3VFAuBuyg' // Flex.io public production key

  // whichever key is specified here will be the key that is used
  const stripe_public_key = isProduction() ? flexio_prod_key : flexio_test_key

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
      card_icons: {
        'Visa': visa,
        'MasterCard': mastercard,
        'American Express': amex,
        'JCB': jcb,
        'Discover': discover,
        'Diners Club': diners,
        'UnionPay': unionpay
      },
      billing_info: getDefaultBillingInfo(),
      billing_error: '',
      stripe_public_key,
      is_fetching: false,
      is_editing: false,
      edit_mode: '',
    }
  }

  export default {
    components: {
      Spinner,
      AccountBillingEditForm
    },
    data() {
      return getDefaultState()
    },
    computed: {
      billing_country_name() {
        return _.get(countries, this.billing_info.billing_country, '')
      }
    },
    mounted() {
      this.fetchBilling()
    },
    methods: {
      getCardLogo(card_name) {
        return this.card_icons[card_name] || ''
      },
      fetchBilling() {
        this.is_fetching = true

        api.fetchBilling().then(response => {
          this.billing_info = _.assign({}, response.data)
          this.billing_error = ''
        }).catch(error => {
          this.billing_info = {}
          this.billing_error = JSON.stringify(error)
        }).finally(() => {
          this.is_fetching = false
        })
      },
      setupBilling() {
        this.edit_mode = 'all'
        this.is_editing = true
      },
      editBillingContact() {
        this.edit_mode = 'contact'
        this.is_editing = true
      },
      editBillingAddress() {
        this.edit_mode = 'address'
        this.is_editing = true
      },
      editCard() {
        this.edit_mode = 'card'
        this.is_editing = true
      },
      onCancelEditBilling() {
        this.is_editing = false
      },
      onBillingUpdated(info) {
        this.billing_info = _.assign({}, this.billing_info, info)
        this.is_editing = false
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

  .break-item:empty
    display: none

</style>
