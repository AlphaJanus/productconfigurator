define([
    'Magento_Ui/js/form/element/file-uploader'
], function (Uploader){
    'use strict';

    return Uploader.extend({
        initUploader: function(fileInput){
            var url = this.uploaderConfig.url + 'value/' + fileInput.name.match(/\[(\d+)\]/)[1] + '/';
            this.uploaderConfig.url = url;
            this._super(fileInput);
        }
    });
});