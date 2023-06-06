<template>
  <div class="normal-center">
    <h1>Groups</h1>

    <!-- <p>
      <button type="submit" class="btn-dark btn-lg" @click="selectAllRows">Select all</button>
      <button type="submit" class="btn-dark btn-lg" @click="clearSelected">Clear selected</button>
    </p> -->
    <b-table striped hover 
      select-mode="multi"
      responsive="true"
      ref="selectableTable"
      selectable
      @row-selected="onRowSelected"
      
      :items="groups" 
      :fields="fields">

      <!-- selection -->
      <!-- <template #cell(selected)="{ rowSelected }">
        <template v-if="rowSelected">
          <span aria-hidden="true" class="table-checkbox">&check;</span>
          <span class="sr-only">Selected</span>
        </template>
        <template v-else>
          <span aria-hidden="true" class="table-checkbox">&#9634;</span>
          <span class="sr-only">Not selected</span>
        </template>
      </template> -->

      <!-- edit -->
      <template #cell(edit)="row">
        <button style="margin-left: 1.5em;" type="submit" class="btn-dark" @click="editRow(row.index)">Edit</button>
        <button style="margin-left: 1.5em;" type="submit" class="btn-dark" @click="deleteRow(row.index)">Delete</button>
      </template>      
    </b-table>

    <p>
      <button type="submit" class="btn-dark btn-lg" @click="addNew">Add new</button>
    </p>    
  </div>
</template>

<script>
import { BTable, BButton } from 'bootstrap-vue';

export default {
    components: { BTable, BButton },
    data() {
      return {
        groups: [],
        fields: [
          //{
          //  key: 'selected',
          //  label: ' '
          //},
          {
            key: 'edit',
            label: ' '
          },
          //{
          //  key: 'id',
          //  label: 'ID',
          //  sortable: true
          //},
          {
            key: 'name',
            label: 'Name',
            sortable: true
          },
          {
            key: 'admin',
            label: 'Administrator',
            sortable: true,
            formatter: (value, key, item) => {
              return value === 1 ? 'yes' : 'no';
            }
          }
        ],
        selected: [] // selected groups
      };
    },
    created() {
      this.fetchGroups();
    },
    methods: {
      fetchGroups() {        
        axios.get('/api/groups').then(response => {
          //console.log(response.data.length);
          //console.log(response.data[0]);
          this.groups = response.data;
        }).catch((ex) => {
          this.$alert('Error fetching groups', '', 'error');
        });
      },
      onRowSelected(items) {
        //console.log('onRowSelected');
        this.selected = items
      },
      selectAllRows() {
        this.$refs.selectableTable.selectAllRows()
      },
      clearSelected() {
        this.$refs.selectableTable.clearSelected()
      },
      editRow(index) {
        this.$router.push('/group/' + this.groups[index].id);
      },
      deleteRow(index) {
        this.$confirm('Are you sure you want to delete this group?', '', 'warning').then(() => {        
          axios.delete('/api/group/' + this.groups[index].id).then(response => {
            this.groups = response.data;
          }).catch((ex) => {
            var msg = ex.response.data.message || 'Error deleting group';
            this.$alert(msg, '', 'error');
          });
        });
      },
      addNew() {
        this.$router.push('/group/new');
      }
    }
  }
</script>
