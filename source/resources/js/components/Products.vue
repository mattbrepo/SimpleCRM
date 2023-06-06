<template>
  <div class="normal-center">
    <h1>Products</h1>

    <div class="form-group">
      <button class="btn-dark btn-lg" @click="addNew">Add new</button>
    </div>    

    <p>
      <button type="submit" class="btn-dark btn-lg" @click="selectAllRows">Select all {{ selected.length == 0 ? '' : '(' + selected.length + ')' }}</button>
      <button type="submit" class="btn-dark btn-lg" @click="clearSelected">Clear selected</button>
    </p>

    <b-table striped hover 
      select-mode="multi"
      responsive="true"
      ref="selectableTable"
      id="my-table"
      selectable
      @row-selected="onRowSelected"
      
      :per-page="perPage"
      :current-page="currentPage"

      :items="filtered"       
      :fields="computedFields">

      <!-- filter row -->
      <template slot="top-row" slot-scope="{ fields }">
        <td v-for="field in fields" :key="field.key">
          <input v-if="field.key !== 'created_at' && field.filterOn" v-model="filters[field.key]" :placeholder="field.label">

          <vc-date-picker v-if="field.key === 'created_at'" v-model="filter_created_at.start" locale="en-GB">
            <template v-slot="{ inputValue, inputEvents }">
              <input :value="inputValue" v-on="inputEvents" placeholder="Start" />
            </template>            
          </vc-date-picker> 
          <vc-date-picker v-if="field.key === 'created_at'" v-model="filter_created_at.end" locale="en-GB">
            <template v-slot="{ inputValue, inputEvents }">
              <input :value="inputValue" v-on="inputEvents" placeholder="End" />
            </template>            
          </vc-date-picker> 
        </td>
      </template>      

      <!-- selection column -->
      <template #cell(selected)="{ rowSelected }">
        <template v-if="rowSelected">
          <span aria-hidden="true" class="table-checkbox">&check;</span>
          <span class="sr-only">Selected</span>
        </template>
        <template v-else>
          <span aria-hidden="true" class="table-checkbox">&#9634;</span>
          <span class="sr-only">Not selected</span>
        </template>
      </template>      

      <!-- edit column -->
      <template #cell(edit)="row">
        <button style="margin-left: 1.5em;" class="btn-dark" @click="editRow(row.item)">Edit</button>
        <button style="margin-left: 1.5em;" class="btn-dark" @click="deleteRow(row.item)">Delete</button>
      </template>      
    </b-table>
    
    <!-- pagination -->
    <b-pagination
      v-model="currentPage"
      :total-rows="rows"
      :per-page="perPage"
      align="center"
      aria-controls="my-table">
    </b-pagination>

    <div class="form-group">
      <button class="btn-dark btn-lg" @click="assignGroup">Assign</button>
      <button class="btn-dark btn-lg" @click="removeGroup">Remove</button>
      <select v-model="group" id="inpGroup">
        <option v-for="group in groups" v-bind:key="group.id" v-bind:value="group.name">{{ group.name }}</option>
      </select>
    </div>
  </div>
</template>

<script>
import { BTable, BButton, BPagination } from 'bootstrap-vue';
import store from '../store';
import moment from 'moment'
import { getFiltered } from '../util'
import Vue from 'vue';
import VCalendar from 'v-calendar'; // https://vcalendar.io/examples/datepickers.html

Vue.use(VCalendar, {
  componentPrefix: 'vc'  // Use <vc-calendar /> instead of <v-calendar />
});

