<template>
  <div class="normal-center">
    <h1>Companies</h1>

    <div class="form-group">
      <button class="main-btn" @click="addNew">Add new</button>
      <button type="submit" class="main-btn" @click="selectAllRows">Select all {{ selected.length == 0 ? '' : '(' + selected.length + ')' }}</button>
      <button type="submit" class="main-btn" @click="clearSelected">Clear selected</button>
    </div>    

    <div class="table-responsive">
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
            <input class="form-control" v-if="field.key !== 'created_at' && field.filterOn" v-model="filters[field.key]" :placeholder="field.label">

            <div class="table-date-filter" v-if="field.key === 'created_at'">
            <vc-date-picker v-model="filter_created_at.start" locale="en-GB">
              <template v-slot="{ inputValue, inputEvents }">
                <input class="form-control" :value="inputValue" v-on="inputEvents" placeholder="Start" />
              </template>            
            </vc-date-picker> 
            <vc-date-picker v-model="filter_created_at.end" locale="en-GB">
              <template v-slot="{ inputValue, inputEvents }">
                <input class="form-control" :value="inputValue" v-on="inputEvents" placeholder="End" />
              </template>            
            </vc-date-picker>
            </div>
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
          <button style="margin-left: 1.5em;" class="table-btn" @click="editRow(row.item)">Edit</button>
          <button v-if="isAdmin" style="margin-left: 1.5em;" class="table-btn" @click="deleteRow(row.item)">Delete</button>
        </template>      
      </b-table>
    </div>

    <!-- pagination -->
    <b-pagination
      v-model="currentPage"
      :total-rows="rows"
      :per-page="perPage"
      align="center"
      aria-controls="my-table">
    </b-pagination>

    <div class="form-group">
      <button v-if="isAdmin" class="main-btn" @click="assignGroup">Assign</button>
      <button v-if="isAdmin" class="main-btn" @click="removeGroup">Remove</button>
      <select v-if="isAdmin" class="form-select" v-model="group" id="inpGroup">
        <option v-for="group in groups" v-bind:key="group.id" v-bind:value="group.name">{{ group.name }}</option>
      </select>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
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
      companies: [],
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
          key: 'country_id',
          label: 'Country',
          sortable: true,
          formatter: (value, key, item) => {
            if (this.countries.length > 0) {
              var country = this.countries.find(x => x.id == value); 
              return typeof country !== 'undefined' ? country.name : '';
            } else {
              return '';
            }
          },
          filterOn: true 
        },
        {
          key: 'user_id',
          label: 'Created by',
          sortable: true,
          formatter: (value, key, item) => {
            if (this.users.length > 0) {
              var user = this.users.find(x => x.id == value); 
              return typeof user !== 'undefined' ? user.name : '' + value;
            } else {
              return '';
            }
          },
          filterOn: true,
          requiresAdmin: true
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
      countries: [],
      groups: [],
      group: '',
      perPage: 10,
      currentPage: 1,
      filters: {
        name: '',
        country_id: '',
        user_id: '',
        created_at: ''
      },
      filter_created_at: {
        start: null,
        end: null
      },
      users: []
    };
  },
  created() {
    this.fetchCompanies();
  },
  methods: {
    fetchCompanies() {      
      axios.get('/api/companies').then(response => {
        //console.log(response.data);
        
        if (this.isAdmin) {
          axios.get('/api/groups').then(response3 => {
            var mygroups = response3.data.filter(x => x.admin == 0);
            mygroups.unshift(''); // add empty group
            this.groups = mygroups;
          }).catch((ex) => {
            this.$alert('Error fetching groups', '', 'error');
          });

          axios.get('/api/users').then(response4 => {
            this.users = response4.data;
          }).catch((ex) => {
            this.$alert('Error fetching users', '', 'error');
          });
        }

        axios.get('/api/countries').then(response2 => {
          this.countries = response2.data;
        }).catch((ex) => {
          this.$alert('Error fetching countries', '', 'error');
        }).finally(() => {
          this.companies = response.data;
          this.$forceUpdate(); // necessary to recompute filtered()
        });
      }).catch((ex) => {
        this.$alert('Error fetching companies', '', 'error');
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
      this.$router.push('/company/' + item.id);
    },
    deleteRow(item) {
      this.$confirm('Are you sure you want to delete this company?', '', 'warning').then(() => {      
        axios.delete('/api/company/' + item.id).then(response => {
          this.companies = response.data;
          this.$forceUpdate(); // necessary to recompute filtered()
        }).catch((ex) => {
          var msg = ex.response.data.message || 'Error deleting company';
          this.$alert(msg, '', 'error');
        });
      });
    },
    addNew() {
      this.$router.push('/company/new');
    },
    getGroupAndCheck() {
      if (this.group === '') {
        this.$alert('A group needs to be selected', '', 'warning');
        return -1;
      }
      if (this.selected.length <= 0) {
        this.$alert('No company selected', '', 'warning');
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

      this.$confirm('Are you sure you want to assign these companies to the selected group?', '', 'warning').then(() => {      
        var post_pars = {
          companies: this.selected
        };
        axios.post('/api/group/' + group_id + '/assign-companies', post_pars).then(response => {
          this.$alert('Companies assigned', '', 'success');
        }).catch((ex) => {
          var msg = ex.response.data.message || 'Error assigning company to grouè';
          this.$alert(msg, '', 'error');
        });
      });
    },
    removeGroup() {
      var group_id = this.getGroupAndCheck();
      if (group_id < 0)
        return;

      this.$confirm('Are you sure you want to remove these companies from the selected group?', '', 'warning').then(() => {      
        var post_pars = {
          companies: this.selected
        };
        axios.post('/api/group/' + group_id + '/remove-companies', post_pars).then(response => {
          this.$alert('Companies removed', '', 'success');
        }).catch((ex) => {
          var msg = ex.response.data.message || 'Error removing company from group';
          this.$alert(msg, '', 'error');
        });
      });
    }
  },
  computed: {
    ...mapGetters(['isAdmin']),
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

      // list of filtered countries
      var filteredCountries = [];
      if (lcFilters.country_id.startsWith('!')) {
        lcFilters.country_id = lcFilters.country_id.substring(1);
        filteredCountries = this.countries.filter(c => !c.name.toLowerCase().includes(lcFilters.country_id));
      } else {
        filteredCountries = this.countries.filter(c => c.name.toLowerCase().includes(lcFilters.country_id));
      }
      
      // list of filtered table items
      const filtered = this.companies.filter(item => {
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
          
          // country filter
          if (key === 'country_id')
            return filteredCountries.filter(c => c.id == item.country_id).length > 0;

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
