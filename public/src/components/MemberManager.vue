<template>
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <spinner size="large" message="Loading members..."></spinner>
    </div>
  </div>
  <div v-else>
    <!-- control bar -->
    <div class="flex-none flex flex-row ph2 ph0-l mh0 mh3-l pt2 pt3-l pb2 bb bb-0-l b--black-10">
      <div class="flex-fill">
        <input
          type="text"
          class="input-reset ba b--black-20 focus-b--transparent focus-outline focus-ow1 focus-o--blue pa2 w-90 w-50-m w-30-l min-w5-m min-w5a-l f6"
          placeholder="Filter members..."
          @keydown.esc="filter = ''"
          v-model="filter"
        >
      </div>
      <div class="flex-none">
        <btn btn-md btn-primary class="btn-add ttu b ba" @click="openModal('modal-add')">Add members</btn>
      </div>
    </div>

    <!-- list -->
    <member-list
      :filter="filter"
      :project-eid="projectEid"
      @item-delete="tryDeleteMember"
      class="flex-fill overflow-auto"
    ></member-list>

    <!-- add modal -->
    <member-add-modal
      open-from=".btn-add"
      close-to=".btn-add"
      ref="modal-add"
      @submit="tryAddMembers"
    ></member-add-modal>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import MemberList from './MemberList.vue'
  import MemberAddModal from './MemberAddModal.vue'
  import Btn from './Btn.vue'

  export default {
    props: ['project-eid'],
    components: {
      Spinner,
      MemberList,
      MemberAddModal,
      Btn
    },
    data() {
      return {
        filter: ''
      }
    },
    computed: {
      is_fetched() {
        return _.get(_.find(this.getAllProjects(), { eid: this.projectEid }), 'members_fetched', false)
      },
      is_fetching() {
        return _.get(_.find(this.getAllProjects(), { eid: this.projectEid }), 'members_fetching', true)
      }
    },
    created() {
      if (!this.projectEid)
        return

      this.tryFetchMembers()
    },
    methods: {
      ...mapGetters([
        'getAllProjects'
      ]),
      openModal(ref) {
        this.$refs[ref].open()
      },
      tryFetchMembers() {
        if (!this.is_fetched)
          this.$store.dispatch('fetchMembers', this.projectEid)
      },
      tryAddMembers(attrs, modal) {
        var project_eid = this.projectEid

        this.$store.dispatch('createMembers', { project_eid, attrs }).then(response => {
          if (response.ok)
          {
            modal.close()
          }
           else
          {
            // TODO: add error handling
          }
        })
      },
      tryDeleteMember(item) {
        var project_eid = this.projectEid
        var eid = _.get(item, 'eid')

        this.$store.dispatch('deleteMember', { project_eid, eid }).then(response => {
          if (response.ok)
          {
            modal.close()
          }
           else
          {
            // TODO: add error handling
          }
        })
      }
    }
  }
</script>
