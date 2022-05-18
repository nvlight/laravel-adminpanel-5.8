<script>
    function changeProfile(){
        $('#file').click();
    }

    $('#file').change(function (){
        if ($(this).val() !== ''){
            upload(this);
        }
    });

    function upload(img){
        let form_data = new FormData();
        form_data.append('file', img.files[0]);
        //form_data.append('_token','{{csrf_token()}}');

        $('#loading').css('display','block');
        $.ajax({
            headers: {
                //'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-CSRF-TOKEN': '{{csrf_token()}}',
            },
            mimeType: 'multipart/form-data',
            url: '{{url('/admin/products/ajax-image-upload')}}',
            type: 'POST',
            data: form_data,
            contentType: false,
            processData: false,
            success: function (data){
                if (data.fail){
                    $('#preview_image').attr('src','{{asset('images/no_image.png')}}')
                    alert(data.errors['file'])
                }else{
                    $('#file_name').val(data);
                    $('#preview_image').attr('src','{{asset('uploads/single')}}/'+data)
                }
                $('#loading').css('display','none');
            },
            error: function (xhr, status, error){
                alert(xhr.responseText);
                $('#preview_image').attr('src','{{asset('images/no_image.png')}}');
            }
        })
    }

    function removeFile() {
        if ($('#file_name').val() != '')
            if (confirm('Вы точно хотите удалить эту картинку?')) {
                $('#loading').css('display', 'block');
                var form_data = new FormData();
                form_data.append('_method', 'DELETE');
                form_data.append('_token', '{{csrf_token()}}');
                $.ajax({
                    url: '{{url('/admin/products/ajax-image-remove')}}'+ '/' + $('#file_name').val(),
                    data: form_data,
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        $('#preview_image').attr('src', '{{asset('images/no_image.png')}}');
                        $('#file_name').val('');
                        $('#loading').css('display', 'none');
                    },
                    error: function (xhr, status, error) {
                        alert(xhr.responseText);
                    }
                });
            }
    }

    function removeFileImg() {
        let imgA = $('a.myimg');
        let img = imgA.data('name');
        if (img != '')
            if (confirm('Вы точно хотите удалить эту картинку?')) {
                $('#loading').css('display', 'block');
                var form_data = new FormData();
                form_data.append('_method', 'DELETE');
                form_data.append('_token', '{{csrf_token()}}');
                $.ajax({
                    url: '{{url('/admin/products/ajax-image-remove')}}'+ '/' + img,
                    data: form_data,
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        $('#preview_image').attr('src', '{{asset('images/no_image.png')}}');
                        imgA.data('name','');
                        $('#loading').css('display', 'none');
                    },
                    error: function (xhr, status, error) {
                        alert(xhr.responseText);
                    }
                });
            }
    }
</script>
