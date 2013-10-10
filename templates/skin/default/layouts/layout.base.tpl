<!doctype html>

{block name='layout_options'}{/block}

<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="ru"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="ru"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="ru"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="ru"> <!--<![endif]-->

<head>
	{* {hook run='html_head_begin'} *}
	{block name='layout_head_begin'}{/block}

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<meta name="description" content="{block name='layout_description'}{$sHtmlDescription}{/block}">
	<meta name="keywords" content="{block name='layout_keywords'}{$sHtmlKeywords}{/block}">

	<title>{block name='layout_title'}{$sHtmlTitle}{/block}</title>

	{**
	 * Стили
	 * CSS файлы подключаются в конфиге шаблона (ваш_шаблон/settings/config.php)
	 *}
	{$aHtmlHeadFiles.css}

	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
	<link href="{cfg name='path.static.skin'}/images/favicon.ico?v1" rel="shortcut icon" />
	<link rel="search" type="application/opensearchdescription+xml" href="{router page="search/opensearch"}" title="{cfg name='view.name'}" />

	{**
	 * RSS
	 *}
	{if $aHtmlRssAlternate}
		<link rel="alternate" type="application/rss+xml" href="{$aHtmlRssAlternate.url}" title="{$aHtmlRssAlternate.title}">
	{/if}

	{if $sHtmlCanonical}
		<link rel="canonical" href="{$sHtmlCanonical}" />
	{/if}


	<script>
        var		PATH_ROOT 					= '{cfg name="path.root.web"}',
                PATH_SKIN		 			= '{cfg name="path.skin.web"}',
                PATH_FRAMEWORK_FRONTEND		= '{cfg name="path.framework.frontend.web"}',
                PATH_FRAMEWORK_LIBS_VENDOR	= '{cfg name="path.framework.libs_vendor.web"}',
                /**
                 * Для совместимости с прошлыми версиями. БУДУТ УДАЛЕНЫ
                 */
                DIR_WEB_ROOT 				= '{cfg name="path.root.web"}',
                DIR_STATIC_SKIN 			= '{cfg name="path.skin.web"}',
                DIR_STATIC_FRAMEWORK 		= '{cfg name="path.framework.frontend.web"}',
                DIR_ENGINE_LIBS	 			= '{cfg name="path.framework.web"}/libs',

                LIVESTREET_SECURITY_KEY = '{$LIVESTREET_SECURITY_KEY}',
                SESSION_ID				= '{$_sPhpSessionId}',
                SESSION_NAME			= '{$_sPhpSessionName}',
                LANGUAGE				= '{$oConfig->GetValue('lang.current')}',
                WYSIWYG					= {if $oConfig->GetValue('view.wysiwyg')}true{else}false{/if};

		var aRouter = [];
		{foreach from=$aRouter key=sPage item=sPath}
			aRouter['{$sPage}'] = '{$sPath}';
		{/foreach}
	</script>

	{**
	 * JavaScript файлы
	 * JS файлы подключаются в конфиге шаблона (ваш_шаблон/settings/config.php)
	 *}
	{$aHtmlHeadFiles.js}

	<script>
		ls.lang.load({json var = $aLangJs});
		ls.lang.load({lang_load name="blog"});

		ls.registry.set('comment_max_tree', {json var=$oConfig->Get('module.comment.max_tree')});
		ls.registry.set('block_stream_show_tip', {json var=$oConfig->Get('block.stream.show_tip')});
	</script>
	

	{block name='layout_head_end'}{/block}
	{* {hook run='html_head_end'} *}
	
	<script>
		ls.registry.set('admin_save_form_ajax_use', {json var=$oConfig->Get('plugin.admin.admin_save_form_ajax_use')});
	</script>
	
</head>


<body class="{$sBodyClasses} {* layout-{cfg name='view.grid.type'} *} {block name='layout_body_classes'}{/block} ls-admin">
	{* {hook run='body_begin'} *}

	{block name='layout_body'}
		<div id="container" class="{* {hook run='container_class'} *} {if $bNoSidebar}no-sidebar{/if}">
			{**
			 * Шапка сайта
			 *}
			<header id="header" role="banner">
				<div class="site-info">
					<h1 class="site-name"><a href="{cfg name='path.root.web'}/admin">{cfg name="view.name"}</a></h1>
				</div>
			</header>


			{* Вспомогательный контейнер-обертка *}
			<div id="wrapper" class="{* {hook run='wrapper_class'} *} clearfix">
				{* Контент *}
				<div id="content-wrapper">
					<div id="content" 
						 role="main"
						 {if $sMenuItemSelect == 'profile'}itemscope itemtype="http://data-vocabulary.org/Person"{/if}>

						{block name='layout_content_toolbar' hide}
							<div class="admin-toolbar clearfix">
								{$smarty.block.child}
							</div>
						{/block}
						
						<div class="content-padding">
							{* {hook run='content_begin'} *}
							{hook run='admin_content_begin'}        {* todo: review: hook names (temp) *}
							
							{block name='layout_content_begin'}{/block}

							{block name='layout_page_title' hide}
								<h2 class="page-header">{$smarty.block.child}</h2>
							{/block}

							{* Навигация *}
							{if $sNav or $sNavContent}
								<div class="nav-group">
									{if $sNav}
										{if in_array($sNav, $aMenuContainers)}
											{$aMenuFetch.$sNav}
										{else}
											{include file="navs/nav.$sNav.tpl"}
										{/if}
									{else}
										{include file="navs/nav.$sNavContent.content.tpl"}
									{/if}
								</div>
							{/if}

							{* Системные сообщения *}
							{if ! $bNoSystemMessages}
								{if $aMsgError}
									{include file='alert.tpl' sAlertStyle='error' mAlerts=$aMsgError}
								{/if}

								{if $aMsgNotice}
									{include file='alert.tpl' mAlerts=$aMsgNotice}
								{/if}
							{/if}

							{block name='layout_content'}{/block}

							{block name='layout_content_end'}{/block}
							{* {hook run='content_end'} *}
						</div>
					</div>


					{* Подвал *}
					<footer id="footer">
						{block name='layout_footer_begin'}{/block}

						<ul>
							<li>&copy; 2008-2013 LiveStreet CMS</li>
						</ul>

						<ul>
							<li><a href="#" class="link-border"><span>Каталог расширений</a></span></li>
							<li><a href="#" class="link-border"><span>Сообщество</a></span></li>
							<li><a href="#" class="link-border"><span>Работа</a></span></li>
						</ul>

						<ul class="fl-r ta-r">
							<li><a href="{cfg name='path.root.web'}" class="link-border"><span>Перейти на сайт</a></span></li>
						</ul>

						{block name='layout_footer_end'}{/block}
					</footer>
				</div>


				{* Сайдбар *}
				{if ! $bNoSidebar}
					<aside id="sidebar" role="complementary">
						{include file='blocks.tpl' group='right'}
					</aside>
				{/if}
			</div> {* /wrapper *}
		</div> {* /container *}
	{/block}


	{* {hook run='body_end'} *}
</body>
</html>