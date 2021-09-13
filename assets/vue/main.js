import Vue from "vue";
import VueFormulate from '@braid/vue-formulate'
import {fr} from "@braid/vue-formulate-i18n";
import Notifications from 'vue-notification'
import FormulateVSelectPlugin from '@cone2875/vue-formulate-select'

import 'vue-select/dist/vue-select.css';
import PasteForm from "./components/PasteForm";
import Paste from "./components/Paste";

Vue.use(Notifications)

Vue.use(VueFormulate, {
    plugins: [fr, FormulateVSelectPlugin],
    locale: 'fr',
    classes: {
        outer: ['form-group'],
        label: ['form-label', 'mt-2'],
        input: ['form-control'],
        inputHasErrors: ['is-invalid'],
        help: ['form-text', 'text-muted', 'small'],
        error: ['text-danger', 'mt-1', 'small'],
        errors: ['list-unstyled', 'text-danger']
    }
});


Vue.config.productionTip = false;

new Vue({
    el: '#app',
    components: {
        PasteForm,
        Paste,
    }
})
