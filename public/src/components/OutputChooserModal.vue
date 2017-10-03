<template>
  <ui-modal
    ref="dialog"
    remove-close-button
    dismiss-on="close-button"
    class="ui-modal--body-margin"
    @hide="onHide"
  >
    <div slot="header" class="w-100">
      <div class="flex flex-row items-center">
        <div class="flex-fill">
          <span class="f4">{{title}}</span>
        </div>
        <div
          class="pointer f3 lh-solid b child black-30 hover-black-60"
          @click="close"
        >
          &times;
        </div>
      </div>
      <div class="mt3" style="margin-bottom: -1rem">
        <file-explorer-bar
          class="fw4 f6 ba b--black-20 mb2 css-explorer-bar"
          :connection="connection"
          :path="base_path"
          @open-folder="openFolder"
        ></file-explorer-bar>
      </div>
    </div>

    <div class="relative">
      <file-chooser-list
        ref="file-chooser"
        class="min-h5 max-h5"
        :connection="connection"
        :path="base_path"
        :folders-only="true"
        :allow-multiple="false"
        @open-folder="openFolder"
        @item-click="updateLocation"
      ></file-chooser-list>
    </div>

    <div slot="footer" class="w-100">
      <div class="flex-fill flex flex-column">
        <div class="flex-none flex flex-row items-center mb3" style="padding: 0 0.75rem">
          <div class="flex-none f6 fw6 mid-gray mr2">Folder</div>
          <input
            type="text"
            class="flex-fll input-reset ba b--black-20 focus-b--transparent focus-outline focus-o--blue ph2 pv1 f6 w-100"
            placeholder="Output folder"
            v-model="connection_path"
          >
        </div>
        <div class="flex flex-row">
          <div class="flex-fill">&nbsp;</div>
          <btn btn-md class="b ttu blue mr2" @click="close()">Cancel</btn>
          <btn btn-md class="b ttu blue" @click="submit()">{{submit_label}}</btn>
        </div>
      </div>
    </div>
  </ui-modal>
</template>

<script>
  import {
    CONNECTION_TYPE_BLANK_PIPE,
    CONNECTION_TYPE_HTTP,
    CONNECTION_TYPE_RSS,
    CONNECTION_TYPE_MYSQL,
    CONNECTION_TYPE_POSTGRES
  } from '../constants/connection-type'
  import { TASK_TYPE_INPUT, TASK_TYPE_OUTPUT } from '../constants/task-type'
  import * as connections from '../constants/connection-info'
  import { mapGetters } from 'vuex'
  import api from '../api'
  import Btn from './Btn.vue'
  import ServiceIcon from './ServiceIcon.vue'
  import ConnectionChooserList from './ConnectionChooserList.vue'
  import FileExplorerBar from './FileExplorerBar.vue'
  import FileChooserList from './FileChooserList.vue'
  import UrlInputList from './UrlInputList.vue'

  const defaultAttrs = () => {
    return {
      eid: null,
      name: 'New Pipe',
      ename: '',
      description: ''
    }
  }

  export default {
    props: {
      'connection': {
        default: () => { return {} },
        type: Object
      },
      'location': {
        default: '/',
        type: String
      }
    },
    components: {
      Btn,
      FileExplorerBar,
      FileChooserList
    },
    data() {
      return {
        connection_path: this.location
      }
    },
    computed: {
      ctype() {
        return _.get(this.connection, 'connection_type', CONNECTION_TYPE_BLANK_PIPE)
      },
      title() {
        return 'Choose Folder'
      },
      submit_label() {
        return 'Choose Folder'
      },
      base_path() {
        var path = this.connection_path.substring(0, this.connection_path.lastIndexOf('/'))
        return path.length == 0 ? '/' : path
      }
    },
    methods: {
      cinfo() {
        return _.find(connections, { connection_type: this.ctype })
      },
      open() {
        this.reset()
        this.$refs['dialog'].open()
        return this
      },
      close() {
        this.$refs['dialog'].close()
      },
      submit() {
        this.$emit('submit', this.connection_path, this)
      },
      reset() {
        this.connection_path = this.location
      },
      onHide() {
        this.reset()
      },
      openFolder(path) {
        this.connection_path = path == '/' ? _.defaultTo(path, '/') : path + '/'
      },
      updateLocation(item) {
        this.connection_path = _.get(item, 'path', '/')
      }
    }
  }
</script>

<style scoped>
  .css-explorer-bar {
    padding: 0.25rem 0.375rem;
  }
</style>
