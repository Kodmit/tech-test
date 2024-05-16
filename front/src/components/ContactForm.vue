<template>
  <div class="contact-form">
    <v-dialog max-width="500" v-model="isOpen">
        <v-card :title="true === editMode ? 'Modifier le contact' : 'Ajouter un contact'" >
          <v-sheet class="mx-auto" width="300">
            <v-form v-model="formValid" fast-fail @submit.prevent="onSubmit()">
              <v-text-field
                  v-model="form.firstName"
                  :rules="rules.namesRules"
                  variant="outlined"
                  label="Prénom"
              ></v-text-field>

              <v-text-field
                  v-model="form.lastName"
                  :rules="rules.namesRules"
                  variant="outlined"
                  label="Nom"
              ></v-text-field>

              <v-text-field
                  v-model="form.email"
                  :rules="rules.emailRule"
                  variant="outlined"
                  label="Email"
              ></v-text-field>

              <v-text-field
                  v-model="form.phoneNumber"
                  :rules="rules.phoneNumberRule"
                  variant="outlined"
                  label="Numéro de téléphone"
              ></v-text-field>

              <v-btn :disabled="!formValid" :loading="loading" class="mt-2" type="submit" block>Sauvegarder</v-btn>
            </v-form>
          </v-sheet>

          <v-card-actions>
            <v-spacer></v-spacer>

            <v-btn
                text="Fermer"
                @click="closeModal()"
            ></v-btn>
          </v-card-actions>
        </v-card>
    </v-dialog>
  </div>
</template>

<script>
import {mapActions} from "vuex";

export default {
  name: 'ContactForm',
  data() {
    return {
      isOpen: false,
      loading: false,
      editMode: false,
      contactIndex: null,
      formValid: false,
      form: {
        firstName: null,
        lastName: null,
        email: null,
        phoneNumber: null,
      },
      rules: {
        namesRules: [
          value => {
            if (value?.length > 2 && value?.length < 255) return true
            return 'La valeur doit être comprise entre 2 et 255 caractères/'
          },
        ],
        emailRule: [
          value => {
            if (/^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(value)) return true
            return 'Email invalide.'
          },
        ],
        phoneNumberRule: [
          value => {
            if (/^0\d{9}$/.test(value)) return true
            return 'Numéro de téléphone invalide.'
          },
        ],
      }
    }
  },
  methods: {
    ...mapActions(['addContact', 'updateContact']),
    onSubmit() {
      this.loading = true;
      this.$axios.request({
        url: true === this.editMode ? '/contacts/' + this.form.id : '/contacts',
        data: this.form,
        method: true === this.editMode ? 'PUT' : 'POST'
      })
          .then(response => {
            this.loading = false;

            if (true === this.editMode) {
              this.updateContact({index: this.contactIndex, updatedContact: response.data});
            } else {
              this.addContact(response.data);
            }
            this.closeModal();
          })

    },
    openModal(contact = null, index = null) {
      if (null !== contact) {
        this.form = contact;
        this.editMode = true;
        this.contactIndex = index;
      }
      this.isOpen = true;
    },
    closeModal() {
      this.isOpen = false;
      this.form = {
        firstName: null,
        lastName: null,
        email: null,
        phoneNumber: null,
      }
      this.editMode = false
    },
  }
}
</script>

<style scoped>

</style>
