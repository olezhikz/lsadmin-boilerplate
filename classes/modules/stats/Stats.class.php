<?php
/**
 * LiveStreet CMS
 * Copyright © 2013 OOO "ЛС-СОФТ"
 * 
 * ------------------------------------------------------
 * 
 * Official site: www.livestreetcms.com
 * Contact e-mail: office@livestreetcms.com
 * 
 * GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * 
 * ------------------------------------------------------
 * 
 * @link http://www.livestreetcms.com
 * @copyright 2013 OOO "ЛС-СОФТ"
 * @author Serge Pustovit (PSNet) <light.feel@gmail.com>
 * 
 */

/*
 * Модуль для расчета статистики
 */

class PluginAdmin_ModuleStats extends Module {

	protected $oMapper = null;

	/*
	 *
	 * тип данных для получения
	 *
	 */

	/*
	 * регистрации пользователей
	 */
	const DATA_TYPE_REGISTRATIONS = 'registrations';
	/*
	 * топики
	 */
	const DATA_TYPE_TOPICS = 'topics';
	/*
	 * комментарии
	 */
	const DATA_TYPE_COMMENTS = 'comments';
	/*
	 * голосование
	 */
	const DATA_TYPE_VOTINGS = 'votings';
	/*
	 * блоги
	 */
	const DATA_TYPE_BLOGS = 'blogs';

	/*
	 *
	 * предустановленные временные интервалы
	 *
	 */

	/*
	 * вчера
	 */
	const TIME_INTERVAL_YESTERDAY = 'yesterday';
	/*
	 * сегодня
	 */
	const TIME_INTERVAL_TODAY = 'today';
	/*
	 * неделя
	 */
	const TIME_INTERVAL_WEEK = 'week';
	/*
	 * месяц
	 */
	const TIME_INTERVAL_MONTH = 'month';


	public function Init() {
		$this->oMapper = Engine::GetMapper(__CLASS__);
	}


	/**
	 * Получить реальный временной интервал в зависимости от типа периода для статистики
	 *
	 * @param $sPeriod		тип периода
	 * @return array		array('from' => '...', 'to' => '...', 'format' => '...', 'interval' => '...')
	 */
	protected function GetStatsGraphPeriod($sPeriod = null) {
		switch($sPeriod) {
			/*
			 * вчера
			 */
			case self::TIME_INTERVAL_YESTERDAY:
				$iTime = mktime(date('H'), date('i'), date('s'), date('n'), date('j') - 1, date('Y'));
				return array(
					'from' => date('Y-m-d 00:00:00', $iTime),
					'to' => date('Y-m-d 23:59:59', $iTime),
					/*
					 * Для одноденных периодов нужны интервалы по пол часа, поэтому в формате указаны часы и минуты.
					 * Убираем ненужные данные (полный 'Y-m-d H:i:00') чтобы подписи влезли, все равно имеем дело с известным интервалом
					 */
					'format' => 'H:i',
					/*
					 * интервал для периода 30 мин
					 */
					'interval' => 60*30
				);
				break;
			/*
			 * сегодня
			 */
			case self::TIME_INTERVAL_TODAY:
				return array(
					'from' => date('Y-m-d 00:00:00'),
					'to' => date('Y-m-d 23:59:59'),
					/*
					 * Для одноденных периодов нужны интервалы по пол часа, поэтому в формате указаны часы и минуты.
					 * Убираем ненужные данные (полный 'Y-m-d H:i:00') чтобы подписи влезли, все равно имеем дело с известным интервалом
					 */
					'format' => 'H:i',
					/*
					 * интервал для периода 30 мин
					 */
					'interval' => 60*30
				);
				break;
			/*
			 * неделя
			 */
			case self::TIME_INTERVAL_WEEK:
				return array(
					/*
					 * полных 7 дней назад (не включая текущий)
					 */
					'from' => date('Y-m-d', mktime(date('H'), date('i'), date('s'), date('n'), date('j') - 7, date('Y'))),
					'to' => date('Y-m-d 23:59:59', mktime(date('H'), date('i'), date('s'), date('n'), date('j') - 1, date('Y'))),
					/*
					 * Для больших периодов интервал 1 день, поэтому часы и меньшие значения не указаны в формате.
					 * Убираем ненужные данные (полный 'Y-m-d') чтобы подписи влезли, все равно имеем дело с известным интервалом
					 */
					'format' => 'm-d',
					/*
					 * интервал для периода 1 день
					 */
					'interval' => 60*60*24
				);
				break;
			/*
			 * месяц
			 */
			case self::TIME_INTERVAL_MONTH:
				/*
				 * используется период по-умолчанию
				 */
				//break;
			/*
			 * период по-умолчанию
			 */
			default:
				return array(
					'from' => date('Y-m-d', mktime(date('H'), date('i'), date('s'), date('n') - 1, date('j'), date('Y'))),
					'to' => date('Y-m-d 23:59:59', mktime(date('H'), date('i'), date('s'), date('n'), date('j') - 1, date('Y'))),
					/*
					 * Для больших периодов интервал 1 день, поэтому часы и меньшие значения не указаны в формате.
					 * Убираем ненужные данные (полный 'Y-m-d') чтобы подписи влезли, все равно имеем дело с известным интервалом
					 */
					'format' => 'm-d',
					/*
					 * интервал для периода 1 день
					 */
					'interval' => 60*60*24
				);
				break;
		}
	}


