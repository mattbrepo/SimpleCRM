<template>
  <nav class="navbar navbar-expand-lg navbar-light">
    <!-- <a class="navbar-brand" href="#">Navbar</a> -->
    <button class="navbar-toggler custom-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav">
        <li v-if="isAuthenticated" class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarMyAccount" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">My Account</a>
          <div class="dropdown-menu" aria-labelledby="navbarMyAccount">
            <router-link class="dropdown-item" to="/profile">Profile</router-link>
            <a class="dropdown-item logout" @click="logout">Logout</a>
          </div>
        </li>
        <li v-if="isAuthenticated && isAdmin" class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarAdmin" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Admin</a>
          <div class="dropdown-menu" aria-labelledby="navbarAdmin">
            <router-link class="dropdown-item" to="/groups">Groups</router-link>
            <router-link class="dropdown-item" to="/users">Users</router-link>
            <router-link class="dropdown-item" to="/products">Products</router-link>
          </div>
        </li>
        <li v-if="isAuthenticated" class="nav-item">
          <router-link class="nav-link" to="/">Home</router-link>
        </li>
        <li v-if="isAuthenticated" class="nav-item">
          <router-link class="nav-link" to="/companies">Companies</router-link>
        </li>
        <li v-if="isAuthenticated" class="nav-item">
          <router-link class="nav-link" to="/contacts">Contacts</router-link>
        </li>
        <li v-if="isAuthenticated" class="nav-item">
          <router-link class="nav-link" to="/orders">Orders</router-link>
        </li>
        <li v-if="isAuthenticated" class="nav-item">
          <router-link class="nav-link" to="/licenses">Licenses</router-link>
        </li>
        <li v-if="!isAuthenticated" class="nav-item">
          <router-link class="nav-link" to="/login">Login</router-link>
        </li>
      </ul>
    </div>
  </nav>
</template>

<script>
import { mapGetters } from 'vuex';
import store from '../store';

export default {
  name: 'navigation',
  methods: {
    logout: function() {
      axios.get('/api/logout').finally(() => { // logout in any case!
        //console.log('loggout');
        this.$store.dispatch('actRemoveWebToken')
          .then(() => this.$router.push({ name: 'Login' }));
      });
    }
  },
  created: function() {
    //prodebug
    //console.log('--- created');
    //console.log(this.$store.getters.isAuthenticated); 
  },
  computed: {
    ...mapGetters(['isAuthenticated', 'isAdmin']),
  }
};
</script>
