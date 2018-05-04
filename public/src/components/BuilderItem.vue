<template>
  <div
    class="flex flex-row"
    :id="item.id"
  >
    <!-- numbers, icons and vertical lines -->
    <div class="flex flex-row relative">
      <!-- number -->
      <div
        class="flex-none pv4 mt2"
        v-if="showNumbers"
      >
        {{index+1}}.
      </div>

      <!-- icon -->
      <div
        class="flex-none pv4 pl3 pr4"
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
        class="absolute h-100 mt2"
        style="right: 60px"
        v-if="(showLine || showInsertButtons) && showIcons"
      >
        <!-- vertical line w/o buttons -->
        <div
          class="bl bw1 b--black-10 pl3 absolute"
          style="top: 61px; bottom: -19px; left: 11px"
          v-if="!is_last && showLine && !showInsertButtons"
        ></div>

        <!-- vertical line (above icon) w/buttons -->
        <div
          class="bl bw1 b--black-10 pl3 absolute"
          style="top: 0; height: 19px; left: 11px"
          v-if="!is_first && showLine && showInsertButtons"
        ></div>

        <!-- vertical line (below icon) w/buttons -->
        <div
          class="bl bw1 b--black-10 pl3 absolute"
          style="top: 61px; bottom: 30px; left: 11px"
          v-if="showLine && showInsertButtons"
        ></div>

        <!-- insert before button -->
        <div
          class="absolute"
          style="top: -10px; left: 0"
          v-if="is_first && showInsertButtons"
        >
          <div class="pointer moon-gray hover-blue link hint--right" aria-label="Insert a new step">
            <i class="db material-icons f3">add_circle</i>
          </div>
        </div>

        <!-- insert after button -->
        <div
          class="absolute"
          style="bottom: 0; left: 0"
          v-if="showInsertButtons"
        >
          <div class="pointer moon-gray hover-blue link hint--right" aria-label="Insert a new step">
            <i class="db material-icons f3">add_circle</i>
          </div>
        </div>
      </div>
    </div>

    <!-- main content -->
    <div
      class="flex-fill flex flex-column bg-white pa4"
      :class="content_cls"
    >
      <div class="flex-fill">
        <BuilderItemConnectionChooser
          :item="item"
          :index="index"
          v-if="item.element == 'connection-chooser'"
        />
        <BuilderItemFileChooser
          :item="item"
          :index="index"
          v-else-if="item.element == 'file-chooser'"
        />
        <BuilderItemForm
          :item="item"
          :index="index"
          v-else-if="item.element == 'form'"
        />
        <BuilderItemSummary
          :item="item"
          :index="index"
          v-else-if="item.element == 'summary-page'"
        />
        <div v-else>
          <span class="silver">
            {{item.element}}
            <span v-if="item.variable">:</span>
          </span>
          {{item.variable}}
        </div>
      </div>
      <div
        class="flex-none mt4 flex flex-row justify-end"
        v-if="is_active && !is_last"
      >
        <el-button
          class="ttu b"
          type="plain"
          @click="$store.commit('builder/GO_PREV_ITEM')"
          v-show="!is_first"
        >
          Back
        </el-button>
        <el-button
          class="ttu b"
          type="primary"
          :disabled="!is_next_allowed"
          @click="$store.commit('builder/GO_NEXT_ITEM')"
        >
          Next
        </el-button>
      </div>
    </div>

  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import { CONNECTION_STATUS_AVAILABLE } from '../constants/connection-status'
  import ServiceIcon from './ServiceIcon.vue'
  import TaskIcon from './TaskIcon.vue'
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
      showLine: {
        type: Boolean,
        default: true
      }
    },
    components: {
      ServiceIcon,
      TaskIcon,
      BuilderItemConnectionChooser,
      BuilderItemFileChooser,
      BuilderItemForm,
      BuilderItemSummary
    },
    computed: {
      ...mapState({
        def: state => state.builder.def,
        prompts: state => state.builder.prompts,
        active_prompt: state  => state.builder.active_prompt,
        active_prompt_idx: state => state.builder.active_prompt_idx
      }),
      ceid() {
        return _.get(this.item, 'connection_eid', null)
      },
      connections() {
        return this.getAllConnections()
      },
      store_connection() {
        return _.find(this.connections, { eid: this.ceid }, null)
      },
      is_first() {
        return this.index == 0
      },
      is_last() {
        return this.index == this.prompts.length - 1
      },
      is_active() {
        return this.index == this.active_prompt_idx
      },
      is_next_allowed() {
        if (this.item.element == 'connection-chooser') {
          return _.get(this.store_connection, 'connection_status', '') == CONNECTION_STATUS_AVAILABLE
        }

        if (this.item.element == 'file-chooser') {
          return _.get(this.item, 'files', []).length > 0
        }

        return true
      },
      content_cls() {
        return {
          'b--white-box': true,
          'bl br': !this.is_first && !this.is_last,
          'bl br bt br2 br--top': this.is_first,
          'bl br bb br2 br--bottom': this.is_last,
          'relative z-1 css-active': this.is_active,
          'o-40 no-pointer-events no-select': !this.is_active
        }
      },
      task_icon() {
        switch (this.item.element) {
          case 'file-chooser': return 'insert_drive_file'
          case 'form':         return 'edit'
          case 'summary-page': return 'check'
        }
      },
      task_color() {
        switch (this.item.element) {
          case 'file-chooser': return '#0ab5f3'
          case 'form':         return '#0ab5f3'
          case 'summary-page': return '#009900'
        }
      }
    },
    methods: {
      ...mapGetters([
        'getAllConnections'
      ])
    }
  }
</script>

<style scoped>
  .css-active {
    margin-left: -4px;
    margin-right: -4px;
    border-radius: 4px;
    border-color: #fff;
    box-shadow: 0 4px 24px -4px rgba(0,0,0,0.2);
    transition: all 0.2s ease;
  }
</style>
