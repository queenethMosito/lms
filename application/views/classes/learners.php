<div id="wsp" class="container">
<div class="row">
		<div class="col-md-12">
			<h4 class="page-header">
				<a href="/client/classes"><i class="fa fa-arrow-left" ></i></a> &nbsp;&nbsp;Manage Class List<br/><br/>
				<small><b>Class:</b> <?php echo $classInfo->class_name?><br/>
				<b>Summary:</b> <?php echo $classInfo->class_description?><br/>
				<b>Date:</b> <?php echo $classInfo->class_start_date . " ".$classInfo->class_start_time ." - ".  $classInfo->class_end_date . " ".$classInfo->class_end_time?><br/>
				<b>Student  Capacity:</b> <?php echo count($enrolledStudents)?> of <?php echo $classInfo->capacity?> </small>
			</h4>
		</div>
		
		<div class="col-md-12">
		<br/><br/>
		<table class="table table-striped table-hover ">
					<thead>
						<tr>
							<td><input type="checkbox" class="selectAll"></td>
							<td><b>Learner Name</b></td>
							<td><b>Student Number</b></td>
							<td><b>Email Address</b></td>
						</tr>
					</thead>
					<tbody>
					<?php if(count($allStudents)>0):?>
						<?php foreach ($allStudents as $student):?>
						<?php $selected='';
						if(in_array( $student->attendee_id, $enrolledStudents))
						    $selected='checked';
						?>
							<tr>
							<td><input type="checkbox" name="attendee_id[]" value="<?php echo $student->attendee_id?>" <?php echo $selected?>></td>
							<td><?php echo $student->first_name . " " . $student->last_name?></td>
							<td><?php echo $student->student_number?></td>
							<td><?php echo $student->email_address?></td>
						</tr>
						<?php endforeach;?>
					<?php else:?>
					<p align="center">Add Learners to the system before creating a class list</p>
					<?php endif;?>
					</tbody>
			</table>
		</div>
		<div class="col-md-12">
		<button type="button" class="btn btn-primary btn-block" onclick="save()">Update List</button>
		</div>
	</div>
</div>
<br/>
<script>
$( document ).ready(function() {
	$('.selectAll').click( function () {
	        
	        if($(this).is(':checked'))
	        {
	        	
	               $('input[name="attendee_id[]"]').prop('checked',true);
	        }
	        else
	        {
	            
	            $('input[name="attendee_id[]"]').prop('checked',false);
	        }
	    });

	});
	
function save()
{
	var totalCapacity = '<?php echo $classInfo->capacity?>';
	var totalSelected =  $('input[name="attendee_id[]"').length;
	if(totalSelected>0)
	{
    	if(	totalSelected>totalCapacity)
    	{
    		bootbox.alert("This class can only cater for" + totalCapacity + " students");
    	}
    	else
    	{
    		var class_id = '<?php echo $classInfo->class_id?>';
    		var studentIDs=[];
            $('input[name="attendee_id[]"').each(function () {
            	if($(this).is(':checked'))
                {
            		studentIDs.push($(this).val());
                }
            });
            var json = {
            		class_id : class_id,
            		studentIDs : studentIDs
				}
            $.post("/client/classes/addClassList", {learners: JSON.stringify(json)}, function(data) {
				location.reload(true);
			});
    	}
	}
	else
	{
		bootbox.alert("Select students before updating the class list ");
		
	}
	
}

</script>
