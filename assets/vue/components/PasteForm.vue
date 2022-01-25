<template>
  <div class="pasteform">
    <div class="container">
      <div class="row">
        <div class="col-12 col-md-9">
          <div class="card mb-3 mt-4" style="background-color: #34394D;color:white;">
            <FormulateForm v-model="formDatas" @submit="handleSubmit" name="pasteForm" class="pForm">
              <div class="card-body">
                <div class="col-12 col-md-6">
                  <FormulateInput
                      name="title"
                      v-model="codeTitle"
                      type="text"
                      placeholder="Titre du paste"
                  />
                </div>
                <div class="col-12 mt-2">
                  <FormulateInput label="Votre code"
                                  v-model="codeInput"
                                  name="content"
                                  type="textarea"
                                  validation="required"
                                  validation-name="Le code"
                                  class="textareacode"
                                  :input-class="['code-custom mt-2']"
                                  placeholder="Insérez votre code"
                  />
                </div>
              </div>
              <div class="card-footer">
                <h4 class="card-title">Autres informations</h4>
                <div class="row">
                  <div class="col-12 col-md-6">
                    <FormulateInput label="Coloration synthaxique"
                                    name="language"
                                    v-model="codeLanguage"
                                    value="text"
                                    type="vue-select"
                                    :options="{
                            text: 'Texte',
                            php: 'PHP',
                            javascript: 'Javascript',
                            html: 'html',
                            css: 'css',
                            json: 'json',
                            yaml: 'yaml',
                            sql: 'sql',
                            python: 'Python',
                            sass: 'sass',
                            scss: 'scss',
                            markdown: 'Markdown',
                            cpp: 'C++',
                            csharp: 'C#',
                            java: 'JAVA',
                            lua: 'lua'
                          }"
                                    validation="required|in:text,php,javascript,html,css,json,markdown,cpp,csharp,java,lua,yaml,python,sql,sass,scss"
                                    validation-name="visibilité"
                    />
                    <FormulateInput label="Visibilité"
                                    name="privacy"
                                    value="unlisted"
                                    type="select"
                                    :options="privacyOptions"
                                    validation="required|in:public,unlisted,private"
                                    validation-name="visibilité"
                    />
                  </div>
                  <div class="col-6"></div>
                  <div class="col-12 col-md-4 mt-4">
                    <FormulateInput type="submit" label="Créer un nouveau paste"
                                    :input-class="['btn btn-primary']"
                                    :disabled="loading"/>
                  </div>
                </div>
              </div>
            </FormulateForm>
            <div class="col-12 p-3">
              <p>En publiant sur la plateforme vous déclarez avoir lu et accepté notre <a href="/charteutilisateur"
                                                                                          target="_blank">Charte
                Utilisateur</a> ainsi que nos
                <a href="/cgu" target="_blank">Conditions Générales d'Utilisation</a>.</p>
              <p>Veillez à ne jamais communiquer d'informations sensibles dans vos pastes. Les <b>pastes privés</b>
                peuvent faire l'objet de vérifications
                de la part de notre équipe de modération.</p>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-3">
          <PasteSidebar/>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import PasteSidebar from "./PasteSidebar";
import {axiosInstance} from "../api/axios";
import {redirectHelper} from "../Helpers/RedirectHelper";

export default {
  name: "PasteForm",
  components: {
    PasteSidebar
  },
  props: {
    userid: String
  },
  data: function () {
    return {
      codeInput: null,
      codeLanguage: 'text',
      codeTitle: null,
      formDatas: null,
      loading: false,
      user: null,
      privacyOptions: {public: 'Public', unlisted: 'Non listé', private: 'Privé'}
    };
  },
  methods: {
    handleSubmit: async function () {
      this.loading = true
      await axiosInstance.post('/pastes', this.formDatas)
          .then(response => {
            this.$formulate.resetValidation('pasteForm')
            this.codeInput = null
            this.codeLanguage = "text"
            this.codeTitle = null
            redirectHelper('/paste/' + response.data.uuid)
          }).catch(e => console.warn(e))
      this.loading = false
    },
    handlePrivacyOptions: function () {
      if (!this.userid) {
        this.privacyOptions = {public: 'Public', unlisted: 'Non listé'}
      }
    }
  },
  mounted() {
    this.handlePrivacyOptions()
  }
};
</script>

<style>
.code-custom {
  min-height: 30rem !important;
  font-family: monospace;
  color: white !important;
  background-color: #292F40 !important;
  border: none !important;
}
</style>
