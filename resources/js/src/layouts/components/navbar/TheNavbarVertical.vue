
<template>
  <div class="relative">

    <div class="vx-navbar-wrapper" :class="classObj">

      <vs-navbar class="vx-navbar navbar-custom navbar-skelton" :color="navbarColorLocal" :class="textColor">

        <!-- SM - OPEN SIDEBAR BUTTON -->
        <feather-icon class="sm:inline-flex xl:hidden cursor-pointer p-2" icon="MenuIcon" @click.stop="showSidebar" />

        <loader />

        <vs-spacer />

        <notification-drop-down />
        
        <logout-button />

      </vs-navbar>
    </div>
  </div>
</template>


<script>
import Loader            from "./components/Loader.vue"
import LogoutButton      from "./components/LogoutButton.vue"
import NotificationDropDown from "./components/NotificationDropDown.vue"

export default {
  name: "the-navbar-vertical",
  props: {
    navbarColor: {
      type: String,
      default: "#fff",
    },
  },
  components: {
    Loader,
    LogoutButton,
    NotificationDropDown,
  },
  computed: {
    navbarColorLocal() {
      return this.$store.state.theme === "dark" && this.navbarColor === "#fff" ? "#10163a" : this.navbarColor
    },
    verticalNavMenuWidth() {
      return this.$store.state.verticalNavMenuWidth
    },
    textColor() {
      return {'text-white': (this.navbarColor != '#10163a' && this.$store.state.theme === 'dark') || (this.navbarColor != '#fff' && this.$store.state.theme !== 'dark')}
    },
    windowWidth() {
      return this.$store.state.windowWidth
    },

    // NAVBAR STYLE
    classObj() {
      if (this.verticalNavMenuWidth == "default")      return "navbar-default"
      else if (this.verticalNavMenuWidth == "reduced") return "navbar-reduced"
      else if (this.verticalNavMenuWidth)              return "navbar-full"
    },
  },
  methods: {
    showSidebar() {
      this.$store.commit('TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE', true);
    }
  }
}
</script>

