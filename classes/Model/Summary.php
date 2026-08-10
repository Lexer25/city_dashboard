<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Summary extends Model
{
	
	public function peopleCounts ($dateExpired) // подсчет разных количеств
	{

	
	$sql='SELECT
    COUNT(DISTINCT P.ID_PEP) AS PEOPLE_TOTAL,
    SUM(CASE WHEN P."ACTIVE" = 0 THEN 1 ELSE 0 END) AS PEOPLE_INACTIVE,
    SUM(CASE WHEN P."ACTIVE" > 0 THEN 1 ELSE 0 END) AS PEOPLE_ACTIVE,
    SUM(CASE WHEN C.ID_PEP IS NULL THEN 1 ELSE 0 END) AS PEOPLE_WITHOUT_CARD,
    (SELECT COUNT(*) FROM CARD WHERE ID_CARDTYPE = 1) AS CARD_TYPE1_TOTAL,
    (SELECT COUNT(*) FROM CARD WHERE TIMEEND < CURRENT_TIMESTAMP) AS CARD_EXPIRED,
    (SELECT COUNT(*) FROM CARD WHERE TIMEEND < \''.$dateExpired.'\') AS CARD_EXPIRED_ON_DATE,
    (SELECT COUNT(*) FROM CARD WHERE "ACTIVE" = 0) AS CARD_INACTIVE
		FROM PEOPLE P
		LEFT JOIN CARD C ON C.ID_PEP = P.ID_PEP AND C.ID_DB = P.ID_DB;;';
		$query = DB::query(Database::SELECT, iconv('UTF-8','windows-1251',$sql))
			->execute(Database::instance('fb'))
			->current();
	
	return $query;
	}
	
	
	
	public function parking_error($id_pep)//полученние информации о нарушениях правил парковки
	{
		$res=__('no_parking_errors');
		$sql='select p.sysnote from people p where p.id_pep='.$id_pep;
 
		$query = DB::query(Database::SELECT, $sql)
			->execute(Database::instance('fb'))
			->get('SYSNOTE', '--');
	
		//echo Debug::vars('52', $query); //exit;
		if (!empty($query))
		{
			
			$res=iconv('windows-1251','UTF-8',$query);
			$order   = array("\r\n", "\n", "\r");
			$replace = '<br />';
			$res= str_replace($order, $replace, $res);
		}
		return $res;
	}

	
}
	

