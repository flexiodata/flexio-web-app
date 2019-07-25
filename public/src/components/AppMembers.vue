<template>
  <div class="flex-fill flex flex-column bg-nearer-white" v-if="is_action_join">
    <PageNotFound
      class="flex-fill"
      v-if="!is_joining_member_logged_in"
    />
    <template v-else>
      <div class="h3"></div>
      <div class="w-100 center mw-doc pa4 bg-white br2 css-white-box" style="max-width: 36rem">
        <div class="tc">
          <i class="material-icons moon-gray" style="font-size: 4rem">people</i>
          <h3 class="flex-fill mt2 fw6 f3">{{join_title}}</h3>
        </div>
        <p>You've been invited to become a member of the team <strong>"{{active_team_name}}"</strong> on Flex.io. Would you like to join this team?</p>
        <div class="h2"></div>
        <div class="flex flex-row items-center justify-end">
          <el-button
            type="text"
            @click="leaveTeam"
          >
            No thanks, please remove me
          </el-button>
          <el-button
            class="ttu fw6"
            type="primary"
            @click="joinTeam"
          >
            Yes, I want to join
          </el-button>
        </div>
      </div>
    </template>
  </div>

  <!-- fetching -->
  <div v-else-if="is_fetching">
    <div class="flex flex-column justify-center h-100 bg-nearer-white">
      <Spinner size="large" message="Loading members..." />
    </div>
  </div>

  <!-- fetched -->
  <div class="flex flex-column bg-nearer-white" v-else-if="is_fetched">
    <div class="h3"></div>
    <div class="w-100 center mw-doc pa4 bg-white br2 css-white-box" style="min-height: 20rem">
      <div class="flex flex-row items-start">
        <h3 class="flex-fill mt0 fw6 f3">Team Members</h3>
        <el-button
          class="ttu fw6"
          type="primary"
          @click="show_add_dialog = true"
        >
          Add Members
        </el-button>
      </div>
      <table class="el-table w-100 mv3">
        <tbody>
          <MemberItem
            :key="member.eid"
            :item="member"
            @resend-invite="resendInvite"
            @remove-member="removeMember"
            v-for="member in members"
          />
        </tbody>
      </table>
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
            @keydown.native.space="addUserTag"
            @keydown.native.tab="addUserTag"
            @keydown.native.188="addUserTag"
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
  import MemberItem from '@/components/MemberItem'
  import PageNotFound from '@/components/PageNotFound'

  export default {
    metaInfo() {
      return {
        title: 'Members',
        titleTemplate: (chunk) => {
          if (this.is_action_join) {
            return `${this.join_title} | Flex.io`
          }

          return chunk ? `${chunk} | ${this.getActiveTeamLabel()}` : 'Flex.io'
        }
      }
    },
    components: {
      Spinner,
      MemberItem,
      PageNotFound
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
        is_fetching: state => state.members.is_fetching,
        is_fetched: state => state.members.is_fetched,
        active_team_name: state => state.teams.active_team_name,
        active_user_eid: state => state.users.active_user_eid,
      }),
      members() {
        return this.getAllMembers()
      },
      joining_member_email() {
        return _.get(this.$route, 'query.email', '')
      },
      is_joining_member_logged_in() {
        return this.joining_member_email == this.getActiveUserEmail()
      },
      is_action_join() {
        return _.get(this.$route, 'params.action') == 'join'
      },
      join_title() {
        return `Join Team "${this.active_team_name}"`
      }
    },
    created() {
      if (!this.is_action_join) {
        this.tryFetchMembers()
      }
    },
    methods: {
      ...mapGetters('users', {
        'getActiveUserEmail': 'getActiveUserEmail'
      }),
      ...mapGetters('members', {
        'getAllMembers': 'getAllMembers'
      }),
      ...mapGetters('teams', {
        'getActiveTeamLabel': 'getActiveTeamLabel'
      }),
      tryFetchMembers() {
        if (!this.is_fetched && !this.is_fetching) {
          var team_name = this.active_team_name

          this.$store.dispatch('members/fetch', { team_name })
        }
      },
      sendInvites() {
        var timeout = 20

        // quick hack to allow multiple users to be added until the API supports it
        _.forEach(this.add_dialog_model.users, user => {
          setTimeout(() => { this.sendInvite(user) }, timeout)
          timeout += 40
        })

        this.show_add_dialog = false
      },
      sendInvite(member) {
        var team_name = this.active_team_name
        var attrs = { member }
        this.$store.dispatch('members/create', { team_name, attrs })
      },
      resendInvite(member) {
        var team_name = this.active_team_name
        var eid = _.get(member, 'eid')
        this.$store.dispatch('members/resendInvite', { team_name, eid })
      },
      removeMember(member) {
        var team_name = this.active_team_name
        var eid = member.eid

        this.$store.dispatch('members/delete', { team_name, eid })
      },
      leaveTeam() {
        var team_name = this.active_team_name
        var eid = this.active_user_eid
        this.$store.dispatch('members/delete', { team_name, eid })
      },
      joinTeam() {
        var team_name = this.active_team_name
        var eid = this.active_user_eid
        var attrs = { member_status: 'A' }
        this.$store.dispatch('members/update', { team_name, eid, attrs })
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
