
<template>
  <div class="h-screen flex w-full bg-img vx-row no-gutter items-center justify-center" id="page-login">
    <div class="vx-col sm:w-1/2 md:w-1/2 lg:w-3/4 xl:w-3/5 sm:m-0 m-4">
      <vx-card>
        <div slot="no-body" class="full-page-bg-color">

          <div class="vx-row no-gutter justify-center items-center">

            <div class="vx-col hidden lg:block lg:w-1/2">
              <img :src="url + '/images/login.png'" alt="register" class="mx-auto">
            </div>

            <div class="vx-col sm:w-full md:w-full lg:w-1/2 d-theme-dark-bg">
              <div class="px-8 pt-8 login-tabs-container">

                <div class="vx-card__title mb-4">
                  <h4 class="mb-4">Login</h4>
                  <p>Welcome back, please login to your account.</p>
                </div>

                <vs-input
                    data-vv-validate-on="blur"
                    name="email"
                    icon-no-border
                    icon="icon icon-user"
                    icon-pack="feather"
                    label-placeholder="Email"
                    v-model="form.email"
                    class="w-full"/>
                <vs-input
                    data-vv-validate-on="blur"
                    type="password"
                    name="password"
                    icon-no-border
                    icon="icon icon-lock"
                    icon-pack="feather"
                    label-placeholder="Password"
                    v-model="form.password"
                    class="w-full mt-6" />

                <div class="flex flex-wrap justify-between mb-3 mt-3">
                  <vs-button :disabled="!validateForm" @click="authenticate">Login</vs-button>
                </div>

              </div>
            </div>
          </div>
        </div>
      </vx-card>
    </div>
  </div>
</template>

<script>
import {login} from '../../auth/auth.js';

export default{
  data() {
    return {
      form: {
        email: '',
        password: ''
      },
      url: window.Laravel.url,
    }
  },
  created(){
    this.$vs.loading();
    this.$store.dispatch('checkUser')
    .then(res => {
      this.$vs.loading.close();
      if (!res[0].length)
        this.$router.push({path: '/s3ndreg2020'});
    })
  },
  computed: {
    validateForm() {
      return this.form.email != '' && this.form.password != '';
    },
  },
  methods: {
    authenticate(){
      login(this.$data.form)
      .then((res) => {
          this.$store.commit('LOGIN', res);
          this.$router.push({path: '/campaigns'});
      })
      .catch((err) => {
          this.$vs.notify({title:'Error',text:'Email or password incorrect',color: '#EA5455',position:'top-center'})
      });
    }
  }
}
</script>

<style lang="scss">
#page-login {
  .social-login-buttons {
    .bg-facebook { background-color: #1551b1 }
    .bg-twitter { background-color: #00aaff }
    .bg-google { background-color: #4285F4 }
    .bg-github { background-color: #333 }
  }
}
</style>
