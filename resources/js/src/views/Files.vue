<template>
    <div id="files" class="data-list-container">
		<vx-card title="Files List">
            <div class="flex justify-between flex-wrap">
                <div>
                    Filter: <v-select v-model="filter" :options="[{label: 'All', value: 0},{label: 'Emails List', value: 1}, {label: 'Attachements', value: 2}]" class="w-full select-large" />
                </div>
                <div>
                    <vs-button class="float-left mr-3" color="success" type="filled" icon-pack="feather" icon="icon-repeat" @click="openMerge" icon-after>Merge Files</vs-button>
                    <vs-button color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="addPrompt = true" icon-after>Add File</vs-button>
                </div>
            </div>
            <vs-table search :data="filtered">
				<template slot="thead">
					<vs-th sort-key="id">#</vs-th>
					<vs-th sort-key="name">NAME</vs-th>
					<vs-th sort-key="count">TYPE</vs-th>
					<vs-th>Actions</vs-th>
				</template>

				<template slot-scope="{data}">
					<vs-tr :key="indextr" v-for="(tr, indextr) in data">
						<vs-td :data="tr.id">
							{{ tr.id }}
						</vs-td>
						<vs-td :data="tr.name">
							{{ tr.name }} 
                            <template v-if="tr.type_id == 1">
                                | Emails: {{ tr.lines }}
                            </template>
						</vs-td>
						<vs-td :data="tr.type_id">
							{{ tr.type_id | type }}
						</vs-td>
						<vs-td>
							<feather-icon icon="EditIcon" svgClasses="w-5 h-5 hover:text-primary stroke-current" class="cursor-pointer ml-2" @click="editFileName(tr.id, tr.name)"/>
							<feather-icon icon="TrashIcon" svgClasses="w-5 h-5 hover:text-danger stroke-current" class="cursor-pointer ml-2" @click="deleteFile(tr.id)"/>
						</vs-td>
					</vs-tr>
				</template>
			</vs-table>
		</vx-card>
        <vs-popup classContent="popup" title="Add File" :active.sync="addPrompt">
			<div class="vx-row mb-3">
                <div class="vx-col sm:w-1/2 w-full">
                    File type: <v-select v-model="data.type" :options="[{label: 'Emails List', value: 1}, {label: 'Attachements', value: 2}]" class="w-full select-large" />
                </div>
            </div>
            <div class="vx-row mb-3">
                <div class="vx-col sm:w-1/2 w-full">
                    <vs-input class="inputx" placeholder="File Name" v-model="data.name" />
                </div>
            </div>
            <div class="vx-row mb-3">
                <div class="vx-col sm:w-1/2 w-full">
                    <vs-button @click="$refs.uploadFileInput.click()">Upload File/Attachement</vs-button>
                    <input type="file" class="hidden" ref="uploadFileInput" @change="saveFile" accept="text/*|image/*">
                    <span class="ml-2" v-if="data.file">{{ data.file.name }}</span>
                </div>
			</div>
			<div class="flex justify-center flax-wrap">
				<vs-button color="primary" type="filled" @click="addFile" >Add File</vs-button>
			</div>
		</vs-popup>
        <vs-popup classContent="popup" title="Merge Files" :active.sync="mergePrompt">
            <h4>Drag & Drop to merge files</h4>
            <div class="vx-row mb-base mt-3">
				<div class="vx-col sm:w-1/2 w-full">
                    <vs-list class="drag-list">
                        <vs-list-header title="Existing Files" color="primary"></vs-list-header>
                        <draggable :list="list" group="files" class="p-2 cursor-move">
                            <vs-list-item v-for="(item, index) in list" :key="index" :title="item.name">
                                <vs-avatar slot="avatar" :text="getConverted(item.lines)" />
                                <feather-icon icon="PlusIcon" svgClasses="w-5 h-5 hover:text-success stroke-current" class="ml-2" @click="addFiletoList(item)"/>
                            </vs-list-item>
                        </draggable>
                    </vs-list>
                </div>
                <div class="vx-col sm:w-1/2 w-full">
                    <vs-list class="drag-list">
                        <vs-list-header :title="`Merged | Total: ${this.mergedTotal}`" color="primary"></vs-list-header>
                        <draggable :list="merge" group="files" class="p-2 cursor-move">
                            <vs-list-item v-for="(item, index) in merge" :key="index" :title="item.name">
                                <vs-avatar slot="avatar" :text="getConverted(item.lines)" />
                                <feather-icon icon="XIcon" svgClasses="w-5 h-5 hover:text-danger stroke-current" class="ml-2" @click="removeFileFromList(item)"/>
                            </vs-list-item>
                        </draggable>
                    </vs-list>
                </div>
            </div>
			
            <div class="vx-row mb-base">
                <div class="vx-col sm:w-1/2 mt-2 w-full">
					<vs-checkbox v-model="deleteAction">Delete Selected Files</vs-checkbox>
				</div>
				<div class="vx-col sm:w-1/2 w-full">
					<vs-input class="inputx" placeholder="New File Name" v-model="name" />
				</div>
			</div>
			<div class="flex justify-center flax-wrap">
				<vs-button color="primary" type="filled" @click="saveMerge" >Save new file</vs-button>
			</div>
		</vs-popup>
         <vs-prompt
            @cancel="close"
            @accept="updateFile"
            @close="close"
            :active.sync="editPrompt">
            <div class="con-exemple-prompt">
                <span>Enter File Name For Attachement</span>
                <vs-input placeholder="Name" vs-placeholder="Name" v-model="name" class="mt-3 w-full" />
            </div>
        </vs-prompt>
    </div>
