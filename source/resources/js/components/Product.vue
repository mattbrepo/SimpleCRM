<template>
  <div class="normal-center">
    <h1 v-if="isNew">New Product</h1>
    <h1 v-if="!isNew">Product {{ $route.params.id }}</h1>

    <form @submit.prevent="saveData">
      <div class="form-group">
        <label for="inpName">Name</label>
        <input v-model="formData.name" type="text" class="form-control" id="inpName">
      </div>
      <div class="form-group">
        <label for="inpSoftware">Software</label>
        <input required v-model="formData.software" type="text" class="form-control" id="inpSoftware">
      </div>
      <div class="form-group">
        <label for="inpMajorVersion">Major Version</label>
        <input v-model="formData.major_version" type="text" class="form-control" id="inpMajorVersion">
      </div>
      <div class="form-group">
        <label for="inpPrice">Price</label>
        <input v-model="formData.price" type="number" class="form-control" id="inpPrice">
      </div>
      <div class="form-group">
        <label for="inpNumLicenses">Number of Licenses</label>
        <input v-model="formData.num_licenses" type="number" step="1" class="form-control" id="inpNumLicenses">
      </div>      
      <div class="form-group">
        <input type="checkbox" id="inpSingle" v-model="formData.single">
        <label for="inpSingle">Single</label>
      </div>
      <div class="form-group">
        <input type="checkbox" id="inpRent" v-model="formData.rent">
        <label for="inpRent">Rent</label>
      </div>
      <div class="form-group">
        <input type="checkbox" id="inpAcademic" v-model="formData.academic">
        <label for="inpAcademic">Academic</label>
      </div>
      <div class="form-group">
        <input type="checkbox" id="inpTrial" v-model="formData.trial">
        <label for="inpTrial">Trial</label>
      </div>
      <div class="form-group">
        <input type="checkbox" id="inpOSWindows" v-model="formData.os_windows">
        <label for="inpOSWindows">Windows</label>
      </div>
      <div class="form-group">
        <input type="checkbox" id="inpOSLinux" v-model="formData.os_linux">
        <label for="inpOSLinux">Linux</label>
      </div>
      <div class="form-group">
        <input type="checkbox" id="inpOSMac" v-model="formData.os_mac">
        <label for="inpOSMac">macOS</label>
      </div>
      <div v-if="!isNew" class="form-group">
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
import { BFormInput, BFormDatalist } from 'bootstrap-vue';

export default {
  components: { BFormInput, BFormDatalist },
  data() {
    return {
      formData: {
        name: '',
        software: '',
        major_version: '',
        price: 0,
        single: false,
        rent: false,
        academic: false,
        trial: false,
        os_windows: false,
        os_linux: false,
        os_mac: false,
        num_licenses: 0,
        groups: []
      }
    };
  },
  created() {
    if (this.$route.params.id === 'new') {
      this.createProduct();
    } else {
      this.fetchProductData();
    }
  },
  computed: {
    isNew: function () {
      return this.$route.params.id === 'new';
    }
  },
  methods: {
    createProduct() {
      axios.get('/api/product/create').then(response => {
        this.formData.name = response.data.name;
        this.formData.software = response.data.software;
        this.formData.major_version = response.data.major_version;
        this.formData.price = response.data.price;
        this.formData.single = response.data.single;
        this.formData.rent = response.data.rent;
        this.formData.academic = response.data.academic;
        this.formData.trial = response.data.trial;
        this.formData.os_windows = response.data.os_windows;
        this.formData.os_linux = response.data.os_linux;
        this.formData.os_mac = response.data.os_mac;
        this.formData.num_licenses = response.data.num_licenses;
      }).catch((ex) => {
        this.$alert('Error creating product', '', 'error');
      });      
    },
    fetchProductData() {    
      axios.get('/api/product/' + this.$route.params.id).then(response => {
        this.formData.name = response.data.name;
        this.formData.software = response.data.software;
        this.formData.major_version = response.data.major_version;
        this.formData.price = response.data.price;
        this.formData.single = response.data.single;
        this.formData.rent = response.data.rent;
        this.formData.academic = response.data.academic;
        this.formData.trial = response.data.trial;
        this.formData.os_windows = response.data.os_windows;
        this.formData.os_linux = response.data.os_linux;
        this.formData.os_mac = response.data.os_mac;
        this.formData.num_licenses = response.data.num_licenses;

        axios.get('/api/product/' + this.$route.params.id + '/groups').then(response3 => {
          this.formData.groups = response3.data;
        }).catch((ex) => {
          this.$alert('Error fetching product groups', '', 'error');
        });
      }).catch((ex) => {
        this.$alert('Error fetching product data', '', 'error');
      });
    },
    saveData() {
      var formSaveData = {
        name: this.formData.name,
        software: this.formData.software,
        major_version: this.formData.major_version,
        price: this.formData.price,
        single: this.formData.single,
        rent: this.formData.rent,
        academic: this.formData.academic,
        trial: this.formData.trial,
        os_windows: this.formData.os_windows,
        os_linux: this.formData.os_linux,
        os_mac: this.formData.os_mac,
        num_licenses: this.formData.num_licenses
      };

      if (this.isNew) {
        axios.post('/api/products', formSaveData).then(response => {
          this.$alert('Product created', '', 'success');
          this.$router.push('/product/' + response.data.id);
          this.fetchProductData();
        }).catch((ex) => {
          var msg = ex.response.data.message || 'Error while creating product';
          this.$alert(msg, '', 'error');
        });
      } else {
        axios.post('/api/product/' + this.$route.params.id, formSaveData).then(response => {
          this.$alert('Product data saved', '', 'success');
        }).catch((ex) => {
          var msg = ex.response.data.message || 'Error while saving product data';
          this.$alert(msg, '', 'error');
        });
      }
    }
  }
}
</script>
