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
        <btn btn-md btn-primary class="btn-add ttu b ba" @click="openAddMemberModal">Add members</btn>
      </div>
    </div>

    <!-- list -->
    <member-list
      :filter="filter"
      @item-delete="tryDeleteMember"
      class="flex-fill overflow-auto"
    ></member-list>

    <!-- add modal -->
    <member-add-modal
      ref="modal-add-member"
      @submit="tryAddMembers"
    ></member-add-modal>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import MemberList from './MemberList.vue'
  import MemberAddModal from './MemberAddModal.vue'
  import Btn from './Btn.vue'

  export default {
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
      // mix this into the outer object with the object spread operator
      ...mapState({
        'is_fetching': 'connections_fetching',
        'is_fetched': 'connections_fetched'
      })
    },
    created() {
      this.tryFetchMembers()
    },
    methods: {
      openAddMemberModal() {
        this.$refs['modal-add-member'].open()
      },
      tryFetchMembers() {
        if (!this.is_fetched)
          this.$store.dispatch('fetchMembers')
      },
      tryAddMembers(attrs, modal) {
        this.$store.dispatch('createMembers', { attrs }).then(response => {
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
        var eid = _.get(item, 'eid')

        this.$store.dispatch('deleteMember', { eid }).then(response => {
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