</template>

<script>
import draggable from 'vuedraggable'
import vSelect from 'vue-select'

export default {
    data(){
        return {
            editPrompt: false,
            mergePrompt: false,
            addPrompt: false,
            filter: {label: 'All', value: 0},
            merge: [],
            name: null,
            deleteAction: false,
            file_id: null,
            data: {
                type: {label: 'Emails List', value: 1},
                file: null,
                name: null,
            },
            list: null,
        }
    },
    computed: {
        files(){
            return this.$store.getters.files;
        },
        filtered(){
             if (this.filter.value)
                return this.$store.getters.files.filter(f => f.type_id == this.filter.value);
            return this.$store.getters.files;
        },
        mergedTotal(){
            if (this.merge.length)
                return this.getConverted(this.merge.reduce((total, f) => total + f.lines, 0));
            else
                return '0';
        }
    },
    methods: {
        editFileName(id, name){
            this.editPrompt = true;
            this.file_id = id;
            this.name = name;
        },
        updateFile(){
            if (this.name == '')
                this.showDialog('File name cannot be empty', 1, 'File Update');
            else
            {
                let data = {file: this.file_id, name: this.name};
                this.$store.dispatch('updateFileName', data)
                .then(res => {
                    this.showDialog(res.data.message, 0, 'File Update');
                });
                this.editPrompt = false;
                this.file_id = null;
                this.name = null;
            }
        },
        deleteFile(id){
            this.$swal({
				title: 'Are you sure?',
				text: "You won't be able to revert this!",
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, delete it!'
				}).then((result) => {
				if (result.value) {
					this.$store.dispatch('deleteFile', id)
                    .then(res => {
                        this.showDialog(res.data.message, 0, 'File');
                    });
				}
			})  
        },
        saveFile(input){
            if (input.target.files && input.target.files[0]) {
				this.data.file = input.target.files[0];
			}
        },
        addFile(){
            if (!this.data.file || !this.data.name)
				this.showDialog('Please fill the form', 1, 'Adding File');
			else
			{
                this.data.type = this.data.type.value;
                this.$store.dispatch('uploadFile', this.data);
                this.data.type = {label: 'Emails List', value: 1};
				this.data.file = null;
				this.data.name = null;
				this.addPrompt = false;
			}
        },
        openMerge(){
            this.mergePrompt = true;
            this.list = this.files.filter(f => f.type_id == 1);
            this.merge = [];
            this.name = null;
            this.deleteAction = false;
        },
        saveMerge(){
            if (!this.name || this.merge.length < 2)
                this.showDialog('Please select files and set file name', 1, 'Merging Files');
            else
            {
                let data = {fs: this.merge.map(f => {
                    return f.id;
                }), name: this.name, deleteAction: this.deleteAction};
                this.$store.dispatch('saveMerge', data)
                .then(res => {
                    this.showDialog(res.data.message, 0, 'Merging');
                    this.mergePrompt = false;
                    this.merge = [];
                    this.name = null;
                    this.deleteAction = false;
                })
            }
        },
        addFiletoList(item){
            this.merge.push(item);
            this.list = this.list.filter(f => f.id != item.id);
        },
        removeFileFromList(item){
            this.merge = this.merge.filter(f => f.id != item.id);
            this.list.push(item);
        },
        getConverted(lines){
            if (lines / 1000 >= 1)
                return `${parseInt(lines / 1000)}K`;
            return `${lines}`;
        },
        close(){
            this.editPrompt = false;
            this.file_id = null;
            this.name = null;
        }
    },
    created(){
        if (this.files.length) return;
        this.$vs.loading();
        this.$store.dispatch('getFiles')
        .then(res => {
            this.$vs.loading.close();
        });
    },
    filters: {
        type(val){
            if (val == 1)
                return "Email List";
            else
                return "Attachement";
        }
    },
    components: {
        draggable,
        'v-select': vSelect,
    }
}
</script>

<style scoped>
.vs-popup{
    width: 800px !important;
}
</style>