<template>
    <div id="attachements">
        <transition name="fade">
        <vx-card title="Selected" class="mb-base w-full selected" v-if="campaignFiles.length">
            <swiper :options="swiperOption" class="related-product-swiper px-12 py-6">
                <template v-for="file in campaignFiles">
                    <transition :key="file.id" name="fade">
                        <swiper-slide class="item rounded">
                            <p class="text-archive name"> <span v-if="file.name">{{ file.name }}</span> <span v-else>None</span> <br/><span class="text-sm">{{ file.path | src }}</span></p>
                            <div class="img back-icon rounded">
                                <img :src="url + '/storage/' + file.path" class="responsive" v-if="isImg(file.path)"/>
                                <feather-icon icon="FileTextIcon" svgClasses="w-16 h-16 stroke-current" v-else />
                                <div class="overlay" v-if="isImg(file.path)"></div>
                            </div>
                            <div class="edit cursor-pointer">
                                <feather-icon icon="EditIcon" svgClasses="w-6 h-6 text-white stroke-current" @click="editFileName(file.id, file.name)" />
                            </div>
                            <div class="delete cursor-pointer">
                                <feather-icon icon="TrashIcon" svgClasses="w-6 h-6 text-danger stroke-current" @click="detachFile(file)"/>
                            </div>
                        </swiper-slide>
                    </transition>
                </template>
                <div class="swiper-button-prev" slot="button-prev"></div>
                <div class="swiper-button-next" slot="button-next"></div>
            </swiper>
        </vx-card>
        </transition>
        <vs-prompt
            @cancel="close"
            @accept="updateFile"
            @close="close"
            :active.sync="editPrompt">
            <div class="con-exemple-prompt">
                <span>Enter File Name For Attachement</span>
                <vs-input placeholder="Name" vs-placeholder="Name" v-model="file_name" class="mt-3 w-full" />
            </div>
        </vs-prompt>
        <vx-card title="All">
            <div class="vx-row">
                <div class="vx-col w-full mb-base">
                    <div class="flex justify-between flax-wrap">
                        <input type="file" class="hidden" ref="uploadFileInput" @change="uploadFile" accept="image/*">
                        <vs-button @click="$refs.uploadFileInput.click()">Upload Attachement</vs-button>
                    </div>
                </div>
            </div>
            <div class="files">
                <template v-for="item in uniqueFiles">
                    <transition :key="item.id" name="fade" >
                        <div class="item rounded">
                            <p class="text-archive name">{{ item.path | src }}</p>
                            <div class="img back-icon rounded">
                                <img :src="url + '/storage/' + item.path" class="responsive" v-if="isImg(item.path)"/>
                                <feather-icon icon="FileTextIcon" svgClasses="w-16 h-16 stroke-current" v-else />
                                <div class="overlay" v-if="isImg(item.path)"></div>
                            </div>
                            <div class="add cursor-pointer" @click="attachFile(item)">
                                <feather-icon icon="PlusCircleIcon" svgClasses="w-8 h-8 text-white stroke-current"/>
                            </div>
                        </div>
                    </transition>
                </template>
            </div>
            <div class="clearfix"></div>
        </vx-card>
    </div>
</template>

<script>
import 'swiper/dist/css/swiper.min.css'
import { swiper, swiperSlide } from 'vue-awesome-swiper'

export default {
    props: {
        campaign: Object,
        files: Array,
    },
    data() {
        return {
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
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev'
                }
            },
            file: null,
            editPrompt: false,
            file_name: null,
            url: window.Laravel.url,
        }
    },
    computed: {
        campaignFiles(){
            if (this.campaign && this.campaign.files.length)
                return this.campaign.files.filter(f => f.type_id == 2);
            return [];
        },
        uniqueFiles(){
            return this.files.filter(f => {
                let exists = 0;
                if (this.campaign)
                    this.campaign.files.forEach(c => {
                        if (c.id == f.id)
                            exists = 1;
                    });
                if (!exists)
                    return f;
            });
        }
    },
    components: {
        swiper,
        swiperSlide,
    },
    methods: {
        uploadFile(input){
            if (input.target.files && input.target.files[0]) {
                let data = {file: input.target.files[0], type: 2};
                this.$store.dispatch('uploadFile', data);
            }
        },
        successUpload(){
            this.$vs.notify({color:'success',title:'Upload Success',text:'Lorem ipsum dolor sit amet, consectetur'})
        },
        editFileName(id, name){
            this.editPrompt = true;
            this.file = id;
            this.file_name = name;
        },
        updateFile(){
            if (this.file_name == '')
                this.$vs.notify({color:'danger',title:'Update Error',text:'File name cannot be empty'})
            else
            {
                let data = {file: this.file, name: this.file_name};
                this.$store.dispatch('updateFileName', data)
                .then(res => {
                    this.showDialog(res.data.message, 0, 'File Update');
                });
                this.editPrompt = false;
                this.file = null;
                this.file_name = null;
            }
        },
        detachFile(file){
            let data = {campaign: this.campaign.id, file: file.id}
            this.$store.dispatch('detachFile', data)
            .then(res => {
                this.showDialog(res.data.message, 0, 'File');
            });
            this.$emit('detachFile', file);
        },
        attachFile(file){
            let data = {campaign: this.campaign.id, file: file.id}
            this.$store.dispatch('attachFile', data)
            .then(res => {
                this.showDialog(res.data.message, 0, 'File');
            });
            this.$emit('attachFile', file);
        },
        isImg(src){
            let ext = src.split('.').pop();
            if (ext == 'png' || ext == 'jpeg' || ext == 'jpg')
                return 1;
            return 0;
        },
        close(){
            this.editPrompt = false;
            this.file = null;
            this.file_name = null;
        }
    },
    filters: {
        src(val){
            return val.replace(/^.*[\\\/]/, '');
        }
    }
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
    .files .item{
        float: left;
    }
    .item .edit, .item .delete {
        position: absolute;
        opacity: 0;
        text-align: center;
        margin: 0;
        z-index: 200;
        border-radius: 50%;
        width: 100px;
        height: 100px;
        border: 0;
        cursor: pointer;
        -webkit-transition: all .3s ease;
        transition: all .3s ease;
        -webkit-animation: imageRebound .7s ease!important;
        animation: imageRebound .7s ease!important;
    }
    .item .edit{
        left: -35%;
        bottom: -40%;
        background: radial-gradient(ellipse at center,rgba(var(--vs-success),1) 0,transparent 70%);
    }
    
    .item .delete{
        right: -35%;
        bottom: -40%;
        background: radial-gradient(ellipse at center,rgba(var(--vs-danger),1) 0,transparent 70%);
        
    }
    .item:hover .edit, .item:hover .delete{
        opacity:1;
    }
    .item .name{
        position: absolute;
        left: 10px;
        top: 5px;
        max-width: 130px;
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
<style>
    .con-vs-dialog{
        z-index: 53002;
    }
    .item .edit .feather-icon {
        top: 10%;
        left: 20%;
    }
    .item .delete .feather-icon {
        top: 10%;
        right: 20%;
    }
    .back-icon .feather-icon{
        opacity: 0.5;
        top: 35%;
    }
    .item .add .feather-icon {
        padding: 20px;
        top: 12%;
    }
</style>