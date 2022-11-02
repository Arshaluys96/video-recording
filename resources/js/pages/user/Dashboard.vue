<template>
    <div class="mt-[10px] flex grid grid-cols-3 gap-4 content-start ml-[10%]">
        <video class="rounded" controls v-for="video in videos" :src="video.name" width="150"></video>
    </div>
    <div class="d-flex">
        <div class="w-1/2 bg-slate-800">
            <div class="w-full text-center">
                <button v-if="button"
                        class="rounded mt-[10px] bg-[#0891b2]" id="start-camera"
                        @click="cameraButton">
                    Start Camera
                </button>
                <button v-else
                        class="rounded  mt-[10px] bg-[#16a34a]" id="stop-record"
                        @click="saveVideo">
                    Save Recording
                </button>
            </div>
            <video
                class="rounded-lg w-full text-center mt-[10px] w-[100%] h-[80%]" ref="videoelem"
                autoplay :srcObject="cameraStream" >
            </video>
        </div>
        <div  class="w-1/2 bg-slate-400 text-blue-900" >
            <div class="ml-[15px]"  >
                <h2 class="mt-[20px] fs-3 text-center">Text</h2>
                <div class="mt-[20px]">
                    <div class="fs-4">
                        Chose text
                    </div>
                    <select v-model="selectedTemplateId"
                            @change="getTemplateText"
                           id="template"
                           name="template"
                           class="bg-blue-50 border border-gray-300  text-sm
                           rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-[200px]
                           p-2.5 dark:bg-blue-700 dark:border-gray-600 dark:placeholder-blue-400
                           dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option v-for="template in templates"
                                :value="template.id">
                            {{ template.name }}
                        </option>
                    </select>
                    <div class="mt-[10px]">
                        {{ selected }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: "UserDashboard",
    data(){
        return{
            users: {},
            videos: {},
            button: true,
            selected: '',
            templates: [],
            recorder: null,
            videoError: '',
            blobContainer: [],
            blobs_recorded : [],
            selectedTemplate: {},
            cameraStream : null,
            media_recorder : null,
            selectedTemplateId: null,
            videoElem : this.$refs.videoelem,
        }
    },

    methods : {
        getTextList() {
            axios.post('/get-text-list/').then(res => {
                this.templates = res.data.templates;
            });
        },

        getVideos() {
            axios.post('/get-videos/').then(res => {
                this.videos = res.data.videos;
            });
        },

        getTemplateText() {
            axios.post('/get-single-template/', { id: this.selectedTemplateId })
                .then(res => {
                    this.selected = res.data.text;
                });
        },

        cameraButton() {
            this.blobContainer = [];
            this.button = false
            navigator.mediaDevices
                .getUserMedia({ video: true, audio: true })
                .then(stream => {
                    this.cameraStream = stream;
                    this.recorder = new MediaRecorder(stream)
                    this.recorder.start();
                    this.recorder.ondataavailable = (e) => {
                        this.blobContainer.push(e.data)
                    };
                    this.recorder.onerror = function (e) {
                        return console.log(e.error || new Error(e.name))
                    }
                });
        },

        saveVideo(){
            this.button = true
            this.recorder.onstop = async (e) => {
                let blobUrl = window.URL.createObjectURL(new Blob(this.blobContainer));
                let blob = await fetch(blobUrl).then(r => r.blob());
                let formData = new FormData();
                formData.append('video', blob);
                axios.post("/video/",
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                ).then(res => {
                    this.videos.push(res.data.file);
                }).catch(res => {
                    this.videoError = "error"
                });
                this.cameraStream.getTracks().forEach(function(track) {
                    track.stop();
                });
            }
            this.$refs.videoelem.pause();
            this.$refs.videoelem.srcObject = null;
            this.recorder.stop();
        },
    },

    created() {
        this.getTextList();
        this.getVideos();
    }
}
</script>

<style scoped>

</style>
