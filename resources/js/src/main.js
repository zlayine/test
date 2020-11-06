import Vue from 'vue'
import App from './App.vue'

// Vuesax Component Framework
import Vuesax from 'vuesax'

Vue.use(Vuesax)

// axios
import axios from "./axios.js"
Vue.prototype.$http = axios


// Theme Configurations
import '../themeConfig.js'

import VueSweetalert2 from 'vue-sweetalert2';

// Globally Registered Components
import './globalComponents.js'


// Vue Router
import router from './router'

// Vuex Store
import store from './store/store'


// Vuejs - Vue wrapper for hammerjs
import { VueHammer } from 'vue2-hammer'
Vue.use(VueHammer)


// PrismJS
import 'prismjs'
import 'prismjs/themes/prism-tomorrow.css'
import {initialize} from './auth/auth.js';

import VueHighlightJS from 'vue-highlightjs'

// Vue select css
// Note: In latest version you have to add it separately
// import 'vue-select/dist/vue-select.css';

import 'sweetalert2/dist/sweetalert2.min.css';

Vue.config.productionTip = false

Vue.use(VueHighlightJS)
Vue.use(VueSweetalert2)

Vue.mixin({
    methods: {
        showDialog: function (msg, error, context) {
            if (error)
                this.$vs.notify({color:'danger', title:`${context} Error`, text: msg, position:'top-center'})
            else
                this.$vs.notify({color:'success', title:`${context} Success`, text:msg, position:'top-center'})
        }
    }
});

initialize(store, router);

new Vue({
    router,
    store,
    render: h => h(App)
}).$mount('#app')
