<template>
  <div>
    <h3 class="mt0 fw6 f3">Plan</h3>
    <AccountPlanForm
      ref="planForm"
      :plan-info.sync="plan_info"
    />
    <div v-show="has_plan">
      <div class="h3"></div>
      <h3 class="mt0 fw6 f3">Payment Information</h3>
      <AccountBillingForm
        @payment-method-set-up="onPaymentMethodSetUp"
      />
    </div>
  </div>
</template>

<script>
  import AccountPlanForm from '@/components/AccountPlanForm'
  import AccountBillingForm from '@/components/AccountBillingForm'

  export default {
    components: {
      AccountPlanForm,
      AccountBillingForm
    },
    data() {
      return {
        plan_info: {},
        billing_info: {}
      }
    },
    computed: {
      has_plan() {
        return _.get(this.plan_info, 'plan_id', '').length > 0
      }
    },
    methods: {
      onPaymentMethodSetUp(info) {
        debugger

        this.billing_info = _.assign({}, this.billing_info, info)

        // use imperitive Javascript for now
        this.$refs.planForm.updatePlan()
      }
    }
  }
</script>
