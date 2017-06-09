<template>
  <ui-modal
    ref="dialog"
    @hide="onHide"
  >
    <div slot="header" class="f4">Share '{{pipe.name}}'</div>

    <!-- list -->
    <member-list
      class="overflow-auto"
      style="min-height: 300px"
      :object-eid="pipe_eid"
    ></member-list>

  </ui-modal>
</template>

<script>
  import MemberList from './MemberList.vue'

  const defaultAttrs = () => {
    return {
      eid: null,
      name: ''
    }
  }

  export default {
    components: {
      MemberList
    },
    data() {
      return {
        pipe: _.extend({}, defaultAttrs())
      }
    },
    computed: {
      pipe_eid() {
        return _.get(this.pipe, 'eid', '')
      }
    },
    methods: {
      open(attrs) {
        this.reset(attrs)
        this.$refs['dialog'].open()
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
