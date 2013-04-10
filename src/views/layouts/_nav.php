<? // FYI, this patial is populated from a view composer ?>

<div class="navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			
			<?// This is the button to expand on mobile ?>
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			
			<a class="brand" href="<?=action('decoy::')?>"><?=Config::get('decoy::site_name')?></a>
			<div class="nav-collapse collapse">
				
				<?// Login state ?>
				<? if (DecoyAuth::check()): ?>
					
					<?// The menu ?>
					<ul class="nav">
						<? foreach($pages as $page): ?>
							
							<?// Dropdown menu?>
							<? if (!empty($page->children)): ?>
								<li class="dropdown <?=$page->active?'active':null?>">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?=$page->label?> <b class="caret"></b></a>
									<ul class="dropdown-menu">
										
										<?// Loop through children ?>
										<? foreach($page->children as $child): ?>
											<? if (!empty($child->divider)): ?>
												<li class="divider"></li>
											<? else: ?>
												<li class="<?=$child->active?'active':null?>"><a href="<?=$child->url?>"><?=$child->label?></a></li>
											<? endif ?>
										<? endforeach ?>
										
									</ul>
								</li>
								
							<?// Standard link ?>
							<? else: ?>
								<li class="<?=$page->active?'active':null?>"><a href="<?=$page->url?>"><?=$page->label?></a></li>
							<? endif ?>
						<? endforeach ?>
					</ul>
					
					<ul class="nav pull-right">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<span>Hi, <?=DecoyAuth::user_name()?>!</span>
								<img src="<?=DecoyAuth::user_photo()?>" class="gravatar"/>
								<b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
								
								<? if (is_a(new DecoyAuth, 'Decoy\Auth')): ?>
									<li><a href="<?=action('decoy::admins')?>">Admins</a></li>
									<li class="divider"></li>
								<? endif ?>
								
								<? $divider = false; ?>
								<? if (DecoyAuth::developer()): $divider = true; ?>
									<li><a href="<?=action('decoy::tasks')?>">Tasks</a></li>
								<? endif ?>
								
								<? if (count(\Decoy\Worker::all())): $divider = true; ?>
									<li><a href="<?=action('decoy::workers')?>">Workers</a></li>
								<? endif ?>
								
								<? if ($divider): ?>
									<li class="divider"></li>
								<? endif ?>
								
								<li><a href="<?=DecoyAuth::user_url()?>">Account</a></li>
								<li><a href="<?=DecoyAuth::logout_url()?>">Log out</a></li>
							</ul>
						</ul>
					
				<? endif ?>
			</div>
		</div>
	</div>
	
	<?// The progress indicator for ajax requests?>
	<?= View::make('decoy::layouts._ajax_progress') ?>
</div>
