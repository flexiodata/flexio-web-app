<template>
  <div class="flex flex-row items-center">
    <div class="flex-fill flex flex-row items-center">
      <h1 class="mv0 fw4 f3" v-if="title.length > 0">{{title}}</h1>
      <h1 class="mv0 fw4 f3 moon-gray" v-else>(No title)</h1>
      <LabelSwitch
        class="dib ml2 hint--bottom-left"
        active-color="#13ce66"
        :aria-label="is_deployed ? 'Turn pipe off' : 'Turn pipe on'"
        :width="58"
        v-model="is_deployed"
      />
      <el-button
        plain
        class="btn-header hint--bottom"
        style="background: transparent"
        aria-label="Edit Properties"
        :class="{ 'invisible': isModeRun }"
        @click="$emit('properties-click')"
      >
        <i class="material-icons v-mid">edit</i>
      </el-button>
    </div>
      <transition name="el-fade-in" mode="out-in">
      <div
        key="actions"
        class="flex-none flex flex-row items-center pv1"
        v-if="!showSaveCancel"
      >
        <el-button
          class="ttu fw6"
          style="min-width: 5rem"
          type="primary"
          size="small"
          :disabled="!allowRun"
          @click="$emit('run-click')"
        >
          Test
        </el-button>
      </div>
      <div
        key="save-cancel"
        class="flex-none flex flex-row items-center pv1"
        v-if="showSaveCancel"
      >
        <el-button
          class="ttu fw6"
          size="small"
          @click="$emit('cancel-click')"
        >
          Cancel
        </el-button>
        <el-button
          class="ttu fw6"
          size="small"
          type="primary"
          @click="$emit('save-click')"
        >
          Save changes
        </el-button>
      </div>
    </transition>
  </div>
</template>

<script>
  import LabelSwitch from '@comp/LabelSwitch'

  export default {
    props: {
      title: {
        type: String,
        required: true
      },
      showSaveCancel: {
        type: Boolean,
        default: false
      },
      isModeRun: {
        type: Boolean,
        required: true
      },
      allowRun: {
        type: Boolean,
        default: true
      }
    },
    components: {
      LabelSwitch
    },
    computed: {
      is_deployed: {
        get() {
          return this.isModeRun
        },
        set(value) {
          this.$emit('update:isModeRun', value)
        }
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .btn-header
    border: 0
    padding: 0
    margin-left: 0.5rem
    opacity: 0.3
    &:hover
      opacity: 1
</style>
