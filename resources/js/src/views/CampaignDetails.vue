
<template>
  <div id="campaign-details">
    <div id="campaign-data">
      <div class="vx-row">
        <div class="vx-col md:w-1/2 w-full mb-base">
          <vx-card title="Email Form" collapse-action>
            <div class="vx-row mb-6">
              <div class="vx-col sm:w-1/3 w-full">
                <span>Campaign Name</span>
              </div>
              <div class="vx-col sm:w-2/3 w-full">
                <vs-input class="w-full" v-model="data.name" />
              </div>
            </div>
            <div class="vx-row mb-6">
              <div class="vx-col sm:w-1/3 w-full">
                <span>From email</span>
              </div>
              <div class="vx-col sm:w-2/3 w-full">
                <vs-input class="w-full" v-model="data.from_email" />
              </div>
            </div>
            <div class="vx-row mb-6">
              <div class="vx-col sm:w-1/3 w-full">
                <span>From name</span>
              </div>
              <div class="vx-col sm:w-2/3 w-full">
                <vs-input class="w-full" type="email" v-model="data.from_name" />
              </div>
            </div>
            <div class="vx-row mb-6">
              <div class="vx-col sm:w-1/3 w-full">
                <span>Subject</span>
              </div>
              <div class="vx-col sm:w-2/3 w-full">
                <vs-input class="w-full" v-model="data.subject" />
              </div>
            </div>
            <div class="vx-row mb-6">
              <div class="vx-col sm:w-1/3 w-full">
                <span>EHLO Hostname</span>
              </div>
              <div class="vx-col sm:w-2/3 w-full">
                <vs-input class="w-full" v-model="data.helo" />
              </div>
            </div>
          </vx-card>
        </div>
        <div class="vx-col md:w-1/2 w-full mb-base">
          <vx-card title="Smtp Form" collapse-action>
            <div class="vx-row mb-6">
              <div class="vx-col sm:w-1/3 w-full">
                <span>Smtp Group</span>
              </div>
              <div class="vx-col sm:w-2/3 w-full">
                <v-select label="name" v-model="data.smtp" :options="smtps" class="w-full select-large" />
              </div>
            </div>
            <div class="vx-row mb-6">
              <div class="vx-col sm:w-1/3 w-full">
                <span>Rotation</span>
              </div>
              <div class="vx-col">
                <vs-input-number v-model="data.rotation" min="1"/>
              </div>
            </div>
            <div class="vx-row">
              <div class="vx-col sm:w-1/3 w-full">
                <span>Email Lists</span>
              </div>
              <div class="vx-col sm:w-2/3 w-full">
                <v-select multiple label="name" :closeOnSelect="false" v-model="data.emails" :options="files.filter(f => f.type_id == 1)" />
              </div>
            </div>
            <div class="vx-row mb-6">
              <div class="vx-col sm:w-1/3 w-full">
              </div>
              <div class="vx-col sm:w-2/3 upload-img mt-5">
                <vs-button @click="$refs.uploadFileInput.click()">Upload New Email List</vs-button>
              </div>
            </div>
            <template>
              <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                  <span>Auto_TLS</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                  <vs-checkbox v-model="data.auto_tls">Activate</vs-checkbox>
                </div>
              </div>
            </template>
          </vx-card>
        </div>
      </div>
      <div class="vx-col w-full mb-base">
        <vx-card title="Header Section" collapse-action>
          <div class="vx-row">
            <div class="vx-col sm:w-1/2 w-full mb-2">
              <vs-textarea class="header_txt" v-model="data.header" label="Header data" width="100%" height="270px" />
            </div>
            <div class="vx-col sm:w-1/2 w-full mb-2">
              <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                  <span>Charset</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                  <v-select v-model="data.charset" :options="charsets" class="w-full select-large" />
                </div>
              </div>
              <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                  <span>Encoding</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                  <v-select v-model="data.encoding" :options="encodings" class="w-full select-large" />
                </div>
              </div>
              <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                  <span>Encryption</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                  <v-select v-model="data.encryption" :options="encryptions" class="w-full select-large" />
                </div>
              </div>
              <div class="vx-row mb-6">
                <div class="vx-col sm:w-1/3 w-full">
                  <span>Content type</span>
                </div>
                <div class="vx-col sm:w-2/3 w-full">
                  <v-select v-model="data.content" :options="contents" class="w-full select-large" />
                </div>
              </div>
              <template v-if="data.content">
                <div class="vx-row mb-6" v-if="data.content.includes('multipart')">
                  <div class="vx-col sm:w-1/3 w-full">
                    <span>ICalendar</span>
                  </div>
                  <div class="vx-col sm:w-2/3 w-full">
                    <vs-checkbox v-model="data.iscal">Activate</vs-checkbox>
                  </div>
                </div>
              </template>
              <transition name="fade">
                <div class="vx-row" v-if="data.iscal">
                  <div class="vx-col sm:w-1/3 w-full">
                  </div>
                  <div class="vx-col sm:w-2/3 w-full">
                    <vs-textarea spellcheck="false" class="cal_txt" v-model="data.cal" label="Calendar data" width="100%" height="300px" />
                  </div>
                </div>
              </transition>
            </div>
          </div>
        </vx-card>
      </div>
      <div class="vx-col w-full mb-base">
        <vx-card title="Body Section" collapse-action>
          <div class="vx-row">
            <vs-tabs alignment="fixed">
              <vs-tab label="HTML">
                <div class="w-full mb-2 editor-holder">
                  <textarea ref="editor" v-model="html_body" class="editor" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"></textarea>
                  <pre v-highlightjs="html_body"><code class="html"></code></pre>
                </div>
              </vs-tab>
              <vs-tab label="TEXT">
                <div class="w-full mb-2">
                  <vs-textarea v-model="data.alt" class="log_txt" width="100%" height="390px" max-height="390px" />
                </div>
              </vs-tab>
            </vs-tabs>
          </div>
        </vx-card>
      </div>

      <div class="vx-col w-full mb-base" collapse-action>
        <vx-card title="Selected Attachements" class="mb-base w-full selected" v-if="data.attachements.length">
          <swiper :options="swiperOption" class="related-product-swiper px-12 py-6">
              <swiper-slide v-for="file in data.attachements" :key="file.id" class="item rounded">
                  <p class="text-archive name"> <span v-if="file.name">{{ file.name }}</span> <span v-else>None</span> <br/> <span class="text-sm">{{ file.path | src }}</span></p>
                  <div class="img back-icon rounded">
                    <img :src="url + '/storage/' + file.path" class="responsive" v-if="isImg(file.path)"/>
                    <feather-icon icon="FileTextIcon" svgClasses="w-16 h-16 stroke-current" v-else />
                    <div class="overlay" v-if="isImg(file.path)"></div>
                  </div>
                  <div class="add cursor-pointer" @click="addToHtml(file.name)">
                    <feather-icon icon="CopyIcon" svgClasses="w-8 h-8 text-white stroke-current"/>
                  </div>
              </swiper-slide>
              <div class="swiper-button-prev" slot="button-prev"></div>
              <div class="swiper-button-next" slot="button-next"></div>
          </swiper>
        </vx-card>
      </div>

      <vs-popup fullscreen title="Attachements" :active.sync="popupActive">
        <attachement :campaign="campaign" :files="files.filter(f => f.type_id == 2)" v-on:attachFile="attachFile" v-on:detachFile="detachFile" />
      </vs-popup>

      <div class="vx-col w-full mb-base">
        <vx-card>
          <div class="flex justify-between flex-wrap">
            <vs-button class="shadow-lg" @click="popupActive=true" type="gradient" color="#7367F0" gradient-color-secondary="#CE9FFC">Attachements</vs-button>
            <div class="flex justify-content-center">
              <vs-button :color="getStatusColor(data.status)" icon-pack="feather" icon="icon-play" :disabled="data.id == 0" icon-after type="filled" v-if="data.status == 0" @click="launch">Launch</vs-button>
              <vs-button :color="getStatusColor(data.status)" icon-pack="feather" icon="icon-rotate-ccw" icon-after type="filled" v-else-if="data.status == 2 || data.status == -1" @click="retry">Retry</vs-button>
              <vs-button :color="getStatusColor(data.status)" icon-pack="feather" icon="icon-loader" class="loading-btn" icon-after type="filled" v-else>Processing</vs-button>
              <vs-button color="primary" icon-pack="feather" icon="icon-activity" icon-after type="filled" class="ml-3" v-if="data.status == 0" @click="launchDebug">Debug</vs-button>
            </div>
            <vs-button color="primary" id="save-loading" type="filled" @click="saveCampaign">Save</vs-button>
          </div>
        </vx-card>
      </div>
      <div class="vx-col w-full mb-base" v-if="campaign">
          <vx-card title="Statistics" collapse-action>
            <div class="vx-row" >
              <div class="vx-col w-full mb-base">
                  <vx-card title="Campaign Overview">
                      <div slot="no-body">
                        <div class="vx-row text-center">
                            <div class="vx-col w-full lg:w-1/5 md:w-full sm:w-1/5 flex flex-col justify-between mb-4 lg:order-first md:order-last sm:order-first order-last">
                                <div class="lg:ml-6 lg:mt-6 md:mt-0 md:ml-0 sm:ml-6 sm:mt-6">
                                    <h2 class="font-bold text-4xl" >{{ getStatusTag(data.status) }}</h2>
                                    <small>Status</small>
                                </div>
                            </div>
                            <div class="vx-col w-full lg:w-4/5 md:w-full sm:w-4/5 justify-center mx-auto lg:mt-0 md:mt-6 sm:mt-0 mt-6">
                                <vue-apex-charts type=radialBar height=385 :options="analyticsData.campaignSends.chartOptions" :series="completedProgres" />
                            </div>
                        </div>
                        <div class="flex flex-row justify-between px-8 pb-4 mt-4">
                            <p class="text-center">
                              <span class="block">Completed</span>
                              <span class="text-2xl font-semibold">{{ completed }}</span>
                            </p>
                            <p class="text-center">
                              <span class="block">Pending</span>
                              <span class="text-2xl font-semibold">{{ pending }}</span>
                            </p>
                            <p class="text-center" v-if="stats">
                              <span class="block">Failed</span>
                              <span class="text-2xl font-semibold">{{ stats.failed }}</span>
                            </p>
                        </div>
                      </div>
                  </vx-card>
              </div>
              <div class="vx-col w-full" v-if="debug">
                  <vs-textarea v-model="logs" disabled class="log_txt" label="Debug Logs" width="100%" height="390px" max-height="390px" />
              </div>
            </div>
          </vx-card>
        </div>
    </div>
    <input type="file" class="hidden" ref="uploadFileInput" @change="uploadFile" accept="text/*">
  </div>
