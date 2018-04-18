<template>
  <div
    class="flex flex-row relative"
    :id="item.id"
  >

    <!-- insert buttons -->
    <div
      class="absolute h-100 ml4"
      v-if="(showLine || showInsertButtons) && showIcons"
    >
      <!-- vertical line w/o buttons -->
      <div
        class="bl bw1 b--black-10 pl3 absolute"
        style="top: 61px; bottom: -19px; left: 15px"
        v-if="!is_last && showLine && !showInsertButtons"
      ></div>

      <!-- vertical line (above icon) w/buttons -->
      <div
        class="bl bw1 b--black-10 pl3 absolute"
        style="top: 0; height: 19px; left: 15px"
        v-if="!is_first && showLine && showInsertButtons"
      ></div>

      <!-- vertical line (below icon) w/buttons -->
      <div
        class="bl bw1 b--black-10 pl3 absolute"
        style="top: 61px; bottom: 30px; left: 15px"
        v-if="showLine && showInsertButtons"
      ></div>

      <!-- insert before button -->
      <div
        class="absolute"
        style="top: -10px; left: 4px"
        v-if="is_first && showInsertButtons"
      >
        <div class="pointer moon-gray hover-blue link hint--right" aria-label="Insert a new step">
          <i class="db material-icons f3">add_circle</i>
        </div>
      </div>

      <!-- insert after button -->
      <div
        class="absolute"
        style="bottom: 0; left: 4px"
        v-if="showInsertButtons"
      >
        <div class="pointer moon-gray hover-blue link hint--right" aria-label="Insert a new step">
          <i class="db material-icons f3">add_circle</i>
        </div>
      </div>
    </div>

    <!-- icon -->
    <div
      class="flex-none pa4 nt2"
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

    <!-- number -->
    <div
      class="flex-none pv4 pr3"
      v-if="showNumbers"
    >
      {{index+1}}.
    </div>

    <!-- main content -->
    <div
      class="flex-fill flex flex-column bg-white pa4"
      style="min-height: 10rem"
      :class="content_cls"
    >
      <div class="flex-fill">
        <BuilderItemConnectionChooser
          :item="item"
          v-if="item.ui == 'connection-chooser'"
        />
        <div v-else>
          <span class="silver">
            {{item.ui}}
            <span v-if="item.variable">:</span>
          </span>
          {{item.variable}}
        </div>
      </div>
      <div class="flex-none mt3 flex flex-row justify-end" v-if="is_active">
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
          v-show="!is_last"
        >
          Next
        </el-button>
        <el-button
          class="ttu b"
          type="primary"
          @click="finishClick"
          v-show="is_last"
        >
          Finish
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
      BuilderItemConnectionChooser
    },
    computed: {
      ...mapState({
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
          case 'summary-page': return 'check'
        }
      },
      task_color() {
        switch (this.item.ui) {
          case 'file-chooser': return '#0ab5f3'
          case 'summary-page': return '#009900'
        }
      },
      is_next_allowed() {
        if (this.item.ui == 'connection-chooser') {
          return _.get(this.store_connection, 'connection_status', '') == CONNECTION_STATUS_AVAILABLE
        }

        return true
      }
    },
    methods: {
      ...mapGetters([
        'getAllConnections'
      ]),
      finishClick() {
        alert('Finished!')
      }
    }
  }
</script>

<style scoped>
  .css-active {
    box-shadow: 0 4px 24px -4px rgba(0,0,0,0.2);
    transition: all 0.15s ease;
  }
</style>
