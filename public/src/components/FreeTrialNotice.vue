<template>
  <div v-if="days_left >= 0 && subscription_id.length == 0">
    <div v-if="days_left == 0">Your free trial period has ended.</div>
    <div v-else>{{msg}}</div>
    <div
      class="mt2"
      v-if="showUpgrade"
    >
      <router-link
        class="el-button el-button--primary el-button--tiny no-underline fw6"
        size="tiny"
        type="primary"
        to="/account/billing"
      >
        Upgrade now
      </router-link>
    </div>
    <slot></slot>
  </div>
</template>

<script>
  import moment from 'moment'
  import { mapGetters } from 'vuex'
  import { pluralize } from '@/utils'

  export default {
    props: {
      showUpgrade: {
        type: Boolean,
        default: false
      }
    },
    computed: {
      msg() {
        var days_left = this.days_left
        var days = pluralize(days_left, 'days', 'day', 'days')
        return `You have ${days_left} ${days} left in your free trial.`
      },
      days_left() {
        return this.getActiveUserTrialDaysLeft()
      },
      subscription_id() {
        return this.getActiveUserStripeSubscriptionId()
      }
    },
    methods: {
      ...mapGetters('users', {
        'getActiveUserStripeSubscriptionId': 'getActiveUserStripeSubscriptionId',
        'getActiveUserTrialDaysLeft': 'getActiveUserTrialDaysLeft'
      })
    }
  }
</script>
