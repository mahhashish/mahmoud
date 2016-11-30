	<div class="box">
		<div class="box-body">
			<h4 class="head mb20 fs14 text-left pl20"><?php echo lang('tips_tip_preview') ;?></h4>	

			<?php  foreach ($records as $record) :?>	

			<div class="match text-center">
				<?php $country_id = $this->leagues_model->get_league_country_by_id($record->league_id); ?>
				<img class="h24 mtm4" src="<?php echo base_url();?>uploads/countries/<?php e($this->countries_model->get_country_flag_by_id($country_id)); ?>"/>
				<?php $league_name = $this->leagues_model->get_league_name_by_id($record->league_id); ?>			
				<span class="text-blue fs14 mr20"><?php e(strtoupper($league_name)); ?></span>
				<span class="fs14 mr20"><?php echo date('M d,Y', strtotime($record->match_date)); ?></span>
				<?php if($record->match_time):?>
				<span class="fs14 mr20"><?php echo date('H:i T', strtotime($record->match_time)); ?></span>
				<?php endif; ?>
				<span class="pull-right">		
				
					<span class="hidden-xs"><?php echo lang('tips_tip_views') ;?></span> 
					<span class="label label-blue ml10"><i class="fa fa-eye mr5"></i><?php echo $record->views;?></span>
					<div class="fb-share-button" data-href="https://tipstergala.com" data-layout="button" data-mobile-iframe="true"></div>

				</span>
			</div>	
			
			<div class="divider-6"></div>
			
			<div class="match fs18 text-center">
			
				<?php if($record->bet_name == 'Outright Winner'):?>
					<?php e($record->bet_name); ?>
				<?php else: ?>
				
				<?php $match = $this->tips_model->get_match_by_id($record->match_id);
				
					foreach($match as $rec):?>

						<span class="text-blue"><?php e($rec->home_team); ?></span>	

						<img class="h30 mtm44" src="<?php echo base_url();?>uploads/teams/<?php e($this->teams_model->get_team_logo_by_name($rec->home_team,$record->league_id)); ?>"/>

						<?php $result = $this->results_model->get_result_by_match_id($record->match_id);
						foreach ($result as $res): if($res->home):?>
							
							<span class="bg-black p10 mh10"><?php echo (string)$res->home; ?></span>-
							<span class="bg-black p10 mh10"><?php echo (string)$res->away; ?></span>
							
						<?php else:?>		
							<span class="bg-black p10 mh10">-</span>:<span class="bg-black p10 mh10">-</span>
							<?php endif; ?>							
						<?php endforeach;?>
						
						
						<img class="h30 mtm44" src="<?php echo base_url();?>uploads/teams/<?php e($this->teams_model->get_team_logo_by_name($rec->away_team,$record->league_id)); ?>"/>					
			
						<span class="text-blue"><?php e($rec->away_team); ?></span>	

				<?php endforeach;?>	
				<?php endif; ?>
			</div>		
			
			<div class="divider-6"></div>

			<h4 class="text-left text-blue"><?php echo lang('tips_description') ;?></h4>

			<?php echo $record->description;?>

			<div class="divider-6"></div>

				
			
			<?php //grab info for related tips bellow
			$league = $record->league_id;
				$event 		= $record->match_id;
				$tip_id		= $record->id;
			    $tip_status = $record->status;
				  
				  
			endforeach;?>

					
		</div><!-- /box-body -->	

	</div><!-- /box-->	

	<div class="box">
		<div class="box-body">
			<h4 class="head text-left pl20 fs14">Comments</h4>	
		<?php if(isset($current_user->id)): ?>
		<?php echo form_open('' , array('id' => 'comment-form')); ?>
			<textarea class="comment_txt" name="comment"></textarea>
			<button type="submit" class="fr btn btn-clean" >Submit</button>
				<div class="clear clearfix"></div>
		<?php echo form_close(); ?>
		<div class="divider-6"></div>
		<?php else: ?>
		<p> Please <a href="<?php base_url() ?>/login">login</a> to comment this tip.</p>
		<?php endif; ?>
		<?php if(count($comments)): ?>
		<h4>What are others saying...</h4>
		<?php foreach($comments as $comment): ?>
		<div class="comment_wrap">
			<div class="c_avatar"><img src="<?php echo base_url() ?>/uploads/tipsters/<?php echo $comment->avatar ?>" ></div>
		
			<div class="comment_content">
				<div class="c_comment"><?php echo nl2br($comment->comment) ?></div>
				<div class="comment_by">By <?php echo $comment->display_name ?></div>
				<div class="comment_time"><?php echo timespan(strtotime($comment->created_at),time()); ?> <?php echo "ago"; ?></div>
				<div class="clear clearfix"></div>
			</div>
		</div>
		
		<?php endforeach;?>
		<?php endif;?>
	</div>
