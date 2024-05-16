import { createStore } from 'vuex'
import contacts from './contacts'

export default new createStore({
  modules: {
    contacts,
  },
})