<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title"><?echo __('log_files')?></h3>
  </div>
  <div class="panel-body">
  
</div>


	<div class="panel panel-primary col-md-3">
	  <div class="panel-heading row"><?echo __('log1');?></div>
	  <div class="panel-body">
		<?
		if(count($list1)>1)
		{
	  		foreach ($list1 as $key =>$value)
			{	  
				echo HTML::anchor('dashboard/sendFile?name='.Kohana::$config->load('artonitcity_config')->dir_log.'\\'.$value, $value).'<br>';
			}
		} else {
			echo __('no_log');
		}
		?>
	  </div>
	</div>

	<div class="panel panel-primary col-md-6 col-md-offset-1">
	  <div class="panel-heading row"><?echo __('log2');?></div>
	  <div class="panel-body">
		<?
		if(count($list1)>1)
		{
			  foreach ($list2 as $key =>$value)
				{	  
					//echo HTML::anchor('dashboard/sendFile?name='.Kohana::$config->load('artonitcity_config')->dir_compare.'\\'.$value, $value).'<br>';
					echo HTML::anchor('dashboard/sendFile?name='.Kohana::$config->load('artonitcity_config')->dir_compare.'\\'.$value, $value).'<br>';
				}
		} else {
			echo __('no_log');
		}
		?>
	  </div>
	</div>



</div>

