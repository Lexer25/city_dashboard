<?php
//echo Debug::vars('2', $serverList);//exit;
/* echo Form::open('dashboard/getIpFromTs2');
	echo Form::button('getIpFromTs2','Скопировать IP адреса из ТС2 в БД СКУД.');

echo Form::close(); */

if(false){

	// $t1=microtime(true);
	// $ip_address='10.25.16.70';
	// $ip_address='10.25.16.76';// это  прошивка 2014
	 $ip_address='192.168.0.33';
	//echo Debug::vars('47',$ip_address);
	$deviceHard = new artonitHTTP($ip_address);
	$deviceHard->getDeviceInfo();
	$deviceHard->getScudMode();
	$deviceHard->disconnect();
	
	//echo Debug::vars('61', $deviceHard); exit;
	// $deviceHard = new artonitHTTP($ip_address);
						
						
						// $deviceHard->getDeviceInfo();// заполняю данные экземпляра из полученных ответов
						
						
		// $deviceHard->disconnect();
		// echo Debug::vars('60',$deviceHard); exit;
					
					
					
	//$deviceHard->getDoorMode();
	//$deviceHard->getInputPortState();
	//$deviceHard->getSoftVersion();
	//$deviceHard->getDeviceInfo();
	//echo Debug::vars('70', $deviceHard); exit;
	//echo Debug::vars('50 time execute', (microtime(true) - $t1));
	//exit;
							
	// $t1=microtime(true);

	// $artonit=new phpArtonitUDP($ip_address, 8192);
	// $artonit->command='GetVersion';
	// $artonit->execute();
	// echo Debug::vars('63', $artonit->command, $artonit->result,$artonit->edesc, $artonit->answer); 

	// $artonit->command='GetMAC';
	// $artonit->execute();
	// echo Debug::vars('67', $artonit->command,$artonit->result,$artonit->edesc, $artonit->answer);

	// $artonit->command='GetJmp';
	// $artonit->execute();
	// echo Debug::vars('71', $artonit->command,$artonit->result,$artonit->edesc, $artonit->answer);

 
	// $artonit->command='GetIO';
	// $artonit->execute();
	// echo Debug::vars('76', $artonit->command,$artonit->result, $artonit->edesc, $artonit->answer);

	// $artonit->command='GetAP0';
	// $artonit->execute();
	// echo Debug::vars('80', $artonit->command,$artonit->result, $artonit->edesc, $artonit->answer);

	// $artonit->command='GetKeyCount';
	// $artonit->execute();
	// echo Debug::vars('84', $artonit->command,$artonit->result, $artonit->edesc, $artonit->answer);



	//echo Debug::vars('86', $deviceHard);
	// echo Debug::vars('87 time execute', (microtime(true) - $t1));

		//echo Debug::vars('96', $key, $value);
	//	$artonit=new phpArtonitUDP($value, 8192);
		/* $artonit->command='GetVersion';
		$artonit->execute();
		//echo Debug::vars('84', $artonit->command,$artonit->result, $artonit->edesc, $artonit->answer);
		$ver=$artonit->answer;
		$artonit->command='GetAP0';
		$artonit->execute();
		$var0=$artonit->answer;
		$artonit->command='GetAP1';
		$artonit->execute();
		$var1=$artonit->answer;
		 */
		// $artonit->command='GetKeyCount';
		// $artonit->execute();
		// $keyCount=$artonit->answer;
		

		$dev=new phpArtonitTS2();
		$dev->connect();
		
		echo Debug::vars('126', $dev->sendcommand('getversion'));
		echo Debug::vars('126', $dev->sendcommand('getdevicetime'));
		$t1=microtime(true);
		$res=array();
		for($i=0; $i<32; $i++){
			$res[]=trim($dev->sendcommand('readkey door=0, cell='.$i));
		
		}
		$dev->close();
	//	echo Debug::vars('135',  (microtime(true)-$t1));exit;
		
		echo Debug::vars('141', Model::factory('dbskud')->checkRfidKeyFormat());
}

?>



