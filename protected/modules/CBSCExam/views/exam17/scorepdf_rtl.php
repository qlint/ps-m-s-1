<?php
$this->breadcrumbs=array(
	Yii::t('app','Student Attentances')=>array('/courses'),
	Yii::t('app','Attendance'),
);
$student_visible_fields   = FormFields::model()->getVisibleFields('Students', 'forStudentProfile');
?>
<style>
/*.score_table{
	border-top:1px #CCC solid;
	margin:30px 0px;
	font-size:15px;
	border-right:1px #CCC solid;
	
}
.score_table td,th{
	border-left:1px #CCC solid;
	padding:5px 6px;
	border-bottom:1px #CCC solid;
	width: 150px;
	text-align:center;
}*/

table.score_table{
	margin:30px 0px;
	font-size:15px;
	border-collapse:collapse
}

table.score_table tr td,th{
	border:1px  solid #C5CED9;
	padding:5px 7px;
	
}

.score_table th { background:DCE6F1;
padding:10px 7px}

.heading{
	text-align:center;
	font-size:24px;
	font-weight:bold;
}
hr{ border-bottom:1px solid #C5CED9; border-top:0px solid #fff;}
</style>


<?php

  if(isset($_REQUEST['id']) && isset($_REQUEST['examid']))
  {
	?>
     <!-- Header -->
  
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td class="first" width="100">
                           <?php $logo=Logo::model()->findAll();?>
                            <?php
                            if($logo!=NULL)
                            {
                                //Yii::app()->runController('Configurations/displayLogoImage/id/'.$logo[0]->primaryKey);
                                echo '<img src="uploadedfiles/school_logo/'.$logo[0]->photo_file_name.'" alt="'.$logo[0]->photo_file_name.'" class="imgbrder" height="100" />';
                            }
                            ?>
                </td>
                <td  valign="middle"  >
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="listbxtop_hdng first" style="text-align:left; font-size:22px; padding-left:10px;">
                                <?php $college=Configurations::model()->findAll(); ?>
                                <?php echo $college[0]->config_value; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="listbxtop_hdng first" style="text-align:left; font-size:14px; padding-left:10px;">
                                <?php echo $college[1]->config_value; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="listbxtop_hdng first" style="text-align:left; font-size:14px; padding-left:10px;">
                                <?php echo Yii::t('app','Phone: ').$college[2]->config_value; ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    <hr />
    <br />
    <!-- End Header -->
  
    <div align="center" style="display:block; text-align:center !important;"><?php echo Yii::t('app','EXAM SCORES');?></div><br />
     <?php 
		 $batch_students = BatchStudents::model()->findAllByAttributes(array('batch_id'=>$_REQUEST['id'],'result_status'=>0));
		$students=Students::model()->findAll("batch_id=:x and is_active=:y and is_deleted=:z", array(':x'=>$_REQUEST['id'],':y'=>1,':z'=>0)); 
		$scores = CbscExamScores17::model()->findAllByAttributes(array('exam_id'=>$_REQUEST['examid']));
		
		$exam = CbscExams17::model()->findByAttributes(array('id'=>$_REQUEST['examid']));
		$exam_group = CbscExamGroup17::model()->findByAttributes(array('id'=>$exam->exam_group_id));
		$sub_name = Subjects::model()->findByAttributes(array('id'=>$exam->subject_id));
		$batch = Batches::model()->findByAttributes(array('id'=>$_REQUEST['id']));
		$course = Courses::model()->findByAttributes(array('id'=>$batch->course_id));
		$sem_enabled= Configurations::model()->isSemesterEnabledForCourse($course->id);
		$semester	=	Semester::model()->findByAttributes(array('id'=>$batch->semester_id));  
		
		
	?>
    <!-- Course details -->
   
       <table style="font-size:14px;background:#DCE6F1;padding:10px 10px;border:#C5CED9 1px;">
           
            <tr>
                <td style="width:150px;"><?php echo Yii::t('app','Course');?></td>
                <td style="width:10px;">:</td>
                <td style="width:350px;">
					<?php 
					if($course->course_name!=NULL)
						echo ucfirst($course->course_name);
					else
						echo '-';
					?>
				</td>
                
                <td width="150"><?php echo Yii::app()->getModule('students')->fieldLabel("Students", "batch_id");?></td>
                <td width="10">:</td>
                <td>
					<?php 
					if($batch->name!=NULL)
						echo ucfirst($batch->name);
					else
						echo '-';
					?>
				</td>
            
            </tr>
            <tr>
            	<td><?php echo Yii::t('app','Total Students');?></td>
                <td>:</td>
                <td >
					<?php 
					if($batch_students!=NULL)
						echo count($batch_students);
					else
						echo '-';
					?>
				</td>
            	<td><?php echo Yii::t('app','Examination');?></td>
                <td>:</td>
                <td width="350">
					<?php 
					if($exam_group->name!=NULL)
						echo ucfirst($exam_group->name);
					else
						echo '-';
					?>
				</td>
            </tr>
            
            <tr>
            	<td><?php echo Yii::t('app','Subject');?></td>
                <td>:</td>
                <td>
					<?php 
					if($sub_name->name!=NULL)
						echo $sub_name->name;
					else
						echo '-';
					?>
				</td>
            	<td><?php echo Yii::t('app','Date');?></td>
                <td>:</td>
                <td>
					<?php 
					if($exam->start_time!=NULL)
					{
						$settings=UserSettings::model()->findByAttributes(array('user_id'=>Yii::app()->user->id));
						if($settings!=NULL)
						{	
							$exam->start_time = date($settings->displaydate,strtotime($exam->start_time));
							echo $exam->start_time;
						}
						else
						{
							echo $exam->start_time;
						}
					}
					else
					{
						echo '-';
					}
					?>
				</td>
            </tr>
            <tr>
                <td><?php echo Yii::t('app','Class');?></td>
                <td>:</td>
                <td><?php echo CbscExamGroup17::model()->getClassName($exam_group->class); ?></td>
 <?php if($sem_enabled==1 and $semester!=NULL){?>
			 
                <td><?php echo Yii::t('app','Semester');?></td>
                <td>:</td>
                <td><?php echo ucfirst($semester->name);?></td>
            </tr>
  <?php } ?>         
        </table>
 
    <!-- END Course details -->
	 <!-- Score Table -->

    
    	<table style="font-size:13px;" class="score_table"  width="100%" cellspacing="0" >
        	<tr style="background:#DCE6F1; text-align:center;">
          		<?php if(Configurations::model()->rollnoSettingsMode() != 2){?>
				 	<td style="width:100px;"><?php echo Yii::t('app','Roll No');?></td>
				<?php }?>
                <?php if(FormFields::model()->isVisible("fullname", "Students", "forStudentProfile")){ ?>
                	<td style="width:100px;"><?php echo Yii::t('app','Name');?></td>
                <?php }?> 
                <td style="width:100px;"><?php echo Yii::t('app','Written Exam');?></td>
                <td style="width:100px;"><?php echo Yii::t('app','Periodic Test');?></td>
                <td style="width:100px;"><?php echo Yii::t('app','Note Book');?></td>
                <td style="width:100px;"><?php echo Yii::t('app','Subject Enrichment');?></td>
                <td style="width:100px;"><?php echo Yii::t('app','Total');?></td>
                 <td style="width:100px;"><?php echo Yii::t('app','Status');?></td>              
                <td style="width:100px;"><?php echo Yii::t('app','Remarks'); ?></td>
               
        	</tr>
         
            <?php 
			$i = 1;
			
			
			foreach($scores as $score)
			 {
			 $student  = Students::model()->findByAttributes(array('id'=>$score->student_id));
			 $batch_student=BatchStudents::model()->findByAttributes(array('student_id'=>$student->id, 'batch_id'=>$batch->id, 'status'=>1));
			 echo "<tr>";
				 
			 	 if(Configurations::model()->rollnoSettingsMode() != 2){
				 	echo "<td width='250'>".$batch_student->roll_no."</td>";
                 } 
				 if(FormFields::model()->isVisible("fullname", "Students", "forStudentProfile")){
				 	echo "<td width='250'>".$student->studentFullName("forStudentProfile")."</td>";
                 } 
				 echo "<td width='100'>".$score->written_exam."</td>";
				 echo "<td width='100'>".$score->periodic_test."</td>";
				 echo "<td width='100'>".$score->note_book."</td>";
				 echo "<td width='100'>".$score->subject_enrichment."</td>";
				 echo "<td width='100'>".$score->total."</td>";?>
				 <td width='100'><?php 
				 	if($score->is_failed == 1){
					  echo Yii::t('app','Fail');
					}else{
					  echo  Yii::t('app','Pass');
					}?> </td>
				<?php  echo "<td width='250'>".$score->remarks."</td>";				 
			 echo "</tr>";
			 $i++;
			}
	 		?>
         
        </table>
	
    
     <!-- END Score Table -->


<?php  }?>