	/**
	 * Заполнить пустыми значениями период дат с нужным для каждого периода интервалом
	 *
	 * @param $aPeriod			период дат (от и до) и другие данные
	 * @return array			массив с нулевыми значениями на каждый промежуток интервала в периоде дат
	 */
	protected function FillDatesRangeForPeriod($aPeriod) {
		/*
		 * интервал прохода по датам
		 */
		$iInterval = $aPeriod['interval'];
		/*
		 * дата начала и счетчик
		 */
		$iCurrentTime = strtotime($aPeriod['from']);
		/*
		 * дата финиша
		 */
		$iFinishTime = strtotime($aPeriod['to']);
		/*
		 * здесь хранятся даты и количество
		 */
		$aData = array();
		/*
		 * заполнить пустыми значениями интервалы в периоде
		 */
		do {
			/*
			 * добавить запись про текущую дату
			 */
			$aData[] = array(
				/*
				 * формат даты берется из периода, где был задан её формат связанный с интервалом
				 */
				'date' => date($aPeriod['format'], $iCurrentTime),
				'count' => 0
			);
			/*
			 * увеличить интервал
			 */
			$iCurrentTime += $iInterval;
		} while ($iCurrentTime <= $iFinishTime);
		return $aData;
	}


	/**
	 * Заполнить реальными данными из запроса период
	 *
	 * @param $aFilledWithZerosPeriods		"пустые" данные периода для каждой даты
	 * @param $aDataStats					полученные данные для дат
	 * @return array						объедененный массив данных
	 */
	protected function MixEmptyPeriodsWithData($aFilledWithZerosPeriods, $aDataStats) {
		if (!is_array($aFilledWithZerosPeriods) or !is_array($aDataStats)) return array();
		foreach($aFilledWithZerosPeriods as &$aFilledPeriod) {
			foreach($aDataStats as $aData) {
				/*
				 * если есть реальные данные для этой даты
				 */
				if ($aFilledPeriod['date'] == $aData['date']) {
					$aFilledPeriod['count'] = $aData['count'];
				}
			}
		}
		return $aFilledWithZerosPeriods;
	}


	/**
	 * Получить данные периода при ручном выборе дат
	 *
	 * @param $sDateStart		дата начала
	 * @param $sDateFinish		дата финиша
	 * @return array
	 */
	protected function SetupCustomPeriod($sDateStart, $sDateFinish) {
		$aPeriod = $this->GetStatsGraphPeriod();
		/*
		 * самое начало суток
		 */
		$aPeriod['from'] = date('Y-m-d 00:00:00', strtotime($sDateStart));
		/*
		 * верхний предел должен быть указан с точностью до секунды и максимальным по времени
		 * т.к. если регистрация в последнем дне выбранного периода была не в 0 часов, 0 минут и 0 секунд - то она не будет учтена,
		 * т.к. мускульный формат date будет сконвертирован в datetime с постфиксом 00:00:00
		 * поэтому нужно указать макс. время суток вручную
		 */
		$aPeriod['to'] = date('Y-m-d 23:59:59', strtotime($sDateFinish));
		return $aPeriod;
	}


