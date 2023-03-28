<!DOCTYPE html>
<html>
<head>
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
</head>
<body>
<div id="app">
    <v-app>
      <v-main>
        <v-content style="padding-top: 10%;">
            <v-container fluid>
              <v-layout align-center justify-center>
                <v-flex xs12 sm8 md4>
                  <v-card class="elevation-12">
                    <v-toolbar class="d-flex justify-space-between mb-6">
                        <v-col >
                            <v-img style="width: 100%;max-width: 190px;" src="<?=base_url('/public/imagen/consejo.jpg')?>">
                            </v-img>
                          </v-col>
                      <v-toolbar-title >Iniciar sesión</v-toolbar-title>
                      <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                      <v-form>
                        <v-text-field
                          clearable
                          v-model="usuario"
                          prepend-icon="mdi-account-circle"
                          label="Número de documento"
                          type="number"
                          :counter="15"
                        ></v-text-field>
                        <v-text-field
                          clearable
                          v-model="clave"
                          prepend-icon="mdi-lock"
                          label="Clave"
                          type="password"
                          
                        ></v-text-field>
                      </v-form>
                    </v-card-text>
      
                    <v-card-actions>
                      <v-spacer></v-spacer>
                      <v-btn color="primary" @click="loguear">Ingresar</v-btn>
                    </v-card-actions>
                  </v-card>
                </v-flex>
              </v-layout>
            </v-container>
          </v-content>
        
      </v-main>
    </v-app>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
  <script>
    new Vue({
      el: '#app',
      vuetify: new Vuetify(),
      data:{
        usuario:null,
        clave:null,
      },
      methods:{
        async loguear(){
          const formData = new FormData();
          let usuario = this.usuario;
          let clave = this.clave;
          formData.append('usuario',usuario);
          formData.append('clave',clave);
          await fetch('<?=base_url("/loguear")?>',{
            method: 'POST',
            body: formData
          }).then(response => response.text())
            .then(data => {
              let result =JSON.parse(data);
              if(result.result=='ok'){

                Swal.fire({
                  position: 'top-center',
                  icon: 'success',
                  title: 'iniciar sesión',
                  showConfirmButton: false,
                  timer: 1500,
                })
                
                location.href=result.direccion;
                
              }
              else{
                Swal.fire({
                  position: 'top-end',
                  icon: 'error',
                  title: 'Error al iniciar sesión',
                  showConfirmButton: false,
                  timer: 1500
                })
              }
            });
        }
      }
    }).mount('#login')
    

  </script>
</body>
</html>