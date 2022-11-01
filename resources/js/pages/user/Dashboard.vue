<template>
    <div class="mt-[10px] flex grid grid-cols-3 gap-4 content-start ml-[10%]">
        <video class="rounded" controls v-for="video in videos" :src="video.name" width="150"></video>
    </div>
    <div class="d-flex">
        <div class="w-1/2 bg-slate-800 ">
            <button class="rounded ml-[200px] mt-[20px] bg-[#0891b2]" id="start-camera"
                    @click="camera_button">
                Start Camera
            </button>
            <button class="rounded ml-5 mt-[20px] bg-[#16a34a]" id="stop-record"
                    @click="stop_button();saveVideo()">
                Save Recording
            </button>
            <video
                class="rounded-lg mt-[20px] ml-[70px] w-[500px] h-[400px]" ref="videoelem"
                autoplay :srcObject="camera_stream" >
            </video>
        </div>
        <div  class="w-1/2 bg-slate-400 text-blue-900" >
            <div class="ml-[15px]"  >
                <h2 class="mt-[20px] fs-3 text-center">Text</h2>
                <div class="mt-[20px]">
                    <div class="fs-4">
                        Chose text
                    </div>
                    <select v-model="selected"
                           id="template"
                           name="template"
                           class="bg-blue-50 border border-gray-300  text-sm
                           rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-[200px]
                           p-2.5 dark:bg-blue-700 dark:border-gray-600 dark:placeholder-blue-400
                           dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option v-for="template in templates"
                                :value="template.text" >
                            {{ template.name }}
                        </option>
                    </select>
                    <div class="mt-[10px] " v-for="user in users">
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
    created() {
        this.getTextList();
        this.getUserName();
        this.getVideos();
    },
    data(){
        return{
            users:{},
            videos:{},
            templates:[],
            selected: '',
            recorder:null,
            videoError:'',
            blobContainer:[],
            blobs_recorded : [],
            selectedTemplate:{},
            camera_stream : null,
            media_recorder : null,
            videoElem : this.$refs.videoelem,
        }
    },

    methods : {
        getUserName() {
            axios.post( '/get-username/').then(res => {
                this.users = res.data.users;
            });
        },

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

        camera_button() {
             navigator.mediaDevices
                .getUserMedia({ video: true, audio: true }).then(stream => {
                 this.camera_stream = stream;
                 this.recorder = new MediaRecorder(stream)
                 this.recorder.start();
                 this.recorder.ondataavailable = (e) =>{
                     this.blobContainer.push(e.data)
                 };
                 this.recorder.onerror =function (e){
                     return console.log(e.error || new Error(e.name))
                 }
             });
        },

        saveVideo(){
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
                this.camera_stream.getTracks().forEach(function(track) {
                    track.stop();
                });
            }
        },

        stop_button() {
            this.$refs.videoelem.pause();
            this.recorder.stop();
        },
    }
}
</script>

<style scoped>

</style>
