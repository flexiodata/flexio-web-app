<template>
  <div>
    <el-alert
      type="success"
      show-icon
      title="Your regional settings were updated successfully."
      :closable="false"
      v-if="show_success"
    />
    <el-alert
      type="error"
      show-icon
      title="There was a problem updating your regional settings."
      @close="show_error = false"
      v-if="show_error"
    />
    <el-form
      class="mt3 el-form--cozy el-form__label-tiny"
      :model="$data"
      @submit.prevent.native
    >
      <el-form-item
        key="timezone"
        label="Timezone"
        prop="timezone"
      >
        <el-select
          class="w-100"
          placeholder="Search for Timezone"
          filterable
          v-model="timezone"
        >
          <el-option
            :label="option.label"
            :value="option.val"
            :key="option.val"
            v-for="option in timezone_options"
          />
        </el-select>
      </el-form-item>
    </el-form>
    <div class="mt3">
      <el-button
        type="primary"
        class="ttu fw6"
        @click="saveChanges"
      >
        Update regional settings
      </el-button>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import { TIMEZONE_UTC, timezones } from '@/constants/timezone'

  const timezone_options = _.map(timezones, (tz) => {
    return { label: tz, val: tz }
  })

  export default {
    data() {
      return {
        timezone: '',
        timezone_options,
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
      ...mapState({
        active_user_eid: state => state.users.active_user_eid,
      })
    },
    mounted() {
      this.initFromActiveUser()
    },
    methods: {
      ...mapGetters('users', {
        'getActiveUser': 'getActiveUser'
      }),
      initFromActiveUser() {
        var user = this.getActiveUser()
        this.timezone = _.get(user, 'timezone', TIMEZONE_UTC)
      },
      saveChanges() {
        var eid = this.active_user_eid
        var attrs = _.pick(this.$data, ['timezone'])
        this.$store.dispatch('users/update', { eid, attrs }).then(response => {
          this.show_success = true
          setTimeout(() => { this.show_success = false }, 4000)
        }).catch(error => {
          this.show_error = true
        })
      }
    }
  }
</script>
