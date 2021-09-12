<template>
  <div class="paste">
    <div class="container mt-4 mb-4" v-if="paste">
      <div class="row">
        <div class="col-12">
          <div class="card text-white" style="background-color:#353B4F;">
            <div class="card-header border-light">
              <h5 class="mb-2 text-white"><b>{{ paste.title ? paste.title : 'untitled' }}</b></h5>
              <p class="mb-0">
                Posté par
                <a href="#" class="text-warning">{{ paste.user ? paste.user.pseudo : 'unknown' }}</a>
                le {{ formatDate(paste.createdAt) }}
              </p>
            </div>
            <div class="card-body">
              <span class="badge bg-primary text-uppercase">{{ pasteLanguage }}</span>
              <span class="badge bg-verylightblue text-uppercase">{{ paste.privacy }}</span>
              <div class="mb-3 float-end">
                <a href="#" class="badge bg-light" @click="handleCopy"><i class="fa fa-copy"></i> Copier</a> &nbsp;
                <a href="#" class="badge bg-warning text-dark"><i class="fa fa-flag"></i> Signaler</a>
                <a href="#" class="badge bg-danger text-light" @click="handleDelete"
                   v-if="parseInt(this.pasteOwner) === parseInt(this.currentuser)"><i class="fa fa-trash"></i> Supprimer</a>
                <div class="editorMode mt-1" v-if="parseInt(this.pasteOwner) === parseInt(this.currentuser)">
                  <input class="form-check-input" type="checkbox" @change="handleEditorMode" id="checkEditorMode">
                  <label class="form-check-label" for="checkEditorMode">
                    Mode édition
                  </label>
                </div>
              </div>
              <prism-editor class="my-editor" v-model="pasteCode" :highlight="highlighter" :readonly="readOnly"
                            line-numbers></prism-editor>
              <div class="form-check my-2 float-end"
                   v-if="parseInt(this.pasteOwner) === parseInt(this.currentuser) && readOnly === false">
                <div class="col-12">
                  <button class="btn btn-primary" @click="handleEdit">Valider les modifications</button>
                </div>
              </div>
            </div>
            <div class="card-body">
              <h5 class="text-white">Version non formatée</h5>
              <div class="form-group">
                <textarea class="form-control text-white" style="background-color: #292F40;" v-model="paste.content"
                          rows="15"></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import 'vue-prism-editor/dist/prismeditor.min.css'; // import the styles somewhere
import 'prismjs';
import 'prismjs/themes/prism-tomorrow.css'
import '../Helpers/prismLanguages'
import {PrismEditor} from 'vue-prism-editor';
import {axiosInstance} from "../api/axios";
import {formatDate} from "../Helpers/dateHelper";
import {notify} from "../Helpers/notifyHelper";
import Prism from 'prismjs/components/prism-core'
import {redirectHelper} from "../Helpers/RedirectHelper";

Prism.manual = true;


export default {
  name: "Paste",
  components: {
    PrismEditor
  },
  props: {
    target: String,
    currentuser: String
  },
  data: function () {
    return {
      pasteId: this.target,
      paste: null,
      pasteCode: null,
      pasteLanguage: null,
      readOnly: true,
      pasteOwner: null,
      formatDate
    }
  },
  methods: {
    getPaste: async function () {
      await axiosInstance.get('/pastes/' + this.pasteId)
          .then(response => {
            this.paste = response.data
            this.pasteCode = response.data.content
            this.pasteLanguage = response.data.language
            this.pasteOwner = response.data.user ? response.data.user.id : null
            console.log(response.data)
          }).catch((e) => console.log(e))
    },
    highlighter: function (code) {
      return Prism.highlight(code, Prism.languages[this.pasteLanguage], this.pasteLanguage)
    },
    handleEditorMode: function () {
      this.readOnly === true ? this.readOnly = false : this.readOnly = true
    },
    handleEdit: async function () {
      await axiosInstance.put('/pastes/' + this.pasteId, {
        content: this.pasteCode
      }).then(response => {
        this.paste = response.data
        notify('Succès !', 'Votre paste a bien été modifié.', 'success')
      }).catch(e => console.warn(e))
    },
    handleDelete: async function (e) {
      e.preventDefault()
      await axiosInstance.delete('/pastes/' + this.pasteId)
      .then(() => redirectHelper('/'))
      .catch(e => console.warn(e))

    },
    handleCopy: function (e) {
      e.preventDefault()
      navigator.clipboard.writeText(this.pasteCode)
          .then(() => notify('Succès !', 'Le code a bien été copié dans le presse-papier.', 'success'))
          .catch(() => notify('Oups...', 'Impossible de copier dans le code dans le presse-papier. Votre navigateur ne supporte' +
              'peut être pas encore cette fonctionnalité.', 'success'))
    }
  },
  mounted() {
    this.getPaste()
  }
}
</script>

<style scoped>
/* required class */
.my-editor {
  /* we dont use `language-` classes anymore so thats why we need to add background and text color manually */
  background: #292F40;
  color: #ccc;
  border-radius: 10px;

  /* you must provide font-family font-size line-height. Example: */
  font-family: Fira code, Fira Mono, Consolas, Menlo, Courier, monospace;
  font-size: 14px;
  line-height: 1.5;
  padding: 5px;
}

/* optional class for removing the outline */
.prism-editor__textarea:focus {
  outline: none;
}
</style>