</template>

<script>
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import { quillEditor } from 'vue-quill-editor'
import Attachement from './Attachements.vue'
import 'swiper/dist/css/swiper.min.css'
import { swiper, swiperSlide } from 'vue-awesome-swiper'
import analyticsData from './analyticsData.js'
import VueApexCharts from 'vue-apexcharts'
import StatisticsCardLine from '@/components/statistics-cards/StatisticsCardLine.vue'
import vSelect from 'vue-select'


export default {
  data() {
    return {
      htmlPrompt: false,
      html_body: null,
      popupActive: false,
      analyticsData: analyticsData,
      logs: '',
      debug: 0,
      data: {
        id: '',
        name: '',
        smtp: '',
        from_email: '',
        from_name: '',
        rotation: 1,
        emails: [],
        attachements: [],
        encoding: '',
        charset: '',
        encryption: '',
        content: '',
        subject: '',
        iscal: false,
        auto_tls: false,
        cal: '',
        header: "MIME-Version: 1.0\nFrom: [from_name] <[from_email]>\nSubject: [subject]\nTo: [to]\nDate: [mail_date]",
        body: '',
        alt: '',
        status: 0,
        helo: '',
        files: [],
      },
      url: window.Laravel.url,
      editorOption: {
        modules: {
          toolbar: [
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{ 'header': 1 }, { 'header': 2 }],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            [{ 'script': 'sub' }, { 'script': 'super' }],
            [{ 'indent': '-1' }, { 'indent': '+1' }],
            [{ 'direction': 'rtl' }],
            [{ 'size': ['small', false, 'large', 'huge'] }],
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            [{ 'font': [] }],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'align': [] }],
            ['clean'],
            ['link'],
          ],
        },
        placeholder: 'Message',
      },
      encryptions: ['tls', 'ssl', 'none'],
      charsets: ['utf-8', 'iso-8859-1', 'us-ascii'],
      encodings: ['7bit', '8bit', 'base64', 'binary', 'quoted-printable'],
      contents: ['text/html', 'text/plain', 'multipart/alternative'],
      swiperOption: {
        slidesPerView: 5,
        spaceBetween: 55,
        breakpoints: {
            1600: {
                slidesPerView: 4,
                spaceBetween: 55,
            },
            1300: {
                slidesPerView: 3,
                spaceBetween: 55,
            },
            900: {
                slidesPerView: 2,
                spaceBetween: 55
            },
            640: {
                slidesPerView: 1,
                spaceBetween: 55
            }
        },
      },
    }
  },
  computed: {
    campaign(){
      return this.$store.getters.campaign;
    },
    smtps(){
      return this.$store.getters.smtps;
    },
    campaign_id(){
      return this.$store.getters.campaign_id;
    },
    files(){
      return this.$store.getters.files;
    },
    pending(){
      if (this.stats)
        return this.stats.pending;
      else
        return 0;
    },
    stats(){
      return this.$store.getters.campaignStats;
    },
    completed(){
      if (this.stats)
        return this.stats.completed;
      else
        return 0;
    },
    completedProgres(){
      if (this.stats)
      {
        let count = parseInt((this.stats.completed / (this.stats.pending + this.stats.completed)) * 100);
        return [(count) ? count : 0];
      }
      else
        return [0];
    },
  },
  methods: {
    setData(){
      this.data.name = this.campaign.name;
      this.data.smtp = this.campaign.smtp_group;
      this.data.from_email = this.campaign.from_email;
      this.data.subject = this.campaign.subject;
      this.data.content = this.campaign.content_type;
      this.data.from_name = this.campaign.from_name;
      this.data.charset = this.campaign.charset;
      this.data.helo = this.campaign.helo;
      this.data.encoding = this.campaign.encoding;
      if (this.campaign.rotation)
      this.data.rotation = this.campaign.rotation;
      this.data.encryption = this.campaign.encryption;
      if (this.campaign.header)
        this.data.header = this.campaign.header;
      this.data.body = this.campaign.body;
      this.data.alt = this.campaign.alt;
      this.html_body = this.campaign.body;
      this.data.cal = this.campaign.cal;
      this.data.iscal = this.campaign.iscal;
      this.data.status = this.campaign.status;
      this.data.auto_tls = this.campaign.auto_tls;
      this.data.emails = this.file_type(this.campaign.files, 1);
      this.data.attachements = this.file_type(this.campaign.files, 2);
    },
    isImg(src){
      let ext = src.split('.').pop();
      if (ext == 'png' || ext == 'jpeg' || ext == 'jpg')
        return 1;
      return 0;
    },
    file_type(files, type){
      if (files)
        return files.filter(f => f.type_id == type).map(f => {
            return f;
        });
      else
        return [];
    },
    uploadFile(input){
      if (input.target.files && input.target.files[0]) {
        let data = {file: input.target.files[0], type: 1};
        this.$store.dispatch('uploadFile', data)
        .then(res => {
          this.showDialog(res.data.message, 0, 'Upload');
        })
      }
    },
    valid_campaign(){
      if (!this.data.name || !this.data.smtp || !this.data.from_email || !this.data.subject || !this.data.content || !this.data.from_name
        || !this.data.charset || !this.data.helo || !this.data.encoding || !this.data.encryption || !this.data.header || !this.data.body || !this.data.emails)
        return (0);
      return (1);
    },
    saveCampaign(){
      this.btnLoading('#save-loading');
      this.data.files = this.data.emails.concat(this.data.attachements);
      this.data.body = this.html_body;
      this.$store.dispatch("saveCampaign", this.data)
      .then(res => {
        this.$vs.loading.close("#save-loading > .con-vs-loading");
        this.showDialog(res.data.message, 0, 'Campaign');
        if (this.data.id == 0)
          this.$router.push({path: `/campaign/${res.data.campaign.id}`})
      })
      .catch(err => {
        this.$vs.loading.close("#save-loading > .con-vs-loading");
        this.showDialog("Error saving!", 1, 'Campaign');
      });
    },
    attachFile(file){
      this.data.attachements.push(this.files.filter(f => f.id == file.id)[0]);
    },
    detachFile(file){
      this.data.attachements = this.data.attachements.filter(f => f != file.id);
    },
    btnLoading(btn_id){
      this.$vs.loading({
        background: 'primary',
        color: '#fff',
        container: btn_id,
        scale: 0.45
      });
    },
    getStatusColor(status) {
      if(status == 0) return "success"
      if(status == 1) return "warning"
      if(status == -1) return "danger"
      if(status == 2) return "primary"
    },
    getStatusTag(status) {
      if(status == 0) return "Pending"
      if(status == 1) return "Processing"
      if(status == -1) return "Failed"
      if(status == 2) return "Completed"
    },
    launch(){
      this.debug = 0;
      this.data.files = this.data.emails.concat(this.data.attachements);
      this.data.body = this.html_body;
      if (!this.valid_campaign())
        this.showDialog('Campaign forms are not completed', 1, 'Campaign');
      else
      this.$store.dispatch("saveCampaign", this.data)
      .then(res => {
        this.showDialog(res.data.message, 0, 'Campaign');
        this.$store.dispatch('launch', this.data.id)
        .then(res => {
          if (res.data.error)
            this.showDialog(res.data.message, 1, 'Campaign');
          else
            this.showDialog(res.data.message, 0, 'Campaign');
        })
      })
    },
    launchDebug(){
      this.debug = 1;
      this.logs = '';
      this.data.files = this.data.emails.concat(this.data.attachements);
      this.data.body = this.html_body;
      if (!this.valid_campaign())
        this.showDialog('Campaign forms are not completed', 1, 'Campaign');
      else
      this.$store.dispatch("saveCampaign", this.data)
      .then(res => {
        this.showDialog(res.data.message, 0, 'Campaign');
        this.$store.dispatch('launchDebug', this.data.id)
        .then(res => {
          if (res.data.error)
            this.showDialog(res.data.message, 1, 'Campaign');
          else
            this.showDialog(res.data.message, 0, 'Campaign');
        })
      })
    },
    retry(){
      let data = {id: this.data.id}
      this.$store.dispatch("retryCampaign", data);
    },
    addToHtml(name){
      this.html_body += ` <img src="cid:${name}" > `
    }
  },
  created() {
    let id = this.$route.params.id;
    this.data.id = parseInt(id);
    if (this.campaign_id === id){
      this.setData();
      return;
    }
    this.$store.dispatch("getSmtps");
    this.$store.dispatch("getFiles");
    this.$store.dispatch('fetchSettings');
    this.$store.commit('SET_CAMPAIGN_ID', id);
    if (id != 0) {
      this.$vs.loading();
      this.$store.dispatch("getCampaign", id)
      .then(res => {
        this.setData();
        this.$vs.loading.close();
      });
    }
  },
  components: {
    quillEditor,
    Attachement,
    swiper,
    swiperSlide,
    VueApexCharts,
    StatisticsCardLine,
    'v-select': vSelect,
  },
  filters: {
    src(val){
      return val.replace(/^.*[\\\/]/, '');
    }
  },
  mounted() {
    if (this.data.id == 0)
      this.$store.commit('RESET_DATA');
    else
    {
      this.$store.dispatch('getCampaignStats', this.data.id);
      window.Echo.channel("stats-channel").listen(".new-stats", e => {
        this.$store.commit("SET_CAMPAIGN_STATS", e.stats);
      });
      window.Echo.channel("status-channel").listen(".status", e => {
        this.$store.commit("UPDATE_CAMPAIGN_STATUS", e.campaign);
        this.data.status = e.campaign.status;
      });
      window.Echo.channel("debug-channel").listen(".debug", e => {
        this.logs += e.debug;
      });
    }
  },
}

