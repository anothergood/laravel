
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
import Vue from 'vue';
import VueRouter from 'vue-router';
import Vuex from 'vuex';
import {routes} from './routes';
import MainApp from './components/MainApp.vue';

Vue.use(VueRouter);
Vue.use(Vuex);

const router = new VueRouter({
  routes,
  mode: 'history'
});

const app = new Vue({
  el: '#app',
  router,
  components: {
    MainApp
  }
});

// window.Vue = require('vue');
//
// import VueRouter from 'vue-router'
// Vue.use(VueRouter)
//
// let routes = [
//     { path: 'login', component: require('./components/LoginComponent.vue')}
//     // { path: '/chat', name: 'chat', component: require('./components/ChatComponent.vue'),
//     //   path: 'login', name: 'login', component: require('./components/LoginComponent.vue'),
//     //   // path: '/private-chat', name: 'private_chat', component: require('./components/PrivateChatComponent.vue'),
//     //   path: '/login-error', name: 'lgon_error', component: require('./components/LoginErrorComponent.vue') }
// ]
//
// const router = new VueRouter({
//     mode: 'history',
//     routes
// })

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

//  Vue.component('example-component', require('./components/ExampleComponent.vue'));
// Vue.component('chat-component', require('./components/ChatComponent.vue'));
// Vue.component('auth-check-component', require('./components/AuthCheckComponent.vue'));
// Vue.component('login-component', require('./components/LoginComponent.vue'));
// Vue.component('main-app', require('./components/MainApp.vue'));


// Vue.component(
//     'passport-clients',
//     require('./components/passport/Clients.vue')
// );
//
// Vue.component(
//     'passport-authorized-clients',
//     require('./components/passport/AuthorizedClients.vue')
// );
//
// Vue.component(
//     'passport-personal-access-tokens',
//     require('./components/passport/PersonalAccessTokens.vue')
// );
