<template>
  <div class="normal-center">
    <h1>{{ formProfile.username }}'s Profile</h1>
    <form @submit.prevent="handleProfileSave">
      <!-- <div class="form-group">
        <label for="inpUsername">Username</label>
        <span type="text" class="form-control" id="inpUsername">{{ formProfile.username }}</span>
      </div> -->
      <div class="form-group">
        <label for="inpEmail">Email</label>
        <input required v-model="formProfile.email" type="text" class="form-control" id="inpUsername">
      </div>
      <button type="submit" class="btn-dark btn-lg">Save Email</button>
    </form>    

    <h1 class="mt-5">New Password</h1>
    <form @submit.prevent="handlePasswordSave">
      <div class="form-group">
        <label for="inpCurrentPassword">Current Password</label>
        <input required v-model="formPassword.currentPassword" type="password" class="form-control" id="inpCurrentPassword">
      </div>
      <div class="form-group">
        <label for="inpNewPassword">New password</label>
        <input required v-model="formPassword.newPassword" type="password" class="form-control" id="inpNewPassword">
      </div>
      <div class="form-group">
        <label for="inpNewPasswordConfirm">New password confirmation</label>
        <input required v-model="formPassword.newPasswordConfirm" type="password" class="form-control" id="inpNewPasswordConfirm">
      </div>
      <button type="submit" class="btn-dark btn-lg">Save New Password</button>
    </form>    
  </div>
</template>

<script>
  export default {
    data() {
      return {
        formProfile: {
          username: '',
          email: ''
        },
        formPassword: {
          currentPassword: '',
          newPassword: '',
          newPasswordConfirm: ''
        }
      };
    },
    created() {
      this.fetchUser();
    },
    methods: {
      fetchUser() {        
        axios.get('/api/profile').then(response => {
          this.formProfile.username = response.data.name;
          this.formProfile.email = response.data.email;
        }).catch((ex) => {
          this.$alert('Error fetching user data', '', 'error');
        });
      },
      handleProfileSave() {
        axios.post('/api/profile', this.formProfile).then(response => {
          this.formProfile.email = response.data.email; 
        }).catch((ex) => {
          this.$alert('Error while saving the email', '', 'error');
        });        
      },
      handlePasswordSave() {
        if (this.formPassword.newPassword != this.formPassword.newPasswordConfirm) {
          this.$alert('The confirmation password is not valid', '', 'error');
          return;
        }

        axios.post('/api/profile/password', this.formPassword).then(response => {
          this.formPassword.currentPassword = '';
          this.formPassword.newPassword = '';
          this.formPassword.newPasswordConfirm = '';
          
          this.$alert('New password saved', '', 'success');
        }).catch((ex) => {
          var msg = ex.response.data.message || 'Error while saving the password';
          this.$alert(msg, '', 'error');
        });
      }
    }
  }
</script>
