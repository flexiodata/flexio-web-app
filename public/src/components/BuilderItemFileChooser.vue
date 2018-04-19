<template>
  <div>
    <div class="tc pb3">
      <h3 class="fw6 f4 mid-gray mt2">Choose files</h3>
    </div>
    <div>
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
  import FileChooser from './FileChooser.vue'

  export default {
    props: {
      item: {
        type: Object,
        required: true
      },
    },
    components: {
      FileChooser
    },
    computed: {
      ...mapState({
        prompts: state => state.builder.prompts
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