	/**
	 * Отконвертировать описание форматирования даты из php в mysql
	 *
	 * @param $sFormat		строка форматирования как в php ("Y-m-d")
	 * @return mixed		строка для мускула ("%Y-%m-%d")
	 */
	public function BuildDateFormatFromPHPToMySQL($sFormat) {
		/*
		 * не использовать литерал \w т.к. он содержит цифры, а их экранировать не нужно!
		 */
		$sFormat = preg_replace('#([a-z])#iu', '%$1', $sFormat);
		/*
		 * хак: если формат даты содежит часы и минуты,
		 * то чтобы не округлять даты в бд к получасам (ведь действие может быть и в 55 минут) - ставим минуты в 30 как среднее
		 * и привязываем действие только к часу (т.е. каждое действие будет в Х часов 30 минут для однодневных интервалов)
		 */
		return str_replace('%i', '30', $sFormat);
	}


	/**
	 * Получить данные для графика
	 *
	 * @param null $sGraphType			тип графика
	 * @param null $sGraphPeriod		именованный период графика
	 * @param null $sDateStart			дата начала периода
	 * @param null $sDateFinish			дата окончания периода
	 */
	public function GatherAndBuildDataForGraph($sGraphType = null, $sGraphPeriod = null, $sDateStart = null, $sDateFinish = null) {
		/*
		 * тип периода для графика
		 */
		if (!in_array($sGraphPeriod, array(self::TIME_INTERVAL_YESTERDAY, self::TIME_INTERVAL_TODAY, self::TIME_INTERVAL_WEEK, self::TIME_INTERVAL_MONTH))) {
			$sGraphPeriod = self::TIME_INTERVAL_MONTH;
		}

		/*
		 * тип данных для графика
		 */
		if (!in_array($sGraphType, array(self::DATA_TYPE_REGISTRATIONS, self::DATA_TYPE_TOPICS, self::DATA_TYPE_COMMENTS, self::DATA_TYPE_VOTINGS))) {
			$sGraphType = self::DATA_TYPE_REGISTRATIONS;
		}


		/*
		 * если указан интервал дат и он был выбран
		 */
		if ($sDateStart and $sDateFinish) {
			// todo: validate
			/*
			 * проверить чтобы дата начала была меньше чем дата конца
			 */
			if ($sDateStart > $sDateFinish) {
				$this->Message_AddError($this->Lang('errors.stats.wrong_date_range'), $this->Lang_Get('error'));
			} else {
				/*
				 * построить данные о периоде
				 */
				$aPeriod = $this->SetupCustomPeriod($sDateStart, $sDateFinish);
				$sGraphPeriod = null;
			}
		}

		/*
		 *
		 * рассчет данных
		 *
		 */

		/*
		 * получить период дат от и до для названия интервала если не был выбран ручной интервал дат
		 */
		if (!isset($aPeriod)) {
			$aPeriod = $this->GetStatsGraphPeriod($sGraphPeriod);
		}
		/*
		 * получить пустой интервал дат для графика
		 */
		$aFilledWithZerosPeriods = $this->FillDatesRangeForPeriod($aPeriod);
		/*
		 * получить существующие данные о типе
		 */
		$aDataStats = $this->GetStatsDataForGraphCorrespondingOnType($sGraphType, $aPeriod);
		/*
		 * объеденить данные
		 */
		$aDataStats = $this->MixEmptyPeriodsWithData($aFilledWithZerosPeriods, $aDataStats);

		/*
		 * данные для графика
		 */
		$this->Viewer_Assign('aDataStats', $aDataStats);
		/*
		 * тип текущего периода
		 */
		$this->Viewer_Assign('sCurrentGraphPeriod', $sGraphPeriod);
		/*
		 * тип текущего графика
		 */
		$this->Viewer_Assign('sCurrentGraphType', $sGraphType);
	}


