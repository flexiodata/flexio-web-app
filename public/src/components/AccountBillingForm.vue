<template>
  <div>
    <div class="mb3 f7 silver ttu fw6">Your Credit Cards</div>
    <div>
      <el-alert
        type="error"
        show-icon
        :title="card_error"
        :closable="false"
        v-if="card_error.length > 0"
      />
      <div class="f6 blankslate" v-if="cards.length == 0">
        <em>No cards to show</em>
      </div>
      <div
        class="mv2 f6 br2 pv2 ph3 bg-nearer-white ba b--black-05 flex flex-row items-center hide-child"
        :key="card.card_id"
        v-for="card in cards"
      >
        <div class="flex-fill flex flex-row items-center">
          <img :src="getCardLogo(card.card_type)" class="mr3" style="width: 36px">
          <span>{{card.card_type}} ending in {{card.card_last4}}</span>
        </div>
        <div>Expires {{card.card_exp_month}}/{{card.card_exp_years}}</div>
        <div
          class="ml3 hint--top"
          aria-label="Remove this card"
        >
          <ConfirmPopover
            class="pointer child black-30 hover-black-60"
            placement="bottom-end"
            title="Confirm remove card?"
            message="You will no longer be able to use this card as a payment method. Are you sure you want to remove this card?"
            confirmButtonText="Remove card"
            :width="400"
            :offset="9"
            @confirm-click="removeCard(card)"
          />
        </div>
      </div>
    </div>
    <div class="overflow-auto" v-if="false">
      <pre class="f7 lh-title"><code class="db" style="white-space: pre-wrap" spellcheck="false">{{cards}}</code></pre>
    </div>
    <div class="h2"></div>
    <div class="mb3 f7 silver ttu fw6">Add a New Payment Method</div>
    <div class="mv3">
      <Card
        class="stripe-card"
        :stripe="stripe_public_key"
        :class="{ complete }"
        :options="stripe_opts"
        @change="complete = $event.complete"
        v-if="show_stripe_form"
      />
    </div>
    <div class="mt3">
      <el-button
        type="primary"
        class="ttu fw6 pay-with-stripe"
        @click="addCard"
        :disabled="!complete"
      >
        Add Card
      </el-button>
    </div>
  </div>
</template>

<script>
  import api from '../api'
  import { isProduction } from '../utils'
  import { Card, createToken } from 'vue-stripe-elements-plus'
  import ConfirmPopover from '@comp/ConfirmPopover'

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

  export default {
    components: {
      Card,
      ConfirmPopover
    },
    data() {
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
        cards: [],
        show_stripe_form: true,
        card_error: '',
        stripe_public_key,
        complete: false,
        stripe_opts: {
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
      this.fetchCards()
    },
    methods: {
      getCardLogo(card_name) {
        return this.card_icons[card_name] || ''
      },
      fetchCards() {
        api.v2_fetchCards().then(response => {
          this.cards = response.data
          this.card_error = ''
        }).catch(error => {
          this.cards = []
          this.card_error = JSON.stringify(error)
        })
      },
      resetCard() {
        this.show_stripe_form = false
        this.$nextTick(() => { this.show_stripe_form = true })
      },
      addCard() {
        // createToken returns a Promise which resolves in a result object with
        // either a token or an error key.
        // See https://stripe.com/docs/api#tokens for the token object.
        // See https://stripe.com/docs/api#errors for the error object.
        // More general https://stripe.com/docs/stripe.js#stripe-create-token.
        createToken().then(data => {
          var token_id = data.token.id

          api.v2_createCard('me', { token: token_id }).then(card_data => {
            this.fetchCards()
            this.resetCard()
          })
        })
      },
      removeCard(card) {
        api.v2_deleteCard('me', card.card_id).then(card_data => {
          this.fetchCards()
        })
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
