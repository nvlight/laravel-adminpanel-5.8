<script>
    $(document).ready(function(){
        $('.select2').select2({
           placeholder: 'Начните вводить наименование товара',
           cache: true,
           ajax:{
               url: '{{url('/admin/products/related')}}',
               delay: 250,
               dataType: 'json',
               data: function (params){
                   return {
                       q: params.term,
                       page: params.page,
                   }
               },
               processResults: function (data, params){
                   return {
                       results: data.items
                   }
               }
           }
        });
    });
</script>
