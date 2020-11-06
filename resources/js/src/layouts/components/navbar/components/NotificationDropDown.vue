<template>
  <!-- NOTIFICATIONS -->
  <vs-dropdown vs-custom-content vs-trigger-click class="cursor-pointer">
    <feather-icon icon="HelpCircleIcon" class="cursor-pointer hover:text-primary mt-1 sm:mr-6 mr-2" />

    <vs-dropdown-menu class="notification-dropdown dropdown-custom vx-navbar-dropdown">

      <div class="notification-top text-center p-5 bg-primary text-white">
        <h3 class="text-white">Info</h3>
        <p class="opacity-75">How to use</p>
      </div>

      <component :is="scrollbarTag" ref="mainSidebarPs" class="scroll-area--nofications-dropdown p-0 mb-10" :settings="settings" :key="$vs.rtl">
        <vs-list>
          <vs-list-header title="Header Tags Generator"></vs-list-header>
            <vs-list-item title="[an] or [NA]" subtitle="alpha-numeric character lower case"></vs-list-item>
            <vs-list-item title="[a]" subtitle="alphabetic character lower case"></vs-list-item>
            <vs-list-item title="[n]" subtitle="numeric character"></vs-list-item>
            <vs-list-item title="[N]" subtitle="numeric character"></vs-list-item>
            <vs-list-item title="[A]" subtitle="alphabetic character upper case"></vs-list-item>
            <vs-list-item title="[AN] or [NA]" subtitle="alpha-numeric character upper case"></vs-list-item>
            <vs-list-item title="[anx] or [nax]" subtitle="generate x characters for selected tag,this applies to all above tags => [an10] [n9]"></vs-list-item>
            <vs-list-item title="[fanx] or [fnax]" subtitle="generate a fixed characters for all tags that has same prefix => [fan8]"></vs-list-item>
            <vs-list-item title="[b'text']" subtitle="encode text to base64 : ex => [b'encode this']"></vs-list-item>
            <vs-list-item title="[u'url']" subtitle="encode url : ex => [u'http://example.com']"></vs-list-item>
            <vs-list-item title="[EQ'']" subtitle="encode text for emojis : ex => [EQ'😀']"></vs-list-item>
          <vs-list-header title="Attachements" color="success"></vs-list-header>
            <vs-list-item title="embedded image" subtitle="if the image specified with cid in html body it will be embedded in the mail"></vs-list-item>
            <vs-list-item title="specify cid" subtitle="cid can be specified with a name of the attachement that has the same FIXED tag in order to be embedded in the mail body=> <img cid='[fan8]'> and image name is [fan8]"></vs-list-item>
            <vs-list-item title="Warning" subtitle="make sure the fixed tag specified in cid is diffrenet from other images' cid"></vs-list-item>
            <vs-list-item title="attachement file" subtitle="if its not specified in the html body, it will go as attachement"></vs-list-item>
          <vs-list-header title="Debug mode" color="warning"></vs-list-header>
            <vs-list-item title="Enabled" subtitle="will send to only your debug email entred in settings"></vs-list-item>
        </vs-list>
      </component>

    </vs-dropdown-menu>
  </vs-dropdown>
</template>

<script>
import VuePerfectScrollbar from 'vue-perfect-scrollbar'

export default {
  components: {
    VuePerfectScrollbar
  },
  data() {
    return {
      settings: {
        maxScrollbarLength: 60,
        wheelSpeed: .60,
      },
    }
  },
  computed: {
    scrollbarTag() { return this.$store.getters.scrollbarTag }
  },
}

</script>

