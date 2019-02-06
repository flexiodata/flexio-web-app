<template>
  <div class="f7 dark-green" v-if="days_left > 0">You have {{days_left}} days left in your free trial</div>
</template>

<script>
  import moment from 'moment'
  import { mapGetters } from 'vuex'

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
      }
    },
    methods: {
      ...mapGetters([
        'getActiveUser'
      ])
    }
  }
</script>
