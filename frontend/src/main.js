import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

// Toast
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'

// FontAwesome
import '@fortawesome/fontawesome-free/css/all.css'

// PrimeVue (VERSI 3)
import PrimeVue from 'primevue/config'
import 'primevue/resources/themes/saga-blue/theme.css'   // ✔ tema yg pasti ada
import 'primevue/resources/primevue.min.css'             // ✔ css utama
import 'primeicons/primeicons.css'

// Components PrimeVue
import Button from 'primevue/button'
import Toolbar from 'primevue/toolbar'
import InputText from 'primevue/inputtext'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Dialog from 'primevue/dialog'
import Dropdown from 'primevue/dropdown'
import Password from 'primevue/password'
import Textarea from 'primevue/textarea'
import Divider from 'primevue/divider'
import IconField from 'primevue/iconfield'
import InputIcon from 'primevue/inputicon'
import './assets/main.css'


// App Init
const app = createApp(App)

app.use(router)
app.use(Toast)
app.use(PrimeVue, { ripple: true })

// Register Components
app.component('Button', Button)
app.component('Toolbar', Toolbar)
app.component('InputText', InputText)
app.component('DataTable', DataTable)
app.component('Column', Column)
app.component('Dialog', Dialog)
app.component('Dropdown', Dropdown)
app.component('Password', Password)
app.component('Textarea', Textarea)
app.component('Divider', Divider)
app.component('IconField', IconField)
app.component('InputIcon', InputIcon)

app.mount('#app')
