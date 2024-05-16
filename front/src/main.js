import axios from './plugins/axios'
import store from "@/store";
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

const app = createApp(App);

const vuetify = createVuetify({
  components,
  directives,
  icons: {
    defaultSet: 'mdi'
  }
})

app.config.globalProperties.$axios = axios;

app
  .use(vuetify)
  .use(router)
  .use(store)
  .mount('#app')