	/**
	 * Получить реальные существующие данные о типе на основе периода
	 *
	 * @param $sGraphType		тип данных для графика
	 * @param $aPeriod			данные периода
	 * @return mixed			данные
	 * @throws Exception
	 */
	protected function GetStatsDataForGraphCorrespondingOnType($sGraphType, $aPeriod) {
		switch ($sGraphType) {
			case self::DATA_TYPE_REGISTRATIONS:
				return $this->PluginAdmin_Users_GetUsersRegistrationStats($aPeriod);
			case self::DATA_TYPE_TOPICS:
				return $this->PluginAdmin_Topics_GetTopicsStats($aPeriod);
			case self::DATA_TYPE_COMMENTS:
				return $this->PluginAdmin_Comments_GetCommentsStats($aPeriod);
			case self::DATA_TYPE_VOTINGS:
				return $this->PluginAdmin_Votings_GetVotingsStats($aPeriod);
			default:
				$aData = array('sGraphType' => $sGraphType, 'aPeriod' => $aPeriod);
				$this->Hook_Run('admin_stats_get_data_corresponding_on_type', $aData);
				/*
				 * хук должен вернуть true в значении ключа result передаваемых параметров, иначе это неизвестный тип данных
				 */
				if (!isset($aData['result']) or !$aData['result']) {
					throw new Exception('admin: error: unknown graph type "' . $sGraphType . '" in ' . __METHOD__);
				}
		}
	}


	/**
	 * Получить прирост объектов за период (на сколько больше зарегистрировалось в текущем периоде чем в прошлом)
	 *
	 * @param $sType		тип объектов для получения прироста
	 * @param $sPeriod		тип периода
	 * @param $bGatherVotes	нужно ли собирать голоса
	 * @return array		array('count' => прирост объектов, 'votings' => данные голосований)
	 */
	public function GetGrowthAndVotingsByTypeAndPeriod($sType, $sPeriod, $bGatherVotes = true) {
		$aGrowthFilter = $this->GetGrowthFilterForType ($sType);
		$aPeriod = $this->GetGrowthQueryRuleForPeriod ($sPeriod);

		/*
		 * получить значение количества объектов в прошлом и текущем периоде
		 */
		$aDataGrowth = $this->oMapper->GetGrowthByFilterAndPeriod($aGrowthFilter, $aPeriod);

		return array(
			/*
			 * количество объектов в прошлом периоде
			 */
			'prev_items' => $aDataGrowth['prev_items'],
			/*
			 * объектов в текущем периоде
			 */
			'now_items' => $aDataGrowth['now_items'],
			/*
			 * прирост
			 */
			'growth' => $aDataGrowth['now_items'] - $aDataGrowth['prev_items'],
			/*
			 * данные голосований (количество и направление)
			 */
			'votings' => $bGatherVotes ? $this->GetVotingsForTypeAndPeriod($aGrowthFilter, $aPeriod) : null
		);
	}


	/**
	 * Получить статистику голосов за обьекты указаного типа в периоде
	 *
	 * @param $aGrowthFilter	фильтр
	 * @param $aPeriod			период
	 * @return array			массив (сколько плюсов, минусов и воздержалось)
	 */
	protected function GetVotingsForTypeAndPeriod($aGrowthFilter, $aPeriod) {
		$aResult = $this->oMapper->GetVotingsForTypeAndPeriod($aGrowthFilter, $aPeriod);
		/*
		 * заполнить значениями по-умолчанию
		 */
		$aVotingStats = array(
			'plus' => 0, 'minus' => 0, 'abstained' => 0
		);
		/*
		 * собрать данные в удобном виде
		 */
		foreach ($aResult as $aData) {
			$aVotingStats[$aData['vote_direction'] == '1' ? 'plus' : ($aData['vote_direction'] == '-1' ? 'minus' : 'abstained')] = $aData['count'];
		}
		$aVotingStats['total_votes'] = array_sum($aVotingStats);

		return $aVotingStats;
	}


