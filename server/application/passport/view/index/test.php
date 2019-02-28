{__NOLAYOUT__}
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=yes">
    <title>1</title>
    
<!-- 
<link rel="stylesheet" href="/static/webuploader/webuploader.css?v=1" />
<script src="/static/md5.js?v=1" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="/static/webuploader/webuploader.js?v=1"></script>
- -->
<script type="text/javascript" src="/static/jquery.min.js"></script> 
<link rel="stylesheet" href="/static/uploadify/uploadify.css" />
<script type="text/javascript" src="/static/uploadify/jquery.uploadify.min.js"></script>

</head>
<body data-l="1">

<!-- header -->

<input type="file" id="upload_picture_1" style="float:left;">
</body>
</html>

<script type="text/javascript">
            //上传图片
            /* 初始化上传插件 */
            $("#upload_picture_1").uploadify({
                'fileSizeLimit' : '5000KB',
                "height"          : 30,
                "swf"             : "/lib/uploadify/uploadify.swf",
                "fileObjName"     : "file",
                "buttonText"      : "上传图片",
                "uploader"        : 'http://183.60.136.3:32389/index.php?a=File&c=index&action=/user/init&param={%22a%22:1,%20%22b%22:2}',
                "width"           : 120,
                'removeTimeout'	  : 1,
                /*'fileTypeExts'	  : '<{$Think.config.PICTURE_UPLOAD.exts_js}>',*/
				'fileTypeExts'	  : '<{$Think.config.PICTURE_UPLOAD.exts_js}>',
                "onUploadSuccess" : uploadPicture_1,
                'onFallback' : function() {
                    alert('未检测到兼容版本的Flash.');
                }
            });
            function uploadPicture_1(file, data){
                console.log(data);
                /*
                var data = $.parseJSON(data);
                var src = '';
                if(data.status){
                    $("#icon_1").val(data.path);
                    src = data.url || '<{$Think.config.PIC_DOMAIN}>' + data.path;
                    $("#icon_1").parent().find('.upload-img-box').html(
                        '<div class="upload-pre-item"><img width="50" src="' + src + '"/></div>'
                    );
                }
            	*/
            }
</script>            