</script>

<style scoped>
  .fade-enter-active, .fade-leave-active {
    transition: opacity .3s;
  }
  .fade-enter, .fade-leave-to {
    opacity: 0;
  }
  .item {
        width: 150px !important;
        height: 150px !important;
        margin-right: 20px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        transition: all 0.2s;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 18px 0px, rgba(0, 0, 0, 0.08) 0px 5px 12px 0px !important;
    }
    .img {
        width: 100%;
        height: 100%;
        background: #fff;
        text-align: center;
    }
    .img img{
      bottom: 0;
      position: absolute;
    }
    .img .overlay{
        width: 100%;
        height: 100%;
        position: absolute;
        transition: all 0.2s;
        background: rgba(0, 0, 0, 0.21);
    }
    .item:hover .overlay{
        background: rgba(0, 0, 0, 0.41);
    }
    .item .name{
        position: absolute;
        left: 10px;
        top: 5px;
        max-width: 135px;
        word-wrap: break-word;
        color: rgb(78, 78, 78);
        font-weight: 400;
        z-index: 10;
        font-family: sans-serif;
        line-height: 16px;
        font-size: 12px;
    }
    .item .name .text-sm{
        color: rgb(46, 45, 45);
        font-weight: 700;
    }
    .item:hover .add {
        opacity:1;
        -webkit-transform:translate(-50%,55%);
        transform:translate(-50%,55%);
    }
    .item .add{
        text-align: center;
        margin: 0;
        opacity: 0;
        position: absolute;
        bottom: 0;
        left: 50%;
        z-index: 200;
        border-radius: 50%;
        width: 200px;
        height: 200px;
        -webkit-transform: translate(-50%,80%);
        transform: translate(-50%,80%);
        border: 0;
        background: radial-gradient(ellipse at center,rgba(var(--vs-success),1) 0,transparent 70%);
        cursor: pointer;
        -webkit-transition: all .3s ease;
        transition: all .3s ease;
        -webkit-animation: imageRebound .7s ease!important;
        animation: imageRebound .7s ease!important;
    }
    
    .item .add::after{
        content: "";
        width: 150px;
        height: 150px;
        position: absolute;
        z-index: 100;
        display: block;
        left: 50%;
        -webkit-transform: translate(-50%,-50%);
        transform: translate(-50%,-50%);
        top: 50%;
        border-radius: 50%;
        border: 1px solid rgba(var(--vs-success),1);
        -webkit-animation: circle 1.5s ease infinite;
        animation: circle 1.5s ease infinite;
    }
