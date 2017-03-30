<template>
  <article class="mb3">
    <div class="flex flex-row items-center pa2 bg-black-05 bb b--black-05">
      <connection-icon :type="ctype" class="v-mid br1 fx-square-2 mr2"></connection-icon>
      <div class="f6 fw6 ttu silver">{{service_name}}</div>
      <div class="flex-fill"></div>
      <a
        class="no-underline f5 b dib pointer pa0 link black-30 hover-black-60 popover-trigger"
        ref="menutrigger"
        tabindex="0"
      >
        <i class="material-icons">menu</i>
      </a>
      <ui-popover
        trigger="menutrigger"
        ref="menu"
        dropdown-position="bottom right"
      >
        <ui-menu
          contain-focus
          has-icons
          :options="menu_options"
          @select="onMenuItemClick"
          @close="$refs.menu.close()"
        ></ui-menu>
      </ui-popover>
    </div>
    <div class="ma3">
      <div class="tl" v-if="is_dropbox || is_google_drive">
        <div class="lh-copy mid-gray f6 mb1">Files will be output to the following folder:</div>
        <div class="flex flex-row items-center">
          <div class="flex-fill f6 mr2 pa2 ba b--black-10 black">
            <span class="i" v-if="location == '/'">{{friendly_location}}</span>
            <span v-else>{{location}}</span>
          </div>
          <btn
            btn-md
            btn-primary
            @click="openOutputChooser"
          >
            <span class="ttu b">Change folder</span>
          </btn>
        </div>
      </div>
      <div class="tl" v-else-if="is_google_sheets">
        <div class="lh-copy mid-gray f6 mb3 i">Each file will be output to Google Sheets as a single sheet.</div>
      </div>
      <div class="tl" v-else-if="is_amazon_s3">
        <div class="lh-copy mid-gray f6 mb3 i">
          Files will be output to the <span class="b black fs-normal">{{connection.database}}</span> bucket.
        </div>
      </div>
      <div class="tl" v-else-if="is_stdout">
        <div class="lh-copy mid-gray f6 mb3 i">Output files from the command line.</div>
        <div class="pv1 ph2 bg-black-05">
          <code class="f6">flexio pipes run pipe-name > output.txt</code>
        </div>
      </div>
    </div>

    <!-- output chooser modal -->
    <output-chooser-modal
      ref="modal-output-chooser"
      :connection="connection"
      :location="location"
      @submit="saveLocation"
      @hide="show_output_chooser_modal = false"
      v-if="show_output_chooser_modal"
    ></output-chooser-modal>
  </article>
</template>

<script>
  import {
    CONNECTION_TYPE_AMAZONS3,
    CONNECTION_TYPE_DROPBOX,
    CONNECTION_TYPE_GOOGLEDRIVE,
    CONNECTION_TYPE_GOOGLESHEETS,
    CONNECTION_TYPE_STDOUT
  } from '../constants/connection-type'
  import { mapGetters } from 'vuex'
  import * as connections from '../constants/connection-info'
  import Btn from './Btn.vue'
  import ConnectionIcon from './ConnectionIcon.vue'
  import OutputChooserModal from './OutputChooserModal.vue'

  export default {
    props: ['item'],
    components: {
      Btn,
      ConnectionIcon,
      OutputChooserModal
    },
    inject: ['pipeEid'],
    data() {
      return {
        show_output_chooser_modal: false
      }
    },
    computed: {
      service_name() {
        return _.result(this, 'cinfo.service_name', '')
      },
      task() {
        return this.item
      },
      ctype() {
        return _.get(this.task, 'metadata.connection_type', '')
      },
      conn_identifier() {
        return _.get(this.task, 'params.connection', '')
      },
      location() {
        return _.get(this.task, 'params.location', '')
      },
      friendly_location() {
        return '<' + this.service_name + ' ' + 'root folder' + '>'
      },
      connection() {
        var connection_eid = _.get(this.task, 'params.connection', '')
        return _.get(this.$store, 'state.objects.'+connection_eid, {
          connection_type: this.ctype
        })
      },
      is_amazon_s3() {
        return this.ctype == CONNECTION_TYPE_AMAZONS3
      },
      is_dropbox() {
        return this.ctype == CONNECTION_TYPE_DROPBOX
      },
      is_google_drive() {
        return this.ctype == CONNECTION_TYPE_GOOGLEDRIVE
      },
      is_google_sheets() {
        return this.ctype == CONNECTION_TYPE_GOOGLESHEETS
      },
      is_stdout() {
        return this.ctype == CONNECTION_TYPE_STDOUT
      },
      menu_options() {
        var items = []

        if (this.is_dropbox)
        {
          items = [{
            id: 'change-folder',
            label: 'Change Folder',
            icon: 'folder_open'
          },{
            type: 'divider'
          }]
        }

        items.push({
          id: 'delete',
          label: 'Remove this output',
          icon: 'delete'
        })

        return items
      }
    },
    methods: {
      cinfo() {
        return _.find(connections, { connection_type: this.ctype })
      },
      openOutputChooser() {
        if (!_.isNil(this.connection))
        {
          this.show_output_chooser_modal = true
          this.$nextTick(() => { this.$refs['modal-output-chooser'].open() })
        }
      },
      saveLocation(location, modal) {
        var eid = this.pipeEid
        var task_eid = _.get(this.item, 'eid', '')
        var attrs = _.cloneDeep(_.omit(this.item, 'eid'))

        _.set(attrs, 'params.location', location)

        // edit pipe task
        this.$store.dispatch('updatePipeTask', { eid, task_eid, attrs }).then(response => {
          if (response.ok)
          {
            if (!_.isNil(modal))
              modal.close()
          }
           else
          {
          }
        })
      },
      onMenuItemClick(menu_item) {
        switch (menu_item.id)
        {
          case 'change-folder': return this.openOutputChooser()
          case 'delete':  return this.$emit('delete', this.item)
        }
      }
    }
  }
</script>
