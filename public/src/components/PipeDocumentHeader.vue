<template>
  <div>
    <div class="flex flex-row">
      <div class="flex-fill flex flex-column">
        <div class="flex flex-row f4 fw6 lh-title">
          <div>{{pipe.name}}</div>
          <LabelSwitch
            class="dib ml3 hint--bottom"
            active-color="#13ce66"
            :aria-label="is_deployed ? 'Turn pipe off' : 'Turn pipe on'"
            :width="58"
            v-model="is_deployed"
          />
        </div>
        <div style="max-width: 60rem">
          <div class="f6 fw4 mt1 lh-copy silver" v-if="pdesc.length > 0">{{pdesc}}</div>
          <div class="f6 fw4 mt1" v-else><em class="moon-gray">(No description)</em></div>
        </div>
      </div>
      <transition name="el-fade-in" mode="out-in">
        <div
          key="actions"
          class="flex-none flex flex-row items-start"
          v-if="!showSaveCancel"
        >
          <el-button
            class="ttu fw6"
            style="min-width: 5rem"
            size="small"
            @click="$emit('properties-click')"
          >
            Edit
          </el-button>
          <el-button
            key="test-button"
            style="min-width: 5rem"
            type="primary"
            size="small"
            :disabled="!allowRun"
            @click="$emit('run-click')"
            v-show="!showTestPanel"
          >
            <span class="ttu fw6">Test</span>
          </el-button>
        </div>
        <div
          key="save-cancel"
          class="flex-none flex flex-row items-start"
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
  </div>
</template>

<script>
  import LabelSwitch from '@comp/LabelSwitch'

  export default {
    props: {
      pipe: {
        type: Object,
        default: () => { return {} }
      },
      showSaveCancel: {
        type: Boolean,
        default: false
      },
      showTestPanel: {
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
      pdesc() {
        var word_count = 50
        var desc = _.get(this.pipe, 'description', '')
        var desc_arr = desc.split(' ')
        return desc_arr.length <= word_count ? desc_arr.join(' ') : desc_arr.slice(0, word_count).join(' ') + '...'
      },
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
    margin-left: 1rem
    opacity: 0.3
    &:hover
      opacity: 1
</style>
