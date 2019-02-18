<template>
  <div class="f7 dark-green" v-if="days_left > 0">{{msg}}</div>
</template>

<script>
  import moment from 'moment'
  import { mapGetters } from 'vuex'
  import { pluralize } from '../utils'

  const TRIAL_LENGTH = 14

  export default {
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
        var days = pluralize(this.days_left, 'days', 'day', 'days')
        return 'You have ' + this.days_left + ' ' + days + ' left in your free trial'
      }
    },
    methods: {
      ...mapGetters([
        'getActiveUser'
      ])
    }
  }
</script>
