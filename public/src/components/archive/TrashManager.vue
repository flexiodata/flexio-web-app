<template>
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <spinner size="large" message="Loading trash..."></spinner>
    </div>
  </div>
  <div v-else>
    <!-- control bar -->
    <div class="pa3 pa4-l pb3-l bb bb-0-l b--black-10" style="max-width: 1152px">
      <div class="flex flex-row">
        <div class="flex-fill flex flex-row items-center">
          <div class="f2 dn db-ns mr3">Trash</div>
          <el-input
            class="w-100 mw5 mr3"
            placeholder="Filter items..."
            @keydown.esc.native="filter = ''"
            v-model="filter"
          />
        </div>
        <div class="flex-none flex flex-row items-center">
          <btn btn-md class="btn-confirm ttu b ba b--dark-red white bg-dark-red" :class="{ disabled: trash_cnt == 0 }" @click="openConfirmModal">Empty trash</btn>
        </div>
      </div>
    </div>

    <!-- list -->
    <trash-list
      class="pl4-l pr4-l pb4-l h-100"
      style="max-width: 1152px"
      :filter="filter"
    ></trash-list>

    <!-- confirm modal -->
    <confirm-modal
      ref="modal-confirm"
      title="Empty trash?"
      submit-label="Delete forever"
      cancel-label="Cancel"
      @confirm="onConfirmModalClose"
      @hide="show_confirm_modal = false"
      v-if="show_confirm_modal"
    >
      <div class="lh-copy mb3">Are you sure you want to delete all items from the trash?</div>
      <div class="ttu dark-red fw6">Warning: You can't undo this action!</div>
    </confirm-modal>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import TrashList from './TrashList.vue'
  import ConfirmModal from './ConfirmModal.vue'
  import Btn from './Btn.vue'

  export default {
    components: {
      Spinner,
      TrashList,
      ConfirmModal,
      Btn
    },
    data() {
      return {
        filter: '',
        show_confirm_modal: false
      }
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        'is_fetching': 'trash_fetching',
        'is_fetched': 'trash_fetched'
      }),
      trash_cnt() {
        return this.getAllTrash().length
      }
    },
    created() {
      this.tryFetchTrashItems()
    },
    methods: {
      ...mapGetters([
        'getAllTrash'
      ]),
      openConfirmModal() {
        this.show_confirm_modal = true
        this.$nextTick(() => { this.$refs['modal-confirm'].open() })
      },
      tryFetchTrashItems() {
        if (!this.is_fetched && !this.is_fetching)
          this.$store.dispatch('fetchTrash')
      },
      onConfirmModalClose(modal) {
        var attrs = { items: this.getAllTrash() }
        this.$store.dispatch('emptyTrash', { attrs })
        modal.close()
      }
    }
  }
</script>
