<template>
  <div class="normal-center">
    <h1>Users</h1>

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
      
      :items="users" 
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
        users: [],
        fields: [
          //{
          //  key: 'selected',
          //  label: ' '
          //},
          {
            key: 'edit',
            label: ' '
          },
          {
            key: 'id',
            label: 'ID',
            sortable: true
          },
          {
            key: 'name',
            label: 'Name',
            sortable: true
          },
          {
            key: 'email',
            label: 'Email',
            sortable: true
          }
        ],
        selected: [] // selected users
      };
    },
    created() {
      this.fetchUsers();
    },
    methods: {
      fetchUsers() {        
        axios.get('/api/users').then(response => {
          //console.log(response.data.length);
          //console.log(response.data[0]);
          this.users = response.data;
        }).catch((ex) => {
          this.$alert('Error fetching users', '', 'error');
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
        this.$router.push('/user/' + this.users[index].id);
      },
      deleteRow(index) {
        this.$confirm('Are you sure you want to delete this user?', '', 'warning').then(() => {        
          axios.delete('/api/user/' + this.users[index].id).then(response => {
            this.users = response.data;
          }).catch((ex) => {
            var msg = ex.response.data.message || 'Error deleting user';
            this.$alert(msg, '', 'error');
          });
        });
      },
      addNew() {
        this.$router.push('/user/new');
      }
    }
  }
</script>
