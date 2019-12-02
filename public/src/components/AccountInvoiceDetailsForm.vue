<template>
  <div>
    <el-form
      class="mt3 el-form--cozy el-form__label-tiny"
      :model="$data"
      @submit.prevent.native
    >
      <el-form-item
        key="invoice_email"
        label="Send invoices to"
        prop="invoice_email"
      >
        <el-input
          placeholder="e.g. accounting@example.com"
          auto-complete="off"
          v-model="invoice_email"
        />
      </el-form-item>
      <el-form-item
        key="invoice_memo"
        label="Additional invoice content"
        prop="invoice_memo"
      >
        <el-input
          type="textarea"
          auto-complete="off"
          :rows="10"
          v-model="invoice_memo"
        />
      </el-form-item>
      <div class="nt2 f8 fw6 black-40">
        <em>If you need additional information such as your company address, VAT information, or anything else listed on invoices, please include it above.</em>
      </div>

    </el-form>
    <ButtonBar
      class="mt4"
      :submit-button-text="'Save changes'"
      @cancel-click="resetForm"
      v-if="is_editing"
    />
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import ButtonBar from '@/components/ButtonBar'

  export default {
    components: {
      ButtonBar
    },
    watch: {
      invoice_email: {
        handler: 'makeDirty'
      },
      invoice_memo: {
        handler: 'makeDirty'
      }
    },
    data() {
      return {
        is_editing: false,
        invoice_email: '',
        invoice_memo: ''
      }
    },
    computed: {
      ...mapState({
        active_user_eid: state => state.users.active_user_eid,
      }),
    },
    methods: {
      ...mapGetters('users', {
        'getActiveUser': 'getActiveUser'
      }),
      resetForm() {
        this.invoice_email = ''
        this.invoice_memo = ''
        this.$nextTick(() => { this.is_editing = false })
      },
      makeDirty(val) {
        this.is_editing = true
      }
    }
  }
</script>
