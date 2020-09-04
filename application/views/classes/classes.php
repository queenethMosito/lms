<style>
#centre-list{
max-height: 400px;
    overflow-y:scroll; 
}
</style>
<!-- Page Content -->
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h4 class="page-header"><a href="/"><i class="fa fa-arrow-left" ></i></a> &nbsp;&nbsp;Class Management</h4>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="form-group">
				<label for="query">Filter</label>
				<input class="form-control" type="text" id="query">
			</div>
			<div class="btn-group" role="group" aria-label="toolbar" style="padding-bottom: 15px;">
				<button type="button" aria-label="toolbar" class="btn btn-default fa fa-plus" onclick="addCentre()"></button>
				<button type="button" aria-label="toolbar" class="btn btn-default fa fa-edit" onclick="editCentre()"> <span style="font-family: sans-serif">Edit selected centre</span></button>
			</div>
		</div>
	</div>
	<div class="row">

		<ul id="centre-list" class="col-md-3">
		
		<?php foreach($classes as $centre): ?>
			<li id="li_<?=$centre->class_id?>" data-starget="<?=$centre->class_description?>" data-json='<?=json_encode($centre)?>' onclick="preview(this)" class="list-group-item">
				<?=$centre->class_name?>
			</li>
		<?php endforeach; ?>

		</ul>

		<div class="col-md-9">
			<div id="preview" class="panel panel-default" style="display: none;">
				<div class="panel-title bg-blue-grey">
					<h4 id="prev-centredescription"></h4>
				</div>
				<div class="panel-body">
				<strong>Class Summary</strong>
					<p id="prev-class"></p>
					<strong>Capacity</strong>
					<p id="prev-capacity"></p>
					<strong>Class Start Date</strong>
					<p id="prev-start-date"></p>
					<strong>Class End Date</strong>
					<p id="prev-end-date"></p>
					<div class="panel-footer">
					<a id="add_learner" target="_blank" class="btn btn-default" style="float: left;margin:2px" role="button">Add Learners to this Class</a> 
					<a id="mark_attendance" target="_blank" class="btn btn-default" style="float: left;margin:2px" role="button">Mark Class Attandance</a>
					<a id="fees_list" target="_blank" class="btn btn-default" style="float: left;margin:2px" role="button">Fines</a>
					<a id="pass_list" target="_blank" class="btn btn-default" style="float: left;margin:2px" role="button">Pass List</a>
					
					<a id="delete_class" target="_blank" class="btn btn-default" style="float: left;margin:2px" role="button">Delete this Class</a>
					</div>
				</div>
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
				<h4 class="modal-title" id="gridSystemModalLabel">Add Class</h4>
			</div>
			<div class="modal-body">
				<div class="container-fluid">
				
					<div class="control-group">
						<label label-default="" class="control-label">Class Name</label>
						<div class="controls">
							<input type="text" id="class_name" class="form-control" title="Class Name">
						</div>
					</div>
					<div class="control-group" style="padding-top: 10px;">
						<label label-default="" class="control-label">Class Summary</label>
						<div class="controls">
							<textarea type="text" id="class_description" class="form-control" title="Class Description" rows="4"></textarea>
						</div>
					</div>
					<div class="control-group" style="padding-top: 10px;">
					<label for="class_start_date" label-default="" class="control-label">Start Date</label>
							<div class="form-group">
				                <div class='input-group date class_start_date' id='class_start_date'>
				                    <input type='text' class="form-control class_start_date" />
				                    <span class="input-group-addon">
				                        <span class="glyphicon glyphicon-calendar"></span>
				                    </span>
				                </div>
				            </div>
				      </div>
				     <div class="control-group" style="padding-top: 10px;">
				       <label for="class_end_date" label-default="" class="control-label">End Date</label>
							<div class="form-group">
				                <div class='input-group date class_end_date' id='class_end_date'>
				                    <input type='text' class="form-control class_end_date" />
				                    <span class="input-group-addon">
				                        <span class="glyphicon glyphicon-calendar"></span>
				                    </span>
				                </div>
				            </div>
				       </div>
				       <div class="control-group" style="padding-top: 10px;">
						<label label-default="" class="control-label">Capacity</label>
						<div class="controls">
							<input type="number" id="capacity" class="form-control" title="Capacity" />
						</div>
					</div>



					
				</div>
			</div>
			<div class="modal-footer">
				<span style="line-height: 3px;margin-bottom: 0px;" id="dialog-alert" role="alert"> </span> <span> <img id="save-ajax-img" style="display: none;" src="/images/ajax/ajax-loader_circle_sm.gif" />
				</span>
				<button type="button" class="btn btn-default" onclick="closedialog()">Close</button>
				<button type="button" class="btn btn-primary" onclick="save()">Save changes</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script>
