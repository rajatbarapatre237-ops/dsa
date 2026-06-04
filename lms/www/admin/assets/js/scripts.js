// ajax script for getting state data
$(document).on('change','#course', function(){
    var classID = $(this).val();
   
    if(classID){
        $.ajax({
            type:'POST',
            url:'getBatch.php',
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

$(document).on('change','#course', function(){
    var classID = $('#class').val();
    var courseID = $(this).val();
   console.log(classID+" "+ courseID);
    if(classID){
        $.ajax({
            type:'POST',
            url:'getCourse.php',
            data:{'class_id':classID,'course_id':courseID},
            success:function(result){
                $('#assign').html(result);
               
            }
        }); 
    }else{
        $('#assign').html('<option value="">Select Assignment</option>');
       
    }
});


 