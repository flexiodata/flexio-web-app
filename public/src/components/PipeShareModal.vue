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
