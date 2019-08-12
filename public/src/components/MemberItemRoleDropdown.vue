<template>
  <el-popover
    trigger="click"
    :class="is_visible ? activeClass : ''"
    :style="style"
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
    </div>
    <slot name="reference" slot="reference">Contributor</slot>
  </el-popover>
</template>

<script>
  import member_roles from '@/data/member-roles.yml'

  export default {
    inheritAttrs: false,
    props: {
      item: {
        type: Object,
        required: true
      },
      activeClass: {
        type: String,
        default: 'black'
      },
    },
    data() {
      return {
        is_visible: false,
        member_roles
      }
    },
    computed: {
      style() {
        return this.is_visible ? 'color: black; opacity: 1' : ''
      }
    },
    methods: {
      isActiveRole(role) {
        return role.type == this.item.role
      },
      changeRole(role_type) {
        this.$emit('change', role_type)
        this.is_visible = false
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .member-role-list
    margin: -12px

  .member-role-item
    cursor: pointer
    padding: 12px 14px
    &:hover
      background-color: rgba(0,0,0,0.03)
    h4
      font-weight: 600
    p
      font-size: 12px

  .member-role-item + .member-role-item
    border-top: 1px solid #f0f0f0
</style>
