$().ready(function(){
    
    $('#selectall').click(function(){
        if( $(this).prop("checked") == true){
            $('[name=id]:checkbox').each(function(){
                $(this).prop("checked",true);
            });
        }else{
            $('[name=id]:checkbox').each(function(){
                $(this).prop("checked",false);
            });
        }
    });

    $('#batchdelete').click(function(){
        var delete_ids = "";
        var got = false;
        $('input:checkbox[name=id]:checked').each(function(){
            if( $(this).prop("checked")==true ){
                got = true;
                if(delete_ids.length ==0)
                    delete_ids = $(this).val();
                else{
                    delete_ids += ","+$(this).val();
                }
            }
        });

        $('#delete_ids').val(delete_ids);
        console.log('delete:'+delete_ids);
        if(!got){
            alert('请选择至少一项');
            return false;
        }else{
            if (!confirm("确认删除？")) {
                return false;
            }else{
                $('#batch_delete_form').submit();
                return true;
            }
        }
    });

});