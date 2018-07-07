<template>
  <div
    class="flex flex-row"
    :id="item.id"
  >
    <!-- numbers, icons and vertical lines -->
    <div class="flex flex-row relative">
      <!-- stylized number w/o icons -->
      <div
        class="flex-none pv4 pl1 pl3-l pr3 pr4-l"
        v-if="showNumbers && !showIcons"
      >
        <div
          style="width: 32px; height: 32px"
          class="br-100 ba flex flex-row justify-center items-center"
          :class="is_active ? 'b--blue' : 'b--transparent'"
        >
          <div
            style="width: 26px; height: 26px"
            class="br-100 white flex flex-row justify-center items-center"
            :class="is_active ? 'bg-blue' : 'bg-silver o-30'"
          >
            {{index+1}}
          </div>
        </div>
      </div>

      <!-- number with icons -->
      <div
        class="flex-none pv4 mt2 tr"
        style="width: 24px"
        v-if="showNumbers && showIcons"
      >
        {{index+1}}.
      </div>

      <!-- icon -->
      <div
        class="flex-none pv4 pl1 pl3-l pr3 pr4-l"
        :class="builder__is_editing && !is_active ? 'o-40' : ''"
        v-if="showIcons"
      >
        <ServiceIcon
          class="br1 square-3"
          :type="item.connection_type"
          v-if="item.connection_type"
        />
        <TaskIcon
          class="br1 square-3"
          :icon="task_icon"
          :bg-color="task_color"
          v-else
        />
      </div>

      <!-- vertical line -->
      <div
        class="absolute w-100 h-100"
        v-if="showLine"
      >
        <!-- vertical line w/o buttons -->
        <div
          class="bl bw1 b--black-10 pl3 absolute pr3 pr4-l"
          style="top: 69px; bottom: -27px; right: -1px"
          v-if="!is_last && showLine && !showInsertButtons"
        ></div>

        <!-- vertical line (above icon) w/buttons -->
        <div
          class="bl bw1 b--black-10 pl3 absolute pr3 pr4-l"
          style="top: 4px; height: 23px; right: -1px"
          v-if="!is_first && showLine && showInsertButtons"
        ></div>

        <!-- vertical line (below icon) w/buttons -->
        <div
          class="bl bw1 b--black-10 pl3 absolute pr3 pr4-l"
          style="top: 69px; bottom: 26px; right: -1px"
          v-if="showLine && showInsertButtons"
        ></div>
      </div>

      <!-- insert buttons -->
      <div
        class="absolute w-100 h-100"
        v-if="showInsertButtons"
      >
        <!-- insert before button -->
        <div
          class="absolute cursor-default pr3 pr4-l"
          style="top: -2px; right: 4px"
          v-if="is_first && showInsertButtons"
        >
          <el-button
            class="hint--right"
            style="border: 0; padding: 0"
            type="text"
            aria-label="Insert a new step"
            :disabled="!is_insert_allowed"
            @click="$emit('insert-step', index)"
          >
            <i class="db material-icons f3 moon-gray hover-blue">add_circle</i>
          </el-button>
        </div>

        <!-- insert after button -->
        <div
          class="absolute cursor-default pr3 pr4-l"
          style="bottom: -4px; right: 4px"
          v-if="showInsertButtons"
        >
          <el-button
            class="hint--right"
            style="border: 0; padding: 0"
            type="text"
            aria-label="Insert a new step"
            :disabled="!is_insert_allowed"
            @click="$emit('insert-step', index+1)"
          >
            <i class="db material-icons f3 moon-gray hover-blue">add_circle</i>
          </el-button>
        </div>
      </div>
    </div>

    <!-- main content -->
    <div
      class="flex-fill flex flex-column bg-white hide-child relative"
      :class="content_cls"
    >
      <div
        class="child flex flex-row items-center absolute right-0"
        :class="this.showContentBorder ? 'mr4' : ''"
        v-show="!is_active"
      >
        <el-button
          class="hint--top"
          style="border: 0; padding: 0"
          type="text"
          aria-label="Edit this step"
          @click="$emit('edit-step', index)"
          v-show="showEditButtons"
        >
          <i class="el-icon-edit-outline pointer f4 black-30 hover-black-60"></i>
        </el-button>
        <el-button
          class="hint--top"
          style="border: 0; padding: 0"
          type="text"
          aria-label="Delete this step"
          @click="$emit('delete-step', index)"
          v-show="showDeleteButtons"
        >
          <i class="el-icon-close pointer f3 black-30 hover-black-60"></i>
        </el-button>
      </div>

      <div class="flex-fill">
        <component
          :is="content_component"
          :item="item"
          :index="index"
          :active-item-idx="activeItemIdx"
          :is-next-allowed.sync="is_next_allowed"
          v-on="$listeners"
          v-if="has_content_component"
        />
        <div v-else>
          Unknown Element: {{item.element}}
        </div>
      </div>
      <div
        class="flex-none mt4 flex flex-row justify-end"
        v-if="builder__is_wizard && is_active && !is_last"
      >
        <el-button
          class="ttu b"
          @click="onPrevClick"
          v-show="!is_first"
        >
          Back
        </el-button>
        <el-button
          class="ttu b"
          type="primary"
          :disabled="!is_next_allowed"
          @click="onNextClick"
        >
          Next
        </el-button>
      </div>
      <div
        class="flex-none mt4 flex flex-row justify-end"
        v-if="!builder__is_wizard && is_active"
      >
        <el-button
          class="ttu b"
          @click="onCancelClick"
        >
          Cancel
        </el-button>
        <el-button
          class="ttu b"
          type="primary"
          :disabled="!is_next_allowed"
          @click="onSaveClick"
          v-if="show_save_button"
        >
          Save Changes
        </el-button>
      </div>
    </div>

  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import ServiceIcon from './ServiceIcon.vue'
  import TaskIcon from './TaskIcon.vue'
  import builder_components from './builder-components'

  const components = _.assign({
    ServiceIcon,
    TaskIcon
  }, builder_components)

  const available_components = _.keys(components)

  export default {
    props: {
      item: {
        type: Object,
        required: true
      },
      index: {
        type: Number,
        required: true
      },
      items: {
        type: Array,
        required: true
      },
      activeItemIdx: {
        type: Number,
        default: -1
      },
      showNumbers: {
        type: Boolean,
        default: true
      },
      showIcons: {
        type: Boolean,
        default: true
      },
      showInsertButtons: {
        type: Boolean,
        default: true
      },
      showEditButtons: {
        type: Boolean,
        default: true
      },
      showDeleteButtons: {
        type: Boolean,
        default: true
      },
      showLine: {
        type: Boolean,
        default: true
      },
      showContentBorder: {
        type: Boolean,
        default: true
      },
      builderMode: {
        type: String
      }
    },
    components,
    data() {
      return {
        is_next_allowed: true,
        available_components
      }
    },
    computed: {
      builder__is_wizard() {
        return this.builderMode == 'wizard' ? true : false
      },
      builder__is_editing() {
        return this.activeItemIdx != -1
      },
      is_active() {
        return this.index == this.activeItemIdx
      },
      is_before_active() {
        return this.index < this.activeItemIdx
      },
      is_after_active() {
        return this.index > this.activeItemIdx
      },
      is_first() {
        return this.index == 0
      },
      is_last() {
        return this.index == this.items.length - 1
      },
      is_insert_allowed() {
        return !this.builder__is_editing
      },
      show_save_button() {
        return this.item.element != 'task-chooser'
      },
      content_border_cls() {
        return !this.showContentBorder ? {} : {
          'b--black-10': true,
          'bl br': !this.is_first && !this.is_last,
          'bl br bt br--top': this.is_first && !this.is_last,
          'bl br bb br--bottom': this.is_last && !this.is_first,
          'ba br2': this.is_first && this.is_last
        }
      },
      content_cls() {
        return _.assign({}, this.content_border_cls, {
          'pv4 br2 css-content': true,
          'ph4': this.showContentBorder || this.is_active,
          'css-active': this.is_active,
          'o-40 no-pointer-events no-select': this.builder__is_editing && !this.is_active
        })
      },
      content_component() {
        var element = this.item.element
        element = _.startCase(element)
        element = element.replace(/\s/g, '')
        return 'BuilderItem' + element
      },
      has_content_component() {
        return this.available_components.indexOf(this.content_component) != -1
      },
      task_icon() {
        switch (this.item.element) {
          case 'file-chooser':   return 'insert_drive_file'
          case 'form':           return 'edit'
          case 'summary':        return 'check'
        }
      },
      task_color() {
        switch (this.item.element) {
          case 'file-chooser':   return '#0ab5f3'
          case 'form':           return '#0ab5f3'
          case 'summary':        return '#009900'
        }
      }
    },
    methods: {
      ...mapGetters([
        'getAllConnections'
      ]),
      onPrevClick() {
        this.$emit('item-prev', this.index)
      },
      onNextClick() {
        this.$emit('item-next', this.index)
      },
      onCancelClick() {
        this.$emit('item-cancel', this.index)
      },
      onSaveClick() {
        this.$emit('item-save', this.index)
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .css-content
    transition: all 0.2s ease

  .css-active
    margin-left: -4px
    margin-right: -4px
    border-radius: 4px
    border-color: #fff
    box-shadow: 0 4px 24px -4px rgba(0,0,0,0.2)
</style>
