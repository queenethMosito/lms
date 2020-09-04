
<style>
.odd{
cursor: pointer;
}
.even{
cursor: pointer;
}
</style>
<div id="wsp" class="container">
	<div class="row">
		<div class="col-md-12">
			<h4 class="page-header">
				<a href="/"><i class="fa fa-arrow-left"></i></a> &nbsp;&nbsp;Learner
				Management
			</h4>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="form-group">
				<label for="q">Search:</label> <input type="text" class="form-control" id="q" autocomplete="off" value="<?=$this->hInput->get('q')?>">
			</div>
		</div>
		<div class="col-md-12">
			<div class="form-group">
				<a data-toggle="modal" onclick="searchDialog()" class="btn btn-lg btn-block btn-default">Add a Learner</a>
			</div>
		</div>
		<div class="col-md-12">
			<hr />
		</div>
	</div>
	<div class="row" id="loading">
		<div class="col-md-12">
			<div class="progress">
				<div class="progress-bar  progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="100"
				 aria-valuemin="0" aria-valuemax="100" style="width: 100%">Loading</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<h4>
				<span id="studentcount"></span>
			</h4>
		</div>
		<div class="col-md-12">
			<div id="result_container">
				<table id="grid" class="table table-striped table-hover studentTable">
					<thead>
						<tr>
							<td><b>Learner Name</b></td>
							<td><b>Student Number</b></td>
							<td><b>Email Address</b></td>
						</tr>
					</thead>
					<tbody class="studentList">
					</tbody>
				</table>
			</div>
		</div>
		
		
	</div>

</div>




<!-- Dialog -->
<div id="containing" class="modal fade" role="dialog" aria-labelledby="gridSystemModalLabel">
	<div id="dlgcontent" class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="gridSystemModalLabel">Add Learner</h4>
			</div>
			<div class="modal-body">
				<div class="container-fluid">
				
					<div class="control-group">
						<label label-default="" class="control-label">First Name</label>
						<div class="controls">
							<input type="text" id="first_name" class="form-control" title="First Name">
						</div>
					</div>
					<div class="control-group">
						<label label-default="" class="control-label">Last Name</label>
						<div class="controls">
							<input type="text" id="last_name" class="form-control" title="Last Name">
						</div>
					</div>
					
					<div class="control-group">
						<label label-default="" class="control-label">Email Address</label>
						<div class="controls">
							<input type="text" id="email_address" class="form-control" title="Email Address">
						</div>
					</div>
				  </div>
			</div>
			<div class="modal-footer">
				<span style="line-height: 3px;margin-bottom: 0px;" id="dialog-alert" role="alert"> </span> <span> <img id="save-ajax-img" style="display: none;" src="/images/ajax/ajax-loader_circle_sm.gif" />
				</span>
				<button type="button" class="btn btn-default" onclick="closedialog()">Close</button>
				<button type="button" class="btn btn-primary" onclick="save()">Add Learner</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<script>
$( document ).ready(function() {
	search();
	 $('.studentTable').DataTable();
	 var query=$("#q").val();
	 $("#query").val(query);
});

$("#q").keyup(function() {
	var myIndex = ++lastIndex;
	setTimeout(function(){
		if(myIndex != lastIndex) return;
		search();
		},
			700);
});
function searchDialog(){
	opendialog("Add Learner");
}
function opendialog(title){
	$('#gridSystemModalLabel').text(title);
	$("#dialog-alert").removeClass("alert")
		.removeClass("pull-left")
		.removeClass("alert-success")
		.removeClass("alert-danger")
		.html('').hide();
	$("#save-ajax-img").hide();
	$("#first_name").val(null);
	$("#last_name").val(null);
	$("#email_address").val(null);
	$("#containing").modal('show');
}

function closedialog(){
	$("#containing").modal('hide');
}
function search() {
	var query=$("#q").val();
	$("#query").val(query);
	$("#loading").show();

	$.ajax({
		url: "/client/attendees/search",
		data: {
			query:query
		},
		dataType: "json",
		type: "POST",
	}).done(
		function (data) {
			 var options = data.result;
		
			 var studentRows='';
			 for (var i = 0; i < options.length; i++) {
				 studentRows+='<tr onclick="selectStudent('+options[i].attendee_id+')">'
						+ '<td>' + options[i
						].fullname 
						+ '<td>' + options[i
						].student_number + '</td>'
						+ '<td>' + options[i
						].email_address + '</td>'
					 		 +'</tr>';
			 }
			 $ ('.studentTable'). dataTable (). fnDestroy ();
			 $(".studentList").html(studentRows);
			 $('.studentTable').DataTable({
				  "pageLength": 50
			 } );
			 
			 $("#studentcount").html('There are ' + data.count +' Students');
		
		$("#loading").hide();		
	});
}
function selectStudent(user_id) {
	var win = window.open('/client/attendees/view/' + user_id, '_blank');
		win.focus();
}
function isEmail(email) {
	  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	  return regex.test(email);
	}
function save(){
	$("#dialog-alert").removeClass("alert")
	.removeClass("pull-left")
	.removeClass("alert-success")
	.removeClass("alert-danger")
	.html('');
	$("#first_name").parents('.control-group').removeClass("has-error");
	 $("#last_name").parents('.control-group').removeClass("has-error");
	 $("#email_address").parents('.control-group').removeClass("has-error");
	var first_name = $("#first_name").val();
	var last_name = $("#last_name").val();
	var email_address = $("#email_address").val();

	
	 if(first_name=="" || last_name=="" ||email_address=="")
	 {
		 $("#first_name").parents('.control-group').removeClass("has-error");
		 $("#last_name").parents('.control-group').removeClass("has-error");
		 $("#email_address").parents('.control-group').removeClass("has-error");
		 if(first_name=="")
		 {
			 $("#first_name").parents('.control-group').addClass("has-error");
		 }
		 if(last_name=="")
		 {
			 $("#last_name").parents('.control-group').addClass("has-error");
		 }
		 if(email_address=="")
		 {
			 $("#email_address").parents('.control-group').addClass("has-error");
		 }
		 
		 
	 }
	 else
	 {
		
		 if( isEmail(email_address))
		 {
			 var json = {
					 attendee_id : null,
					 first_name : first_name,
					 last_name : last_name,
					 email_address : email_address
				}

				$("#save-ajax-img").show();
				$.post("/client/attendees/submitLearner", {learner: JSON.stringify(json)}, function(data) {
					data = JSON.parse(data);
					$("#dialog-alert").addClass("alert")
						.addClass("pull-left")
						.addClass(data.result == "success" ? "alert-success" : "alert-danger")
						.html(data.message)
						.show();
					location.reload(true);
					$("#save-ajax-img").hide();
				});
		 }
		 else
		 {
			 $("#email_address").parents('.control-group').addClass("has-error");
		 }
		 
	 }


	
}
</script>