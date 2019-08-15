<template>
  <tr>
    <td class="w-100">
      <div class="flex flex-row items-center">
        <img :src="gravatar_url" class="br-100 mr3"/>
        <div class="truncate" v-if="has_full_name">
          <div class="fw6 body-color">{{full_name}}</div>
          <div class="mt1 black-50">{{item.email}}</div>
        </div>
        <div v-else>
          <div class="fw6 body-color">{{item.email}}</div>
        </div>
      </div>
    </td>
    <td class="tl nowrap" style="min-width: 9rem">
      <div
        v-if="is_member_pending"
        v-require-rights:teammember.update
      >
        <div
          v-if="is_invite_resending"
        >
          Sending...
        </div>
        <div
          class="flex flex-row items-center"
          v-else-if="is_invite_resent"
        >
          <i class="el-icon-success dark-green mr1"></i>
          <span class="dark-green">Invite sent!</span>
        </div>
        <el-button
          type="text"
          style="border: 0; padding: 0"
          @click="resendInvite"
          v-else
        >
          Resend invite
        </el-button>
      </div>
    </td>
    <td class="tl nowrap" style="min-width: 9rem">
      <div class="fw6">
        <span
          v-require-rights:teammember.update
          v-if="is_member_owner"
        >
          Owner
        </span>
        <MemberItemRoleDropdown
          width="310"
          :item="item"
          @change-role="updateRole"
          @remove-member="removeMember"
          v-require-rights:teammember.update="!is_member_active_user"
          v-else
        >
          <el-button
            slot="reference"
            class="fw6 role-button"
            type="text"
          >
            <span>{{role_title}}</span>
            <i class="dropdown-caret" v-require-rights:teammember.update.hidden="!is_member_active_user"></i>
          </el-button>
        </MemberItemRoleDropdown>
      </div>
    </td>
  </tr>
</template>

<script>
  import { mapState } from 'vuex'
  import { getFullName } from '@/utils'
  import { OBJECT_STATUS_PENDING } from '@/constants/object-status'
  import member_roles from '@/data/member-roles.yml'
  import MemberItemRoleDropdown from '@/components/MemberItemRoleDropdown'

  export default {
    props: {
      item: {
        type: Object,
        required: true
      }
    },
    components: {
      MemberItemRoleDropdown
    },
    computed: {
      ...mapState({
        active_user_eid: state => state.users.active_user_eid,
        active_team_name: state => state.teams.active_team_name
      }),
      gravatar_url() {
        return 'https://secure.gravatar.com/avatar/' + _.get(this.item, 'email_hash') + '?d=mm&s=40'
      },
      full_name() {
        return getFullName(this.item)
      },
      has_full_name() {
        return this.full_name.trim().length > 0
      },
      role_title() {
        var role = _.find(member_roles, r => r.type == _.get(this.item, 'role'))
        return role ? role.name : 'Unknown'
      },
      is_member_owner() {
        return _.get(this.item, 'role') == 'O'
      },
      is_member_active_user() {
        return _.get(this.item, 'eid') == this.active_user_eid
      },
      is_member_pending() {
        return _.get(this.item, 'member_status') == OBJECT_STATUS_PENDING
      },
      is_invite_resending() {
        return _.get(this.item, 'vuex_meta.is_invite_resending')
      },
      is_invite_resent() {
        return _.get(this.item, 'vuex_meta.is_invite_resent')
      },
    },
    methods: {
      resendInvite() {
        var team_name = this.active_team_name
        var eid = _.get(this.item, 'eid')
        this.$store.dispatch('members/resendInvite', { team_name, eid })
      },
      removeMember() {
        var team_name = this.active_team_name
        var eid = _.get(this.item, 'eid')
        this.$store.dispatch('members/delete', { team_name, eid })
      },
      updateRole(role) {
        var team_name = this.active_team_name
        var eid = _.get(this.item, 'eid')
        var attrs = { role }
        this.$store.dispatch('members/update', { team_name, eid, attrs })
      }
    }
  }
</script>

<style lang="stylus" scoped>
  td
    padding-left: 1rem
    padding-right: 1rem
  td:first-child
    padding-left: 0.5rem
    padding-right: 0.5rem
  td:last-child
    padding-left: 0.5rem
    padding-right: 0.5rem

  .role-button
    padding: 0
    border: 0
    color: #606266 // match Element UI's body color
    &:hover
      color: #303133

  .dropdown-caret
    display: inline-block
    margin-left: 2px
    width: 0
    height: 0
    border: 4px solid transparent
    border-top-color: inherit
</style>
