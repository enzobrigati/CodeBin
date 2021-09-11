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
                <a :href="'/user/'" class="text-warning">{{ paste.user ? paste.user.pseudo : 'unknown' }}</a>
                le {{ formatDate(paste.createdAt) }}
              </p>
            </div>
            <div class="card-body">
              <span class="badge bg-primary text-uppercase">{{ pasteLanguage }}</span>
              <div class="options mb-3 float-end">
                <a href="#" class="badge bg-light">Copier</a> &nbsp;
                <a href="#" class="badge bg-danger">Signaler</a>
              </div>
              <prism-editor class="my-editor" v-model="pasteCode" :highlight="highlighter" :readonly="readOnly"
                            line-numbers></prism-editor>
            </div>
            <div class="card-body">
              <h5 class="text-white">Version non formatée</h5>
              <div class="form-group">
                <textarea class="form-control text-white" style="background-color: #292F40;" v-model="paste.content" rows="15" :readonly="false"></textarea>
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
import {redirectHelper} from "../Helpers/RedirectHelper";
import Prism from 'prismjs/components/prism-core'

Prism.manual = true;


export default {
  name: "Paste",
  components: {
    PrismEditor
  },
  props: {
    target: String
  },
  data: function () {
    return {
      pasteId: this.target,
      paste: null,
      pasteCode: null,
      pasteLanguage: null,
      readOnly: true,
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
      }).catch(() => redirectHelper('/'))
    },
    highlighter: function (code) {
      return Prism.highlight(code, Prism.languages[this.pasteLanguage], this.pasteLanguage)
    },
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