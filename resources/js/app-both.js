/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

Vue.use(require('vue-moment'));

//Vue.config.productionTip = false;

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

//EventComponents
Vue.component('event-iofv3-result-component', require('./components/event-components/EventIofv3ResultComponent.vue').default);

const app = new Vue({
    el: '#app',
    data: {
        // declare message with an empty value
    },

});
