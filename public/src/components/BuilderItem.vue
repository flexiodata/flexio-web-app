<template>
  <div
    class="flex flex-row"
    :id="item.id"
  >
    <!-- numbers, icons and vertical lines -->
    <div class="flex flex-row relative">
      <!-- number -->
      <div
        class="flex-none pv4 mt2 tr"
        style="width: 24px"
        v-if="showNumbers"
      >
        {{index+1}}.
      </div>

      <!-- icon -->
      <div
        class="flex-none pv4 pl1 pl3-l pr3 pr4-l"
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

      <!-- vertical line and insert buttons -->
      <div
        class="absolute w-100 h-100"
        v-if="(showLine || showInsertButtons) && showIcons"
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
        class="child absolute right-0 mr4"
        v-if="showDeleteButtons"
      >
        <el-button
          class="hint--top"
          style="border: 0; padding: 0"
          type="text"
          aria-label="Delete this step"
          @click="$emit('delete-step', index)"
        >
          <i class="el-icon-close pointer f3 black-30 hover-black-60"></i>
        </el-button>
      </div>

      <div class="flex-fill">
        <BuilderItemTaskChooser
          title="Choose the task to insert"
          :show-title="true"
          :item="item"
          :index="index"
          :active-item-idx="activeItemIdx"
          :is-next-allowed.sync="is_next_allowed"
          :builder-mode="builderMode"
          v-on="$listeners"
          v-if="item.element == 'task-chooser'"
        />
        <BuilderItemConnectionChooser
          :item="item"
          :index="index"
          :active-item-idx="activeItemIdx"
          :is-next-allowed.sync="is_next_allowed"
          :builder-mode="builderMode"
          v-on="$listeners"
          v-else-if="item.element == 'connection-chooser'"
        />
        <BuilderItemFileChooser
          :item="item"
          :index="index"
          :active-item-idx="activeItemIdx"
          :is-next-allowed.sync="is_next_allowed"
          :builder-mode="builderMode"
          v-on="$listeners"
          v-else-if="item.element == 'file-chooser'"
        />
        <BuilderItemForm
          :item="item"
          :index="index"
          :active-item-idx="activeItemIdx"
          :is-next-allowed.sync="is_next_allowed"
          :builder-mode="builderMode"
          v-on="$listeners"
          v-else-if="item.element == 'form'"
        />
        <BuilderItemSummary
          :item="item"
          :index="index"
          :active-item-idx="activeItemIdx"
          :is-next-allowed.sync="is_next_allowed"
          :builder-mode="builderMode"
          v-on="$listeners"
          v-else-if="item.element == 'summary-prompt'"
        />
        <BuilderItemTaskJsonEditor
          :item="item"
          :index="index"
          :active-item-idx="activeItemIdx"
          :is-next-allowed.sync="is_next_allowed"
          :builder-mode="builderMode"
          v-on="$listeners"
          v-else-if="item.element == 'task-json-editor'"
        />
        <div
          v-else
        >
          <span class="silver">
            {{item.element}}
            <span v-if="item.variable">:</span>
          </span>
          {{item.variable}}
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
  import BuilderItemTaskChooser from './BuilderItemTaskChooser.vue'
  import BuilderItemTaskJsonEditor from './BuilderItemTaskJsonEditor.vue'
  import BuilderItemConnectionChooser from './BuilderItemConnectionChooser.vue'
  import BuilderItemFileChooser from './BuilderItemFileChooser.vue'
  import BuilderItemForm from './BuilderItemForm.vue'
  import BuilderItemSummary from './BuilderItemSummary.vue'

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
      showDeleteButtons: {
        type: Boolean,
        default: true
      },
      showLine: {
        type: Boolean,
        default: true
      },
      builderMode: {
        type: String
      }
    },
    components: {
      ServiceIcon,
      TaskIcon,
      BuilderItemTaskChooser,
      BuilderItemTaskJsonEditor,
      BuilderItemConnectionChooser,
      BuilderItemFileChooser,
      BuilderItemForm,
      BuilderItemSummary
    },
    data() {
      return {
        is_next_allowed: true
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
      content_cls() {
        return {
          'pa4 b--black-10': true,
          'bl br': !this.is_first && !this.is_last,
          'bl br bt br2 br--top': this.is_first,
          'bl br bb br2 br--bottom': this.is_last,
          'css-active': this.is_active,
          'o-40 no-pointer-events no-select': this.builder__is_editing && !this.is_active
        }
      },
      task_icon() {
        switch (this.item.element) {
          case 'file-chooser':   return 'insert_drive_file'
          case 'form':           return 'edit'
          case 'summary-prompt': return 'check'
        }
      },
      task_color() {
        switch (this.item.element) {
          case 'file-chooser':   return '#0ab5f3'
          case 'form':           return '#0ab5f3'
          case 'summary-prompt': return '#009900'
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
  .css-active
    margin-left: -4px
    margin-right: -4px
    border-radius: 4px
    border-color: #fff
    box-shadow: 0 4px 24px -4px rgba(0,0,0,0.2)
    transition: all 0.2s ease
</style>
