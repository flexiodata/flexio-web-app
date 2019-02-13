<template>
  <el-popover
    trigger="click"
    :class="is_visible ? activeClass : ''"
    :style="style"
    v-on="$listeners"
    v-bind="$attrs"
    v-model="is_visible"
  >
    <div class="b mb2" v-if="title.length > 0">{{title}}</div>
    <div class="tl">{{message}}</div>
    <div class="mt3 w-100 flex flex-row justify-end">
      <el-button
        size="small"
        :class="cancelButtonClass"
        @click="is_visible = false"
      >
        {{cancelButtonText}}
      </el-button>
      <el-button
        size="small"
        :class="confirmButtonClass"
        :type="confirmButtonType"
        @click="onConfirmClick"
      >
        {{confirmButtonText}}
      </el-button>
    </div>
    <slot name="reference" slot="reference"><i class="material-icons">delete</i></slot>
  </el-popover>
</template>

<script>
  export default {
    inheritAttrs: false,
    props: {
      title: {
        type: String,
        default: 'Confirm delete?'
      },
      message: {
        type: String,
        default: 'Are you sure you want to delete this item?'
      },
      activeClass: {
        type: String,
        default: 'black'
      },
      cancelButtonText: {
        type: String,
        default: 'Cancel'
      },
      confirmButtonText: {
        type: String,
        default: 'Delete'
      },
      cancelButtonClass: {
        type: String,
        default: 'ttu fw6'
      },
      confirmButtonClass: {
        type: String,
        default: 'ttu fw6'
      },
      confirmButtonType: {
        type: String,
        default: 'danger'
      }
    },
    data() {
      return {
        is_visible: false
      }
    },
    computed: {
      style() {
        return this.is_visible ? 'color: black; opacity: 1' : ''
      }
    },
    methods: {
      onConfirmClick() {
        this.is_visible = false
        this.$nextTick(() => { this.$emit('confirm-click') })
      }
    }
  }
</script>
