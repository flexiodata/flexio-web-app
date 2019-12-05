<template>
  <div v-if="is_fetched && (days_left > 0 || current_plan_id.length == 0)">
    <div>{{msg}}</div>
    <div
      class="mt2"
      v-if="showUpgrade && current_plan_id.length == 0"
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
  import api from '@/api'
  import plans from '@/data/usage-plans.yml'
  import { isProduction } from '@/utils'
  import { mapGetters } from 'vuex'
  import { pluralize } from '@/utils'

  const TRIAL_LENGTH = 14

  export default {
    props: {
      showUpgrade: {
        type: Boolean,
        default: false
      }
    },
    data() {
      return {
        is_fetching: false,
        is_fetched: false,
        plan_info: {},
        plan_error: ''
      }
    },
    computed: {
      today() {
        return moment()
      },
      signed_up_date() {
        return moment(_.get(this.getActiveUser(), 'created'))
      },
      days_since_sign_up() {
        var duration = moment.duration(this.today.diff(this.signed_up_date))
        return duration.as('days')
      },
      days_left() {
        var diff = Math.ceil(TRIAL_LENGTH - this.days_since_sign_up)
        return Math.max(diff, 0)
      },
      msg() {
        var days_left = this.days_left
        var days = pluralize(days_left, 'days', 'day', 'days')
        return `You have ${days_left} ${days} left in your free trial`
      },
      current_plan_id() {
        return _.get(this.plan_info, 'plan_id', '')
      }
    },
    created() {
      this.fetchPlan()
    },
    methods: {
      ...mapGetters('users', {
        'getActiveUser': 'getActiveUser'
      }),
      fetchPlan() {
        this.is_fetching = true
        this.is_fetched = false

        api.fetchPlan().then(response => {
          this.plan_info = _.assign({}, response.data)
          this.plan_error = ''
          this.is_fetched = true
        }).catch(error => {
          this.plan_info = getDefaultPlanInfo()
          this.plan_error = JSON.stringify(error)
        }).finally(() => {
          this.is_fetching = false
        })
      },
    }
  }
</script>