	/**
	 * Получить фильтр для объекта
	 *
	 * @param $sType		тип объектов для получения фильтра
	 * @return array		фильтр
	 * @throws Exception	при неизвестном типе объекта
	 */
	protected function GetGrowthFilterForType($sType) {
		switch ($sType) {
			case self::DATA_TYPE_TOPICS:
				return array(
					'table' => Config::Get('db.table.topic'),
					'conditions' => array(
						'topic_publish' => 1,
					),
					'period_row_name' => 'topic_date_add',
					/*
					 * для отображения линейки голосов за объект
					 */
					'target_type' => 'topic',
					'join_table_primary_key' => 'topic_id',
				);
			case self::DATA_TYPE_COMMENTS:
				return array(
					'table' => Config::Get('db.table.comment'),
					'conditions' => array(
						'comment_publish' => 1,
					),
					'period_row_name' => 'comment_date',
					/*
					 * для отображения линейки голосов за объект
					 */
					'target_type' => 'comment',
					'join_table_primary_key' => 'comment_id',
				);
			case self::DATA_TYPE_BLOGS:
				return array(
					'table' => Config::Get('db.table.blog'),
					'conditions' => array(
						'blog_type' => array('open', 'close', 'invite'),
					),
					'period_row_name' => 'blog_date_add',
					/*
					 * для отображения линейки голосов за объект
					 */
					'target_type' => 'blog',
					'join_table_primary_key' => 'blog_id',
				);
			case self::DATA_TYPE_REGISTRATIONS:
				return array(
					'table' => Config::Get('db.table.user'),
					'conditions' => array(
						'user_activate' => 1,
					),
					'period_row_name' => 'user_date_register',
					/*
					 * для отображения линейки голосов за объект
					 */
					'target_type' => 'user',
					'join_table_primary_key' => 'user_id',
				);
			default:
				throw new Exception('admin: error: unknow type in ' . __METHOD__ . ': ' . $sType);
		}
	}


	/**
	 * Получить части sql-запросов периода для выбора данных из БД по имени периода
	 *
	 * @param $sPeriod			именованое название периода
	 * @return array			массив с датой "от" и "до"
	 */
	protected function GetGrowthQueryRuleForPeriod($sPeriod) {
		switch ($sPeriod) {
			/*
			 * вчера
			 */
			case self::TIME_INTERVAL_YESTERDAY:
				return array(
					'now_period' => 'BETWEEN CURRENT_DATE - INTERVAL 1 DAY AND CURRENT_DATE',
					'prev_period' => 'BETWEEN CURRENT_DATE - INTERVAL 2 DAY AND CURRENT_DATE - INTERVAL 1 DAY'
				);
			/*
			 * неделя
			 */
			case self::TIME_INTERVAL_WEEK:
				return array(
					'now_period' => 'BETWEEN CURRENT_DATE - INTERVAL 1 WEEK AND CURRENT_DATE',
					'prev_period' => 'BETWEEN CURRENT_DATE - INTERVAL 2 WEEK AND CURRENT_DATE - INTERVAL 1 WEEK'
				);
			/*
			 * месяц
			 */
			case self::TIME_INTERVAL_MONTH:
				return array(
					'now_period' => 'BETWEEN CURRENT_DATE - INTERVAL 1 MONTH AND CURRENT_DATE',
					'prev_period' => 'BETWEEN CURRENT_DATE - INTERVAL 2 MONTH AND CURRENT_DATE - INTERVAL 1 MONTH'
				);
			/*
			 * сегодня
			 */
			case self::TIME_INTERVAL_TODAY:
				/*
				 * использовать период по-умолчанию
				 */
			/*
			 * период по-умолчанию (1 день - сегодня)
			 */
			default:
				return array(
					'now_period' => 'BETWEEN CURRENT_DATE AND CURRENT_DATE + INTERVAL 1 DAY',
					'prev_period' => 'BETWEEN CURRENT_DATE - INTERVAL 1 DAY AND CURRENT_DATE'
				);
		}
	}


}

?>