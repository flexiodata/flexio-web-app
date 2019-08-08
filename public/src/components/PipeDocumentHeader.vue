<template>
  <div>
    <div class="flex flex-row">
      <div class="flex-fill flex flex-column">
        <div class="flex flex-row items-center">
          <div class="f4 fw6 lh-title">{{pipe.name}}</div>
          <LabelSwitch
            class="ml3 hint--bottom"
            active-color="#13ce66"
            :aria-label="is_deployed ? 'Turn function off' : 'Turn function on'"
            :width="58"
            v-model="is_deployed"
          />
        </div>
        <div class="mt3">
          <div class="code f7 b" v-html="spreadsheet_command_syntax"></div>
          <p class="mb0 f6" v-show="pdesc.length > 0">{{pdesc}}</p>
        </div>
      </div>
      <transition name="el-fade-in" mode="out-in">
        <div
          key="actions"
          class="flex-none flex flex-row items-start ml3"
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
          class="flex-none flex flex-row items-start ml3"
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
  import { mapState } from 'vuex'
  import { getJsDocObject, getSpreadsheetSyntaxStr } from '@/utils/pipe'
  import LabelSwitch from '@/components/LabelSwitch'

  export default {
    props: {
      pipe: {
        type: Object,
        default: () => {}
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
      ...mapState({
        active_team_name: state => state.teams.active_team_name
      }),
      pdesc() {
        var jsdoc_obj = getJsDocObject(this.pipe)
        return _.get(jsdoc_obj, 'description', '')
      },
      spreadsheet_command_syntax() {
        return getSpreadsheetSyntaxStr(this.active_team_name, this.pipe, true)
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
