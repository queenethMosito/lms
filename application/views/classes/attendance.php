<div id="wsp" class="container">
<div class="row">
		<div class="col-md-12">
			<h4 class="page-header">
				<a href="/client/classes"><i class="fa fa-arrow-left" ></i></a> &nbsp;&nbsp;Manage Class Attandance<br/><br/>
				<small><b>Class:</b> <?php echo $classInfo->class_name?><br/>
				<b>Summary:</b> <?php echo $classInfo->class_description?><br/>
				<b>Date:</b> <?php echo $classInfo->class_start_date . " ".$classInfo->class_start_time ." - ".  $classInfo->class_end_date . " ".$classInfo->class_end_time?><br/>
				<b>Student  Capacity:</b> <?php echo count($attendance)?> of <?php echo $classInfo->capacity?> </small>
			</h4>
		</div>
		</div>
		<div class="row">
		
		<?php if(count($attendance)>0):?>
		<form id="attendance_form">
    		<?php foreach ($attendance as $att):?>
    			<div class="col-md-12">
    				<div class="card">
                        <div class="body">
                        	<table style="width:100%">
                        		<tr>
                            		<td  width="30%">
                            			<h4>
                                            <?php echo $att->first_name . " " . $att->last_name;?><br/>
                                            <small>Student Number : <?php echo $att->student_number?><br/>
                                            Email : <?php echo $att->email_address?></small>
                                            <input type="hidden" class="form-control" name="attendance[<?php echo $att->attendance_id?>][attendance_id]" value="<?php echo $att->attendance_id?>" >
                                        </h4>
                                     </td>
                            		<td>
                            		<?php 
                            		$required = $att->required==1 ? "checked" : "";
                            		$attended = $att->attended==1 ? "checked" : "";
                            		$communicated = $att->communicated==1 ? "checked" : "";
                            		$fine_25 = "";
                            		$fine_50 = "";
                            		if($att->communicated == 1)
                            		{
                            		    if($att->fines->fine_amount==25)
                            		    {
                            		        $fine_25 ="checked";
                            		    }
                            		    elseif($att->fines->fine_amount==50)
                            		    {
                            		        $fine_50 ="checked";
                            		    }
                            		}
                            		?>
                            			<input type="checkbox" name="attendance[<?php echo $att->attendance_id?>][required]" value="1" <?php echo $required;?>> Required</label><br/>
                                		<label class="radio-inline"><input type="radio" class="attendanceOptions"  <?php echo $attended;?> data-id="<?php echo $att->attendance_id?>" name="attendance[<?php echo $att->attendance_id?>][attendance]" value="1"> Attended</label>
    									<label class="radio-inline"><input type="radio" class="attendanceOptions" <?php echo $communicated;?> data-id="<?php echo $att->attendance_id?>" name="attendance[<?php echo $att->attendance_id?>][attendance]" value="0"> Communicated</label>
    									<div class="fines_<?php echo $att->attendance_id?>">
    									
    										<label class="radio-inline"><input type="radio" <?php echo $fine_25;?> name="attendance[<?php echo $att->attendance_id?>][fines]" value="25"> R25</label>
    									<label class="radio-inline"><input type="radio" <?php echo $fine_50;?> name="attendance[<?php echo $att->attendance_id?>][fines]" value="50"> R50</label>
    									</div>
                            		</td>
                        		</tr>
                        		
                        	</table>                        
                        </div>
                    </div>
    			</div>
    		<?php endforeach;?>
    		</form>
		<?php else:?>
		<p align="center">Please add students to this class before completing the attendance register.</p>
		<?php endif;?>
		<div class="col-md-12">
		<button type="button" class="btn btn-primary btn-block" onclick="save()">Submit Register</button>
		</div>
		</div>
</div>
<script>
$( document ).ready(function() {
	

	
	});

function save()
{
	var formArray = $('#attendance_form').serialize();
	$.ajax({
		url: "/client/classes/addRegister",
		data: formArray,
		dataType: "json",
		type: "POST"
	}).done(function (data) {
		bootbox.alert({
		    message: "Attendance register has been updated",
		    callback: function () {
		    	location.reload(true);
		    }
		})
	});
}

</script>