var selected_centre = null;
var map = null;
function opendialog(title){
	$('#gridSystemModalLabel').text(title);
	$("#dialog-alert").removeClass("alert")
		.removeClass("pull-left")
		.removeClass("alert-success")
		.removeClass("alert-danger")
		.html('').hide();
	$("#save-ajax-img").hide();
	$("#containing").modal('show');
}

function closedialog(){
	$("#containing").modal('hide');
}

function save(){
	$("#dialog-alert").removeClass("alert")
	.removeClass("pull-left")
	.removeClass("alert-success")
	.removeClass("alert-danger")
	.html('');
	var class_id = $("#containing").data("id");
	var class_name = $("#class_name").val();
	var class_description = $("#class_description").val();
	var class_start_date = $("#class_start_date").find("input").val();
	var class_end_date = $("#class_end_date").find("input").val();
	var capacity = $("#capacity").val();
	
	 if(class_name=="" || class_description=="" ||class_start_date=="" || class_end_date=="" || capacity=="")
	 {
		 $("#class_name").parents('.control-group').removeClass("has-error");
		 $("#class_description").parents('.control-group').removeClass("has-error");
		 $("#class_start_date").parents('.control-group').removeClass("has-error");
		 $("#class_end_date").parents('.control-group').removeClass("has-error");
		 $("#capacity").parents('.control-group').removeClass("has-error");
		 if(class_name=="")
		 {
			 $("#class_name").parents('.control-group').addClass("has-error");
		 }
		
		 if(class_description=="")
		 {
			 $("#class_description").parents('.control-group').addClass("has-error");
		 }
		 if(class_start_date=="")
		 {
			 $("#class_start_date").parents('.control-group').addClass("has-error");
		 }
		 if(class_end_date=="")
		 {
			 $("#class_end_date").parents('.control-group').addClass("has-error");
		 }
		 if(capacity=="")
		 {
			 $("#capacity").parents('.control-group').addClass("has-error");
		 }
		 
	 }
	 else
	 {
		 var json = {
					 class_id : class_id,
					class_name : class_name,
					class_description : class_description,
					class_start_date : class_start_date,
					class_end_date : class_end_date,
					capacity : capacity
			}

			$("#save-ajax-img").show();
			$.post("/client/classes/submitclasses", {classes: JSON.stringify(json)}, function(data) {
				data = JSON.parse(data);
				$("#dialog-alert").addClass("alert")
					.addClass("pull-left")
					.addClass(data.result == "success" ? "alert-success" : "alert-danger")
					.html(data.message)
					.show();
				    updateClassessList(json);
				$("#save-ajax-img").hide();
			});
	 }


	
}

function updateClassessList(json){
	$.post("/client/classes/viewclasslist", function(data){
		$('#centre-list').html(data);
		var class_description=json.class_name;
		$("#query").val(class_description.replace("'","`"));
		$("#query").keyup();
	});




}

function addCentre(){
	$("#containing").data("id",null);
	$("#class_name").val(null);
	$("#class_description").val(null);
	$(".class_start_date").val(null);
	$(".class_end_date").val(null);
	$("#capacity").val(null);
	opendialog("Add Class");
}

function editCentre(){
	var centre = selected_centre;
	if(centre != null){
		$("#containing").data("id",centre.class_id);
		$("#class_name").val(centre.class_name);
		$("#class_description").val(centre.class_description);
		$("#class_start_date").find("input").val(centre.class_start_date + " " +centre.class_start_time);
		$("#class_end_date").find("input").val(centre.class_end_date + " " +centre.class_end_time);
		$("#capacity").val(centre.capacity);
		opendialog("Edit Class");
	}
}

