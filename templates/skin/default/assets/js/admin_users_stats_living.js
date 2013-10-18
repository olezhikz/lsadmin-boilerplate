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
	Статистика проживаний пользователей
	Обработка селекта стран или городов
	Аякс смена параметров
 */

var ls = ls || {};

ls.admin_users_stats_living = (function($) {
	
	this.selectors = {
		/*
		 	просмотр короткой статистики по проживанию пользователей на странице статистики пользователей (селект)
		 */
		users_stats_living_stats_short_view_select: '#admin_users_stats_living_stats_short_view_select',
		users_stats_living_stats_short_view_count: '#admin_users_stats_living_stats_short_view_count',
		users_stats_living_stats_short_view_percentage: '#admin_users_stats_living_stats_short_view_percentage',

		/*
			для удобства (последняя запятая отсутствует)
		 */
		last_element: 'without_comma'
	};

	// ---

	/**
	 * Показать количество и процентное соотнешение для выбранного элемента в селекте
	 *
	 * @param iCount				количество
	 * @param iTotalUsersCount		всего пользователей
	 * @constructor
	 */
	this.ShowShortViewLivingSelectData = function(iCount, iTotalUsersCount) {
		$ (this.selectors.users_stats_living_stats_short_view_count).text(iCount);
		$ (this.selectors.users_stats_living_stats_short_view_percentage).text((iCount*100/iTotalUsersCount).toFixed(2) + ' %');
	};

	// ---

	/**
	 * Установить значение для выбранного элемента селекта по-умолчанию
	 *
	 * @param iTotalUsersCount		всего пользователей
	 * @constructor
	 */
	this.InitSelectDefaultValue = function() {
		if ($ (this.selectors.users_stats_living_stats_short_view_select).length == 1) {
			this.ShowShortViewLivingSelectData($ (this.selectors.users_stats_living_stats_short_view_select).val(), iTotalUsersCount);
		}
	};

	// ---

	return this;
	
}).call(ls.admin_users_stats_living || {}, jQuery);

// ---

jQuery(document).ready(function($) {

	/*
	 	смена элемента в селекте проживания на странице статистики пользователей
	 */
	$ (document).on ('change.admin', ls.admin_users_stats_living.selectors.users_stats_living_stats_short_view_select, function() {
		ls.admin_users_stats_living.ShowShortViewLivingSelectData($ (this).val(), iTotalUsersCount);
	});
	/*
	 	инит текущим значением селекта проживания для отображения короткого вида
	 */
	ls.admin_users_stats_living.InitSelectDefaultValue();





	/*
	 	todo:
	 	аякс обработка нажатия на кнопки статистики пользователей по странам и городам
	 */
	$ (document).on ('click.admin', '#admin_users_stats_living .js-ajax-load', function() {
		$ ('#admin_users_stats_living').addClass('loading');

		ls.ajax.load(
			$ (this).attr('href'),
			{
				get_short_answer: true,
				request_type: 'living_stats'
			},
			function(data) {
				/*
				 	если нет ошибки и есть данные
				 */
				if (!data.bStateError) {
					/*
					 	вывести данные в блок
					 */
					$ ('#admin_users_stats_living').html(data.result);
					ls.admin_users_stats_living.InitSelectDefaultValue();
				}

				$ ('#admin_users_stats_living').removeClass('loading');
			}.bind(this),
			{
				type: 'POST',
				dataType: 'json'
			}
		);
		return false;
	});

});
