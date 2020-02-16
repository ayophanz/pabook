<template>
    <div class="uploader"
        @dragenter="OnDragEnter"
        @dragleave="OnDragLeave"
        @dragover.prevent
        @drop="onDrop"
        :class="{ dragging: isDragging }">
        
        <div class="upload-control" v-show="images.length">
            <label for="file" class="btn btn-primary btn-flat">Select a file</label>
        </div>

        <div v-show="!images.length">
            <i class="fa fa-cloud-upload"></i>
            <p>Drag your images here</p>
            <div>OR</div>
            <div class="file-input">
                <label for="file">Select a file</label>
                <input type="file" id="file" @change="onInputChange" multiple>
            </div>
        </div>

        <div class="images-preview" v-show="images.length">
            <div class="img-wrapper" v-for="(image, index) in images" :key="index">
                <span class="remove-img" @click="removeImg(index)"><i class="fas fa-times-circle"></i></span>
                <img :src="image" :alt="`Image Uplaoder ${index}`">
                <div class="details">
                    <span class="name" v-text="files[index].name"></span>
                    <span class="size" v-text="getFileSize(files[index].size)"></span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props:['dataValue'],
    data: () => ({
        isDragging: false,
        dragCount: 0,
        files: [],
        images: []
    }),
    methods: {
        removeImg(index) {
            this.files.splice(index, 1);
            this.images.splice(index, 1);
        },
        OnDragEnter(e) {
            e.preventDefault();
            this.dragCount++;
            this.isDragging = true;
            return false;
        },
        OnDragLeave(e) {
            e.preventDefault();
            this.dragCount--;
            if (this.dragCount <= 0)
                this.isDragging = false;
        },
        onInputChange(e) {
            const files = e.target.files;
            Array.from(files).forEach(file => this.addImage(file));
        },
        onDrop(e) {
            e.preventDefault();
            e.stopPropagation();
            this.isDragging = false;
            const files = e.dataTransfer.files;
            Array.from(files).forEach(file => this.addImage(file));
        },
        addImage(file) {
            if (!file.type.match('image.*')) {
                this.$toastr.e(`${file.name} is not an image`);
                return;
            }
            
            const img = new Image(),
                reader = new FileReader();
            if(file['size'] < 300000) {
                this.files.push(file);
                reader.onload = (e) => this.images.push(e.target.result);
                reader.readAsDataURL(file);
            }else{
                toast.fire({
                    type: 'error',
                    title: 'You are uploading large file, Please upload only less than 300kb file.'
                })
            }    
            
        },
        getFileSize(size) {
            const fSExt = ['Bytes', 'KB', 'MB'];
            let i = 0;
            
            while(size > 900) {
                size /= 1024;
                i++;
            }
            return `${(Math.round(size * 100) / 100)} ${fSExt[i]}`;
        },
        upload() {
            let images = [];
            let count = 0;
            this.files.forEach(file => {
                images.push([{filesize:file.size}, {filename:file.name}, {image:this.images[count]}]);
                count++;
            });
            this.$emit('images', images);
        },
        resetField() {
            this.isDragging = false;
            this.dragCount = 0;
            this.files = [];
            this.images = [];
        }
    },
    created() {
        fire.$on('resetGallery', this.resetField); 
        fire.$on('uploadImage', this.upload); 
    }
}
</script>

<style lang="scss" scoped>
.uploader {
    width: 100%;
    background: #ced4da;
    color: #fff;
    padding: 40px 15px;
    text-align: center;
    border: 1px solid #ced4da;
    font-size: 20px;
    position: relative;
    &.dragging {
        background: #fff;
        color: #2196F3;
        border: 3px dashed #ced4da;
        .file-input label {
            background: #2196F3;
            color: #fff;
        }
    }
    i.main-icon {
        font-size: 85px;
    }
    .file-input {
        width: 200px;
        margin: auto;
        height: 68px;
        position: relative;
        label,
        input {
            background: #3490dc;
            color: #ffffff;
            width: 100%;
            position: absolute;
            left: 0;
            top: 0;
            padding: 10px;
            margin-top: 7px;
            cursor: pointer;
        }
        input {
            opacity: 0;
            z-index: -2;
        }
    }
    .images-preview {
        display: flex;
        flex-wrap: wrap;
        margin-top: 20px;
        flex-flow: wrap-reverse;
        flex-direction: row-reverse;
        .img-wrapper {
            width: 160px;
            display: flex;
            flex-direction: column;
            margin: 10px;
            height: 150px;
            justify-content: space-between;
            background: #fff;
            box-shadow: 5px 5px 20px #3e3737;
            position: relative;
            img {
                max-height: 105px;
            }
        }
        .details {
            font-size: 12px;
            background: #fff;
            color: #000;
            display: flex;
            flex-direction: column;
            align-items: self-start;
            padding: 3px 6px;
            .name {
                overflow: hidden;
                height: 18px;
            }
        }
    }
    .upload-control {
        position: absolute;
        width: 100%;
        background: #fff;
        top: 0;
        left: 0;
        padding: 10px;
        padding-bottom: 4px;
        text-align: right;
        button, label {
            font-size: 15px;
            cursor: pointer;
        }
        label {
            margin: 0px;
        }
    }
}
span.remove-img {
    background: white;
    position: absolute;
    right: -10px;
    top: -5px;
    border-radius: 100px;
    display: inherit;
    height: 19px;
}
span.remove-img i {
    color: red;
    vertical-align: top;
}
span.remove-img:hover {
    transform: scale(1.2);
    cursor: pointer;
}
</style>