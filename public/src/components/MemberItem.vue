<template>
  <tr>
    <td class="w-100">
      <div class="flex flex-row items-center">
        <img :src="gravatar_url" class="br-100 mr3"/>
        <div v-if="has_full_name">
          <div class="fw6 body-color">{{full_name}}</div>
          <div class="mt1 black-50">{{item.email}}</div>
        </div>
        <div v-else>
          <div class="fw6 body-color">{{item.email}}</div>
        </div>
      </div>
    </td>
    <td class="tr nowrap">
      <div class="mh3">
        <div
          v-if="is_invite_resending"
        >
          Sending invite...
        </div>
        <div
          class="flex flex-row items-center justify-end"
          v-else-if="is_invite_resent"
        >
          <i class="el-icon-success dark-green mr1"></i>
          <span class="dark-green">Invite sent!</span>
        </div>
        <el-button
          type="text"
          @click="onResendInvite"
          v-else-if="is_member_pending"
        >
          Resend invite
        </el-button>
      </div>
    </td>
    <td>
      <ConfirmPopover
        placement="bottom-end"
        title="Confirm remove?"
        message="Are you sure you want to remove this member?"
        confirm-button-text="Remove"
        @confirm-click="onRemoveMember"
        v-if="!is_member_owner"
      >
        <el-button
          slot="reference"
          class="ttu fw6"
          type="danger"
          size="small"
        >
          Remove
        </el-button>
      </ConfirmPopover>
    </td>
  </tr>
</template>

<script>
  import { OBJECT_STATUS_PENDING } from '@/constants/object-status'
  import ConfirmPopover from '@/components/ConfirmPopover'

  export default {
    props: {
      item: {
        type: Object,
        required: true
      }
    },
    components: {
      ConfirmPopover
    },
    computed: {
      gravatar_url() {
        return 'https://secure.gravatar.com/avatar/' + _.get(this.item, 'email_hash') + '?d=mm&s=40'
      },
      full_name() {
        return _.get(this.item, 'first_name', '') + ' ' + _.get(this.item, 'last_name', '')
      },
      has_full_name() {
        return this.full_name.trim().length > 0
      },
      is_member_owner() {
        return _.get(this.item, 'eid') == _.get(this.item, 'member_of.eid')
      },
      is_member_pending() {
        return _.get(this.item, 'member_status') == OBJECT_STATUS_PENDING
      },
      is_invite_resending() {
        return _.get(this.item, 'vuex_meta.is_invite_resending')
      },
      is_invite_resent() {
        return _.get(this.item, 'vuex_meta.is_invite_resent')
      }
    },
    methods: {
      onResendInvite() {
        this.$emit('resend-invite', this.item)
      },
      onRemoveMember() {
        this.$emit('remove-member', this.item)
      }
    }
  }
</script>

<style lang="stylus" scoped>
  td
    padding-left: 0.5rem
    padding-right: 0.5rem
</style>
