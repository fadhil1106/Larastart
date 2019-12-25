<style>
.widget-user-header {
  background-position: center center;
  background-size: cover;
  height: 250px !important;
}
.img-profile{
  height: auto !important;
  width: 100% !important;
  max-width: 190px;
}
.widget-user-profile{
  left: 40% !important;
}

.widget-user-profile > img{
  border: 3px solid #ffffff;
}
</style>

<template>
  <div class="container">
    <div class="row">
      <div class="col-md-12 mt-3">
        <div class="card card-widget widget-user">
          <!-- Add the bg color to the header using any of the bg-* classes -->
          <div class="widget-user-header text-white blue-gradient" style="background-image: url('./img/user-cover.jpg')">
            <h3 class="widget-user-username text-right">{{ form.name }}</h3>
            <h5 class="widget-user-desc text-right">{{ form.email }}</h5>
            <div class="widget-user-profile">
              <img class="img-circle img-profile" :src="getProfilePhoto()" alt="User Avatar" />
          </div>
          </div>
          
          <div class="card-footer">
            <div class="row">
              <div class="col-sm-4 border-right">
                <div class="description-block">
                  <h5 class="description-header">3,200</h5>
                  <span class="description-text">SALES</span>
                </div>
                <!-- /.description-block -->
              </div>
              <!-- /.col -->
              <div class="col-sm-4 border-right">
                <div class="description-block">
                  <h5 class="description-header">13,000</h5>
                  <span class="description-text">FOLLOWERS</span>
                </div>
                <!-- /.description-block -->
              </div>
              <!-- /.col -->
              <div class="col-sm-4">
                <div class="description-block">
                  <h5 class="description-header">35</h5>
                  <span class="description-text">PRODUCTS</span>
                </div>
                <!-- /.description-block -->
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header p-2">
            <ul class="nav nav-pills">
              <li class="nav-item">
                <a class="nav-link" href="#settings" data-toggle="tab">Activity</a>
              </li>
              <li class="nav-item">
                <a class="nav-link active" href="#settings" data-toggle="tab">Settings</a>
              </li>
            </ul>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="tab-content">
              <div class="tab-pane active" id="settings">
                <form class="form-horizontal">
                  <div class="form-group row">
                    <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                      <input
                        type="text"
                        v-model="form.name"
                        class="form-control"
                        id="inputName"
                        placeholder="Name"
                        :class="{ 'is-invalid': form.errors.has('name') }"
                      />
                      <has-error :form="form" field="name"></has-error>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                      <input
                        type="email"
                        v-model="form.email"
                        class="form-control"
                        id="inputEmail"
                        placeholder="Email"
                        :class="{ 'is-invalid': form.errors.has('email') }"
                      />
                      <has-error :form="form" field="email"></has-error>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputExperience" class="col-sm-2 col-form-label">Experience</label>
                    <div class="col-sm-10">
                      <textarea
                        class="form-control"
                        v-model="form.bio"
                        id="inputExperience"
                        placeholder="Experience"
                        :class="{ 'is-invalid': form.errors.has('bio') }"
                      ></textarea>
                      <has-error :form="form" field="bio"></has-error>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputProfilePhoto" class="col-sm-2 col-form-label">Profile Photo</label>
                    <div class="col-sm-10">
                      <input
                        type="file"
                        @change="updateProfile"
                        class="form-control-file"
                        id="inputProfilePhoto"
                        :class="{ 'is-invalid': form.errors.has('photo') }"
                      />
                      <small id="photoHelpBlock" class="form-text text-muted">
                        Photo must be less than 2MB.
                      </small>
                      <has-error :form="form" field="photo"></has-error>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputPassword" class="col-sm-2 col-form-label">Change Password</label>
                    <div class="col-sm-10">
                      <input
                        type="password"
                        v-model="form.password"
                        class="form-control"
                        id="inputPassword"
                        placeholder="Password"
                        :class="{ 'is-invalid': form.errors.has('password') }"
                      />
                      <strong>(Leave empty if not changing)</strong>
                      <has-error :form="form" field="password"></has-error>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="offset-sm-2 col-sm-10">
                      <button @click.prevent="updateInfo" type="submit" class="btn btn-success">Update</button>
                    </div>
                  </div>
                </form>
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.card-body -->
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      currentPhoto:"",
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
    getProfilePhoto(){
      let profilePhoto = (this.form.photo.match(/\//) ? this.currentPhoto : this.form.photo);
      return "img/profile/"+profilePhoto;
    },

    updateInfo(){
      this.$Progress.start();
      this.form.put('api/profile')
      .then(() => {
        this.$Progress.finish();
        Fire.$emit('loadProfile');
        toast.fire({
          icon: 'success',
          title: 'Profile updated successfully'
        })
      })
      .catch(() => {
        this.$Progress.fail();
        toast.fire({
          icon: 'error',
          title: 'Profile update error'
        })
      })
    },

    updateProfile(e) {
      //console.log('Uploading file......');
      let file = e.target.files[0];
      console.log(file);
      let reader = new FileReader();
      if (file['size'] < 2111775) {
        reader.onloadend = (file) => {
          // console.log("RESULT", reader.result);
          this.form.photo = reader.result;
        };
        reader.readAsDataURL(file);
      }else{
        Swal.fire(
          'Oppss....',
          'Your photo must be less than 2MB',
          'error'
        )
      }
    },

    loadProfile(){
      this.$Progress.start();
      axios
        .get("api/profile")
        .then(({ data }) => {
          this.$Progress.finish();
          this.currentPhoto = data.photo;
          return this.form.fill(data);
        })
        .catch(() => {
          this.$Progress.fail();
        });
    }
  },

  mounted() {
    console.log("Component mounted.");
  },

  created() {
    this.loadProfile();

    Fire.$on('loadProfile', () => {
      this.loadProfile();
    })
  }
};
</script>
