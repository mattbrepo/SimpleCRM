<template>
  <div class="normal-center">
    <h1 v-if="isNew">New Contact</h1>
    <h1 v-if="!isNew">Contact {{ isAdmin ? $route.params.id : '' }}</h1>
    <form @submit.prevent="saveData">
      <div class="form-group">
        <label for="inpTitle">Title</label>
        <select v-model="formData.title" class="form-control" id="inpTitle">
          <option v-for="title in formData.titles" v-bind:key="title">
            {{ title }}
          </option>
        </select>        
      </div>
      <div class="form-group">
        <label for="inpFirstName">First Name</label>
        <input v-model="formData.first_name" type="text" class="form-control" id="inpFirstName">
      </div>
      <div class="form-group">
        <label for="inpLastName">Last Name</label>
        <input required v-model="formData.last_name" type="text" class="form-control" id="inpLastName">
      </div>
      <div class="form-group">
        <label for="inpEmail">Email</label>
        <input v-model="formData.email" type="text" class="form-control" id="inpEmail">
      </div>
      <div class="form-group">
        <label for="inpCompany">Company</label>
        <b-form-input list="listCompanies" id="inpCompany" v-model="formData.company.name"></b-form-input>
        <b-form-datalist id="listCompanies">
          <option v-for="company in formData.companies" v-bind:key="company.id" v-bind:value="company.name">{{ company.name }}</option>
        </b-form-datalist>
        <!-- <select required v-model="formData.company.name" class="form-control" id="inpCompany">
          <option v-for="company in formData.companies" v-bind:key="company.id" v-bind:value="company.name">
            {{ company.name }}
          </option>
        </select> -->
      </div>
      <div class="form-group">
        <label for="inpNote">Note</label>
        <input v-model="formData.note" type="text" class="form-control" id="inpNote">
      </div>
      <div v-if="isAdmin" class="form-group">
        <label for="inpSource">Source</label>
        <select v-model="formData.source" class="form-control" id="inpSource">
          <option v-for="source in formData.sources" v-bind:key="source">
            {{ source }}
          </option>
        </select>        
      </div>
      <div v-if="isAdmin" class="form-group">
        <label for="inpSiteUser">Site User</label>
        <input v-model="formData.site_user" type="text" class="form-control" id="inpSiteUser">
      </div>
      <div v-if="!isNew && isAdmin" class="form-group">
        <label>Created by</label>
        <router-link :to="{path: '/user/' + formData.user.id}">{{ formData.user.name }}</router-link>
      </div>      
      <div v-if="!isNew && isAdmin" class="form-group">
        <label>Associated Groups</label>
        <ul>
          <li v-for="item in formData.groups" :key="item.id">
            <router-link :to="{path: '/group/' + item.id}">{{ item.name }}</router-link>
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
import { mapGetters } from 'vuex';
import { BFormInput, BFormDatalist } from 'bootstrap-vue';

export default {
  components: { BFormInput, BFormDatalist },
  data() {
    return {
      formData: {
        title: '',
        first_name: '',
        last_name: '',
        email: '',
        company: {},
        note: '',
        source: '',
        site_user: '',
        user: {},
        companies: [],
        groups: [],
        sources: ['Contact Us', 'Email', 'Campaign', 'Kode'],
        titles: ['Mr', 'Mrs', 'Miss', 'Dr']
      }
    };
  },
  created() {
    if (this.$route.params.id === 'new') {
      this.createContact();
    } else {
      this.fetchContactData();
    }
  },
  computed: {
    ...mapGetters(['isAdmin']),
    isNew: function () {
      return this.$route.params.id === 'new';
    }
  },
  methods: {
    createContact() {
      axios.get('/api/contact/create').then(response => {
        this.formData.title = response.data.title;
        this.formData.first_name = response.data.first_name;
        this.formData.last_name = response.data.last_name;
        this.formData.email = response.data.email;
        this.formData.source = response.data.source;
        this.formData.site_user = response.data.site_user;
        this.formData.note = response.data.note;
        this.fetchCompanies();
      }).catch((ex) => {
        this.$alert('Error creating contact', '', 'error');
      });      
    },
    fetchContactData() {    
      axios.get('/api/contact/' + this.$route.params.id).then(response => {
        this.formData.title = response.data.title;
        this.formData.first_name = response.data.first_name;
        this.formData.last_name = response.data.last_name;
        this.formData.email = response.data.email;
        this.formData.source = response.data.source;
        this.formData.site_user = response.data.site_user;
        this.formData.note = response.data.note;

        axios.get('/api/company/' + response.data.company_id).then(response2 => {
          this.formData.company = response2.data;
        }).catch((ex) => {
          this.$alert('Error fetching contact company', '', 'error');
        });
        this.fetchCompanies();

        if (this.isAdmin) {
          axios.get('/api/contact/' + this.$route.params.id + '/groups').then(response3 => {
            this.formData.groups = response3.data;
          }).catch((ex) => {
            this.$alert('Error fetching contact groups', '', 'error');
          });
          
          axios.get('/api/user/' + response.data.user_id).then(response4 => {
            this.formData.user = response4.data;
          }).catch((ex) => {
            this.$alert('Error fetching contact user', '', 'error');
          });
        }

      }).catch((ex) => {
        this.$alert('Error fetching contact data', '', 'error');
      });
    },
    fetchCompanies() {
      axios.get('/api/companies').then(response => {
        this.formData.companies = response.data;
      }).catch((ex) => {
        this.$alert('Error fetching companies', '', 'error');
      });
    },
    saveData() {
      var formSaveData = {
        title: this.formData.title,
        first_name: this.formData.first_name,
        last_name: this.formData.last_name,
        email: this.formData.email,
        company_id: -1,
        note: this.formData.note,
        source: this.formData.source,
        site_user: this.formData.site_user
      };

      var company = this.formData.companies.find(g => g.name === this.formData.company.name);
      if (typeof company !== "undefined") {
        formSaveData.company_id = company.id;
      } else {
        this.$alert('Company not valid', '', 'error');
        return;
      }

      if (this.isNew) {
        axios.post('/api/contacts', formSaveData).then(response => {
          this.$alert('Contact created', '', 'success');
          this.$router.push('/contact/' + response.data.id);
          this.fetchContactData();
        }).catch((ex) => {
          var msg = ex.response.data.message || 'Error while creating contact';
          this.$alert(msg, '', 'error');
        });
      } else {
        axios.post('/api/contact/' + this.$route.params.id, formSaveData).then(response => {
          this.$alert('Contact data saved', '', 'success');
        }).catch((ex) => {
          var msg = ex.response.data.message || 'Error while saving contact data';
          this.$alert(msg, '', 'error');
        });
      }
    }
  }
}
</script>
