import Vue from 'vue'
import lang from 'element-ui/lib/locale/lang/en'
import locale from 'element-ui/lib/locale'
import {
  Alert,
  Breadcrumb,
  BreadcrumbItem,
  Button,
  Checkbox,
  CheckboxButton,
  CheckboxGroup,
  Collapse,
  CollapseItem,
  DatePicker,
  Dialog,
  Dropdown,
  DropdownItem,
  DropdownMenu,
  Form,
  FormItem,
  Icon,
  Input,
  Menu,
  MenuItem,
  Message,
  MessageBox,
  Option,
  Pagination,
  Popover,
  Radio,
  RadioButton,
  RadioGroup,
  Select,
  Step,
  Steps,
  Switch,
  Table,
  TableColumn,
  Tabs,
  TabPane,
  Tag
} from 'element-ui'

// setup for Element UI

locale.use(lang)

Vue.use(Alert)
Vue.use(Breadcrumb)
Vue.use(BreadcrumbItem)
Vue.use(Button)
Vue.use(Checkbox)
Vue.use(CheckboxButton)
Vue.use(CheckboxGroup)
Vue.use(Collapse)
Vue.use(CollapseItem)
Vue.use(DatePicker)
Vue.use(Dialog)
Vue.use(Dropdown)
Vue.use(DropdownItem)
Vue.use(DropdownMenu)
Vue.use(Form)
Vue.use(FormItem)
Vue.use(Icon)
Vue.use(Input)
Vue.use(Menu)
Vue.use(MenuItem)
Vue.use(Option)
Vue.use(Pagination)
Vue.use(Popover)
Vue.use(Radio)
Vue.use(RadioButton)
Vue.use(RadioGroup)
Vue.use(Select)
Vue.use(Step)
Vue.use(Steps)
Vue.use(Switch)
Vue.use(Table)
Vue.use(TableColumn)
Vue.use(Tabs)
Vue.use(TabPane)
Vue.use(Tag)

Vue.prototype.$message = Message
Vue.prototype.$msgbox = MessageBox
Vue.prototype.$alert = MessageBox.alert
Vue.prototype.$confirm = MessageBox.confirm
Vue.prototype.$prompt = MessageBox.prompt
