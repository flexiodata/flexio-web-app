<template>
  <!-- fetching -->
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100 bg-nearer-white">
      <Spinner size="large" message="Loading members..." />
    </div>
  </div>

  <!-- fetched -->
  <div class="flex flex-column bg-nearer-white" v-else-if="is_fetched">
    <div>
      <div class="h3"></div>
      <div class="center mw-doc pa4 bg-white br2 css-white-box" style="min-height: 20rem">
        <div class="flex flex-row items-start">
          <h3 class="flex-fill mt0 fw6 f3">Team Members</h3>
          <el-button
            class="ttu fw6"
            type="primary"
            @click="show_add_dialog = true"
          >
            Add Member
          </el-button>
        </div>
        <table class="el-table w-100 mv3">
          <tbody>
            <tr
              :key="member.eid"
              v-for="member in members"
            >
              <td class="w-100">
                <div class="flex flex-row items-center">
                  <img :src="getGravatarUrl(member)" class="br-100 mr3"/>
                  <div v-if="hasFullName(member)">
                    <div class="fw6 body-color">{{member.first_name}} {{member.last_name}}</div>
                    <div class="mt1 black-50">{{member.email}}</div>
                  </div>
                  <div v-else>
                    <div class="fw6 body-color">{{member.email}}</div>
                  </div>
                </div>
              </td>
              <td>
                <ConfirmPopover
                  placement="bottom-end"
                  title="Confirm remove?"
                  message="Are you sure you want to remove this member?"
                  confirm-button-text="Remove"
                  @confirm-click="removeMember(member)"
                  v-show="!isOwner(member)"
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
          </tbody>
        </table>
      </div>
    </div>

    <el-dialog
      width="42rem"
      top="4vh"
      title="Add Team Members"
      :modal-append-to-body="false"
      :visible.sync="show_add_dialog"
      @open="onAddDialogOpen"
      @close="onAddDialogClose"
    >
      <el-form
        ref="form"
        class="el-form--cozy el-form__label-tiny"
        label-position="top"
        :model="add_dialog_model"
      >
        <p class="f5">Enter the email addresses of the people you would like to invite to your team. New team members will get an email with a link to accept the invitation.</p>
        <el-form-item
          key="users"
          prop="users"
          label="Send invites to the following email addresses"
        >
          <el-select
            ref="email-select"
            class="w-100"
            placeholder="Enter email addresses"
            multiple
            filterable
            allow-create
            default-first-option
            popper-class="dn"
            @keydown.native.188="addUserTag"
            @keydown.native.tab="addUserTag"
            @keydown.native.space="addUserTag"
            v-model="add_dialog_model.users"
          >
            <el-option
              :label="option.label"
              :value="option.val"
              :key="option.val"
              v-for="option in add_dialog_model.options"
            />
          </el-select>
        </el-form-item>
      </el-form>
      <div class="mt4 w-100 flex flex-row justify-end">
        <el-button
          class="ttu fw6"
          @click="show_add_dialog = false"
        >
          Cancel
        </el-button>
        <el-button
          class="ttu fw6"
          type="primary"
          @click="sendInvites"
        >
          Send Invites
        </el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import ConfirmPopover from '@comp/ConfirmPopover'

  export default {
    metaInfo() {
      return {
        title: 'Members',
        titleTemplate: (chunk) => {
          // if undefined or blank then we don't need the pipe
          return chunk ? `${chunk} | ${this.getActiveTeamLabel()}` : 'Flex.io';
        }
      }
    },
    components: {
      Spinner,
      ConfirmPopover
    },
    data() {
      return {
        show_add_dialog: false,
        add_dialog_model: {
          users: [],
          options: []
        }
      }
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        'is_fetching': 'members_fetching',
        'is_fetched': 'members_fetched',
        'active_team_name': 'active_team_name'
      }),
      members() {
        return this.getAllMembers()
      }
    },
    created() {
      this.tryFetchMembers()
    },
    methods: {
      ...mapGetters([
        'getAllMembers',
        'getActiveTeamLabel'
      ]),
      tryFetchMembers() {
        if (!this.is_fetched && !this.is_fetching) {
          this.$store.dispatch('v2_action_fetchMembers', { team_name: this.active_team_name }).catch(error => {
            // TODO: add error handling?
          })
        }
      },
      sendInvites() {
        var timeout = 10

        // quick hack to allow multiple users to be added until the API supports it
        _.forEach(this.add_dialog_model.users, user => {
          setTimeout(() => {
            var attrs = { member: user }

            this.$store.dispatch('v2_action_createMember', { team_name: this.active_team_name, attrs }).catch(error => {
              // TODO: add error handling?
            })
          }, timeout)

          timeout += 40
        })

        this.show_add_dialog = false
      },
      getGravatarUrl(member) {
        return 'https://secure.gravatar.com/avatar/' + member.email_hash + '?d=mm&s=40'
      },
      hasFullName(member) {
        return _.get(member, 'first_name', '').length > 0 && _.get(member, 'last_name', '').length > 0
      },
      isOwner(member) {
        return _.get(member, 'username') == this.active_team_name
      },
      removeMember(member) {
        var eid = member.eid
        this.$store.dispatch('v2_action_deleteMember', { team_name: this.active_team_name, eid }).catch(error => {
          // TODO: add error handling?
        })
      },
      addUserTag() {
        var $select = this.$refs['email-select']
        var query = _.get($select, '$data.query', '')
        if (query.length > 0) {
          this.add_dialog_model.users = this.add_dialog_model.users.concat([query])
        }
        this.$nextTick(() => $select.$refs.input.focus())
      },
      onAddDialogOpen() {
        this.$nextTick(() => this.$refs['email-select'].focus())
      },
      onAddDialogClose() {
        this.add_dialog_model = {
          users: [],
          options: []
        }
      }
    }
  }
</script>
