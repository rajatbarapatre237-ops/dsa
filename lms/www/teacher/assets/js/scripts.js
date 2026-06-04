// ajax script for getting state data
$(document).on('change','#course', function(){
    var classID = $(this).val();
   
    if(classID){
        $.ajax({
            type:'POST',
            url:'getbatch.php',
            data:{'id':classID},
            success:function(result){
                $('#course').html(result);
                $('#assign').html('<option value="">No Assignment</option>');
                
            }
        }); 
    }else{
        $('#course').html('<option value="">Select Course</option>');
       
    }
});





 