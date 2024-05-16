<template>
  <div class="home">
    <ContactForm ref="contactFormModal"></ContactForm>
    <v-container>
      <v-row justify="center">
        <v-col cols="12" sm="4">
          <v-btn class="add-contact-btn" @click="$refs.contactFormModal.openModal()">Ajouter un contact</v-btn>
          <v-alert v-if="error" text="Une erreur est survenue lors de la récupération."></v-alert>
          <v-card>
            <v-card-title>Liste des Contacts</v-card-title>
            <v-card-text>

              <v-list lines="two" v-if="contacts.all.length > 0">
                  <ContactItem v-for="(contact, index) in contacts.all" :key="index" :contact="contact" :index="index" />
              </v-list>
              <v-list v-else>
                <v-list-item>Pas de contacts disponibles</v-list-item>
              </v-list>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
import ContactItem from "@/components/ContactItem.vue";
import ContactForm from "@/components/ContactForm.vue";
import { mapState, mapActions } from 'vuex';

export default {
  name: 'HomeView',
  computed: {
    ...mapState(['contacts']),
  },
  components: {
    ContactItem,
    ContactForm
  },
  data() {
    return {
      openModal: false,
      error: false
    }
  },
  mounted() {
    this.$axios.get('/contacts')
        .then(response => {
          this.setContacts(response.data)
        })
        .catch(() => {
          this.error = true
        })
  },
  methods: {
    ...mapActions(['setContacts']),
  }
}
</script>
<style scoped>
.home {
  text-align: left;
}

.add-contact-btn {
  margin-bottom: 10px;
}
</style>