<template>
  <v-dialog
    v-model="dialog"
    persistent
    max-width="600"
    @keydown.esc="dialog = false"
  >
    <v-card
      :loading="loading"
    >
      <template slot="progress">
        <v-progress-linear
          color="secondary"
          height="10"
          indeterminate
        ></v-progress-linear>
      </template>
      <v-toolbar dense color="secondary">
        <v-toolbar-title class="white--text">Nuevo usuario</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn
          icon
          @click.stop="dialog = false"
        >
          <v-icon color="white">
            mdi-close
          </v-icon>
        </v-btn>
      </v-toolbar>
      <div class="px-5 pb-5">
        <validation-observer ref="userObserver" v-slot="{ invalid }">
          <form v-on:submit.prevent="submit">
            <v-card-text>
              <validation-provider
                v-slot="{ errors }"
                name="name"
                rules="required|min:3|alpha_spaces"
              >
                <v-text-field
                  label="Nombre"
                  v-model="userForm.name"
                  data-vv-name="name"
                  :error-messages="errors"
                  prepend-icon="mdi-account"
                  autofocus
                ></v-text-field>
              </validation-provider>
              <validation-provider
                v-slot="{ errors }"
                name="document_type_id"
                rules="required"
              >
                <v-select
                  :items="documentTypes"
                  item-text="name"
                  item-value="id"
                  label="Tipo de documento"
                  v-model="userForm.document_type_id"
                  data-vv-name="document_type_id"
                  :error-messages="errors"
                  prepend-icon="mdi-card-account-details-outline"
                ></v-select>
              </validation-provider>
              <validation-provider
                v-slot="{ errors }"
                name="username"
                rules="required|min:3|integer"
              >
                <v-text-field
                  label="Documento de Identidad"
                  v-model="userForm.username"
                  data-vv-name="username"
                  :error-messages="errors"
                  prepend-icon="mdi-card-account-details"
                ></v-text-field>
              </validation-provider>
              <validation-provider
                v-slot="{ errors }"
                name="password"
                :rules="edit ? '' : 'required|min:4'"
              >
                <v-text-field
                  label="Contraseña"
                  v-model="userForm.password"
                  data-vv-name="password"
                  :error-messages="errors"
                  prepend-icon="mdi-lock"
                  :append-icon="shadowPassword ? 'mdi-eye' : 'mdi-eye-off'"
                  @click:append="() => (shadowPassword = !shadowPassword)"
                  :type="shadowPassword ? 'password' : 'text'"
                ></v-text-field>
              </validation-provider>
              <validation-provider
                v-slot="{ errors }"
                name="email"
                rules="required|email"
              >
                <v-text-field
                  label="Email"
                  v-model="userForm.email"
                  data-vv-name="email"
                  :error-messages="errors"
                  prepend-icon="mdi-at"
                ></v-text-field>
              </validation-provider>
              <validation-provider
                v-slot="{ errors }"
                name="phone"
                rules="required|min:7|integer"
              >
                <v-text-field
                  label="Teléfono"
                  v-model="userForm.phone"
                  data-vv-name="phone"
                  :error-messages="errors"
                  prepend-icon="mdi-account"
                ></v-text-field>
              </validation-provider>
              <validation-provider
                v-slot="{ errors }"
                name="address"
                rules="required|min:3"
              >
                <v-text-field
                  label="Dirección"
                  v-model="userForm.address"
                  data-vv-name="address"
                  :error-messages="errors"
                  prepend-icon="mdi-map-marker"
                ></v-text-field>
              </validation-provider>
              <validation-provider
                v-slot="{ errors }"
                name="area_id"
                rules="required"
              >
                <v-select
                  :items="areas"
                  item-text="name"
                  item-value="id"
                  label="Área"
                  v-model="userForm.area_id"
                  data-vv-name="area_id"
                  :error-messages="errors"
                  prepend-icon="mdi-briefcase"
                ></v-select>
              </validation-provider>
              <validation-provider
                v-slot="{ errors }"
                name="roles"
                rules="required"
              >
                <v-select
                  v-model="userForm.roles"
                  :items="roles"
                  item-text="name"
                  item-value="id"
                  label="Roles"
                  data-vv-name="roles"
                  :error-messages="errors"
                  prepend-icon="mdi-key"
                  multiple
                ></v-select>
              </validation-provider>
            </v-card-text>
            <v-card-actions>
              <v-btn
                block
                type="submit"
                color="primary"
                :disabled="invalid || loading"
              >
                Guardar
              </v-btn>
            </v-card-actions>
          </form>
        </validation-observer>
      </div>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: 'UserForm',
  props: {
    roles: {
      type: Array,
      required: true
    },
    areas: {
      type: Array,
      required: true
    },
    documentTypes: {
      type: Array,
      required: true
    },
  },
  data: function() {
    return {
      dialog: false,
      edit: false,
      shadowPassword: true,
      userForm: {
        name: '',
        username: '',
        password: '',
        email: '',
        address: '',
        phone: '',
        document_type_id: null,
        area_id: null,
        roles: [],
      },
      loading: false,
    }
  },
  methods: {
    showDialog(user = null) {
      if (user) {
        this.edit = true
        this.userForm = {
          ...user
        }
      } else {
        this.edit = false
        this.userForm = {
          name: '',
          username: '',
          password: '',
          email: '',
          address: '',
          phone: '',
          document_type_id: null,
          area_id: null,
          roles: [],
        }
      }
      this.dialog = true
      this.$nextTick(() => {
        this.$refs.userObserver.reset()
      })
    },
    async submit() {
      try {
        let valid = await this.$refs.userObserver.validate()
        if (valid) {
          this.loading = true
          if (this.edit) {
            await axios.patch(`user/${this.userForm.id}`, Object.fromEntries(Object.entries(this.userForm).filter(([_, v]) => v != '')))
          } else {
            await axios.post('user', this.userForm)
          }
          this.$toast.info('Usuario creado correctamente')
          this.$emit('updateList')
          this.dialog = false
        }
      } catch(error) {
        this.$refs.userObserver.reset()
        if ('errors' in error.response.data) {
          this.$refs.userObserver.setErrors(error.response.data.errors)
        }
      } finally {
        this.edit = false
        this.loading = false
      }
    }
  },
}
</script>