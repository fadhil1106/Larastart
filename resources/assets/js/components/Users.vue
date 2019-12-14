<template>
  <div class="container">
    <div class="row mt-4 justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Users Table</h3>

            <div class="card-tools">
              <button class="btn btn-success" @click="newModal">
                Add New
                <i class="fas fa-user-plus fa-fw"></i>
              </button>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body table-responsive p-0">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Type</th>
                  <th>Registered At</th>
                  <th>Modify</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in users" :key="user.id">
                  <td>{{user.id}}</td>
                  <td>{{user.name}}</td>
                  <td>{{user.email}}</td>
                  <td>{{user.type | upText}}</td>
                  <td>{{user.created_at | myDate}}</td>
                  <td>
                    <a href="#" @click="editModal(user)">
                      <i class="fa fa-edit blue"></i>
                    </a>
                    /
                    <a href="#" @click="deleteUser(user.id)">
                      <i class="fa fa-trash red"></i>
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
    </div>

    <!-- Modal -->
    <div
      class="modal fade"
      id="addNew"
      tabindex="-1"
      role="dialog"
      aria-labelledby="addNewLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 v-show="editmode" class="modal-title" id="addNewLabel">Update User</h5>
            <h5 v-show="!editmode" class="modal-title" id="addNewLabel">Add New User</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form @submit.prevent="editmode ? updateUser() : createUser()">
          <div class="modal-body">
            <div class="form-group">
              <input
                v-model="form.name"
                placeholder="Name"
                type="text"
                name="name"
                class="form-control"
                :class="{ 'is-invalid': form.errors.has('name') }"
              />
              <has-error :form="form" field="name"></has-error>
            </div>

            <div class="form-group">
              <input
                v-model="form.email"
                placeholder="Email Address"
                type="email"
                name="email"
                class="form-control"
                :class="{ 'is-invalid': form.errors.has('email') }"
              />
              <has-error :form="form" field="email"></has-error>
            </div>
            
            <div class="form-group">
              <textarea
                v-model="form.bio"
                placeholder="Short bio (Optional)"
                type="bio"
                name="bio"
                class="form-control"
                :class="{ 'is-invalid': form.errors.has('bio') }"
              />
              <has-error :form="form" field="email"></has-error>
            </div>

            <div class="form-group">
              <select
                name="type"
                v-model="form.type"
                id="type"
                class="form-control"
                :class="{'is-invalid': form.errors.has('type')}"
              >
                <option value>Select User Role</option>
                <option value="admin">Admin</option>
                <option value="user">Standard User</option>
                <option value="author">Author</option>
              </select>
              <has-error :form="form" field="type"></has-error>
            </div>

            <div class="form-group">
              <input
                v-model="form.password"
                placeholder="Password"
                type="password"
                name="password"
                class="form-control"
                :class="{ 'is-invalid': form.errors.has('password') }"
              />
              <has-error :form="form" field="password"></has-error>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            <button v-show="editmode" type="submit" class="btn btn-success">Update</button>
            <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
          </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  
  data() {
    return {
      editmode: false,
      users : {},
      form: new Form({
        id: "",
        name: "",
        email: "",
        password: "",
        type: "",
        bio: "",
        photo: ""
      })
    };
  },

  methods: {
    editModal(user){
      this.editmode = true;
      this.form.reset();
      $('#addNew').modal('show');
      this.form.fill(user);
    },
    newModal(){
      this.editmode = false;
      this.form.reset();
      $('#addNew').modal('show');
    },

    deleteUser(id){
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.value) {
          this.$Progress.start();
          this.form.delete('api/user/'+id).then(()=>{
            Swal.fire(
              'Deleted!',
              'Your data has been deleted.',
              'success'
            )
            this.$Progress.finish();
            Fire.$emit('AfterCreate');
          }).catch(()=>{
            Swal("Failed!", "There was something wrong.", "warning");
          })
         
        }
      })
    },

    loadUsers(){
      this.$Progress.start();
      axios.get("api/user")
      .then(({ data }) => { 
        this.$Progress.finish();
        return this.users = data.data
      })
      .catch(() => {
        this.$Progress.fail();
      })
    },

    createUser(){
      this.$Progress.start();
      this.form.post('api/user')
      .then(() => {
         Fire.$emit('AfterCreate');
         $('#addNew').modal('hide');
         
         toast.fire({
           icon: 'success',
           title: 'User created successfully'
         })
         this.$Progress.finish();
      })
      .catch(() => {
        this.$Progress.fail();
      })
    },

    updateUser(){
      //console.log('Editing data');
      this.$Progress.start();
      this.form.put('api/user/'+this.form.id)
      .then(() => {
        Fire.$emit('AfterCreate');
        $('#addNew').modal('hide');

        Swal.fire(
          'Updated!',
          'Your data has been updated.',
          'success'
        )

        this.$Progress.finish();
      })
      .catch(() => {
        //Error
      });
    }
  },
  created() {

    this.loadUsers();

    Fire.$on('AfterCreate', () => {
      this.loadUsers();
    })
    //setInterval(() => this.loadUsers(), 3000);
  }

};
</script>
