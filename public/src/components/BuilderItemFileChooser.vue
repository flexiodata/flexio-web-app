<template>
  <div>
    <div class="tl pb3">
      <div class="dib" v-if="false">
        <TaskIcon class="br1 square-4" icon="insert_drive_file" bg-color="#0ab5f3" />
      </div>
      <h3 class="fw6 f3 mid-gray mt0 mb2">Choose files</h3>
    </div>
    <div v-show="is_shown">
      <file-chooser
        class="bb b--light-gray"
        style="max-height: 24rem"
        :connection="store_connection"
        @selection-change="updateFiles"
        v-if="ceid"
      />
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import TaskIcon from './TaskIcon.vue'
  import FileChooser from './FileChooser.vue'

  export default {
    props: {
      item: {
        type: Object,
        required: true
      },
      index: {
        type: Number,
        required: true
      }
    },
    components: {
      TaskIcon,
      FileChooser
    },
    computed: {
      ...mapState({
        prompts: state => state.builder.prompts,
        active_prompt_idx: state => state.builder.active_prompt_idx
      }),
      id() {
        return _.get(this.item, 'id', '')
      },
      prompt() {
        return _.find(this.prompts, { id: this.id }, {})
      },
      ceid() {
        return _.get(this.prompt, 'connection_eid', null)
      },
      connections() {
        return this.getAllConnections()
      },
      store_connection() {
        return _.find(this.connections, { eid: this.ceid }, null)
      },
      is_shown() {
        return this.index <= this.active_prompt_idx
      }
    },
    methods: {
      ...mapGetters([
        'getAllConnections'
      ]),
      updateFiles(files) {
        this.$store.commit('BUILDER__UPDATE_ACTIVE_ITEM', { files })
      }
    }
  }
</script>
