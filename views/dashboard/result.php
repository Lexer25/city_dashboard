<? //http://itchief.ru/lessons/bootstrap-3/30-bootstrap-3-tables;?>
<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title"><?echo __('result')?></h3>
  </div>
  <div class="panel-body">
  
    <?echo Form::open('Dashboard/device_control');
	//echo Debug::vars('9', $_SESSION['res'], krsort($_SESSION['res']), $_SESSION['res'] );
	echo $content;
	$res='';
	echo '<br><h1>'. __('last_command').'<br></h1>';
	
	if (Arr::get($_SESSION, 'res') != null) 
	{
		
		foreach (array_reverse(Arr::get($_SESSION, 'res')) as $key=>$value)
		{
			$res=$res.'<p><b>'.$key. '</b> '.$value.'</p>';
		}
	} else {
		$res=__('no_data');
	}
	
	echo '<p class="small">'.$res.'</p>';
	
	?>

	
<?echo Form::close();?>	
  </div>
</div>