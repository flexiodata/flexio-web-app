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
        v-for="role in member_roles"
      >
        <i class="material-icons md-18 b mr2" v-if="isActiveRole(role)">check</i>
        <i class="material-icons md-18 b mr2" style="color: transparent" v-else>check</i>
        <div>
          <h4 class="ma0">{{role.name}}</h4>
          <p class="tl mt2 mb0">{{role.description}}</p>
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
  import member_roles from '@/data/member-roles.yml'
  import { getFullName } from '@/utils'

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
      }),
      is_member_owner() {
        return _.get(this.item, 'eid') == _.get(this.item, 'member_of.eid')
      },
      is_member_active_user() {
        return _.get(this.item, 'eid') == this.active_user_eid
      }
    },
    methods: {
      isActiveRole(role) {
        return role.type == this.item.role
      },
      changeRole(role_type) {
        this.$emit('change-role', role_type)
        this.is_visible = false
      },
      removeMember(member) {
        var full_name = getFullName(this.item)
        var member = full_name.length > 0 ? full_name : _.get(this.item, 'email', '')
        var msg = this.is_member_active_user ? 'Are you sure you want to leave this team?' : `Are you sure you want to remove <strong>${member}</strong> from this team?`
        var title = this.is_member_active_user ? 'Leave team?' : `Remove ${member}?`

        this.$confirm(msg, title, {
          confirmButtonClass: 'ttu fw6 el-button--danger',
          cancelButtonClass: 'ttu fw6',
          confirmButtonText: this.is_member_active_user ? 'Leave' : 'Remove',
          cancelButtonText: 'Cancel',
          dangerouslyUseHTMLString: !this.is_member_active_user ? true : false,
          type: 'warning'
        }).then(() => {
          this.$emit('remove-member', member)
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
    padding: 12px
    &:hover
      background-color: rgba(0,0,0,0.03)
    h4
      font-weight: 600
    p
      font-size: 12px
</style>
