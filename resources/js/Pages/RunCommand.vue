<template>
	<AppLayout title="Run Command">
	    <template #header>
	        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
	            Run Command
	        </h2>
	    </template>

	    <div class="pt-8 pb-10">
	        <div class="max-w-4xl mx-auto px-2 px-6 lg:px-8 space-y-2">
	        	<InputLabel for="command" value="Type your command below" />
	        	<TextInput
	        	    id="command"
	        	    name="command"
	        	    v-model="command"
	        	    class="w-full"
	        	/>
	        	<div v-if="output" class="text-black">
	        		Output: {{output}}
	        	</div>
	        	<div class="flex justify-end gap-2 text-black">
	        	    <primary-button @click="submit">Submit</primary-button>
	        	</div>
	        </div>
	    </div>
	</AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

export default {
	components: {
		AppLayout,
		InputLabel,
		TextInput,
		PrimaryButton
	},
	data() {
		return {
			command: null,
			output: null
		}
	},
	props: {

	},
	methods: {
		submit() {
			this.output = null;
			axios.post(route('post-run-command'), {
				command: this.command
			}).then((res) => {
				this.output = res.data.output ?? (res.data.success ? 'Command executed successfully' : '');
			}).catch((err) => {
				console.log(err.response.data);
			})
		}
	}
}
</script>