<?php

session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: ".base_url());
  exit(); 
}
?>
<!DOCTYPE html>
<html>
<head>
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
</head>
<body>
  <div id="app">
    <v-app>
      <v-main>
        <v-row>
            <!-- encabezado -->
            <v-toolbar style="height: 90px;">
                <v-col cols="2" class="mt-10">
                  <v-img src="<?=base_url('/public/imagen/consejo.jpg')?>">
                  </v-img>
                </v-col>
                <v-toolbar-title style="justify-content: center;">
                   <h4 class="mt-10"> SEGUIMIENTO OFICINA DEL DESPACHO DE LA PRESIDENCIA</h4> 
                </v-toolbar-title>
                <v-col class="mt-10 d-flex justify-end">
                  <v-btn
                    class="mx-2"
                    fab
                    dark
                    small
                    color="brown lighten-1"
                    @click="cerraLogin"
                  >
                    <v-icon dark>
                      mdi-close
                    </v-icon>
                  </v-btn>
                </v-col>
            </v-toolbar>
        </v-row>
        <v-row>
            <v-col cols="6">
                <v-card-text>
                    <v-text-field
                        v-model="search"
                        solo
                        rounded
                        label="Buscar"
                        append-icon="mdi-magnify"
                        single-line
                        hide-details
                        @keyup.enter="buscar"
                    ></v-text-field>
                  </v-card-text>
            </v-col>
            <v-row class="d-flex justify-end mb-6">
              <v-col cols="3">
                <v-btn
                  class="mx-2 mt-5"
                  fab
                  dark
                  color="orange lighten-1"
                  @click="bModalRegistro=true"
                >
                  <v-icon dark>
                    mdi-plus
                  </v-icon>
                </v-btn>
              </v-col>
              <v-col cols="3">
                <v-btn
                  class="mx-2 mt-5"
                  fab
                  dark
                  color="green lighten-2"
                  @click="descarExcel"
                >
                  <v-icon dark>
                    mdi-file-excel
                  </v-icon>
                </v-btn>
              </v-col>
            </v-row>
        </v-row>
        <!-- Filtros -->
        <v-row class="d-flex justify-space-around " style="margin: 10px;padding: 1px;">
          <v-col >
            <v-autocomplete
              v-model="filtrarAlerta"
              @change="filtrar"
              label="Filtrar Alertas "
              :items="aAlerta"
              item-text="nombre"
              item-value="id"
              clearable>          
            </v-autocomplete>
          </v-col>
          <v-col >
            <v-autocomplete
              v-model="filtrarAsunto"
              @change="filtrar"
              label="Filtrar Asunto "
              :items="aAsuntos"
              item-text="nombre"
              item-value="id_asunto"
              clearable>
            </v-autocomplete>
          </v-col>
          <v-col>
            <v-autocomplete
              v-model="filtrarRemitente"
              @change="filtrar"
              label="Filtrar Remitente"
              :items="aRemitente"
              item-text="nombre"
              item-value="id_remitente_Destinatario"
              clearable>
            </v-autocomplete> 
          </v-col>
          <v-col >
            <v-autocomplete
              v-model="filtrarDestinatario"
              @change="filtrar"
              label="Filtrar Destinatario"
              :items="aDestinatario"
              item-text="nombre"
              item-value="id_remitente_Destinatario"
              clearable>
            </v-autocomplete>
          </v-col>
        </v-row>
        <v-row class="ml-5 my-10">
          <v-col>
            <v-simple-table dense>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">
                      No. Registro
                    </th>
                    <th class="text-left" style="min-width: 120px;">
                      Fecha
                    </th>
                    <th class="text-left" style="min-width: 200px;">
                      Alertas
                    </th>
                    <th class="text-left">
                      Asunto
                    </th>
                    <th class="text-left">
                      Remitente
                    </th>
                    <th class="text-left">
                      Destinatario
                    </th>
                    <th class="text-left" >
                      Término para contestar
                    </th>
                    <th class="text-left" style="min-width: 150px;">
                      Estado
                    </th>
                    <th class="text-left">
                      Medio de recepción
                    </th>
                    <th class="text-left" style="min-width: 220px;">
                      Observaciones
                    </th>
                    <th class="text-left">
                      Acciones
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    style="height:10px !important;"
                    v-for="(item,i) in aDatosMostrar"
                    :key="item.id"
                  >
                    <td>
                      <v-text-field
                          v-model="item.n_registro"
                          class="my-0" 
                      ></v-text-field>
                    </td>
                    <td @click="cargarModalFecha(item)">
                      <v-text-field
                        disabled
                        v-model="item.fecha"
                        class="my-0" 
                      ></v-text-field>
                    </td>
                    <!-- Alertas -->
                    <td>
                      <v-alert
                      v-if="item.diasVencidos==0"
                      dense
                      disabled
                      outlined
                      type="info"
                      text
                      > Vence hoy</v-alert>
                      <v-alert
                      v-else-if="item.diasVencidos<0"
                      dense
                      disabled
                      outlined
                      type="error"
                      text
                      > Vencido</v-alert>
                      <v-alert
                        v-else-if="item.diasVencidos<=4"
                        dense
                        outlined
                        disabled
                        type="warning"
                        text
                      >Falta {{item.diasVencidos}} día</v-alert>
                      <v-alert
                      v-else
                      dense
                      disabled
                      outlined
                      type="success"
                      text
                      >Tiene {{item.diasVencidos}} día</v-alert>
                    </td>
                    <td>
                      <v-select
                        v-model="item.id_asunto"
                        :items="aAsuntos"
                        item-text="nombre"
                        item-value="id_asunto"
                        @change="calcularDias(item)"
                      ></v-select>
                    </td>
                    <td>
                      <v-select
                        v-model="item.id_remitente"
                        :items="aRemitente"
                        item-text="nombre"
                        item-value="id_remitente_Destinatario"
                      ></v-select>
                    </td>
                    <td>
                      <v-select
                        v-model="item.id_destinatario"
                        :items="aDestinatario"
                        item-text="nombre"
                        item-value="id_remitente_Destinatario"
                      ></v-select>
                    </td>
                    <td>
                      <v-text-field
                        v-model="item.dias"
                        class="my-0"
                      ></v-text-field>
                    </td>
                    <td>
                      <v-select
                        v-model="item.id_estado"
                        :items="aEstado"
                        item-text="nombre"
                        item-value="id_estado"
                      ></v-select>
                    </td>
                    <td>
                      <v-select
                        v-model="item.id_medio"
                        :items="aMedio"
                        item-text="nombre"
                        item-value="id_medio"
                      ></v-select>
                    </td>
                    <td>
                      <v-textarea
                        v-model="item.observacion"
                        class="my-0"
                        rows="1"
                        value=""
                      ></v-textarea>
                    </td>
                    <td>
                      <v-row>
                        <v-col cols="5">
                          <v-btn
                          class="ma-2"
                          text
                          icon
                          color="black"
                          @click="eliminarFila(item.id_documento)"
                          >
                          <v-icon>mdi-delete</v-icon>
                          </v-btn>
                        </v-col>
                        <v-col cols="5">
                          <v-btn
                          class="ma-2"
                          text
                          icon
                          color="black"
                          @click="guardarFila(item)"
                          >
                          <v-icon>mdi-content-save-outline</v-icon>
                          </v-btn>
                        </v-col>
                      </v-row>
                    </td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
            <div class="text-center">
              <v-pagination
                v-model="nPagina"
                :length="nCantidadPaginas"
              ></v-pagination>
            </div>
          </v-col>
        </v-row>

        <!-- modal de agregar la informacion -->
        <v-dialog
          v-model="bModalRegistro"
          persistent
          scrollable
          max-width="600px"
        >
          <v-card>
            <v-card-title>
              <span class="text-h5">Agregar la informacion de la tabla</span>
            </v-card-title>
            <v-card-text>
              <v-container>
                <v-row>
                  <v-col
                    cols="12"
                    sm="6"
                    md="2"
                  >
                  <v-text-field
                    v-model="oDatosGuardar.n_registro"
                    class="my-0" 
                    label="No. Registro"
                  ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                    sm="6"
                    md="3"
                    @click="cargarModalFecha(oDatosGuardar)"
                  >
                  <v-text-field
                    disabled
                    v-model="oDatosGuardar.fecha"
                    class="my-0" 
                    label="Fecha"
                  ></v-text-field>
                  </v-col>
                  <v-col 
                    cols="12"
                    sm="6"
                  >
                    <v-select
                      v-model="oDatosGuardar.id_asunto"
                      :items="aAsuntos"
                      item-text="nombre"
                      item-value="id_asunto"
                      label="Asunto"
                      @change="calcularDias(oDatosGuardar)"
                    ></v-select>
                  </v-col>
                  <v-col 
                    cols="12"
                    sm="6"
                  >
                    <v-select
                      v-model="oDatosGuardar.id_remitente"
                      :items="aRemitente"
                      item-text="nombre"
                      label="Remitente"
                      item-value="id_remitente_Destinatario"
                    ></v-select>
                  </v-col>
                  <v-col
                    cols="12"
                    sm="6"
                  >
                    <v-select
                      v-model="oDatosGuardar.id_destinatario"
                      :items="aDestinatario"
                      item-text="nombre"
                      label="Destinatario"
                      item-value="id_remitente_Destinatario"
                    ></v-select>
                  </v-col>
                  <v-col
                    cols="12"
                    sm="6"
                    md="4"
                  >
                  <v-text-field
                    label=" Término para contestar"
                    v-model="oDatosGuardar.dias"
                    class="my-0" 
                  ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                    sm="6"
                  >
                  <v-select
                    v-model="oDatosGuardar.id_estado"
                    :items="aEstado"
                    item-text="nombre"
                    label="Estado"
                    item-value="id_estado"
                  ></v-select>
                  </v-col>
                  <v-col
                    cols="12"
                    sm="6"
                  >
                  <v-select
                    v-model="oDatosGuardar.id_medio"
                    :items="aMedio"
                    item-text="nombre"
                    item-value="id_medio"
                    label=" Medio de recepción"
                  ></v-select>
                  </v-col>
                  <v-col
                    cols="12"
                    sm="6"
                  >
                  <v-textarea
                    label="Observaciones"
                    v-model="oDatosGuardar.observacion"
                    class="my-0"
                    rows="1"
                    value=""
                  ></v-textarea>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                color="blue darken-1"
                text
                @click="bModalRegistro = false"
              >
                Cerrar
              </v-btn>
              <v-btn
                color="blue darken-1"
                text
                @click="guardarFila(oDatosGuardar)"
              >
                Guardar
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <!-- modal seleccionar fecha -->
        <v-dialog
          ref="dialog"
          v-model="bModalFecha"
          :return-value.sync="date"
          persistent
          width="290px"
          >
          <v-date-picker
            v-model="date"
            scrollable
          >
            <v-spacer></v-spacer>
            <v-btn
              text
              color="primary"
              @click="bModalFecha = false"
            >
              Cancel
            </v-btn>
            <v-btn
              text
              color="primary"
              @click="seleccionarFechaModal(date)"
            >
              OK
            </v-btn>
          </v-date-picker>
        </v-dialog>
      </v-main>
    </v-app>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.13.1/xlsx.full.min.js"></script> 
  <script>
    new Vue({
      el: '#app',
      vuetify: new Vuetify(),
      data:{
        bModalRegistro:false,
        date:null,
        aTabla: [],
        aDatosMostrar:[],
        oAsuntoSeleccionado:{},
        nContador:0,
        aAsuntos:[],
        aRemitente:[],
        aDestinatario:[],
        aEstado:[],
        aMedio:[],
        menu: false,
        bModalFecha:false,
        oItemDocumentoSeleccionado:{},
        aAlerta:[{id:0,nombre:'Vence hoy'},{id:1,nombre:'Vencidos'},{id:2,nombre:'Al dia'},{id:3,nombre:'Dias atrasados'}],
        search:'',
        filtrarAlerta:'',
        filtrarAsunto:'',
        filtrarRemitente:'',
        filtrarDestinatario:'',
        aTempTabla:[],
        nPagina:1,
        nCantidadPorPagina:5,
        nCantidadPaginas:1,
        oDatosGuardar:{
          filaNueva:true,
        },
      },
      async created(){
        await this.cargarselec();
        await this.cargardocum();
        
        this.cargarDatosMostrar();
      },
      async mounted(){
      },
      methods:{
        cargarDatosMostrar(){
          this.nCantidadPaginas = Math.round(this.aTabla.length/this.nCantidadPorPagina);
          this.aDatosMostrar = this.aTabla.slice((this.nPagina-1)*this.nCantidadPorPagina,(this.nPagina)*this.nCantidadPorPagina);
        },
        cargarModalFecha(itemDocumento){
          this.oItemDocumentoSeleccionado=itemDocumento;
          this.date = this.oItemDocumentoSeleccionado.fecha;
          this.bModalFecha=true;
        },
        seleccionarFechaModal(sFecha){
          this.oItemDocumentoSeleccionado.fecha = sFecha;
          this.bModalFecha=false;
        },
        calcularDias(itemTabla){
          let temp = this.aAsuntos.find(item => item.id_asunto==itemTabla.id_asunto)
          itemTabla.dias = temp.dias
          
        },
        agregarFila(){
          
          this.aTabla.push({
            id:this.nContador,
            fecha: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
            asunto:-1,
            remitente:'',
            destinatario:'',
            termino:0,
            estado:'',
            medio:'',
            observaciones:'',
            filaNueva:true,
          });
          this.nContador++
        },
        async eliminarFila(id_documento){
          
          Swal.fire({
            title: 'Estás seguro de eliminar la fila?',
            // text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'cancelar',
            confirmButtonText: 'Sí, eliminar!'
          }).then((result) => {
            if (result.isConfirmed) {

              const formData = new FormData();
              formData.append('id_documento',id_documento);
              fetch('<?=base_url("/eliminarFila")?>', {
                method: 'POST',
                body: formData
              }).then(response => response.text())
              .then(data => {
                let result =JSON.parse(data);
                if(result.result=='ok'){
                  // alert("Guardado");
                  Swal.fire(
                    'Eliminado!',
                  )
                  this.aTabla = this.aTabla.filter(item=> item.id_documento!=id_documento);
                }
                else{

                  Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: 'Error al eliminar',
                    showConfirmButton: false,
                    timer: 1500
                  })

                }
              });
              
            }
          })
         
        },
        async guardarFila(oDatosGuardar){
          
          if(oDatosGuardar.n_registro==undefined||oDatosGuardar.n_registro==""){
            Swal.fire({
              icon: 'mdi-alert-outline',
              title: 'Por favor llene el registro',
              showConfirmButton: false,
              timer: 1500,
            })
            return;
          }
          else if(oDatosGuardar.fecha==undefined||oDatosGuardar.fecha==""){
            Swal.fire({
              icon: 'error',
              title: 'Por favor llene el fecha',
              showConfirmButton: false,
              timer: 1500,
            })
            return;
          }
          else if(oDatosGuardar.id_asunto==undefined||oDatosGuardar.id_asunto==""){
            Swal.fire({
              icon: 'error',
              title: 'Por favor llene el asunto',
              showConfirmButton: false,
              timer: 1500,
            })
            return;
          }
          else if(oDatosGuardar.id_remitente==undefined||oDatosGuardar.id_remitente==""){
            Swal.fire({
              icon: 'error',
              title: 'Por favor llene el remitente',
              showConfirmButton: false,
              timer: 1500,
            })
            return;
          }
          else if(oDatosGuardar.id_destinatario==undefined||oDatosGuardar.id_destinatario==""){
            Swal.fire({
              icon: 'error',
              title: 'Por favor llene el destinatario',
              showConfirmButton: false,
              timer: 1500,
            })
            return;
          }
          else if(oDatosGuardar.id_estado==undefined||oDatosGuardar.id_estado==""){
            Swal.fire({
              icon: 'error',
              title: 'Por favor llene el estado',
              showConfirmButton: false,
              timer: 1500,
            })
            return;
          }
          else if(oDatosGuardar.id_medio==undefined||oDatosGuardar.id_medio==""){
            Swal.fire({
              icon: 'error',
              title: 'Por favor llene el medio',
              showConfirmButton: false,
              timer: 1500,
            })
            return;
          }
          const formData = new FormData();
          formData.append('n_registro',oDatosGuardar.n_registro);
          formData.append('fecha',oDatosGuardar.fecha);
          formData.append('observacion',oDatosGuardar.observacion);
          formData.append('id_estado',oDatosGuardar.id_estado);
          formData.append('id_medio',oDatosGuardar.id_medio);
          formData.append('id_asunto',oDatosGuardar.id_asunto);
          formData.append('id_remitente',oDatosGuardar.id_remitente);
          formData.append('id_destinatario',oDatosGuardar.id_destinatario);
          formData.append('dias',oDatosGuardar.dias);
        

          if (oDatosGuardar.filaNueva == undefined) {
            
            formData.append('id_documento',oDatosGuardar.id_documento);
            await fetch('<?=base_url("/actualizarFila")?>', {
              method: 'POST',
              body: formData
            }).then(response => response.text())
            .then(data => {
              let result =JSON.parse(data);
              if(result.result=='ok'){
              
                Swal.fire({
                  position: 'top-center',
                  icon: 'success',
                  title: 'Actualizado',
                  showConfirmButton: false,
                  timer: 1500,
                })
              }
              else{
                Swal.fire({
                  position: 'top-end',
                  icon: 'error',
                  title: 'Error al actualizar',
                  showConfirmButton: false,
                  timer: 1500
                })
              }
            });
            
          } else {
            await fetch('<?=base_url("/guardarFila")?>', {
              method: 'POST',
              body: formData
            }).then(response => response.text())
            .then(data => {
              let result =JSON.parse(data);
              if(result.result=='ok'){
                // alert("Guardado");
                Swal.fire({
                  position: 'top-center',
                  icon: 'success',
                  title: 'Guardado',
                  showConfirmButton: false,
                  timer: 1500,
                })

                this.oDatosGuardar={filaNueva:true}
              }
              else{

                Swal.fire({
                  position: 'top-end',
                  icon: 'error',
                  title: 'Error al guardar',
                  showConfirmButton: false,
                  timer: 1500
                })
              }
            });
          }
        },
        async cargarselec(){

          //se carga los asuntos
          let aTempdata=[];
          await fetch('<?=base_url("/obtenerAsuntos")?>')
          .then(response => response.text())
          .then(data => {
            aTempdata=JSON.parse(data);
          });
          this.aAsuntos=aTempdata;

          //se carga los remitentes 
          await fetch('<?=base_url("/obtenerRemitenteDestinatario")?>')
          .then(response => response.text())
          .then(data => {
            aTempdata=JSON.parse(data);
          });
          this.aRemitente=aTempdata.filter(item=>item.tipo==0||item.tipo==2);
          this.aDestinatario=aTempdata.filter(item=>item.tipo==1||item.tipo==2);

          //se carga los destinatario
          // await fetch('<?=base_url("/obtenerRemitenteDestinatario")?>')
          // .then(response => response.text())
          // .then(data => {
          //   aTempdata=JSON.parse(data);
          // });
          

          //se carga los estado
          await fetch('<?=base_url("/obtenerEstado")?>')
          .then(response => response.text())
          .then(data => {
            aTempdata=JSON.parse(data);
          });
          this.aEstado=aTempdata;

          //se carga los medio
          await fetch('<?=base_url("/obtenerMedio")?>')
          .then(response => response.text())
          .then(data => {
            aTempdata=JSON.parse(data);
          });
          this.aMedio=aTempdata;

        },
        async cargardocum(){

          //se carga los documento
          let aTempdata=[];
          await fetch('<?=base_url("/obtenerDocumento")?>')
          .then(response => response.text())
          .then(data => {
            aTempdata=JSON.parse(data);
            console.log(aTempdata)
          });
          this.aTabla=aTempdata;
        },
        async buscar(){

          const formData = new FormData();
          formData.append("sbuscar",this.search)

          let aTempdata=[];
          await fetch('<?=base_url("/buscador")?>',{
            method: 'POST',
            body: formData
          })
          .then(response => response.text())
          .then(data => {
            aTempdata=JSON.parse(data);
            console.log(aTempdata)
          });
          this.aTabla=aTempdata;

          this.nPagina = 1;
          this.cargarDatosMostrar();
        },
        async filtrar(){

          let aTempdata=[];
          await fetch('<?=base_url("/filtrar")?>?sFiltrar='+this.filtrarAsunto+"&rfiltrar="+this.filtrarRemitente+"&dFiltrar="+this.filtrarDestinatario+"&aFiltrar="+this.filtrarAlerta)
          .then(response => response.text())
          .then(data => {
            aTempdata=JSON.parse(data);
            console.log(aTempdata)
          });
          this.aTabla=aTempdata;
          this.nPagina = 1;
          this.cargarDatosMostrar();
        },
        async descarExcel(){
          let data=[];
          let filename='informe.xlsx';
          this.aTabla;
          for (let index = 0; index < this.aTabla.length; index++) {
            let asunto = this.aAsuntos.find(item=>item.id_asunto==this.aTabla[index].id_asunto).nombre;
            let remitente = this.aRemitente.find(item=>item.id_remitente_Destinatario==this.aTabla[index].id_remitente).nombre;
            let destinatario = this.aDestinatario.find(item=>item.id_remitente_Destinatario==this.aTabla[index].id_destinatario).nombre;
            let estado = this.aEstado.find(item=>item.id_estado==this.aTabla[index].id_estado).nombre;
            let Medio = this.aMedio.find(item=>item.id_medio==this.aTabla[index].id_medio).nombre;

            data.push({
              NoRegistro:this.aTabla[index].n_registro,
              Fecha:this.aTabla[index].fecha,
              Alertas:this.aTabla[index].diasVencidos,
              Asunto:asunto,
              Remitente:remitente,
              Destinatario:destinatario,
              Contestar:this.aTabla[index].dias,
              Estado:estado,
              Medio:Medio,
              Observacion:this.aTabla[index].Observacion,
            })
            
          }

            var ws = XLSX.utils.json_to_sheet(data);
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "informe");
            XLSX.writeFile(wb,filename);
        },
        async cerraLogin(){
          
          await fetch('<?=base_url("/cerraLogin")?>')
          location.reload();
        }

      },
      watch:{
        nPagina(){
          this.aDatosMostrar = this.aTabla.slice((this.nPagina-1)*this.nCantidadPorPagina,(this.nPagina)*this.nCantidadPorPagina);  
        }
      }
    })

    

  </script>
 
</body>
</html>