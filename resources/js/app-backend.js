
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

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('manage-category-component', require('./components/ManageCategoryComponent.vue').default);
Vue.component('user-avatar-component', require('./components/UserAvatarComponent.vue').default);
Vue.component('oevent-list-component', require('./components/OeventListComponent').default);
Vue.component('private-news-dashboard-component', require('./components/PrivateNewsDashboardComponent.vue').default);

//Modals
Vue.component('delete-file-modal-component', require('./components/modals/DeleteFileModalComponent.vue').default);
Vue.component('delete-content-modal-component', require('./components/modals/DeleteContentModalComponent').default);

//FormComonents
Vue.component('password-field-component', require('./components/form-components/PasswordFieldComponent.vue').default);
Vue.component('editorial-field-component', require('./components/form-components/EditorialFieldComponent').default);
Vue.component('date-time-input-component', require('./components/form-components/DateTimeInputComponent').default);
Vue.component('date-input-component', require('./components/form-components/DateInputComponent').default);

Vue.component('event-iofv3-result-component', require('./components/event-components/EventIofv3ResultComponent.vue').default);

//Vue.component('multiselect-input-component', require('./components/form-components/MultiselectInputCompoenent').default);
Vue.component('toasted-component', require('./components/ToastedComponent.vue').default);

const app = new Vue({
    el: '#app',
    data: {
        // declare message with an empty value
    },

});