export default {
  components: { BTable, BButton, BPagination },
  data() {
    return {
      products: [],
      fields: [
        {
          key: 'selected',
          label: ' ',
          filterOn: false
        },        
        {
          key: 'edit',
          label: ' ',
          filterOn: false
        },
        {
          key: 'name',
          label: 'Name',
          sortable: true,
          filterOn: true
        },
        {
          key: 'price',
          label: 'Price',
          sortable: true,
          filterOn: true
        },        
        {
          key: 'type',
          label: 'Type',
          sortable: false,
          formatter: (value, key, item) => {
            return ((item.single) ? 'single ' : 'site ') +
                   ((item.rent) ? 'rent ' : 'permanent ') + 
                   ((item.academic) ? 'academic ' : 'commercial ');
                   ((item.trial) ? 'trial' : '');
          },
          filterOn: false
        },
        {
          key: 'created_at',
          label: 'Date Created',
          sortable: true, 
          formatter: (value, key, item) => {
            return moment(String(value)).format('DD/MM/YYYY');
          },
          filterOn: true,
          requiresAdmin: true
        }
      ],
      selected: [],
      groups: [],
      group: '',
      perPage: 10,
      currentPage: 1,
      filters: {
        name: '',
        price: '',
        email: '',
        type: '',
        created_at: ''
      },
      filter_created_at: {
        start: null,
        end: null
      }
    };
  },
  created() {
    this.fetchProducts();
  },
  methods: {
    fetchProducts() {      
      axios.get('/api/products').then(response => {
        //console.log(response.data);
        this.products = response.data;
        
        axios.get('/api/groups').then(response3 => {
          var mygroups = response3.data.filter(x => x.admin == 0);
          mygroups.unshift(''); // add empty group
          this.groups = mygroups;
        }).catch((ex) => {
          this.$alert('Error fetching groups', '', 'error');
        });
      }).catch((ex) => {
        this.$alert('Error fetching products', '', 'error');
      });
    },
    onRowSelected(items) {
      this.selected = items
    },
    selectAllRows() {
      this.$refs.selectableTable.selectAllRows()
    },
    clearSelected() {
      this.$refs.selectableTable.clearSelected()
    },
    editRow(item) {
      this.$router.push('/product/' + item.id);
    },
    deleteRow(item) {
      this.$confirm('Are you sure you want to delete this product?', '', 'warning').then(() => {      
        axios.delete('/api/product/' + item.id).then(response => {
          this.products = response.data;
          this.$forceUpdate(); // necessary to recompute filtered()
        }).catch((ex) => {
          var msg = ex.response.data.message || 'Error deleting product';
          this.$alert(msg, '', 'error');
        });
      });
    },
    addNew() {
      this.$router.push('/product/new');
    },
    getGroupAndCheck() {
      if (this.group === '') {
        this.$alert('A group needs to be selected', '', 'warning');
        return -1;
      }
      if (this.selected.length <= 0) {
        this.$alert('No product selected', '', 'warning');
        return -1;
      }

      var mygroups = this.groups.filter(x => x.name == this.group);
      if (mygroups.length !== 1) {
        this.$alert('The selected group has not been found', '', 'error');
        return - 1;
      }

      return mygroups[0].id;
    },
    assignGroup() {
      var group_id = this.getGroupAndCheck();
      if (group_id < 0)
        return;

      this.$confirm('Are you sure you want to assign these products to the selected group?', '', 'warning').then(() => {      
        var post_pars = {
          products: this.selected
        };
        axios.post('/api/group/' + group_id + '/assign-products', post_pars).then(response => {
          this.$alert('Products assigned', '', 'success');
        }).catch((ex) => {
          var msg = ex.response.data.message || 'Error assigning product to grouè';
          this.$alert(msg, '', 'error');
        });
      });
    },
    removeGroup() {
      var group_id = this.getGroupAndCheck();
      if (group_id < 0)
        return;

      this.$confirm('Are you sure you want to remove these products from the selected group?', '', 'warning').then(() => {      
        var post_pars = {
          products: this.selected
        };
        axios.post('/api/group/' + group_id + '/remove-products', post_pars).then(response => {
          this.$alert('Products removed', '', 'success');
        }).catch((ex) => {
          var msg = ex.response.data.message || 'Error removing product from group';
          this.$alert(msg, '', 'error');
        });
      });
    }
  },
  computed: {
    rows() {
      return this.filtered.length;
    },
    computedFields() {
      if (this.isAdmin) {
        return this.fields;
      }
      return this.fields.filter(field => !field.requiresAdmin);
    },
    filtered() {
      // filters to lower case
      var lcFilters = Object.assign({}, this.filters);
      Object.keys(lcFilters).map(key => lcFilters[key] = lcFilters[key].toLowerCase());

      // list of filtered table items
      const filtered = this.products.filter(item => {
        return Object.keys(this.filters).every(key => {
          // date filter
          if (key === 'created_at') {
            var itemDate = moment(item[key]).toDate();
            itemDate.setHours(0,0,0,0);
            if (this.filter_created_at.start !== null) {
              if (itemDate < this.filter_created_at.start)
                return false;
            }
            if (this.filter_created_at.end !== null) {
              if (itemDate > this.filter_created_at.end)
                return false;
            }
            return true;
          }

          // filter not set
          if (lcFilters[key] === '')
            return true;

          if (key === 'price') {
            if (lcFilters[key].startsWith('!')) {
              return parseFloat(item[key]) !== parseFloat(lcFilters[key].substring(1));
            } else if (lcFilters[key].startsWith('<')) {
              return parseFloat(item[key]) < parseFloat(lcFilters[key].substring(1));
            } else if (lcFilters[key].startsWith('>')) {
              return parseFloat(item[key]) > parseFloat(lcFilters[key].substring(1));
            } else {
              return parseFloat(item[key]) === parseFloat(lcFilters[key]);
            }
            return true;
          }

          // string filters
          if (lcFilters[key].startsWith('!')) {
            return !String(item[key]).toLowerCase().includes(lcFilters[key].substring(1));
          } else {
            return String(item[key]).toLowerCase().includes(lcFilters[key]);
          }
        });
      });
      return filtered;
    }        
  }
}
</script>
