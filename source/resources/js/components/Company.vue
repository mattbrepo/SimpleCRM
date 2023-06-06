<template>
  <div class="normal-center">
    <h1 v-if="isNew">New Company</h1>
    <h1 v-if="!isNew">Company {{ isAdmin ? $route.params.id : '' }}</h1>

    <form @submit.prevent="saveData">
      <div class="form-group">
        <label for="inpName">Name</label>
        <input required v-model="formData.name" type="text" class="form-control" id="inpName">
      </div>
      <div class="form-group">
        <label for="inpWebsite">Website</label>
        <input v-model="formData.website" type="text" class="form-control" id="Website">
      </div>
      <div class="form-group">
        <label for="inpNote">Note</label>
        <input v-model="formData.note" type="text" class="form-control" id="inpNote">
      </div>
      <div class="form-group">
        <label for="inpCountry">Country</label>
        <select v-model="formData.country.name" class="form-control" id="inpCountry">
          <option v-for="country in formData.countries" v-bind:key="country.id" v-bind:value="country.name">
            {{ country.name }}
          </option>
        </select>
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

export default {
  data() {
    return {
      formData: {
        name: '',
        website: '',
        note: '',
        country: {},
        user: {},
        countries: [],
        groups: []
      },
      fromOrder: false,
      order_name: ''
    };
  },
  created() {
    if (this.$route.params.id === 'new') {
      this.createCompany();
    } else {
      this.fetchCompanyData();
    }
  },
  computed: {
    ...mapGetters(['isAdmin']),
    isNew: function () {
      return this.$route.params.id === 'new';
    }
  },
  methods: {
    createCompany() {
      axios.get('/api/company/create').then(response => {
        this.formData.name = response.data.name;
        this.formData.website = response.data.website;
        this.formData.note = response.data.note;
        this.fetchCountries();

        if (this.$route.query.fromOrder === "1") {
          this.fromOrder = true;
          this.formData.name = decodeURIComponent(this.$route.query.company);
          this.order_name = decodeURIComponent(this.$route.query.order_name);
        }
      }).catch((ex) => {
        this.$alert('Error creating company', '', 'error');
      });
    },
    fetchCompanyData() {
      axios.get('/api/company/' + this.$route.params.id).then(response => {
        this.formData.name = response.data.name;
        this.formData.website = response.data.website;
        this.formData.note = response.data.note;

        axios.get('/api/country/' + response.data.country_id).then(response2 => {
          this.formData.country = response2.data;
        }).catch((ex) => {
          this.$alert('Error fetching company country', '', 'error');
        });
        this.fetchCountries();

        if (this.isAdmin) {
          axios.get('/api/company/' + this.$route.params.id + '/groups').then(response3 => {
            this.formData.groups = response3.data;
          }).catch((ex) => {
            this.$alert('Error fetching company groups', '', 'error');
          });
          
          axios.get('/api/user/' + response.data.user_id).then(response4 => {
            this.formData.user = response4.data;
          }).catch((ex) => {
            this.$alert('Error fetching company user', '', 'error');
          });
        }

      }).catch((ex) => {
        this.$alert('Error fetching company data', '', 'error');
      });
    },
    fetchCountries() {
      axios.get('/api/countries').then(response => {
        this.formData.countries = response.data;
      }).catch((ex) => {
        this.$alert('Error fetching countries', '', 'error');
      });
    },
    saveData() {
      var formSaveData = {
        name: this.formData.name,
        website: this.formData.website,
        note: this.formData.note,
        country_id: -1
      };

      var country = this.formData.countries.find(g => g.name === this.formData.country.name);
      if (typeof country !== "undefined") {
        formSaveData.country_id = country.id;
      }

      if (this.isNew) {
        axios.post('/api/companies', formSaveData).then(response => {
          if (this.fromOrder) {
            // it goes back to the order
            this.$router.push({ path: '/order/new', query: { fromCompany: '1', order_name: encodeURIComponent(this.order_name), company: encodeURIComponent(this.formData.name) }});
          } else {
            this.$alert('Company created', '', 'success');
            this.$router.push('/company/' + response.data.id);
            this.fetchCompanyData();
          }
        }).catch((ex) => {
          var msg = ex.response.data.message || 'Error while creating company';
          this.$alert(msg, '', 'error');
        });
      } else {
        axios.post('/api/company/' + this.$route.params.id, formSaveData).then(response => {
          this.$alert('Company data saved', '', 'success');
        }).catch((ex) => {
          var msg = ex.response.data.message || 'Error while saving company data';
          this.$alert(msg, '', 'error');
        });
      }
    }
  }
}
</script>
