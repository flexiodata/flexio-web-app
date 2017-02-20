<template>
  <div>
    <form novalidate @submit.prevent="submit">
      <ui-select
        label="Timezone"
        has-search
        v-model="timezone"
        :options="timezones"
      >
      </ui-select>
      <btn
        btn-lg
        btn-primary
        class="b ttu"
        @click="saveChanges"
      >Save Changes</btn>
    </form>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import { TIMEZONE_UTC, timezones } from '../constants/timezone'
  import Btn from './Btn.vue'

  export default {
    components: {
      Btn
    },
    data() {
      return {
        timezone: '',
        timezones: timezones
      }
    },
    watch: {
      active_user_eid: function(val, old_val) {
        this.initFromActiveUser()
      }
    },
    computed: {
      ...mapState([
        'active_user_eid'
      ]),
      active_user() {
        var user = this.getActiveUser()
        return user ? user : {}
      }
    },
    mounted() {
      this.initFromActiveUser()
    },
    methods: {
      ...mapGetters([
        'getActiveUser'
      ]),
      initFromActiveUser() {
        this.timezone = _.get(this.active_user, 'timezone', TIMEZONE_UTC)
      },
      saveChanges() {
        var eid = this.active_user_eid
        var attrs = _.pick(this.$data, ['timezone'])
        this.$store.dispatch('updateUser', { eid, attrs })
      }
    }
  }
</script>
