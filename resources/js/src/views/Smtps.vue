<template>
	<div id="smtps" class="data-list-container">
		<vx-card title="Smtps List">
			<template slot="actions">
				<vs-button color="primary" type="filled" icon-pack="feather" icon="icon-plus" @click="addPrompt = true" icon-after>Add Smtp Group</vs-button>
			</template>
			<vs-table search :data="smtps">
				<template slot="thead">
					<vs-th sort-key="id">#</vs-th>
					<vs-th sort-key="name">NAME</vs-th>
					<vs-th sort-key="count">SMTPS</vs-th>
					<vs-th>Actions</vs-th>
				</template>

				<template slot-scope="{data}">
					<vs-tr :key="indextr" v-for="(tr, indextr) in data">
						<vs-td :data="tr.id">
							{{ tr.id }}
						</vs-td>
						<vs-td :data="tr.name">
							{{ tr.name }}
						</vs-td>
						<vs-td :data="tr.smtps_count">
							{{ tr.smtps_count }}
						</vs-td>
						<vs-td>
							<feather-icon icon="EyeIcon" svgClasses="w-5 h-5 hover:text-primary stroke-current" class="cursor-pointer ml-2" @click="showSmtp(tr)" />
							<feather-icon icon="EditIcon" svgClasses="w-5 h-5 hover:text-primary stroke-current" class="cursor-pointer ml-2" @click="editSmtp(tr.id, tr.name)" />
							<feather-icon icon="TrashIcon" svgClasses="w-5 h-5 hover:text-danger stroke-current" class="cursor-pointer ml-2" @click="deleteSmtp(tr.id)"/>
						</vs-td>
					</vs-tr>
				</template>
			</vs-table>
		</vx-card>
		<vs-prompt
            @cancel="close"
            @accept="updateSmtp"
            @close="close"
            :active.sync="editPrompt">
            <div class="con-exemple-prompt">
                <span>Enter Smtp Group Name</span>
                <vs-input placeholder="Name" vs-placeholder="Name" v-model="name" class="mt-3 w-full" />
            </div>
        </vs-prompt>
		<vs-popup classContent="popup" title="Add Smtp Group" :active.sync="addPrompt">
			<div class="vx-row mb-base">
				<div class="vx-col sm:w-1/2 w-full">
					<vs-input class="inputx" placeholder="Smtp Group Name" v-model="name" />
				</div>
				<div class="vx-col sm:w-1/2 w-full">
					<vs-button @click="$refs.uploadFileInput.click()">Upload Smtps List</vs-button>
					<input type="file" class="hidden" ref="uploadFileInput" @change="saveFile" accept="text/*">
				</div>
			</div>
			<div class="flex justify-center flax-wrap">
				<vs-button color="primary" type="filled" @click="addSmtp" >Add Group</vs-button>
			</div>
		</vs-popup>
		<vs-popup classContent="popup" title="Smtps List" :active.sync="showPrompt">
			<vs-list>
				<vs-list-item :key="index" :title="`${tr.username} | ${tr.password} | ${tr.host} | ${tr.port}`" v-for="(tr, index) in group_smtps"></vs-list-item>
			</vs-list>
		</vs-popup>
	</div>
</template>

<script>
export default {
	data()
	{
		return {
			addPrompt: false,
			editPrompt: false,
			showPrompt: false,
			smtp: 0,
			group: null,
			name: null,
			file: null,
		}
	},
	computed: {
		smtps(){
			return this.$store.getters.smtps;
		},
		group_smtps(){
			if (this.group)
				return this.group.smtps;
			return [];
		}
	},
	methods: {
		editSmtp(id, name){
			this.editPrompt = true;
			this.smtp = id;
			this.name = name;
		},
		deleteSmtp(id){
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
					this.$store.dispatch('deleteSmtp', id)
					.then(res => {
						this.showDialog(res.data.message, 0, 'Smtp');
					});
				}
			})
		},
		updateSmtp(){
			let data = {smtp: this.smtp, name: this.name}
			this.$store.dispatch('updateSmtp', data)
			.then(res => {
                this.showDialog(res.data.message, 0, 'Smtp');
            });
			this.close();
		},
		close(){
			this.editPrompt = false;
			this.showPrompt = false;
			this.smtp = 0;
			this.name = null;
		},
		addSmtp(){
			if (!this.name)
				this.showDialog('Please fill the form', 1, 'Adding Smtp');
			else
			{
				let data = {name: this.name, file: this.file}
				this.$store.dispatch('addSmtp', data)
				.then(res => {
					this.showDialog(res.data.message, 0, 'Smtp');
				});
				this.name = null;
				this.file = null;
				this.addPrompt = false;
			}
		},
		saveFile(input){
			if (input.target.files && input.target.files[0]) {
				this.file = input.target.files[0];
			}
		},
		showSmtp(group){
			if (!group.smtps)
				this.$store.dispatch('getSmtpsData', group.id).then(res => {
					this.group = this.smtps.filter(s => s.id == group.id)[0];
				});
			else
				this.group = this.smtps.filter(s => s.id == group.id)[0];
			this.showPrompt = true;
		},
	},
	created(){
		if (this.smtps.length) return;
		this.$vs.loading();
		this.$store.dispatch('getSmtps')
		.then(res => {
            this.$vs.loading.close();
        });
	},

}
</script>