</div>
	
	<?php if($tip_status == 2):?> 
	
	<div class="box">
		<div class="box-body">
			<h4 class="head text-left pl20 fs14"><?php echo lang('tips_related_tips') ;?></h4>	
			

			<?php $related = $this->tips_model->get_related_tips($event,$tip_id);
	
	            if (isset($related) && is_array($related) && count($related)) : ?>
	
	          <div class="table-responsive mt10">
				<table class='table table-striped'>
					<thead class="bg-black">
						<tr>
							<th class="pnp"><?php echo lang('tips_championship'); ?></th>
							<th class="pn"></th>
							<th class="pn"></th>
							<th class="pn"><?php echo lang('tips_match'); ?></th>
							<th class="pn"></th>
							<th class="pn"></th>
							<th class="pnn"><?php echo lang('tips_tipster'); ?></th>
							<th class="pnn"><?php echo lang('tips_posted_on'); ?></th>
						</tr>
					</thead>				
				
					<tbody>
				
					<?php foreach ($related as $record) :?>
					
					<tr>
						
							<td class="text-left">
								<?php $country_id = $this->leagues_model->get_league_country_by_id($record->league_id); ?>
								
								<img class="h24 mtm4 mr10" src="<?php echo base_url();?>uploads/countries/<?php e($this->countries_model->get_country_flag_by_id($country_id)); ?>"/>

								<span class="mr10"><?php e($this->leagues_model->get_league_name_by_id($record->league_id)); ?>

								</span>
							</td>
							
							<?php if($record->bet_name == 'Outright Winner'):?>
								<?php e($this->leagues_model->get_league_name_by_id($record->league_id)); ?>
							<?php else: ?>
				
							<?php $match = $this->tips_model->get_match_by_id($record->match_id);
							
								foreach($match as $rec):?>

								<td class="text-right">
									<?php e($rec->home_team); ?>	
								</td>	
								<td class="w30 pn text-right">	
									<img class="h30 mtm4" src="<?php echo base_url();?>uploads/teams/<?php e($this->teams_model->get_team_logo_by_name($rec->home_team,$record->league_id)); ?>"/>
								</td>
								<td class="pn">
									<a href="<?php echo base_url(); ?>tips/preview/<?php echo $record->id; ?>">
										<span class="text-blue mh5">info</span>
									</a>	
								</td>
								<td class="w30 pn text-left">	
									<img class="h30 mtm4" src="<?php echo base_url();?>uploads/teams/<?php e($this->teams_model->get_team_logo_by_name($rec->away_team,$record->league_id)); ?>"/>
								</td>	
								<td class="text-left">
									<?php e($rec->away_team); ?>	
								</td>	

							<?php endforeach;?>
							<?php endif; ?>
							
							<div class="fr">
								<td class="text-center">

									<a href="<?php echo base_url();?>tipsters/profile/<?php echo $record->created_by;?>">	
										 <?php echo $this->tipsters_model->get_user_display_name_by_id($record->created_by);?>
									</span>
								</td>	
								<td class="w30 pn text-left">	
									<?php echo relative_time(strtotime($record->created_on)); ?>

								</td>
							</div>
						
					
					</tr>
					<?php endforeach; 
					else:?>
					</tbody>
				</table>	
					<p class="mt10 text-left text-red"><?php echo lang('tips_no_related'); ?></p>
			</div>
					
					
			<?php endif; ?>			

		</div><!-- /box-body -->	

	</div><!-- /box-->	
	
	<?php endif; ?>	
