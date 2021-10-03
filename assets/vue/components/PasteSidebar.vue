<template>
  <div class="pastesidebar">
    <div class="card mb-3 mt-4 text-light" style="background-color: #34394D">
      <div class="card-header">Derniers pastes publics</div>
      <div class="paste_sidebar_list pt-2" v-if="pastes">
        <div class="paste_sidebar_element" v-for="paste in pastes" :key="paste.id">
          <p class="paste_sidebar_element__title mb-0">
            <a :href="'/paste/' + paste.uuid">
              {{ paste.title ? paste.title : 'untitled'}}
            </a>
          </p>
          <p class="paste_sidebar_element__subtitle small text-muted mt-0"><span class="text-uppercase">{{ paste.language }}</span> | <TimeAgo locale="fr" :datetime="paste.createdAt" long></TimeAgo></p>
        </div>
      </div>
      <div class="text-center p-2" v-else>
        <Loader/>
      </div>
    </div>
  </div>
</template>

<script>
import {formatDate} from "../Helpers/dateHelper";
import { TimeAgo } from 'vue2-timeago/dist/vue2-timeago.umd'
import {axiosInstance} from "../api/axios";
import Loader from "../Ui/Loader";

export default {
  name: "PasteSidebar",
  components: {
    Loader,
    TimeAgo
  },
  data: function () {
    return {
      pastes: null,
      formatDate: formatDate
    }
  },
  methods: {
    getPastes: async function () {
      await axiosInstance.get('/pastes/public')
      .then(response => {
        this.pastes = response.data['hydra:member']
      }).catch(e => console.warn(e))
    }
  },
  mounted() {
    this.getPastes()
  }
}
</script>

<style scoped>
.paste_sidebar_element {
  margin-left: 1rem;
}
</style>