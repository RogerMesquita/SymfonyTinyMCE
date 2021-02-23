import tinymce from 'tinymce';
import "tinymce/themes/silver/theme";
import "tinymce/plugins/image/plugin"
import "tinymce/icons/default/icons.min";
import "tinymce/skins/ui/oxide/skin.min.css";
import "tinymce/skins/ui/oxide/content.inline.min.css";
import "tinymce/skins/content/default/content.css";

// import "../../node_modules/tinymce/skins/lightgray/skin.min.css";
// import "../../node_modules/tinymce/skins/lightgray/content.min.css";


var form = document.querySelector('#tinymce_editor');

tinymce.init({
    selector: '#post_content',
    themes: "modern",
    plugins: 'image',
    toolbar: 'image',
    automatic_uploads: true,
    images_upload_url: '/attachment/'+form.dataset.postId, //url -> post_id
    file_picker_types: 'image',
    file_picker_callback: function (cb,value,meta){
        var input = document.createElement('input');
        input.setAttribute('type','file');
        input.setAttribute('accept','image/*');
        input.onchange = function (){
            var file = this.files[0];
            let reader = new FileReader();
            reader.onload = function (){
                let id = 'blobid' + (new Date()).getTime();
                let blobCache = tinymce.activeEditor.editorUpload.blobCache;
                let base64 = reader.result.split(',')[1];
                var blobInfo = blobCache.create(id,file,base64);
                blobCache.add(blobInfo);

                cb(blobInfo.blobUri(),{title: file.name});
            };
            reader.readAsDataURL(file);
        };
        input.click();
    }
})