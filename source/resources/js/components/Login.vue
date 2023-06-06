<template>
  <div class="vertical-center login-block">
    <img src="../assets/logo.png" style="margin: 0 0 2% 0;">
    <form class="login" @submit.prevent="handleLogin">
      <div class="form-group">
        <!-- <label>User name</label> -->
        <input required v-model="formData.username" type="text" class="form-control form-control-lg" placeholder="Username"/>
      </div>

      <div class="form-group">
        <!-- <label>Password</label> -->
        <input required v-model="formData.password" type="password" class="form-control form-control-lg" placeholder="Password"/>
      </div>

      <button type="submit" class="btn btn-dark btn-lg btn-block">Login</button>
    </form>
  </div>
</template>

<script>
  export default {
    data() {
      return {
        formData: {
          username: 'a', //%prodebug%
          password: 'a'
        }
      };
    },
    methods: {
      handleLogin: function() {
        axios.get('/sanctum/csrf-cookie').then(response => {
          axios.post('/api/login', this.formData).then(response => {
            //this.$store.commit('addWebToken', response.data.token);
            this.$store.dispatch('actAddWebToken', response.data.token)
              .then(() => this.$router.push({ name: 'Home' }))
              .catch(() => this.$alert('Something has gone terribly wrong', '', 'error'));

            //prodebug
            //console.log('response.data.token:');
            //console.log(response.data.token);
            //console.log('localStorage:');
            //console.log(localStorage);

            // err: "Redirected from “/login” to “/” via a navigation guard"
            //this.$router.push('/');
            //this.$router.push('/home');
            //this.$router.push(this.$route.query.redirect || '/');
            //this.$router.push({name:'Home'});
          }).catch(() => {
            //prodebug
            //console.log('catch error');
            //console.log(response);
            this.$alert('You have entered an invalid username or password', '', 'error');
          });
        });
      }
    }
  }
</script>
