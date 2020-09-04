<!-- Page Content -->
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h3 class="page-header">Welcome to the Learner Management System</h3>
		</div>
	</div>
	<div class="row">
	<!--- Class Management --->
		<div class="col-md-3">
			<a href="/client/classes"
				style="float: right; color: #ffffff; cursor: pointer; text-decoration: none;">
				<div class="info-box bg-blue-grey hover-expand-effect" style="cursor: pointer;">
					<div class="icon">
						<i class="fa fa-fw fa-location-arrow"></i>
					</div>
					<div class="content">
						<div class="text">
							<h4 style="color: #ffffff;">Classes Management</h4>
						</div>
					</div>
				</div>
			</a>
		</div>
		
		<!-- Learner Management -->
		<div class="col-md-3">
			<a href="/client/attendees" style="float: right; color: #ffffff; cursor: pointer; text-decoration: none;">
				<div class="info-box bg-blue-grey hover-expand-effect" style="cursor: pointer;">
					<div class="icon">
						<i class="fa fa-fw fa-users"></i>
					</div>
					<div class="content">
						<div class="text">
							<h4 style="color: #ffffff;">Learner Management</h4>
						</div>
					</div>
				</div>
			</a>
		</div>
		
		<!-- Pass -->
		<div class="col-md-3">
			<div class="info-box bg-orange hover-expand-effect">
                        <div class="icon">
                            <i class="fa fa-fw fa-certificate"></i>
                        </div>
                        <div class="content"  >
                            <div class="text" ><h4 style="color: #ffffff;">Total Pass</h4></div>
                            <div style="color: #ffffff; cursor: pointer; text-decoration: none;" class="number count-to" data-from="0" data-to="<?php echo $pass?>" data-speed="15" data-fresh-interval="1"><?php echo $pass?></div>
                        </div>
               </div>
		</div>
		<!-- Fines -->
		<div class="col-md-3">
			<div class="info-box bg-light-green hover-expand-effect">
                        <div class="icon">
                            <i class="fa fa-fw fa-money"></i>
                        </div>
                        <div class="content"  >
                            <div class="text" ><h4 style="color: #ffffff;">Total Fines</h4></div>
                            <div style="color: #ffffff; cursor: pointer; text-decoration: none;" class="number count-to" data-from="0" data-to="<?php echo $fines?>" data-speed="15" data-fresh-interval="1">R<?php echo $fines?></div>
                        </div>
               </div>
		</div>
	</div>
	
	<br/>
	
	<div class="row">
		<div class="col-md-12">
			<h4 class="page-header">Admin Calendar</h4>
		</div>
		<div class="col-md-12">
    		<div class="selectedCalendar" style="padding-bottom:30px">
    				 <div id='calendar'></div>
    		</div>
		</div>
	</div>
</div>
<div id="preloader" style="display:none;"></div>
<script>
function loadCalender()
{
	

	var class_name="";
    var class_description="";
	var start_time="";
    varend_time="";
    var duration="";
    var capacity="";
    var class_duration=""
    
    var count=0;
	var event = [];
	$.post('/client/index/getSessions', function(data){
		data = JSON.parse(data);
      
        for(var c=0; c<data.length;c++)
		 {
        	class_name=data[c].class_name;
        	content="<h3 align='center'>"+ class_name+"</h3>"+"<br/>"+
        	"<b>Class Description</b>:"+data[c].class_description +"<br/>"+
        	"<b>Class Capacity</b>:"+data[c].capacity +"<br/>";
        	var start_date=new Date(data[c].class_start_date +" " + data[c].class_start_time);
        	console.log(data[c].class_start_date +" " +  data[c].class_start_time);
			var end_date=new Date(data[c].class_end_date +" " +  data[c].class_end_time);
			  
        	event.push({
				   title:class_name,
				   start:start_date,
				   end:end_date,
				   allDay:false,
				   description:content
		   });
   		
		 }
     for(key in event)
	 var calendar = $('#calendar').fullCalendar( 'renderEvent', event[key] , true  );

   	 var calendar = $('#calendar').fullCalendar('rerenderEvents');
	});

	

}

$( document ).ready(function() {
	$('#preloader').hide();

		
	var calendar = $('#calendar').fullCalendar({
	     header: {
	         left: 'prev,next today',
	         center: 'title',
	         right: 'month,agendaWeek,agendaDay'
	     },
	     navLinks: true, // can click day/week names to navigate views
	     eventRender: function(event, element) {
	    	 element.find('span.fc-title').html(element.find('span.fc-title').text());
	    	 element.find('div.fc-title').html(element.find('div.fc-title').text());	 
		    },
		    eventClick: function(data, event, view) {
		    	tooltip = '<div class="tooltiptopicevent" style="width:auto;height: auto;background:#fff;position:absolute;z-index:10001;padding:10px 10px 10px 10px ;  line-height: 200%;box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);">'  + data.description  + '</div>';


	            $("body").append(tooltip);
	            $(this).mouseover(function (e) {
	                $(this).css('z-index', 10000);
	                $('.tooltiptopicevent').fadeIn('500');
	                $('.tooltiptopicevent').fadeTo('10', 1.9);
	            }).mousemove(function (e) {
	                $('.tooltiptopicevent').css('top', e.pageY + 10);
	                $('.tooltiptopicevent').css('left', e.pageX + 20);
	            });
		    },
		    eventMouseover: function (data, event, view) {
		    	tooltip = '<div class="tooltiptopicevent" style="width:auto;height: auto;background:#fff;position:absolute;z-index:10001;padding:10px 10px 10px 10px ;  line-height: 200%;box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);">'  + data.description  + '</div>';


	            $("body").append(tooltip);
	            $(this).mouseover(function (e) {
	                $(this).css('z-index', 10000);
	                $('.tooltiptopicevent').fadeIn('500');
	                $('.tooltiptopicevent').fadeTo('10', 1.9);
	            }).mousemove(function (e) {
	                $('.tooltiptopicevent').css('top', e.pageY + 10);
	                $('.tooltiptopicevent').css('left', e.pageX + 20);
	            });
	            
		    },
	        eventMouseout: function (data, event, view) {
	        	 //tooltip.hide()

	        },
	        dayClick: function () {
	        	 $(this).css('z-index', 8);

		            $('.tooltiptopicevent').remove();
	        },
	        eventResizeStart: function () {
	           // tooltip.hide()
	        },
	        eventDragStart: function () {
	           // tooltip.hide()
	        },
	        viewDisplay: function () {
	//tooltip.hide()
	        },
	 });
	
	loadCalender();
	 
});


</script>