</style>

<style lang="scss">
#avatar-col {
  width: 10rem;
}

#page-user-view {
  table {
    td {
      vertical-align: top;
      min-width: 140px;
      padding-bottom: .8rem;
      word-break: break-all;
    }

    &:not(.permissions-table) {
      td {
        @media screen and (max-width:370px) {
          display: block;
        }
      }
    }
  }
}


@media screen and (min-width:1201px) and (max-width:1211px),
only screen and (min-width:636px) and (max-width:991px) {
  #account-info-col-1 {
    width: calc(100% - 12rem) !important;
  }

}

</style>

<style>
.header_txt .vs-textarea {
  height: 250px;
}
.cal_txt .vs-textarea {
  height: 280px;
}
.log_txt .vs-textarea {
  height: 370px;
}
.loading-btn .icon-loader{
  animation:spin 4s linear infinite;
}
@keyframes spin { 100% { -webkit-transform: rotate(360deg); transform:rotate(360deg); } }


</style>

<style>
.editor-holder{
	width: 800px;
	height: 500px;
	margin-top: 20px;
	border-radius: 3px;
	position: relative;
	top: 0;
	background: #1f1f1f !important;
	overflow: auto;
	box-shadow: 5px 5px 10px 0px rgba(0, 0, 0, 0.4);
	transition: all 0.5s ease-in-out;
}
.editor-holder textarea,.editor-holder code {
		width: 100%;
		height: auto;
		min-height: 500px;
		
    border: 0;
		
    position: absolute;
		top: 0;
		left: 0;
		
    margin: 0;
		padding: 20px !important;
		
    font-size: 14px;
		font-family: Consolas,Liberation Mono,Courier,monospace;
		line-height: 21px;
		
    overflow: visible;
		
    transition: all 0.5s ease-in-out;
}

.editor-holder textarea{
		background: transparent !important;
		z-index: 2;
		height: auto;
		resize: none;
		color: #fff;
    text-shadow: 0px 0px 0px rgba(0, 0, 0, 0);
    text-fill-color: transparent;
		-webkit-text-fill-color: transparent;
}
.editor-holder textarea::-webkit-input-placeholder{
			color: rgba(255, 255, 255, 1);
		}
		
.editor-holder textarea:focus{
			outline: 0;
			border: 0;
			-webkit-box-shadow: none;
			-moz-box-shadow: none;
			box-shadow: none;
		}

.editor-holder textarea code{
		z-index: 1;
	}
</style>