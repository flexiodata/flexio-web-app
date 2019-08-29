<template>
  <el-popover
    trigger="click"
    v-on="$listeners"
    v-bind="$attrs"
    v-model="is_visible"
  >
    <div class="member-role-list">
      <div
        class="flex flex-row member-role-item"
        :key="role.name"
        @click="changeRole(role.type)"
        v-require-rights:teammember.update
        v-for="role in member_roles"
      >
        <i class="material-icons md-18 b mr2" v-if="isActiveRole(role)">check</i>
        <i class="material-icons md-18 b mr2" style="color: transparent" v-else>check</i>
        <div>
          <h4 class="ma0">{{role.name}}</h4>
          <p class="tl mt1 mb0">{{role.description}}</p>
        </div>
      </div>
      <div
        class="flex flex-row member-role-item bt b--black-05"
        @click="removeMember(item)"
        v-if="!is_member_owner"
      >
        <i class="material-icons md-18 b mr2" style="color: transparent">check</i>
        <div class="pv1 red">{{is_member_active_user ? 'Leave team' : 'Remove'}}</div>
      </div>
    </div>
    <slot name="reference" slot="reference">Contributor</slot>
  </el-popover>
</template>

<script>
  import { mapState } from 'vuex'
  import { getFullName } from '@/utils'
  import member_roles from '@/data/member-roles.yml'

  export default {
    inheritAttrs: false,
    props: {
      item: {
        type: Object,
        required: true
      }
    },
    data() {
      return {
        is_visible: false,
        member_roles
      }
    },
    computed: {
      ...mapState({
        active_user_eid: state => state.users.active_user_eid,
        active_team_name: state => state.teams.active_team_name
      }),
      is_member_owner() {
        return _.get(this.item, 'role') == 'O'
      },
      is_member_active_user() {
        return _.get(this.item, 'eid') == this.active_user_eid
      }
    },
    methods: {
      isActiveRole(role) {
        return role.type == this.item.role
      },
      isLesserRole(role_type) {
        // map roles to integers
        var mapper = { 'U': 0, 'C': 1, 'A': 2, 'O': 3 }
        var item_strength = mapper[this.item.role]
        var role_strength = mapper[role_type]
        return item_strength >= role_strength
      },
      changeRole(role_type) {
        // hide the popover while we're confirming
        this.is_visible = false

        if (this.is_member_active_user && this.isLesserRole(role_type)) {
          this.$confirm('You are attempting to change your role to one with less rights. Are you sure you want to do this?', 'Accept lesser role?', {
            type: 'warning',
            confirmButtonClass: 'ttu fw6 el-button--danger',
            cancelButtonClass: 'ttu fw6',
            confirmButtonText: 'Accept lesser role',
            cancelButtonText: 'Cancel'
          }).then(() => {
            this.$emit('change-role', role_type)
            this.is_visible = false
          }).catch(() => {
            this.is_visible = true
          })
        } else if (role_type == 'A' /* new role is an administrator */) {
          var full_name = getFullName(this.item)
          var email = _.get(this.item, 'email', '')
          var member = full_name.length > 0 ? full_name : email
          var email_str = full_name.length > 0 ? ` (${email})` : ''

          var msg = `Are you sure you want to make <strong>${member}${email_str}</strong> an administrator?`
          var title = `Make ${member} an administrator?`

          this.$confirm(msg, title, {
            type: 'warning',
            confirmButtonClass: 'ttu fw6 el-button--danger',
            cancelButtonClass: 'ttu fw6',
            confirmButtonText: 'Make administrator',
            cancelButtonText: 'Cancel',
            dangerouslyUseHTMLString: true
          }).then(() => {
            this.$emit('change-role', role_type)
            this.is_visible = false
          }).catch(() => {
            this.is_visible = true
          })
        } else {
          this.$emit('change-role', role_type)
        }
      },
      removeMember(member) {
        if (this.is_member_active_user) {
          return this.leaveTeam(member)
        } else {
          var full_name = getFullName(this.item)
          var email = _.get(this.item, 'email', '')
          var member = full_name.length > 0 ? full_name : email
          var email_str = full_name.length > 0 ? ` (${email})` : ''

          var msg = `Are you sure you want to remove <strong>${member}${email_str}</strong> from this team?`
          var title = `Remove ${member}?`

          // hide the popover while we're confirming
          this.is_visible = false

          this.$confirm(msg, title, {
            type: 'warning',
            confirmButtonClass: 'ttu fw6 el-button--danger',
            cancelButtonClass: 'ttu fw6',
            confirmButtonText: 'Remove',
            cancelButtonText: 'Cancel',
            dangerouslyUseHTMLString: true
          }).then(() => {
            this.$emit('remove-member', member)
            this.is_visible = false
          }).catch(() => {
            this.is_visible = true
          })
        }
      },
      leaveTeam(member) {
        var msg = `<p>Are you sure you want to leave this team?</p><div class="h1"></div><p>Enter the team name <strong>"${this.active_team_name}"</strong> below to confirm this is what you want to do.</p>`
        var title = 'Leave team?'

        // hide the popover while we're confirming
        this.is_visible = false

        this.$prompt(msg, title, {
          confirmButtonClass: 'ttu fw6 el-button--danger',
          cancelButtonClass: 'ttu fw6',
          confirmButtonText: 'Leave',
          cancelButtonText: 'Cancel',
          dangerouslyUseHTMLString: true,
          inputValidator: (val) => {
            return val == this.active_team_name ? true : 'The team name does not match'
          },
        }).then(() => {
          this.$emit('leave-team', member)
          this.is_visible = false
        }).catch(() => {
          this.is_visible = true
        })
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .member-role-list
    margin: -12px

  .member-role-item
    cursor: pointer
    padding: 14px 12px
    &:hover
      background-color: rgba(0,0,0,0.03)
    h4
      font-weight: 600
    p
      font-size: 12px
      padding-right: 12px
</style>
