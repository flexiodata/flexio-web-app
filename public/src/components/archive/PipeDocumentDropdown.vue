<template>
  <el-popover
    placement="top-end"
    trigger="click"
    transition="el-zoom-in-top"
    v-model="visible"
  >
    <el-button
      class="ttu fw6"
      type="plain"
      size="tiny"
      slot="reference"
      title="Pipe settings"
    >
      <div>
        <i class="material-icons v-mid md-24">menu</i>
        <i class="material-icons v-mid nl1 md-18">expand_more</i>
      </div>
    </el-button>
    <div class="menu-content">
      <div class="ttu fw6 f7 moon-gray menu-header">Editor view</div>
      <div
        class="flex flex-row items-center f7 el-dropdown-menu__item editor-item"
        @click="emitEditorChange(view)"
        v-for="view in editor_views"
      >
        <i class="material-icons md-18 mr2" v-if="isViewSelected(view.val)">check</i>
        <i class="material-icons md-18 mr2" style="color: transparent" v-else>check</i>
        <span>{{view.label}}</span>
      </div>
      <div class="mv2 bt b--black-05"></div>
      <div class="flex flex-row items-center el-dropdown-menu__item" @click="emitMenuItem('schedule')">
        <i class="material-icons md-18 mr2">date_range</i> Schedule
      </div>
      <div class="flex flex-row items-center el-dropdown-menu__item" @click="emitMenuItem('deploy')">
        <i class="material-icons md-18 mr2">archive</i> Deploy
      </div>
      <div class="flex flex-row items-center el-dropdown-menu__item" @click="emitMenuItem('properties')">
        <i class="material-icons md-18 mr2">edit</i> Properties
      </div>

    </div>
  </el-popover>
</template>

<script>
  const PIPEDOC_EDITOR_SDK_JS  = 'sdk-js'
  const PIPEDOC_EDITOR_BUILDER = 'builder'
  const PIPEDOC_EDITOR_JSON    = 'json'
  const PIPEDOC_EDITOR_YAML    = 'yaml'

  const editor_views = [
    { val: PIPEDOC_EDITOR_BUILDER, label: 'Visual builder' },
    { val: PIPEDOC_EDITOR_SDK_JS,  label: 'Javascript SDK' },
    { val: PIPEDOC_EDITOR_JSON,    label: 'JSON'           },
    { val: PIPEDOC_EDITOR_YAML,    label: 'YAML'           }
  ]

  export default {
    props: {
      editor: {
        type: String,
        default: 'builder'
      }
    },
    data() {
      return {
        editor_views,
        visible: false
      }
    },
    methods: {
      isViewSelected(view) {
        return view == this.editor
      },
      emitEditorChange(view) {
        this.$emit('switch-editor', view.val)
        this.visible = false
      },
      emitMenuItem(evt_name) {
        this.$emit(evt_name)
        this.visible = false
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .menu-content
    min-width: 180px
    margin: -12px
    padding: 10px 0
  .menu-header
    padding: 4px 20px
  .editor-item
    line-height: 26px
</style>
