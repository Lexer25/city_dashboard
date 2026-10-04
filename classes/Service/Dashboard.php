<?php defined('SYSPATH') or die('No direct script access.');

class Service_Dashboard {

    /**
     * Собирает все данные для панели управления.
     * Раньше эта логика была размазана по action_index().
     */
    public function getDashboardData($config_windows)
    {
        $result = array(
            'windows1' => array(),
            'windows2' => array(),
            'windows3' => array(),
            'analyt_result' => array(),
        );

        if (Arr::get($config_windows, 'windows1', FALSE)) {
            $result['windows1'] = $this->getWin1();
        }

        if (Arr::get($config_windows, 'windows2', FALSE)) {
            $result['windows2'] = Model::Factory('Stat')->getEquipment();
        }

        if (Arr::get($config_windows, 'windows3', FALSE)) {
            $result['windows3'] = Model::Factory('Stat')->getLoadOrder();
        }

        // Аналитика считается всегда (как было в оригинале)
        $result['analyt_result'] = Model::Factory('Stat')->analyt_result();

        return $result;
    }

    /**
     * 31.03.2026 Сбор информации для окна №1.
     * Метод ПЕРЕНЕСЁН из контроллера БЕЗ ИЗМЕНЕНИЙ.
     */
    public function getWin1()
    {
        $config = Kohana::$config->load('artonitcity_config');
        $days = (int) $config->count_day_befor_end_time;
        $dateExpired = date('d.m.Y', strtotime("+{$days} days"));
        $people_model = Model::factory('summary');
        $counts = $people_model->peopleCounts($dateExpired);

        $result = array();
        $result['people_count']            = Arr::get($counts, 'PEOPLE_TOTAL', 22);
        $result['key_people_delete']       = Arr::get($counts, 'PEOPLE_INACTIVE');
        $result['getPeopleWithoutCard']    = Arr::get($counts, 'PEOPLE_WITHOUT_CARD');
        $result['timeExpired']             = $dateExpired;
        $result['count_card_late_next_week'] = Arr::get($counts, 'CARD_EXPIRED_ON_DATE');
        $result['getcardexpired']          = Arr::get($counts, 'CARD_EXPIRED');
        $result['getCardNotActive']        = Arr::get($counts, 'CARD_INACTIVE');
        $result['getPeopleCardCount']      = Arr::get($counts, 'CARD_TYPE1_TOTAL');

        return $result;
    }

    /**
     * 2.04.2026 Сбор информации для окна №1 по бюро пропусков.
     * Метод ПЕРЕНЕСЁН из контроллера БЕЗ ИЗМЕНЕНИЙ.
     */
    public function getWin1Guest()
    {
        $config = Kohana::$config->load('artonitcity_config');
        $days = (int) $config->count_day_befor_end_time;
        $dateExpired = date('d.m.Y', strtotime("+{$days} days"));

        $people_model = Model::factory('people');
        $card_model   = Model::factory('identifier');

        $result = array();
        $result['guestCount']              = $people_model->getGuestCount();
        $result['guestArchiveCount']       = $people_model->getGuestArchiveCount();
        $result['guestCardCount']          = $card_model->getGuestCardCount();
        $result['guestArchiveCardCount']   = $card_model->getGuestArchiveCardCount();

        return $result;
    }
	
	
	
	/**
     * 4.10.2026 Сбор Проверка форматов в базе данных СКУД.
     * Метод ПЕРЕНЕСЁН из контроллера dbskid БЕЗ ИЗМЕНЕНИЙ.
     */
		public function checkRfidKeyFormat()
	{
		$sql='select c.id_card from card c
		where (c.id_card like \'%a%\'
		or c.id_card like \'%b%\'
		or c.id_card like \'%c%\'
		or c.id_card like \'%d%\'
		or c.id_card like \'%e%\'
		or c.id_card like \'%f%\')
		and c.id_cardtype=1
		';
		
		$query_db = DB::query(Database::SELECT, $sql)
				->execute(Database::instance('fb'))
				->as_array();	
		return $query_db;
		
	}
	
	
}
