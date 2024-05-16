<template>
  <v-list-item>
    <v-list-item-title>{{ contact.firstName }} {{ contact.lastName }}</v-list-item-title>
    <v-list-item-subtitle>{{ contact.email }}</v-list-item-subtitle>
    <v-list-item-subtitle>{{ contact.phoneNumber }}</v-list-item-subtitle>

    <template v-slot:prepend>
      <v-avatar color="grey-lighten-1">
        <v-icon color="white">mdi-account</v-icon>
      </v-avatar>
    </template>

    <template v-slot:append>
      <v-btn
          color="grey-lighten-1"
          icon="mdi-pencil"
          variant="text"
          @click="editContact(contact, index)"
      ></v-btn>
      <v-btn
          color="red-lighten-1"
          icon="mdi-trash-can"
          variant="text"
          :loading="loading"
          @click="deleteContact(contact.id, index)"
      ></v-btn>
    </template>
  </v-list-item>

</template>

<script>
import {mapActions} from "vuex";

export default {
  name: 'ContactItem',
  props: {
    contact: Object,
    index: Number
  },
  data() {
    return {
      loading: false
    }
  },
  mounted() {
    console.log(this.$refs);
  },
  methods: {
    ...mapActions(['deleteContact']),
    editContact(contact, index) {
      this.$refs.contactFormModal.openModal(contact, index);
    },
    deleteContact(contactId, index) {
      this.loading = true;
      this.$axios.delete('/contacts/' + contactId)
          .then(() => {
            this.deleteContact(index)
            this.loading = false;
          })
    },
  }
}
</script>

<style scoped>

</style>
