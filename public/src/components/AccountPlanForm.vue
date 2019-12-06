<template>
  <div>
    <!-- fetching -->
    <div v-if="is_fetching">
      <div class="pa1 flex flex-row items-center">
        <Spinner size="small" />
        <span class="ml2 f6">Loading...</span>
      </div>
    </div>

    <!-- fetched -->
    <div v-else>
      <div class="flex flex-row items-center mb3">
        <div class="flex-fill f7 silver ttu fw6" v-if="!has_plan || is_editing_plan">Choose a plan</div>
        <div class="flex-fill f7 silver ttu fw6" v-else>Your Current Plan</div>
        <FreeTrialNotice class="f7 dark-green" />
      </div>

      <div
        class="flex flex-column flex-row-l items-stretch justify-between nl2 nr2"
        v-if="!has_plan || is_editing_plan"
      >
        <div
          class="flex-fill mh2 mb3 mb0-l ph3 tc br3 cursor-default"
          style="box-shadow: inset 0 -4px 12px rgba(0,0,0,0.075)"
          :class="isCurrentPlan(usage_plan, edit_plan_id) ? 'bg-blue white' : 'bg-nearer-white'"
          :key="usage_plan['id']"
          v-for="usage_plan in usage_plans"
        >
          <div class="mv4 fw6">{{usage_plan['Name']}}</div>
          <div class="mv4">
            <span class="f1">${{usage_plan['Price']}}</span><span class="f6">/user/mo</span>
          </div>
          <div class="mv4 mb3">
            <div>{{usage_plan['Executions']}} executions</div>
            <div class="mt2">{{usage_plan['Members']}} </div>
            <div class="mt2">{{usage_plan['Teams']}} </div>
          </div>
          <div class="mv3 pt2 pb1">
            <div v-if="isCurrentPlan(usage_plan, edit_plan_id)">
              <i class="el-icon-success f2" style="color: #fff"></i>
            </div>
            <el-button
              type="primary"
              class="w-100 mw5 ttu fw6"
              @click="selectPlan(usage_plan)"
              v-else-if="isPlanGreater(usage_plan, current_usage_plan)"
            >
              Select Plan
            </el-button>
            <el-button
              plain
              class="w-100 ttu fw6"
              @click="selectPlan(usage_plan)"
              v-else
            >
              Select Plan
            </el-button>
          </div>
        </div>
      </div>
      <div v-else>
        <div class="mv2 f6 br2 pv2 ph3 bg-nearer-white ba b--black-05">
          <div class="flex flex-column flex-row-l items-center justify-between">
            <div class="ph2 pv2 f4 fw6 tc">{{current_usage_plan['Name']}}</div>
            <div class="ph2 pv2 tc">
              <div>{{current_usage_plan['Executions']}} executions</div>
            </div>
            <div class="ph2 pv2">
              <span class="f1">${{current_usage_plan['Price']}}</span><span class="f6">/user/mo</span>
            </div>
            <div class="ph2 pv2">
              <el-button
                type="primary"
                class="ttu fw6"
                @click="is_editing_plan = true"
              >
                Change Plan
              </el-button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="edit_plan_id.length > 0">
        <div class="mt4 mb3 f7 silver ttu fw6" v-if="!has_plan || is_editing_plan || is_editing_seats">Choose the number of seats</div>
        <div class="mt4 mb3 f7 silver ttu fw6" v-else>Number of seats</div>
        <p class="f6">
          You have <strong>{{plan_info.seat_cnt}} seat(s)</strong> on your current plan.
          <el-button
            type="text"
            style="border: none; padding: 0"
            @click="is_editing_seats = true"
            v-show="has_plan && !is_editing_plan && !is_editing_seats"
          >
            Manage seats...
          </el-button>
        </p>
        <el-form
          class="mt3 el-form--cozy el-form__label-tiny"
          :model="$data"
          @submit.prevent.native
          v-if="!has_plan || is_editing_plan || is_editing_seats"
        >
          <el-form-item
            key="seat_cnt"
            prop="seat_cnt"
          >
            <el-select
              placeholder="Choose the number of seats"
              style="max-width: 100px"
              v-model="edit_plan_info.seat_cnt"
            >
              <el-option
                :label="option.label"
                :value="option.val"
                :key="option.val"
                v-for="option in seat_options"
              />
            </el-select>
          </el-form-item>
          <div
            class="flex flex-row items-center"
            v-if="is_editing_coupon"
          >
            <el-input
              type="text"
              placeholder="Add coupon"
              style="width: 200px; margin-right: 10px"
              clearable
              v-model="coupon_code"
            />
            <el-button
              class="ttu fw6"
              type="primary"
              @click="applyCoupon"
            >
              Apply
            </el-button>
            <el-button
              type="text"
              style="border: none; padding: 0"
              @click="cancelEditCoupon"
            >
              <i class="material-icons md-18">close</i>
            </el-button>
          </div>
          <div class="flex flex-row items-center" v-else-if="edit_plan_info.coupon_id.length > 0">
            <p class="f6">
              Coupon code <strong>{{edit_plan_info.coupon_id}}</strong> will be applied!
              <el-button
                type="text"
                style="border: none; padding: 0"
                @click="is_editing_coupon = true"
              >
                Change coupon code...
              </el-button>
            </p>
          </div>
          <div v-else>
            <el-button
              type="text"
              style="border: none; padding: 0"
              @click="is_editing_coupon = true"
            >
              Have a coupon?
            </el-button>
          </div>
        </el-form>
        <el-alert
          type="error"
          show-icon
          :title="error_msg"
          @close="error_msg = ''"
          v-if="error_msg.length > 0"
        />
        <div v-show="is_editing_plan || is_editing_seats">
          <ButtonBar
            class="bt b--black-10 mt3 pt3"
            :submit-button-text="'Save Changes'"
            :submit-button-disabled="has_plan_errors"
            @cancel-click="cancelEdit"
            @submit-click="updatePlan"
            v-if="has_plan || (subscription_id.length > 0 && !has_plan)"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import api from '@/api'
  import usage_plans from '@/data/usage-plans.yml'
  import { mapState, mapGetters } from 'vuex'
  import { isProduction } from '@/utils'
  import Spinner from 'vue-simple-spinner'
  import FreeTrialNotice from '@/components/FreeTrialNotice'
  import ButtonBar from '@/components/ButtonBar'

  const getDefaultPlanInfo = () => {
    return {
      subscription_id: '',
      subscription_item_id: '',
      plan_id: '',
      seat_cnt: 1,
      coupon_id: ''
    }
  }

  const getDefaultState = () => {
    var my_plans = _.filter(usage_plans, (p) => { return p['id'] != 'enterprise' })

    return {
      is_fetching: false,
      is_editing_plan: false,
      is_editing_seats: false,
      is_editing_coupon: false,
      coupon_code: '',
      seat_options,
      usage_plans: my_plans,
      plan_info: getDefaultPlanInfo(),
      edit_plan_info: getDefaultPlanInfo(),
      error_msg: '',
    }
  }

  var seat_options = []
  for (var i = 1; i < 100; ++i) {
    seat_options.push({ label: '' + i, val: i })
  }

  export default {
    props: {
      planInfo: {
        type: Object,
        default: () => {}
      }
    },
    components: {
      Spinner,
      FreeTrialNotice,
      ButtonBar
    },
    watch: {
      edit_plan_info: {
        handler: 'onPlanInfoChanged',
        immediate: true,
        deep: true
      }
    },
    data() {
      return getDefaultState()
    },
    computed: {
      ...mapState({
        active_user_eid: state => state.users.active_user_eid,
      }),
      edit_plan_id() {
        return _.get(this.edit_plan_info, 'plan_id', '')
      },
      // from YAML
      current_usage_plan() {
        var plan_key = isProduction() ? 'stripe_plan_id' : 'stripe_test_plan_id'
        var plan = _.find(this.usage_plans, (p) => {
          return p[plan_key] == this.edit_plan_id
        })

        return _.defaultTo(plan, {})
      },
      subscription_id() {
        return _.get(this.getActiveUser(), 'stripe_subscription_id', '')
      }
      has_plan() {
        var plan_id = _.get(this.plan_info, 'plan_id', '')
        return this.hasPlan(plan_id)
      },
      has_plan_errors() {
        if (this.edit_plan_id.length == 0)
          return true

        return false
      },
      unit_price() {
        var price = _.get(this.current_usage_plan, 'Price', 0)
        return price.toFixed(2)
      },
      discount() {
        var price = 0
        return price.toFixed(2)
      },
      item_total_amount() {
        var qty = this.edit_plan_info.seat_cnt
        var price = (this.unit_price * qty)
        return price.toFixed(2)
      },
      subtotal_amount() {
        var qty = this.edit_plan_info.seat_cnt
        var price = (this.unit_price * qty)
        return price.toFixed(2)
      },
      total_amount() {
        var qty = this.edit_plan_info.seat_cnt
        var price = (this.unit_price * qty) - this.discount
        return price.toFixed(2)
      },
    },
    mounted() {
      this.fetchPlan()
    },
    methods: {
      ...mapGetters('users', {
        'getActiveUser': 'getActiveUser'
      }),
      fetchPlan() {
        this.is_fetching = true

        api.fetchPlan().then(response => {
          this.plan_info = _.assign({}, getDefaultPlanInfo(), response.data)
          this.edit_plan_info = _.assign({}, getDefaultPlanInfo(), response.data)
          this.plan_error = ''
        }).catch(error => {
          this.plan_info = getDefaultPlanInfo()
          this.edit_plan_info = getDefaultPlanInfo()
          this.plan_error = JSON.stringify(error)
        }).finally(() => {
          this.is_fetching = false
        })
      },
      hasPlan(plan_id) {
        var plan_key = isProduction() ? 'stripe_plan_id' : 'stripe_test_plan_id'
        var plan_ids = _.map(this.usage_plans, p => p[plan_key])
        return plan_ids.indexOf(plan_id) != -1
      },
      isCurrentPlan(plan, plan_id) {
        var plan_key = isProduction() ? 'stripe_plan_id' : 'stripe_test_plan_id'
        return plan[plan_key] == plan_id
      },
      isPlanGreater(plan1, plan2) {
        if (plan1 && plan2) {
          return parseFloat(plan1['Price']) > parseFloat(plan2['Price'])
        } else {
          return true
        }
      },
      applyCoupon() {
        var coupon_id = this.coupon_code
        this.edit_plan_info = _.assign({}, this.edit_plan_info, { coupon_id })
        this.is_editing_coupon = false
      },
      cancelEditCoupon() {
        this.is_editing_coupon = false
      },
      cancelEdit() {
        this.edit_plan_info = _.assign({}, this.plan_info)
        this.is_editing_seats = false
        this.is_editing_plan = false
        this.is_editing_coupon = false
        this.error_msg = ''
      },
      selectPlan(plan) {
        var plan_key = isProduction() ? 'stripe_plan_id' : 'stripe_test_plan_id'
        var plan_id = plan[plan_key]
        this.edit_plan_info = _.assign({}, this.edit_plan_info, { plan_id })
      },
      updatePlan() {
        var payload = _.omit(this.edit_plan_info, ['subscription_id', 'discount'])

        api.updatePlan('me', payload).then(response => {
          this.plan_info = _.assign({}, getDefaultPlanInfo(), response.data)
          this.edit_plan_info = _.assign({}, getDefaultPlanInfo(), response.data)
          this.is_editing_seats = false
          this.is_editing_plan = false
          this.is_editing_coupon = false
        }).catch(error => {
          this.error_msg = _.get(error, 'response.data.error.message', '')
        })
      },
      onPlanInfoChanged(info) {
        var eid = this.active_user_eid
        var stripe_subscription_id = _.get(info, 'subscription_id', '')
        this.$store.commit('users/UPDATED_USER', { eid, item: { stripe_subscription_id } })
        this.$emit('update:planInfo', info)
      }
    }
  }
</script>

<style lang="stylus" scoped>
  th,
  td
    padding: 6px 8px
    font-size: .875rem
    line-height: 1.5
  tr:last-child
    td
      padding-bottom: 0
  tr.subtotal
    td
      padding-top: 12px
</style>
