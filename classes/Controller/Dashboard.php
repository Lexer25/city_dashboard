<?php defined('SYSPATH') or die('No direct script access.');

class Controller_dashboard extends Controller_Template {

   public $template = 'template';
   //Широки шаблон
   //для использьвания необходимо указать 
   //$this->template = View::factory($this->template_width);
   public $template_width = 'template_width';
   
  	
	public function before()
	{
			
			parent::before();
			$session = Session::instance();
		
	}
	
	public function action_index()
	{
		$config_windows = Kohana::$config->load('artonitcity_config')->main_windows;
		$_SESSION['menu_active'] = 'index';

		// Вся сборка данных — в сервисе
		$service = new Service_Dashboard();
		$data = $service->getDashboardData($config_windows);
		$data['countErrKeyFormatRfid'] = count($service->checkRfidKeyFormat());

		// То, что не относится к «сборке данных» (счётчики-однострочники),
		// можно оставить в контроллере или тоже перенести в сервис.
		//$countErrKeyFormatRfid = count(Model::factory('dbskud')->checkRfidKeyFormat());
		$about = Model::factory('Parkdb')->aboutDB('fb');

		$content = View::factory('dashboard/dashboard', array(
			'list_windows1'         => $data['windows1'],
			'list_windows2'         => $data['windows2'],
			'list_windows3'         => $data['windows3'],
			'analyt_result'         => $data['analyt_result'],
			'countErrKeyFormatRfid' => $data['countErrKeyFormatRfid'] ,
			'about'                 => $about,
			'config_windows'        => $config_windows,
		));

		$this->template->content = $content;
	}
		
		


			
		/**2.04.2026 Сбор информации для окна №1 по бюро пропусков
					
		*/
			public function getWin1Guest()
			{
				$config = Kohana::$config->load('artonitcity_config');
				$days = (int) $config->count_day_befor_end_time;
				$dateExpired=date('d.m.Y', strtotime("+{$days} days"));//дата для расчета
				$people_model = Model::factory('people');
				$card_model = Model::factory('identifier');
				
				$result=array();
				$result['guestCount']=$people_model->getGuestCount();//количество гостей
				$result['guestArchiveCount']=$people_model->getGuestArchiveCount();//количество гостей в архиве
				$result['guestCardCount']=$card_model->getGuestCardCount();//количество карт у гостей
				$result['guestArchiveCardCount']=$card_model->getGuestArchiveCardCount();//количество карт у гостей в архиве
								
               
				
				
				
				return $result;
			}
			
			
			
			
			
			
			
			
			
	/** 14.09.2024 Просмотр списка карт с неправильным форматом
	*/
	public function action_ErrKeyFormatRfid()
	{
		//echo Debug::vars('95', Model::factory('dbskud')->checkRfidKeyFormat()); exit;
		//
		$res= Model::factory('dbskud')->checkRfidKeyFormat();
		$var=array();
		if(is_array($res)){
			foreach($res as $key=>$value)
			{
				$var[]='"'.Arr::get($value,'ID_CARD').'"';
				
			}
			
		}
		
		if(count($var)){
		$mess=__('Ошибка формата карт :cardlist. Номер карты должен содержать строку цифры и буквы ABCDEF. Удалите карту и зарегистрируйте ее еще раз.', array(':cardlist'=>implode(",", $var)));
		
		throw new Exception ('Неправильный формат карт '. $mess);
		
		$content = View::factory('dashboard/result', array(
			'content' => $mess,
		));
		$this->template->content = $content;
		} else {
			
			$this->redirect('/');
		}
		
	}

		public function action_log()
		{
			$_SESSION['menu_active'] = 'log';

			$config = Kohana::$config->load('artonitcity_config');

			// Левая колонка — рекурсивный список старых логов
			$res1 = Model::Factory('Log')->getList();

			// Правое окно — framework
			$rootFramework  = $config->dir_log_framework;
			$subPathFramework = Arr::get($_GET, 'path1', '');
			$extsFramework    = array('php', 'log', 'txt', 'csv');
			$fm = Model::Factory('Log')->listDirectory($rootFramework, $subPathFramework, $extsFramework);

			// Третье окно — dir_log_ArtonitServices
			$rootServices   = $config->dir_log_ArtonitServices;
			$subPathServices = Arr::get($_GET, 'path2', '');
			$extsServices    = array('json', 'txt', 'log');
			$fmServices = Model::Factory('Log')->listDirectory($rootServices, $subPathServices, $extsServices);

			$content = View::factory('dashboard/log', array(
				'list1'       => $res1,
				'fm'          => $fm,
				'root'        => $rootFramework,
				'fm_services' => $fmServices,
				'root_services' => $rootServices,
			));
			$this->template->content = $content;
		}

		public function action_sendFile()
		{
			$file = Arr::get($_GET, 'name');

			$config = Kohana::$config->load('artonitcity_config');

			// Разрешаем отдавать только из dir_log_framework, dir_log, dir_compare
			$allowedDirs = array();
			foreach (array('dir_log_framework', 'dir_log', 'dir_compare') as $key) {
				$d = realpath($config->$key);
				if ($d) {
					$allowedDirs[] = $d;
				}
			}

			$realFile = realpath($file);
			$allowed  = FALSE;
			foreach ($allowedDirs as $dir) {
				if ($realFile && strpos($realFile, $dir) === 0) {
					$allowed = TRUE;
					break;
				}
			}

			if (!$allowed || !is_file($realFile)) {
				throw new HTTP_Exception_404('File not found');
			}

			Model::Factory('Log')->send_file($realFile);
		}
    
