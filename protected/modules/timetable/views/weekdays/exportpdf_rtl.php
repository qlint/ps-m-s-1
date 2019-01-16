<?php
$this->breadcrumbs=array(
	Yii::t('app','Weekdays')=>array('index'),
	Yii::t('app','Manage'),
);
?>
<style>
.timetable{
	 border-collapse:collapse;	
}
.timetable td{
	padding:10px;
	border:1px solid #C5CED9 ;
	width:auto;
	font-size:10px;
	text-align:center;
}
hr{ 
border-bottom:1px solid #C5CED9; 
border-top:0px solid #fff;}
</style>

	<?php
	if(isset($_REQUEST['id']) and $_REQUEST['id']!=NULL)
	{
		//Getting dates in a week
		$day = date('w');
		$week_start = date('Y-m-d', strtotime('-'.$day.' days'));
		$week_end = date('Y-m-d', strtotime('+'.(6-$day).' days'));
		$date_between = array();
		$begin = new DateTime($week_start);
		$end = new DateTime($week_end);
		
		$daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);
		
		foreach($daterange as $date){
			$date_between[] = $date->format("Y-m-d");
		}
		if(!in_array($week_end,$date_between))
		{
			$date_between[] = date('Y-m-d',strtotime($week_end));
		}   
	?> 
        <!-- Header -->
            <table width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="first" width="100">
                               <?php 
							   $filename=  Logo::model()->getLogo();
								if($filename!=NULL)
                                {
                                    //Yii::app()->runController('Configurations/displayLogoImage/id/'.$logo[0]->primaryKey);
                                    echo '<img src="uploadedfiles/school_logo/'.$filename[2].'" alt="'.$filename[2].'" class="imgbrder" height="100" />';
                                }
                                ?>
                    </td>
                    <td valign="middle">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="listbxtop_hdng first">
                                    <?php $college=Configurations::model()->findAll(); ?>
                                    <?php echo $college[0]->config_value; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="listbxtop_hdng first">
                                    <?php echo $college[1]->config_value; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="listbxtop_hdng first">
                                    <?php echo 'Phone: '.$college[2]->config_value; ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
      <hr />
        <!-- End Header --> 
    <div align="center" style="display:block; text-align:center;"><?php echo Yii::t('app','CLASS TIME TABLE');?></div><br />
    <!-- Course details -->
     <table style="font-size:14px; background:#DCE6F1; padding:10px 10px;border:#C5CED9 1px solid;">
            <?php $batch = Batches::model()->findByAttributes(array('id'=>$_REQUEST['id']));
                  $course_name = Courses::model()->findByAttributes(array('id'=>$batch->course_id));
				  $class_teacher = Employees::model()->findByAttributes(array('id'=>$batch->employee_id));
            ?>
            <tr>
                <td style="width:130px;"><?php echo Yii::t('app','Course');?></td>
                <td style="width:10px;">:</td>
                <td style="width:550px;"><?php echo $course_name->course_name; ?></td>
            
                <td  style="width:130px;"><?php echo Yii::app()->getModule('students')->fieldLabel("Students", "batch_id");?></td>
                <td style="width:10px;">:</td>
                <td><?php echo $batch->name; ?></td>
            </tr>
            <tr>
                <td><?php echo Yii::t('app','Class Teacher');?></td>
                <td>:</td>
                <td >
					<?php 
					if($class_teacher!=NULL)
					{
						echo Employees::model()->getTeachername($class_teacher->id);
					}
					else
					{
						echo '-';
					}
					?>
				</td>
   				<?php
				$total_students = Students::model()->countByAttributes(array('batch_id'=>$_REQUEST['id'],'is_active'=>1,'is_deleted'=>0));
				?>
                <td><?php echo Yii::t('app','Total students');?></td>
                <td>:</td>
                <td width="195"><?php echo $total_students; ?></td>
            </tr>
           
        </table>

    <!-- END Course details -->
     <?php    
	$times=Batches::model()->findAll("id=:x", array(':x'=>$_REQUEST['id']));
	$weekdays=Weekdays::model()->findAll("batch_id=:x", array(':x'=>$_REQUEST['id']));
	if(count($weekdays)==0)
		$weekdays=Weekdays::model()->findAll("batch_id IS NULL");
	?>
    <br /><br />
    <?php 
	$criteria=new CDbCriteria;
	$criteria->condition = "batch_id=:x";
    $criteria->params = array(':x'=>$_REQUEST['id']);
    $criteria->order = "STR_TO_DATE(start_time, '%h:%i %p')";    
		$timing = ClassTimings::model()->findAll($criteria);
		$count_timing = count($timing);
		if(isset($timing) and $timing!=NULL)
		{
	?>

		
		<table  align="left" width="100%" id="table" cellspacing="0" cellpadding="0" class="timetable" >
			<tr style="background:#DCE6F1">
			  <td  style="background:#DCE6F1;">&nbsp;</td>
			  <?php 
					foreach($timing as $timing_1)
					{
						$time1		=	Configurations::model()->convertTime($timing_1->start_time);
						$time2		=	Configurations::model()->convertTime($timing_1->end_time); 
						//echo $timing_1->start_time.'<br>';  ?>
					<?php echo '<td style="font-size:11px;background:#E1EAEF;word-break:break-all;">'.$time1 .' -<br> '.$time1.'</td>';?>
				<?php 	}
			   ?>
			</tr> <!-- timetable_tr -->
			
			<?php if($weekdays[0]['weekday']!=0)
			{ ?>
			<tr>
				<td><?php echo 	 Yii::t('app','SUN') ;?></td>
		 
				 <?php
					  for($i=0;$i<$count_timing;$i++)
					  {
						  ?>
						<td class="td">
							<?php
		$set =  TimetableEntries::model()->findByAttributes(array('batch_id'=>$_REQUEST['id'],'weekday_id'=>$weekdays[0]['weekday'],'class_timing_id'=>$timing[$i]['id'])); 			
						if(count($set)==0)
						{		
							$is_break = ClassTimings::model()->findByAttributes(array('id'=>$timing[$i]['id'],'is_break'=>1));
							if($is_break!=NULL)
							{	
								echo  Yii::t('app','Break');	
							}	
						}
						elseif($set->is_elective ==0)
						{
							$time_sub = Subjects::model()->findByAttributes(array('id'=>$set->subject_id));
							if($time_sub!=NULL)
							{
								if($set->split_subject!=0 and $set->split_subject!=NULL){ 
									if($time_sub->split_subject){
										$subject_splits	= SubjectSplit::model()->findByPk($set->split_subject);
										$name_sub	=	$subject_splits->split_name." (".$time_sub->name.")";
									}
									else{
										$name_sub	=	$time_sub->name;
									} 
								}else{
									$name_sub	=	$time_sub->name;
								} 
								echo $name_sub .'<br>';
								$time_emp = Employees::model()->findByAttributes(array('id'=>$set->employee_id));
								if($time_emp!=NULL)
								{
									$is_substitute = TeacherSubstitution::model()->findByAttributes(array('leave_requested_emp_id'=>$time_emp->id,'time_table_entry_id'=>$set->id));																		
									if($is_substitute and in_array($is_substitute->date_leave,$date_between))
									{
										$employee = Employees::model()->findByAttributes(array('id'=>$is_substitute->substitute_emp_id));
										echo '<span style="font-size:9px;">(' .Employees::model()->getTeachername($employee->id).')</span>';										
									}
									else
									{
										echo '<span style="font-size:9px;">(' .Employees::model()->getTeachername($time_emp->id).')</span>';
									}									
								}
							}
							else
							{
								echo '-<br>';
							}
						}
						else
						{
							$elec_sub = Electives::model()->findByAttributes(array('id'=>$set->subject_id));
							$electname = ElectiveGroups::model()->findByAttributes(array('id'=>$elec_sub->elective_group_id,'batch_id'=>$_REQUEST['id']));
																			
							$time_emp = Employees::model()->findByAttributes(array('id'=>$set->employee_id));
																			
							if($electname!=NULL)
							{
								echo $electname->name.'<br>';
								
							}
						}
				 ?>
							
						  </td>
					<?php  }
					  ?>
				 </tr>

			  <?php }  ?>
			  <?php   if($weekdays[1]['weekday']!=0)
			  { ?>
			  <tr>
				<td><?php echo 	 Yii::t('app','MON') ;?></td>
			  
					 <?php
					  for($i=0;$i<$count_timing;$i++)
					  {
						?> <td>
					   <?php 
								
				$set =  TimetableEntries::model()->findByAttributes(array('batch_id'=>$_REQUEST['id'],'weekday_id'=>$weekdays[1]['weekday'],'class_timing_id'=>$timing[$i]['id'])); 			
						if(count($set)==0)
						{	
								$is_break = ClassTimings::model()->findByAttributes(array('id'=>$timing[$i]['id'],'is_break'=>1));
								if($is_break!=NULL)
								{	
									echo  Yii::t('app','Break');	
								}	
						}
						elseif($set->is_elective==0)
						{
							$time_sub = Subjects::model()->findByAttributes(array('id'=>$set->subject_id));
							if($time_sub!=NULL)
							{
								if($set->split_subject!=0 and $set->split_subject!=NULL){ 
									if($time_sub->split_subject){
										$subject_splits	= SubjectSplit::model()->findByPk($set->split_subject);
										$name_sub	=	$subject_splits->split_name." (".$time_sub->name.")";
									}
									else{
										$name_sub	=	$time_sub->name;
									} 
								}else{
									$name_sub	=	$time_sub->name;
								} 
								echo $name_sub .'<br>';
								$time_emp = Employees::model()->findByAttributes(array('id'=>$set->employee_id));
								if($time_emp!=NULL)
								{
									$is_substitute = TeacherSubstitution::model()->findByAttributes(array('leave_requested_emp_id'=>$time_emp->id,'time_table_entry_id'=>$set->id));																		
									if($is_substitute and in_array($is_substitute->date_leave,$date_between))
									{
										$employee = Employees::model()->findByAttributes(array('id'=>$is_substitute->substitute_emp_id));
										echo '<span style="font-size:9px;">(' .Employees::model()->getTeachername($employee->id).')</span>';										
									}
									else
									{
										echo '<span style="font-size:9px;">(' .Employees::model()->getTeachername($time_emp->id).')</span>';
									}
								}
							}
							else
							{
								echo '-<br>';
							}
						}
						else
						{
							$elec_sub = Electives::model()->findByAttributes(array('id'=>$set->subject_id));
							$electname = ElectiveGroups::model()->findByAttributes(array('id'=>$elec_sub->elective_group_id,'batch_id'=>$_REQUEST['id']));
																			
							$time_emp = Employees::model()->findByAttributes(array('id'=>$set->employee_id));
																			
							if($electname!=NULL)
							{
								echo $electname->name.'<br>';
								
							}
							
						}
						?> </td>
						<?php  
					 }
					?>
				  <!--timetable_td -->
				
			  </tr><!--timetable_tr -->
			  <?php } ?>
			 <?php  if($weekdays[2]['weekday']!=0)
			  {
			  ?>
				  <tr>
				<td ><?php echo 	 Yii::t('app','TUE') ;?></td>
			  
				 <?php
					  for($i=0;$i<$count_timing;$i++)
					  {
						?> <td>
					<?php		
						$set =  TimetableEntries::model()->findByAttributes(array('batch_id'=>$_REQUEST['id'],'weekday_id'=>$weekdays[2]['weekday'],'class_timing_id'=>$timing[$i]['id'])); 			
						if(count($set)==0)
						{	
							$is_break = ClassTimings::model()->findByAttributes(array('id'=>$timing[$i]['id'],'is_break'=>1));
							if($is_break!=NULL)
							{	
								echo  Yii::t('app','Break');	
							}	
						}
						elseif($set->is_elective==0)
						{
							$time_sub = Subjects::model()->findByAttributes(array('id'=>$set->subject_id));
							if($time_sub!=NULL)
							{
								if($set->split_subject!=0 and $set->split_subject!=NULL){ 
									if($time_sub->split_subject){
										$subject_splits	= SubjectSplit::model()->findByPk($set->split_subject);
										$name_sub	=	$subject_splits->split_name." (".$time_sub->name.")";
									}
									else{
										$name_sub	=	$time_sub->name;
									} 
								}else{
									$name_sub	=	$time_sub->name;
								} 
								echo $name_sub .'<br>';
								$time_emp = Employees::model()->findByAttributes(array('id'=>$set->employee_id));
								if($time_emp!=NULL)
								{
									$is_substitute = TeacherSubstitution::model()->findByAttributes(array('leave_requested_emp_id'=>$time_emp->id,'time_table_entry_id'=>$set->id));																		
									if($is_substitute and in_array($is_substitute->date_leave,$date_between))
									{
										$employee = Employees::model()->findByAttributes(array('id'=>$is_substitute->substitute_emp_id));
										echo '<span style="font-size:9px;">(' .Employees::model()->getTeachername($employee->id).')</span>';										
									}
									else
									{
										echo '<span style="font-size:9px;">(' .Employees::model()->getTeachername($time_emp->id).')</span>';
									}
								}
							}
							else
							{
								echo '-<br>';
							}
						}
						else
						{
							$elec_sub = Electives::model()->findByAttributes(array('id'=>$set->subject_id));
							$electname = ElectiveGroups::model()->findByAttributes(array('id'=>$elec_sub->elective_group_id,'batch_id'=>$_REQUEST['id']));
																			
							$time_emp = Employees::model()->findByAttributes(array('id'=>$set->employee_id));
																			
							if($electname!=NULL)
							{
								echo $electname->name.'<br>';
								
							}
						}
							?>
							  </td> 
					<?php  }
					?><!--timetable_td -->
				
			  </tr><!--timetable_tr -->
			  <?php } ?>
			  <?php
			  if($weekdays[3]['weekday']!=0)
			  { ?>
				  <tr>
				<td><?php echo 	 Yii::t('app','WED') ;?></td>
			 
				 <?php
					  for($i=0;$i<$count_timing;$i++)
					  {
						?> <td >
								<?php 
									$set =  TimetableEntries::model()->findByAttributes(array('batch_id'=>$_REQUEST['id'],'weekday_id'=>$weekdays[3]['weekday'],'class_timing_id'=>$timing[$i]['id'])); 			
						if(count($set)==0)
						{	
								$is_break = ClassTimings::model()->findByAttributes(array('id'=>$timing[$i]['id'],'is_break'=>1));
								if($is_break!=NULL)
								{	
									echo  Yii::t('app','Break');	
								}							
						}
						elseif($set->is_elective==0)
						{
							$time_sub = Subjects::model()->findByAttributes(array('id'=>$set->subject_id));
							if($time_sub!=NULL)
							{
								if($set->split_subject!=0 and $set->split_subject!=NULL){ 
									if($time_sub->split_subject){
										$subject_splits	= SubjectSplit::model()->findByPk($set->split_subject);
										$name_sub	=	$subject_splits->split_name." (".$time_sub->name.")";
									}
									else{
										$name_sub	=	$time_sub->name;
									} 
								}else{
									$name_sub	=	$time_sub->name;
								} 
								echo $name_sub .'<br>';
								$time_emp = Employees::model()->findByAttributes(array('id'=>$set->employee_id));
								if($time_emp!=NULL)
								{
									$is_substitute = TeacherSubstitution::model()->findByAttributes(array('leave_requested_emp_id'=>$time_emp->id,'time_table_entry_id'=>$set->id));																		
									if($is_substitute and in_array($is_substitute->date_leave,$date_between))
									{
										$employee = Employees::model()->findByAttributes(array('id'=>$is_substitute->substitute_emp_id));
										echo '<span style="font-size:9px;">(' .Employees::model()->getTeachername($employee->id).')</span>';										
									}
									else
									{
										echo '<span style="font-size:9px;">(' .Employees::model()->getTeachername($time_emp->id).')</span>';
									}
								}
							}
							else
							{
								echo '-<br>';
							}
						}
						else
						{
							$elec_sub = Electives::model()->findByAttributes(array('id'=>$set->subject_id));
							$electname = ElectiveGroups::model()->findByAttributes(array('id'=>$elec_sub->elective_group_id,'batch_id'=>$_REQUEST['id']));
							$time_emp = Employees::model()->findByAttributes(array('id'=>$set->employee_id));
																			
							if($electname!=NULL)
							{
								echo $electname->name.'<br>';
								
							}
						}
								?>
							  </td>
					 <?php           
					 }
					?><!--timetable_td -->
				
			  </tr><!--timetable_tr -->
			  <?php }
			  ?>
			  <?php
			  if($weekdays[4]['weekday']!=0)
			  {  ?>
				  <tr>
				<td><?php echo 	 Yii::t('app','THU') ;?></td>
			 
				  <?php
					  for($i=0;$i<$count_timing;$i++)
					  {
						?><td>
					   <?php  
						$set =  TimetableEntries::model()->findByAttributes(array('batch_id'=>$_REQUEST['id'],'weekday_id'=>$weekdays[4]['weekday'],'class_timing_id'=>$timing[$i]['id'])); 			
						if(count($set)==0)
						{	
								$is_break = ClassTimings::model()->findByAttributes(array('id'=>$timing[$i]['id'],'is_break'=>1));
								if($is_break!=NULL)
								{	
									echo  Yii::t('app','Break');	
								}	
						}
						elseif($set->is_elective==0)
						{
							$time_sub = Subjects::model()->findByAttributes(array('id'=>$set->subject_id));
							if($time_sub!=NULL)
							{
								if($set->split_subject!=0 and $set->split_subject!=NULL){ 
									if($time_sub->split_subject){
										$subject_splits	= SubjectSplit::model()->findByPk($set->split_subject);
										$name_sub	=	$subject_splits->split_name." (".$time_sub->name.")";
									}
									else{
										$name_sub	=	$time_sub->name;
									} 
								}else{
									$name_sub	=	$time_sub->name;
								} 
								echo $name_sub .'<br>';
								$time_emp = Employees::model()->findByAttributes(array('id'=>$set->employee_id));
								if($time_emp!=NULL)
								{
									$is_substitute = TeacherSubstitution::model()->findByAttributes(array('leave_requested_emp_id'=>$time_emp->id,'time_table_entry_id'=>$set->id));																		
									if($is_substitute and in_array($is_substitute->date_leave,$date_between))
									{
										$employee = Employees::model()->findByAttributes(array('id'=>$is_substitute->substitute_emp_id));
										echo '<span style="font-size:9px;">(' .Employees::model()->getTeachername($employee->id).')</span>';										
									}
									else
									{
										echo '<span style="font-size:9px;">(' .Employees::model()->getTeachername($time_emp->id).')</span>';
									}
								}
							}
							else
							{
								echo '-<br>';
							}
						}
						else
						{
							$elec_sub = Electives::model()->findByAttributes(array('id'=>$set->subject_id));
							$electname = ElectiveGroups::model()->findByAttributes(array('id'=>$elec_sub->elective_group_id,'batch_id'=>$_REQUEST['id']));
																			
							$time_emp = Employees::model()->findByAttributes(array('id'=>$set->employee_id));
																			
							if($electname!=NULL)
							{
								echo $electname->name.'<br>';
								
							}
						}
						?>
							  </td>  
					 <?php
					 }
					?><!--timetable_td -->
				
			  </tr><!--timetable_tr -->
			  <?php } ?>
			  <?php
			  if($weekdays[5]['weekday']!=0)
			  { ?>
			  
				  <tr>
				<td><?php echo 	 Yii::t('app','FRI') ;?></td>
			   
				 <?php
					  for($i=0;$i<$count_timing;$i++)
					  {
						?><td>
						<?php		
						$set =  TimetableEntries::model()->findByAttributes(array('batch_id'=>$_REQUEST['id'],'weekday_id'=>$weekdays[5]['weekday'],'class_timing_id'=>$timing[$i]['id'])); 			
						if(count($set)==0)
						{	
							$is_break = ClassTimings::model()->findByAttributes(array('id'=>$timing[$i]['id'],'is_break'=>1));
							if($is_break!=NULL)
							{	
								echo  Yii::t('app','Break');	
							}	
						}
						elseif($set->is_elective==0)
						{
							$time_sub = Subjects::model()->findByAttributes(array('id'=>$set->subject_id));
							if($time_sub!=NULL)
							{
								if($set->split_subject!=0 and $set->split_subject!=NULL){ 
									if($time_sub->split_subject){
										$subject_splits	= SubjectSplit::model()->findByPk($set->split_subject);
										$name_sub	=	$subject_splits->split_name." (".$time_sub->name.")";
									}
									else{
										$name_sub	=	$time_sub->name;
									} 
								}else{
									$name_sub	=	$time_sub->name;
								} 
								echo $name_sub .'<br>';
								$time_emp = Employees::model()->findByAttributes(array('id'=>$set->employee_id));
								if($time_emp!=NULL)
								{
									$is_substitute = TeacherSubstitution::model()->findByAttributes(array('leave_requested_emp_id'=>$time_emp->id,'time_table_entry_id'=>$set->id));																		
									if($is_substitute and in_array($is_substitute->date_leave,$date_between))
									{
										$employee = Employees::model()->findByAttributes(array('id'=>$is_substitute->substitute_emp_id));
										echo '<span style="font-size:9px;">(' .Employees::model()->getTeachername($employee->id).')</span>';										
									}
									else
									{
										echo '<span style="font-size:9px;">(' .Employees::model()->getTeachername($time_emp->id).')</span>';
									}
								}
							}
							else
							{
								echo '-<br>';
							}
						}
						else
						{
							$elec_sub = Electives::model()->findByAttributes(array('id'=>$set->subject_id));
							$electname = ElectiveGroups::model()->findByAttributes(array('id'=>$elec_sub->elective_group_id,'batch_id'=>$_REQUEST['id']));
																			
							$time_emp = Employees::model()->findByAttributes(array('id'=>$set->employee_id));
																			
							if($electname!=NULL)
							{
								echo $electname->name.'<br>';
								
							}
						}
						 ?>
							  </td>
					  <?php        
					 }
					?><!--timetable_td -->
				
			  </tr><!--timetable_tr -->
			  <?php }  ?>
			  <?php
			  if($weekdays[6]['weekday']!=0)
			  { ?>
			  <tr>
				<td><?php echo 	 Yii::t('app','SAT') ;?></td>
				
				  <?php
					  for($i=0;$i<$count_timing;$i++)
					  {
						?><td class="td">
						<?php	
									$set =  TimetableEntries::model()->findByAttributes(array('batch_id'=>$_REQUEST['id'],'weekday_id'=>$weekdays[6]['weekday'],'class_timing_id'=>$timing[$i]['id'])); 			
						if(count($set)==0)
						{	
							$is_break = ClassTimings::model()->findByAttributes(array('id'=>$timing[$i]['id'],'is_break'=>1));
							if($is_break!=NULL)
							{	
								echo  Yii::t('app','Break');	
							}
						}
						elseif($set->is_elective==0)
						{
							$time_sub = Subjects::model()->findByAttributes(array('id'=>$set->subject_id));
							if($time_sub!=NULL)
							{
								if($set->split_subject!=0 and $set->split_subject!=NULL){ 
									if($time_sub->split_subject){
										$subject_splits	= SubjectSplit::model()->findByPk($set->split_subject);
										$name_sub	=	$subject_splits->split_name." (".$time_sub->name.")";
									}
									else{
										$name_sub	=	$time_sub->name;
									} 
								}else{
									$name_sub	=	$time_sub->name;
								} 
								echo $name_sub .'<br>';
								$time_emp = Employees::model()->findByAttributes(array('id'=>$set->employee_id));
								if($time_emp!=NULL)
								{
									$is_substitute = TeacherSubstitution::model()->findByAttributes(array('leave_requested_emp_id'=>$time_emp->id,'time_table_entry_id'=>$set->id));																		
									if($is_substitute and in_array($is_substitute->date_leave,$date_between))
									{
										$employee = Employees::model()->findByAttributes(array('id'=>$is_substitute->substitute_emp_id));
										echo '<span style="font-size:9px;">(' .Employees::model()->getTeachername($employee->id).')</span>';										
									}
									else
									{
										echo '<span style="font-size:9px;">(' .Employees::model()->getTeachername($time_emp->id).')</span>';
									}

								}
							}
							else
							{
								echo '-<br>';
							}
						}
						else
						{
							$elec_sub = Electives::model()->findByAttributes(array('id'=>$set->subject_id));
							$electname = ElectiveGroups::model()->findByAttributes(array('id'=>$elec_sub->elective_group_id,'batch_id'=>$_REQUEST['id']));
																			
							$time_emp = Employees::model()->findByAttributes(array('id'=>$set->employee_id));
																			
							if($electname!=NULL)
							{
								echo $electname->name.'<br>';
								
							}
						}
						 ?>
							  </td>
				   <?php            
					 }
					?><!--timetable_td -->
				
			  </tr>
			<?php } ?>
		  </table>
		
	<?php
	 }
     else
	 {
	?>
		
        <?php echo  '<i>'.Yii::t('app','No Class Timings is set for this').' '.Yii::app()->getModule('students')->fieldLabel("Students", "batch_id").'</i>'; ?>
       
    	
	<?php
	 }
	 ?>
     
       <?php /*?><?php
		$batch = Weekdays::model()->findAll("batch_id=:x", array(':x'=>$_REQUEST['id']));
		if(count($batch)==0)
		$batch = Weekdays::model()->findAll("batch_id IS NULL");
		?><?php */?>
        
        <?php
		
	}
	?>
 
