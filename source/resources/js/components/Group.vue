<template>
  <div class="normal-center">
    <h1>Group {{ $route.params.id }}</h1>
    <form @submit.prevent="saveData">
      <div class="form-group">
        <label for="inpName">Name</label>
        <input required v-model="formData.name" type="text" class="form-control" id="inpName">
      </div>
      <div class="form-group">
        <!-- <input type="checkbox" id="inpAdministrator" v-model="formData.admin" readonly> -->
        <!-- the admin flag needs to be changed directly in the DB -->
        <span v-if="formData.admin" aria-hidden="true" class="table-checkbox">&check;</span>
        <span v-if="!formData.admin" aria-hidden="true" class="table-checkbox">&#9634;</span>

        <label for="inpAdministrator">Administrator</label>
      </div>
      <div v-if="!isNew" class="form-group">
        <label>Associated Users</label>
          <ul>
            <li v-for="item in formData.users" :key="item.id">
              <router-link :to="{path: '/user/' + item.id}">{{ item.name }}</router-link>
            </li>
          </ul>
      </div>
      <p>
        <button v-if="!isNew" type="submit" class="btn-dark btn-lg">Save</button>
        <button v-if="isNew" type="submit" class="btn-dark btn-lg">Create</button>
      </p>
    </form>  
  </div>

</template>

<script>
export default {
  data() {
   return {
    formData: {
     name: '',
     admin: false,
     users: []
    }
   };
  },
  created() {
    if (this.$route.params.id === 'new') {
      this.createGroup();
    } else {
      this.fetchGroupData();
    }
  },
  computed: {
    isNew: function () {
      return this.$route.params.id === 'new';
    }
  },  
  methods: {
    createGroup() {
      axios.get('/api/groups/create').then(response => {
        this.formData.name = response.data.name;
        this.formData.admin = response.data.admin === 1;
        this.formData.users = [];
      }).catch((ex) => {
        this.$alert('Error creating group', '', 'error');
      });      
    },
    fetchGroupData() {    
      axios.get('/api/group/' + this.$route.params.id).then(response => {
        this.formData.name = response.data.name;
        this.formData.admin = response.data.admin === 1;
        axios.get('/api/group/' + this.$route.params.id + '/users').then(response => {
          //console.log(response.data);
          this.formData.users = response.data;
        }).catch((ex) => {
          this.$alert('Error fetching group\'s users', '', 'error');
        });
      }).catch((ex) => {
        this.$alert('Error fetching group data', '', 'error');
      });
    },
    saveData() {
      if (this.isNew) {
        axios.post('/api/groups', this.formData).then(response => {
          this.$alert('Group created', '', 'success');
          this.$router.push('/group/' + response.data.id);
        }).catch((ex) => {
          var msg = ex.response.data.message || 'Error while creating group';
          this.$alert(msg, '', 'error');
        });
      } else {
        axios.post('/api/group/' + this.$route.params.id, this.formData).then(response => {
          this.$alert('Group data saved', '', 'success');
        }).catch((ex) => {
          var msg = ex.response.data.message || 'Error while saving group data';
          this.$alert(msg, '', 'error');
        });
      }
    }
  }
}
</script>
