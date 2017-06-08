<template>
  <ui-modal
    ref="dialog"
    @hide="onHide"
  >
    <div slot="header" class="f4">Share '{{pipe.name}}'</div>

    <!-- list -->
    <member-list
      class="overflow-auto"
    ></member-list>

  </ui-modal>
</template>

<script>
  import api from '../api'
  import { ACTION_TYPE_READ } from '../constants/action-type'
  import Btn from './Btn.vue'
  import MemberList from './MemberList.vue'

  const defaultAttrs = () => {
    return {
      eid: null,
      name: ''
    }
  }

  export default {
    components: {
      Btn,
      MemberList
    },
    data() {
      return {
        pipe: _.extend({}, defaultAttrs())
      }
    },
    methods: {
      open(attrs) {
        this.reset(attrs)
        this.$refs['dialog'].open()

        // -- hard coded for now --

        var pipe_eid = _.get(this.pipe, 'eid')

        var rights = [{
          "object_eid": pipe_eid,
          "access_code": 'n7z50rgbybbn',
          "actions": [ACTION_TYPE_READ]
        }]

        api.createRights({ attrs: { rights } }).then(create_response => {
          console.log(create_response)

          api.fetchRights({ objects: pipe_eid }).then(fetch_response => {
            console.log(fetch_response)
          })
        })
      },
      close() {
        this.$refs['dialog'].close()
      },
      reset(attrs) {
        this.pipe = _.extend({}, defaultAttrs(), attrs)
      },
      onHide() {
        this.$emit('hide', this)
      }
    }
  }
</script>
