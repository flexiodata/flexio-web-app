<template>
  <el-popover
    placement="top-end"
    trigger="click"
    transition="el-zoom-in-top"
    v-model="visible"
  >
    <el-button
      class="ttu b"
      type="text"
      size="tiny"
      slot="reference"
    >
      <i class="material-icons v-mid black-30 md-24">menu</i>
      <i class="material-icons v-mid black-30 nl1 md-18">expand_more</i>
    </el-button>
    <div class="menu-content">
      <div class="ttu fw6 f8 moon-gray menu-header">Editor</div>
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
      <div class="el-dropdown-menu__item" @click="emitMenuItem('view-properties')">Properties</div>
      <div class="el-dropdown-menu__item" @click="emitMenuItem('view-configure')">Build &amp; Test</div>
      <div class="el-dropdown-menu__item" @click="emitMenuItem('view-history')">History</div>
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
    padding: 0 20px 4px
  .editor-item
    line-height: 28px
</style>
