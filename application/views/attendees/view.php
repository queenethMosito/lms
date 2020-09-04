<div id="wsp" class="container">
<div class="row">
		<div class="col-md-12">
			<h4 class="page-header">
				<a href="/client/admin/students"><i class="fa fa-arrow-left"></i></a> &nbsp;&nbsp;Learner Details:
				<?=$student->first_name?>
				<?=$student->last_name?> - 
				<?=$student->student_number?>
			</h4>
		</div>
	</div>
	<div class="row">
    	<div class="col-md-12">
    		<!-- Nav tabs -->
			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active"><a href="#classes" aria-controls="classes" role="tab" data-toggle="tab" >Classes
			</ul>
			<!-- Tab panes -->
			<div class="tab-content">
    			<div role="tabpanel" class="tab-pane active" id="classes">
    			<br/>
    			<div class="row">
    				<?php if(count($student->class_history)>0):?>
        				<?php foreach ($student->class_history as $history):?>
        						<div class="col-md-12">
            						<div class="card">
                           			 <div class="body">
                           			 	<table style="width:100%">
                                    		<tr>
                                        		<td  width="40%">
                                        			<h4>
                                                        <?php echo $history->class_name?><br/>
                                                        <small>Course Description : <?php echo $history->class_description?><br/>
                                                        Date : <?php echo $history->class_start_date . " " . $history->class_start_time . " - ". $history->class_end_date. " ".$history->class_end_time?></small>
                                                    </h4>
                                                 </td>
                                        		<td>
                                        		<?php 
                                        		$attendance=$history->attendance;
                                        		$required = $attendance->required==1 ? '<i class="fa fa-check" aria-hidden="true"></i>' : '<i class="fa fa-times" aria-hidden="true"></i>';
				                                $attended = $attendance->attended==1 ? '<i class="fa fa-check" aria-hidden="true"></i>' : '<i class="fa fa-times" aria-hidden="true"></i>';
    	                                        $communicated = $attendance->communicated==1 ? '<i class="fa fa-check" aria-hidden="true"></i>' : '<i class="fa fa-times" aria-hidden="true"></i>';
                                               
    	                                        ?>
                                        		<b>Required Attendance :</b> <?php echo $required;?><br/>
                                        		<b>Attended :</b> <?php echo $attended;?>&nbsp;&nbsp;&nbsp;&nbsp;<b>Communicated :</b> <?php echo $communicated;?><br/>
                                        		<?php if( $attendance->communicated==1 && $attendance->fines->fine_amount>0):?>
                                        			<b>Fine : </b><i class="fa fa-check" aria-hidden="true"></i><br/>
                                        			<b>Fine Date: </b> <?php echo $attendance->fines->fine_date?><br/>
                                        			<b>Fine Amount: </b> R<?php echo $attendance->fines->fine_amount?><br/>
                                        		<?php elseif(($attendance->communicated==1 && $attendance->fines->fine_amount=='') || $attendance->communicated==0):?>
                                        		<b>Fine : </b><i class="fa fa-times" aria-hidden="true"></i>
                                        		<?php endif;?>
                                        		</td>
                                    		</tr>
                                    		
                                    	</table> 
                           			 </div>
                           			</div>
        						</div>
        				<?php endforeach;?>
    				<?php else:?>
    				<p align="center">This student doesnot have any classes</p>
    				<?php endif;?>
    			</div>
    			</div>
    			
			</div>
    	</div>
	</div>
	
	
	
	
</div>
