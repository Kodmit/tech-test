export default {
  state: {
    all: [],
  },
  mutations: {
    setContacts(state, contacts) {
      state.all = contacts;
    },
    addContact(state, contact) {
      state.all.push(contact);
    },
    deleteContact(state, index) {
      state.all.splice(index, 1);
    },
    updateContact(state, { index, updatedContact }) {
      state.all.splice(index, 1, updatedContact);
    },
  },
  actions: {
    setContacts({ commit }, contacts) {
      commit('setContacts', contacts);
    },
    addContact({ commit }, contact) {
      commit('addContact', contact);
    },
    deleteContact({ commit }, index) {
      commit('deleteContact', index);
    },
    updateContact({ commit }, payload) {
      commit('updateContact', payload);
    },
  }
}