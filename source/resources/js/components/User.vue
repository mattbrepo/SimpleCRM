<template>
  <div class="normal-center">
    <h1>User {{ $route.params.id }}</h1>
    <form @submit.prevent="saveData">
      <div class="form-group">
        <label for="inpName">Name</label>
        <input required v-model="formData.name" type="text" class="form-control" id="inpName">
      </div>
      <div class="form-group">
        <label for="inpEmail">Email</label>
        <input required v-model="formData.email" type="text" class="form-control" id="inpEmail">
      </div>
      <div class="form-group">
        <label for="inpGroup">Group</label>
        <select required v-model="formData.group.name" class="form-control" id="inpGroup">
          <option v-for="group in formData.groups" v-bind:key="group.id" v-bind:value="group.name">
            {{ group.name }}
          </option>
        </select>
      </div>
      <div v-if="isNew" class="form-group">
        <label for="inpNewPassword">New password</label>
        <input required v-model="formData.newPassword" type="password" class="form-control" id="inpNewPassword">
      </div>
      <div v-if="isNew" class="form-group">
        <label for="inpNewPasswordConfirm">New password confirmation</label>
        <input required v-model="formData.newPasswordConfirm" type="password" class="form-control" id="inpNewPasswordConfirm">
      </div>      
      <p>
        <button v-if="!isNew" type="submit" class="btn-dark btn-lg">Save</button>
        <button v-if="isNew" type="submit" class="btn-dark btn-lg">Create</button>
      </p>
    </form>

    <h1 v-if="this.$route.params.id !== 'new'" class="mt-5">Reset Password</h1>
    <form v-if="this.$route.params.id !== 'new'" @submit.prevent="savePassword">
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
      formData: {
        name: '',
        email: '',
        group: {},
        groups: [],
        newPassword: '', // used for new user
        newPasswordConfirm: ''
      },
      formPassword: {
        newPassword: '',
        newPasswordConfirm: ''
      }
    };
  },
  created() {
    if (this.$route.params.id === 'new') {
      this.createUser();
    } else {
      this.fetchUserData();
    }
  },
  computed: {
    isNew: function () {
      return this.$route.params.id === 'new';
    }
  },
  methods: {
    createUser() {
      axios.get('/api/users/create').then(response => {
        this.formData.name = response.data.name;
        this.formData.email = response.data.email;
        this.fetchGroups();

        axios.get('/api/group/' + response.data.user_group_id).then(response2 => {
          this.formData.group = response2.data;
        }).catch((ex) => {
          this.$alert('Error fetching user group', '', 'error');
        });          
      }).catch((ex) => {
        this.$alert('Error creating user', '', 'error');
      });      
    },
    fetchUserData() {
      axios.get('/api/user/' + this.$route.params.id).then(response => {
        this.formData.name = response.data.name;
        this.formData.email = response.data.email;
        this.fetchGroups();

        axios.get('/api/user/' + this.$route.params.id + '/group').then(response2 => {
          this.formData.group = response2.data;
        }).catch((ex) => {
          this.$alert('Error fetching user group', '', 'error');
        });          
      }).catch((ex) => {
        this.$alert('Error fetching user data', '', 'error');
      });
    },
    fetchGroups() {
      axios.get('/api/groups').then(response => {
        this.formData.groups = response.data;
      }).catch((ex) => {
        this.$alert('Error fetching groups', '', 'error');
      });
    },
    saveData() {
      var formSaveData = {
        name: this.formData.name,
        email: this.formData.email,
        group_id: this.formData.groups.find(g => g.name === this.formData.group.name).id
      };

      if (this.isNew) {
        if (this.formData.newPassword != this.formData.newPasswordConfirm) {
          this.$alert('The confirmation password is not valid', '', 'error');
          return;
        }

        formSaveData['newPassword'] = this.formData.newPassword;
        axios.post('/api/users', formSaveData).then(response => {
          this.$alert('User created', '', 'success');
          this.$router.push('/user/' + response.data.id);
        }).catch((ex) => {
          var msg = ex.response.data.message || 'Error while creating user';
          this.$alert(msg, '', 'error');
        });
      } else {
        axios.post('/api/user/' + this.$route.params.id, formSaveData).then(response => {
          this.$alert('User data saved', '', 'success');
        }).catch((ex) => {
          var msg = ex.response.data.message || 'Error while saving user data';
          this.$alert(msg, '', 'error');
        });
      }
    },
    savePassword() {
      if (this.formPassword.newPassword != this.formPassword.newPasswordConfirm) {
        this.$alert('The confirmation password is not valid', '', 'error');
        return;
      }

      this.$confirm('Are you sure you want to chage this user password?', '', 'warning').then(() => {
        axios.post('/api/user/' + this.$route.params.id + '/password', this.formPassword).then(response => {
          this.formPassword.newPassword = '';
          this.formPassword.newPasswordConfirm = '';
            
          this.$alert('New password saved', '', 'success');
        }).catch((ex) => {
          var msg = ex.response.data.message || 'Error while saving the password';
          this.$alert(msg, '', 'error');
        });
      });
    }      
  }
}
</script>
