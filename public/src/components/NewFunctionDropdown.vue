<template>
  <el-popover
    trigger="click"
    v-on="$listeners"
    v-bind="$attrs"
    v-model="is_visible"
  >
    <div class="new-function-list">
      <div
        :class="isDivider(item.type) ? '' : 'new-function-item'"
        :key="item.name"
        @click="selectItem(item.type)"
        v-for="item in new_items"
      >
        <div class="bt b--black-05" v-if="isDivider(item.type)"></div>
        <div class="flex flex-row items-start" v-else>
          <i class="material-icons md-18 b blue new-function-icon">{{item.icon}}</i>
          <div>
            <h4 class="ma0">{{item.name}}</h4>
            <p class="tl mt1 mb0">{{item.description}}</p>
          </div>
        </div>
      </div>
    </div>
    <slot name="reference" slot="reference">New Item</slot>
  </el-popover>
</template>

<script>
  import { mapState } from 'vuex'
  import { getFullName } from '@/utils'
  import new_items from '@/data/new-function-dropdown-items.yml'

  export default {
    inheritAttrs: false,
    data() {
      return {
        is_visible: false,
        new_items
      }
    },
    methods: {
      isDivider(type) {
        return type == 'divider'
      },
      selectItem(type) {
        if (!this.isDivider(type)) {
          this.$emit('click', type)
          this.is_visible = false
        }
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .new-function-list
    margin: -12px

  .new-function-item
    cursor: pointer
    padding: 10px 12px
    &:hover
      background-color: rgba(0,0,0,0.03)
    h4
      font-weight: 600
    p
      font-size: 12px
      padding-right: 12px

  .new-function-icon
    margin: 2px 12px 0 2px
</style>