function preview(t){
	for(var i in $(t)[0].parentElement.children){
		var id = $(t)[0].parentElement.children[i].id;
		$('#'+id).removeClass('active');
	}
	$(t).addClass('active');
	var centre = $(t).data("json");
	selected_centre = centre;
	$("#prev-class").text(centre.class_description);
	$("#prev-capacity").text(centre.capacity);
	$("#prev-start-date").text(centre.class_start_date + " " +centre.class_start_time);
	$("#prev-end-date").text(centre.class_end_date + " " +centre.class_end_time);


	$("#preview").show();
	
}



function processSearch(){



	var term = $.trim($("#query").val()).toLowerCase();
	console.log(term);
	var count = 0;
	if(term == "") {
		$("#centre-list > li").each(function() {
			 $(this).show();
			 if(count == 0){
				 preview($(this)[0]);
			 }
			 count++;
        });

	}
	else{
		$("#centre-list > li").each(function() {
            if ($(this).text().toLowerCase().search(term) > -1) {
                $(this).show();
                if(count == 0){
   				 preview($(this)[0]);
	   			 }
	   			 count++;
            }
            else {
                $(this).hide();
            }
        });
	}
}

function incrementPreview(up){
	var count = 0;
	$("#centre-list > li").each(function() {
		 var visible = $(this).is(":visible");
		 var active = $(this).hasClass("active");

		 if(active && visible && !up){
			 var e = $(this).next();
			 console.log(e.text());
			 if(e[0] != null){
				if(e.is(":visible")){
			 		preview(e);
			 		return false;
				}
			 }
		 }
		 else if(active && visible && up){
			 var e = $(this).prev();
			 if(e[0] != null){
				 if(e.is(":visible")){
			 		preview(e);
			 		return false;
				 }
			 }
		 }
		 if(visible){
			 count++;
		 }

   });
}

$( document ).ready(function() {
	$("#query").focus();
	$("#query").keyup(function(e) {
	    switch (e.which) {
// 	    case 37:
// 	        $('div').stop().animate({
// 	            left: '-=10'
// 	        }); //left arrow key
// 	        break;
	    case 38:
// 	        console.log("up");
			incrementPreview(true);
	        break;
// 	    case 39:
// 	        $('div').stop().animate({
// 	            left: '+=10'
// 	        }); //right arrow key
// 	        break;
	    case 40:
	    	incrementPreview(false);
// 	    	console.log("down");
	        break;
	    default:
	    	processSearch();
	    }
	}).keyup();
	$('.class_start_date').datetimepicker({
		format: 'YYYY-MM-DD HH:mm',
		minDate:new Date()
	});
	$('.class_end_date').datetimepicker({
		format: 'YYYY-MM-DD HH:mm',
		minDate:new Date()});

	
	$("#delete_class").click(function() {
		var class_id = selected_centre.class_id;
		bootbox.confirm("Are you sure you want to remove this class? This action can't be undone.", function(result){ 
			$("#save-ajax-img").show();
			$.post("/client/classes/deleteclasses", {class_id: class_id}, function(data) {
				
				location.reload(true);
				
			});
		});


	});
	$("#add_learner").click(function() {
		var class_id = selected_centre.class_id;
		var win = window.open('/client/classes/learners/' + class_id, '_blank');
		win.focus();


	});
	$("#mark_attendance").click(function() {
		var class_id = selected_centre.class_id;
		var win = window.open('/client/classes/attendance/' + class_id, '_blank');
		win.focus();
	});
	$("#fees_list").click(function() {
		var class_id = selected_centre.class_id;
		var win = window.open('/client/classes/fees_list/' + class_id, '_blank');
		win.focus();
	});
	$("#pass_list").click(function() {
		var class_id = selected_centre.class_id;
		var win = window.open('/client/classes/pass_list/' + class_id, '_blank');
		win.focus();
	});
	
});

</script>