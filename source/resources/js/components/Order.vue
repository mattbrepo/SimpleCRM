<template>
  <div class="normal-center">
    <h1 v-if="isNew">New Order</h1>
    <h1 v-if="!isNew">Order {{ isAdmin ? $route.params.id : '' }}</h1>

    <form @submit.prevent="saveData">

      <!-- order data -->
      <div class="form-group">
        <div class="row">
          <label for="inpName" class="col-sm-2 col-form-label">Name</label>
          <div class="col">
            <input v-model="formData.name" type="text" class="form-control" id="inpName" :readonly="!isNew && !isAdmin">
          </div>
        </div>

        <div class="row mt-3">
          <label for="inpCompany" class="col-sm-2 col-form-label">Company</label>
          <div class="col">
            <!-- not even admin can change the company once is selected -->
            <input v-if="this.companySet" v-model="formData.company.name" type="text" class="form-control" id="inpCompany" readonly>
            <b-form-input v-if="!this.companySet" list="listCompanies" id="inpCompany" class="form-control" v-model="formData.company.name"></b-form-input>
            <b-form-datalist id="listCompanies">
              <option v-for="company in formData.companies" v-bind:key="company.id" v-bind:value="company.name">{{ company.name }}</option>
            </b-form-datalist>
          </div>
          <div class="col">
            <button v-if="!this.companySet" class="btn-dark btn-lg" type="button" @click="handleAddCompanyClick()">Add Company</button>
          </div>
        </div>
      </div>
      <button v-if="!this.companySet" class="btn-dark btn-lg" type="button" @click="handleSetCompanyClick()">Next</button>

      <div v-if="this.companySet">
        <!-- order_products -->
        <div class="form-group border border-dark pt-3 pr-3 pb-3 pl-3" v-for="op in formData.order_products" v-bind:key="op.id">
          <p class="mt-2">
            <button v-if="isNew || isAdmin" class="btn-dark btn-lg" type="button" @click="handleRemoveClick(op)">Remove</button>
          </p>

          <div class="row">
            <div class="col-sm">
              <label for="inpProduct">Product</label>
              <!-- not even admin can change the product once is selected (but it can remove it!) -->
              <input v-if="!isNew" v-model="op.name" type="text" id="inpProduct" readonly>

              <b-form-input style="display: inline;width: 20%;" v-if="isNew" list="listProducts" id="inpProduct" v-model="op.name" :state="op.valid_name" @change="handleChangeProduct(op)"></b-form-input>
              <b-form-datalist id="listProducts">
                <option v-for="product in formData.products" v-bind:key="product.id" v-bind:value="product.name">{{ product.name }}</option>
              </b-form-datalist>

              <label for="inpPrice">Price</label>
              <input v-model="op.price" type="number" id="inpPrice" readonly>
              <label for="inpDiscount">Discount</label>
              <input v-model="op.discount" type="number" id="inpDiscount" :readonly="!isAdmin" @change="handleChangeDiscount(op)">
              <label for="inpTotal">Total</label>
              <input v-model="op.total" type="number" id="inpTotal" readonly>
              <label for="inpNote">Note</label>
              <input v-model="op.note" type="text" id="inpNote" :readonly="!isNew && !isAdmin">
            </div>
          </div>

          <!-- licenses -->
          <div v-if="!isNew">
            <div class="form-group border border-dark mt-3 pt-3 pr-3 pb-3 pl-3" v-for="lic in op.licenses" v-bind:key="lic.id">
              <div class="row">
                <div class="col-sm">
                  <label for="inpContact">Contact</label>
                  <b-form-input style="display: inline;width: 20%;" list="listContacts" id="inpContact" v-model="lic.contact_id" :readonly="lic.lic_req_filename!='' && !isAdmin"></b-form-input>
                  <b-form-datalist id="listContacts">
                    <option v-for="contact in formData.contacts" v-bind:key="contact.id">{{ contact.first_name + ' ' + contact.last_name }}</option>
                  </b-form-datalist>

                  <label for="inpStartDate">Start Date</label>
                  <input type="date" id="inpStartDate" :value="lic.start_date">
                  <label for="inpEndDate">End Date</label>
                  <input type="date" id="inpEndDate" :value="lic.end_date">
                </div>
              </div>
              <div class="row">
                <div class="col-sm">
                  <label for="inpLicReqFileName">Request</label>
                  <input type="file" id="inpLicReqFileName" accept=".areq" @change="handleLicReqFileName($event, lic)">
                  <label for="inpLicFileName">License</label>
                  <input type="text" id="inpLicFileName" :value="lic.lic_filename">
                </div>
              </div>
            </div>
          </div>
        </div>
        

        <!-- final data and buttons -->
        <p>
          <button v-if="isNew || isAdmin" class="btn-dark btn-lg" type="button" @click="handleAddClick">Add</button>
        </p>
        <div class="form-group">
          <label for="inpGrandTotal">Grand Total</label>
          <input v-model="formData.total" type="number" class="form-control" id="inpGrandTotal" readonly>
          <input v-if="isAdmin" type="checkbox" id="inpInvoiced" v-model="formData.invoiced">
          <label v-if="isAdmin" for="inpInvoiced">Invoiced</label>
        </div>
        
        <p>
          <button v-if="isNew" type="submit" class="btn-dark btn-lg">Create</button>
          <button v-if="!isNew" type="submit" class="btn-dark btn-lg">Save</button>
        </p>
      </div>
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
        name: '',
        company: {},
        invoiced: false,
        total: 0,
        order_products: [],
        companies: [],
        contacts: [],
        products: [],
      },
      opNewId: -1,
      companySet: false
    };
  },
  created() {
    if (this.$route.params.id === 'new') {
      this.companySet = false;
      this.createOrder();
    } else {
      this.companySet = true;
      this.fetchOrderData();
    }
  },
  computed: {
    ...mapGetters(['isAdmin']),
    isNew: function () {
      return this.$route.params.id === 'new';
    }
  },
  methods: {
    fetchCompanies() {
      axios.get('/api/companies').then(response => {
        this.formData.companies = response.data;

        if (typeof this.$route.query.company !== "undefined") {
          this.formData.company = this.formData.companies.find(g => g.name === decodeURIComponent(this.$route.query.company));
          this.formData.name = decodeURIComponent(this.$route.query.order_name);
        }
      }).catch((ex) => {
        this.$alert('Error fetching companies', '', 'error');
      });
    },
    fetchProducts() {
      axios.get('/api/products').then(response => {
        this.formData.products = response.data;
      }).catch((ex) => {
        this.$alert('Error fetching products', '', 'error');
      });
    },
    createOrder() {
      this.handleAddClick();
      this.fetchCompanies();
      this.fetchProducts();
    },
    fetchOrderData() {    
      axios.get('/api/order/' + this.$route.params.id).then(response => {
        this.formData.name = response.data.name;
        this.formData.invoiced = response.data.invoiced;
        this.formData.total = response.data.total;

        axios.get('/api/order/' + this.$route.params.id + '/order_products_licenses').then(response2 => {
          this.formData.order_products = response2.data;
        }).catch((ex) => {
          this.$alert('Error fetching order order_products', '', 'error');
        });

        axios.get('/api/company/' + response.data.company_id).then(response3 => {
          this.formData.company = response3.data;
        }).catch((ex) => {
          this.$alert('Error fetching order company', '', 'error');
        });

        if (this.isAdmin) {
          this.fetchProducts();
        }
      }).catch((ex) => {
        this.$alert('Error fetching order data', '', 'error');
      });
    },
    saveData() {
      if (this.isNew) {
        if (this.formData.order_products.length <= 0) {
          this.$alert('No product selected', '', 'error');
          return;          
        }

        var company = this.formData.companies.find(g => g.name === this.formData.company.name);
        if (typeof company === "undefined") {
          this.$alert('Company not valid', '', 'error');
          return;
        }

        var formSaveData = {
          name: this.formData.name,
          company_id: company.id,
          invoiced: this.formData.invoiced,
          order_products: []
        };

        for (let op of this.formData.order_products) {
          var prod = this.formData.products.find(p => p.name === op.name);
          if (typeof prod === "undefined") {
            this.$alert('Product is not valid: ' + op.name, '', 'error');
            return;
          }

          formSaveData.order_products.push({
            prod_name: prod.name,
            prod_id: prod.id,
            discount: op.discount,
            note: op.note
          });
        }
      
        axios.post('/api/orders', formSaveData).then(response => {
          this.$alert('Order created', '', 'success');
          this.$router.push('/order/' + response.data.id);
          this.fetchOrderData();
        }).catch((ex) => {
          var msg = ex.response.data.message || 'Error while creating order';
          this.$alert(msg, '', 'error');
        });
      } else {
        var formSaveData = new FormData();
        formSaveData.append('name', this.formData.name);
        formSaveData.append('invoiced', this.formData.invoiced);
        formSaveData.append('order_products', this.formData.order_products.length);

        for (let i = 0; i < this.formData.order_products.length; i++) {
          let op = this.formData.order_products[i];
          let prod = this.formData.products.find(p => p.name === op.name);
          if (typeof prod === "undefined") {
            this.$alert('Product is not valid: ' + op.name, '', 'error');
            return;
          }

          formSaveData.append(i + '_product_id', prod.id);
          for (let j = 0; j < op.licenses.length; j++) {
            let lic = op.licenses[j];
            formSaveData.append(i + '_' + j + '_lic_req_filename', lic.lic_req_filename);
          }
        }
        
        let config = {
          header: { 'Content-Type' : 'multipart/form-data' } // necessary to send files
        }
        axios.post('/api/order/' + this.$route.params.id, formSaveData, config).then(response => {
          this.$alert('Order data saved', '', 'success');
        }).catch((ex) => {
          var msg = ex.response.data.message || 'Error while saving order data';
          this.$alert(msg, '', 'error');
        });
      }
    },
    handleAddClick() {
      if (this.isNew) {
        var opNew = Object.assign({}, this.formData.order_products[0]);
        opNew.id = this.opNewId--;
        opNew.name = '';
        opNew.price = 0;
        opNew.discount = 0;
        opNew.total = 0;
        opNew.note = '';
        opNew.valid_name = null; // useful for product name validation
        opNew.licenses = [];
        this.formData.order_products.push(opNew);
      } else if (this.isAdmin) {

      }
    },
    handleRemoveClick(op) {
      if (this.isNew) {
        const index = this.formData.order_products.indexOf(op);
        this.formData.order_products.splice(index, 1);
      } else if (this.isAdmin) {

      }
    },
    handleChangeProduct(op) {
      if (!this.isNew) {
        // changing the product is allowed only when the product is new (even for Admin)
        exit();
      }
      
      this.resetOrderProductPrice(op);
      if (op.name === '') {
        op.valid_name = null;
      } else {
        var prod = this.formData.products.find(p => p.name === op.name);
        if (typeof prod !== "undefined") {
          op.valid_name = true;
          op.price = prod.price;
          op.total = prod.price;
        } else {
          op.valid_name = false;
        }
      }
    },
    resetOrderProductPrice(op) {
      op.price = 0;
      op.discount = 0;
      op.total = 0;
    },
    handleChangeDiscount(op) {
      if (!this.isAdmin || op.valid_name !== true) {
        exit();
      }

      op.total = op.price * (1 - op.discount / 100);
    },
    handleLicReqFileName(event, lic) {
      var files = event.target.files;
      if (files.length === 1) {
        lic.lic_req_filename = files[0];
      } else {
        lic.lic_req_filename = null;
      }
    },
    handleAddCompanyClick() {
      this.$router.push({ path: '/company/new', query: { fromOrder: '1', order_name: encodeURIComponent(this.formData.name), company: encodeURIComponent(this.formData.company.name) }});
    },
    handleSetCompanyClick() {
      var company = this.formData.companies.find(g => g.name === this.formData.company.name);
      if (typeof company === "undefined") {
        this.$alert('Company not valid', '', 'error');
        return;
      }

      this.companySet = true;
    }
  }
}
</script>
