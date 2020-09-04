<div id="wsp" class="container">
<div class="row">
		<div class="col-md-12">
			<h4 class="page-header">
				<a href="/client/classes"><i class="fa fa-arrow-left"></i></a> &nbsp;&nbsp;Class Pass List
			</h4>
		</div>
	</div>
	
	<div class="row">
	<div class="col-md-12">
			<div id="result_container">
				<table id="grid" class="table table-striped table-hover feesTable">
					<thead>
						<tr>
							<td><b>Learner Name</b></td>
							<td><b>Student Number</b></td>
							<td><b>Email Address</b></td>
							<td><b>Attendence</b></td>
						</tr>
					</thead>
					<tbody>
					<?php if(count($fines)>0):?>
						<?php foreach ($fines as $fine):?>
							<td><?php echo $fine->first_name . " ".$fine->first_name?></td>
							<td><?php echo $fine->student_number?></td>
							<td><?php echo $fine->email_address?></td>
							<td><i class="fa fa-check" aria-hidden="true"></i></td>
						<?php endforeach;?>
					<?php endif;?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	
	
</div>
<script>
$( document ).ready(function() {
	
	 $('.feesTable').DataTable();
});
</script>