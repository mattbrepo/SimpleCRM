import Vue from 'vue';
import Vuex from 'vuex';
import createPersistedState from "vuex-persistedstate";
import axios from 'axios';

Vue.use(Vuex);

export const store = new Vuex.Store({
  state: {
    token: '',
    admin: false,
    debugMsg: ''
  },
  mutations: { 
    addWebToken: function(state, webToken) {
      //prodebug
      //console.log('addWebToken');
      //console.log(webToken);
      localStorage.setItem('user-token', webToken);
      state.token = webToken;
    },
    removeWebToken: function(state) {
      localStorage.removeItem('user-token');
      state.token = '';
    },
    setAdmin: function(state, adminValue) {
      state.admin = adminValue;
    },
    setDebugMsg: function(state, msg) {
      state.debugMsg = msg;
    }
  },
  getters: {
    isAuthenticated: state => {
      //prodebug
      //console.log('---- getters isAuthenticated');
      //console.log(localStorage.getItem('user-token'));
      //console.log(state.token);
      //console.log(state.);
      //console.log('-------------------------------');
      return state.token != '';
    },
    isAdmin: state => {
      return state.admin;
    },
    getDebugMsg: state => {
      return state.debugMsg;
    }
  },
  actions: {
    actAddWebToken(context, webToken) {
      context.commit('addWebToken', webToken);

      // check if user is admin
      axios.get('/api/profile/group').then(response => context.commit('setAdmin', response.data.admin == 1)).catch(() => context.commit('setAdmin', false))
    },
    actRemoveWebToken(context) {
      context.commit('removeWebToken');
    },
    actSetAdmin(context, adminValue) {
      context.commit('setAdmin', adminValue);
    }
  },
  plugins: [createPersistedState()] // preserve data when page is reloaded
});

// -------------- axios Authorization Bearer
axios.interceptors.request.use(request => {
  if (store.state.token !== '') {
    request.headers.common['Authorization'] = 'Bearer ' + store.state.token;
  }
  //console.log('Starting Request', JSON.stringify(request, null, 2))
  return request
})

axios.interceptors.response.use(response => {
  //console.log('Response:', JSON.stringify(response, null, 2))
  return response
})
// --------------

export default store;