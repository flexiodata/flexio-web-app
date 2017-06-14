<template>
  <ui-modal
    ref="dialog"
    class="has-footer"
    @hide="onHide"
  >
    <div slot="header" class="f4">Share '{{pipe.name}}'</div>

    <member-add-form
      :object-eid="pipe_eid"
      v-if="pipe_eid.length > 0 && is_owner"
    ></member-add-form>

    <!-- list -->
    <member-list
      style="min-height: 260px"
      :object-eid="pipe_eid"
      :owner-eid="owner_eid"
      v-if="pipe_eid.length > 0"
    ></member-list>

  </ui-modal>
</template>

<script>
  import { mapState } from 'vuex'
  import MemberAddForm from './MemberAddForm.vue'
  import MemberList from './MemberList.vue'

  const defaultAttrs = () => {
    return {
      eid: '',
      name: ''
    }
  }

  export default {
    components: {
      MemberAddForm,
      MemberList
    },
    data() {
      return {
        pipe: _.extend({}, defaultAttrs())
      }
    },
    computed: {
      ...mapState([
        'active_user_eid'
      ]),
      pipe_eid() {
        return _.get(this.pipe, 'eid', '')
      },
      owner_eid() {
        return _.get(this.pipe, 'owned_by.eid', '')
      },
      is_owner() {
        return this.owner_eid == this.active_user_eid
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
