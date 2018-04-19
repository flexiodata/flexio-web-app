<template>
  <div
    class="flex flex-row relative"
    :id="item.id"
  >

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
      v-if="(showLine || showInsertButtons) && showIcons"
    >
      <!-- vertical line w/o buttons -->
      <div
        class="bl bw1 b--black-10 pl3 absolute"
        style="top: 61px; bottom: -19px; left: 44px"
        v-if="!is_last && showLine && !showInsertButtons"
      ></div>

      <!-- vertical line (above icon) w/buttons -->
      <div
        class="bl bw1 b--black-10 pl3 absolute"
        style="top: 0; height: 19px; left: 44px"
        v-if="!is_first && showLine && showInsertButtons"
      ></div>

      <!-- vertical line (below icon) w/buttons -->
      <div
        class="bl bw1 b--black-10 pl3 absolute"
        style="top: 61px; bottom: 30px; left: 44px"
        v-if="showLine && showInsertButtons"
      ></div>

      <!-- insert before button -->
      <div
        class="absolute"
        style="top: -10px; left: 33px"
        v-if="is_first && showInsertButtons"
      >
        <div class="pointer moon-gray hover-blue link hint--right" aria-label="Insert a new step">
          <i class="db material-icons f3">add_circle</i>
        </div>
      </div>

      <!-- insert after button -->
      <div
        class="absolute"
        style="bottom: 0; left: 33px"
        v-if="showInsertButtons"
      >
        <div class="pointer moon-gray hover-blue link hint--right" aria-label="Insert a new step">
          <i class="db material-icons f3">add_circle</i>
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
          v-if="item.ui == 'connection-chooser'"
        />
        <BuilderItemFileChooser
          :item="item"
          :index="index"
          v-else-if="item.ui == 'file-chooser'"
        />
        <BuilderItemInput
          :item="item"
          :index="index"
          v-else-if="item.ui == 'input'"
        />
        <BuilderItemSummaryPage
          :item="item"
          :index="index"
          v-else-if="item.ui == 'summary-page'"
        />
        <div v-else>
          <span class="silver">
            {{item.ui}}
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
          @click="$store.commit('BUILDER__GO_PREV_ITEM')"
          v-show="!is_first"
        >
          Back
        </el-button>
        <el-button
          class="ttu b"
          type="primary"
          :disabled="!is_next_allowed"
          @click="$store.commit('BUILDER__GO_NEXT_ITEM')"
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
  import BuilderItemInput from './BuilderItemInput.vue'
  import BuilderItemSummaryPage from './BuilderItemSummaryPage.vue'

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
      showLine: {
        type: Boolean,
        default: true
      },
      showInsertButtons: {
        type: Boolean,
        default: true
      },
      showIcons: {
        type: Boolean,
        default: true
      },
      showNumbers: {
        type: Boolean,
        default: true
      }
    },
    components: {
      ServiceIcon,
      TaskIcon,
      BuilderItemConnectionChooser,
      BuilderItemFileChooser,
      BuilderItemInput,
      BuilderItemSummaryPage
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
        if (this.item.ui == 'connection-chooser') {
          return _.get(this.store_connection, 'connection_status', '') == CONNECTION_STATUS_AVAILABLE
        }

        if (this.item.ui == 'file-chooser') {
          return _.get(this.item, 'files', []).length > 0
        }

        if (this.item.ui == 'input') {
          return _.get(this.item, 'value', '').length > 0
        }

        return true
      },
      content_cls() {
        return {
          'b--white-box': true,
          'bl br': !this.is_first && !this.is_last,
          'bl br bt br2 br--top': this.is_first,
          'bl br bb br2 br--bottom': this.is_last,
          'relative z-2 css-active': this.is_active,
          'o-40 no-pointer-events': !this.is_active
        }
      },
      task_icon() {
        switch (this.item.ui) {
          case 'file-chooser': return 'insert_drive_file'
          case 'input':        return 'edit'
          case 'summary-page': return 'check'
        }
      },
      task_color() {
        switch (this.item.ui) {
          case 'file-chooser': return '#0ab5f3'
          case 'input':        return '#0ab5f3'
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
    box-shadow: 0 4px 24px -4px rgba(0,0,0,0.2);
    transition: all 0.15s ease;
  }
</style>