	public function ErrMess ($err=false)
	{
		$content = View::factory('dashboard/errorpage');
		$this->template->content = $content;
	}
	
	/* public function action_opendoor()// обработка команды открывания дверей
	{
		Log::instance()->add(Log::NOTICE, 'Получил запрос opendoor');
		$res=Model::Factory('Device')->sendCommand('127.0.0.1', 1967, '333', 'opendoor door=0');
		$content = View::factory('dashboard/result', array(
			'content' => $res,
		));
	    $this->template->content = $content;
	} */
	
	
	/**31.08.2024  функция записи массива данных в файл
	*/
	public function saveFile($fileName, $data)
	{
				$fileName=$fileName.".csv";
				$fp = fopen($fileName, 'w');
				foreach ($data as $key=>$value)
				{
//если $value массив, то сохраняю через fputcsv
					if(is_array($value)){
						fputcsv ($fp, $value,';');
					} else {
						
						fwrite ($fp, $value.PHP_EOL);
					}
				}
					
				
			
		fclose($fp); //Закрытие файла
		
	}
	
	


	/** 2.09.2024 экспорт состояния СКУД в файл
	
	*/
	public function action_saveStateSkud()
	{
		// заголовок отчета
		$reportTitle=array();
		//$reportTitle[]=array('Отчет о состоянии СКУД','Отчет о состоянии СКУД',);
		//$reportTitle[]=array('','','','',date('Y-m-d H:i:s'));
		$reportTitle[]=array('id', 
				// iconv('UTF-8','windows-1251','Название'), 
				// iconv('UTF-8','windows-1251','Тип'),
				// iconv('UTF-8','windows-1251','Активность'),
				// iconv('UTF-8','windows-1251','Строка подключения'),
				'name',
				'type',
				'is_active',
				'connectionString',
				'mac',
						'onLine',
						'isWP',
						'isTest',
						'door_0',
						'door_1',
						'inputPortState',
						'softVersion',
						'keyCount',
						'timestamp',
				
				);
		
		//список контроллеров и их состояние
		$deviceList=Model::factory('Device')->getdeviceList();
		
		
		//echo Debug::vars('635', $deviceList);exit;
		
		foreach($deviceList as $key=>$value)
		{
			$device=new Device ($value);
			$deviceInfo=new DeviceInfo($value, trim(Model::Factory('Stat')->getDeviceStatData($value)));
			//echo Debug::vars('641', $key, $value, $device, $deviceInfo);exit;
			//if($key>107) {echo Debug::vars('653', $device, $deviceInfo);exit;}
			
			
			$reportTitle[]=array($device->id, 
					$device->name,
					$device->type,
					$device->is_active? 'Yes':'No',
					$device->connectionString,
						$deviceInfo->mac,
						$deviceInfo->onLine? 'Yes':'No',
						$deviceInfo->isWP? 'Yes':'No',
						$deviceInfo->isTest? 'Yes':'No',
						$deviceInfo->doorMode_0,
						$deviceInfo->doorMode_1 ,
						is_array($deviceInfo->inputPortState)?  implode("", $deviceInfo->inputPortState) : '',
						$deviceInfo->softVersion,
						is_array($deviceInfo->keyCount)? implode(",", $deviceInfo->keyCount) : '',
						date("H:i:s d.m.Y", $deviceInfo->timeGetData) ,
						);
			
			
		}
		
		//список "неправильных" карт
		$objectName= isset(Kohana::$config->load('artonitcity_config')->city_name)? Kohana::$config->load('artonitcity_config')->city_name : '';
		$file_name="scud_report_".$objectName.'_'.date('Y_m_d_H-i-s').".csv";
		
			$fp = fopen($file_name, 'w');
			
						foreach ($reportTitle as $fields) {
							fputcsv($fp, $fields, ';');
						}

						fclose($fp);
					$file=$file_name;
						if (file_exists($file)) {
				// сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
				// если этого не сделать файл будет читаться в память полностью!
				if (ob_get_level()) {
				  ob_end_clean();
				}
				// заставляем браузер показать окно сохранения файла
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename=' . basename($file));
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($file));
				// читаем файл и отправляем его пользователю
				readfile($file);
				exit;
			  }
			  
  
		$this->redirect('/Dashboard');
		
	}



	/** 3.09.2024 выборка IP адресов из ТС2 и вставка их в БД СКУД.
	*/
	public function action_getIpFromTs2()
	{
		//получаю список транспортных серверов
		
		//получаю список контроллеров
		$deviceList=Model::factory('Device')->getdeviceList();
		//далее надо работать только с типом 1 и 2 (они обслуживаются в ТС2
		
		foreach($deviceList as $key=>$value)
		{
			$dev=new Device($key);
			//echo Debug::vars('735', $dev);exit;
			switch($dev->type){
							case 1: //контроллеры типа Артонит
							case 2: //контроллеры типа Артонит
							
								//созданю экземпляр класса работы через ТС2
								$TS2client=new TS2client();
								$TS2client->startServer();
								
								$command ='h56 deviceinfo name="'.$dev->name.'"';
								$TS2client->sendMessage($command);
								$answer=$TS2client->readMessage();
								$TS2client->stopClient();
								echo Debug::vars('746', $command, $answer);exit;
								
								$dev->connect();
								
							if($dev->connection) {
								
						//		$t1=microtime(true);
								$command='readkey door='.Arr::get($key, 'ID_READER').', cell='.$i;
									//echo Debug::vars('447', $command);//exit;
									$strdata=trim($dev->sendcommand($command));
								$device=new Device($key);
								echo Debug::vars('730', $key, $value, $device);exit;
							}
							break;
							default;
							break;
				}
		}
	}
	
}
