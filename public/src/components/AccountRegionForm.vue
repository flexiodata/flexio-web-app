<template>
  <div>
    <ui-alert @dismiss="show_success = false" type="success" :dismissible="false" v-show="show_success">
      Your regional settings were updated successfully.
    </ui-alert>
    <ui-alert @dismiss="show_error = false" type="error" v-show="show_error">
      There was a problem updating your regional settings.
    </ui-alert>
    <form novalidate @submit.prevent="submit">
      <ui-select
        label="Timezone"
        has-search
        v-model="timezone"
        :options="timezones"
      />
      <div class="mt3">
        <el-button type="primary" class="ttu b" @click="saveChanges">Save Changes</el-button>
      </div>
    </form>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import { TIMEZONE_UTC, timezones } from '../constants/timezone'

  export default {
    data() {
      return {
        timezone: '',
        timezones: timezones,
        show_success: false,
        show_error: false
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
        this.$store.dispatch('updateUser', { eid, attrs }).then(response => {
          if (response.ok)
          {
            this.show_success = true
            setTimeout(() => { this.show_success = false }, 4000)
          }
           else
          {
            this.show_error = true
          }
        })
      }
    }
  }
</script>
