<div id="wsp" class="container">
<div class="row">
		<div class="col-md-12">
			<h4 class="page-header">
				<a href="/client/classes"><i class="fa fa-arrow-left"></i></a> &nbsp;&nbsp;Class Fines
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
							<td><b>Fine Date</b></td>
							<td><b>Fine Amoount</b></td>
						</tr>
					</thead>
					<tbody>
					<?php if(count($fines)>0):?>
						<?php foreach ($fines as $fine):?>
							<td><?php echo $fine->first_name . " ".$fine->first_name?></td>
							<td><?php echo $fine->student_number?></td>
							<td><?php echo $fine->email_address?></td>
							<td><?php echo $fine->fine_date?></td>
							<td>R<?php echo $fine->fine_amount?></td>
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