
		{if $oParameter->getNeedToShowSpecialIntegerForm()}
		
			{assign var="aValidatorData" value=$oParameter->getValidator()}
			{assign var="aValidatorParams" value=$aValidatorData['params']}
			
			{* Границы от и до разрешенных значений целого числа *}
			{assign var="aItemsToShow" value=range($aValidatorParams['min'],$aValidatorParams['max'])}
			
			<select name="{$sInputDataName}" class="input-text input-width-250">
				{foreach from=$aItemsToShow item=sValue}
					<option value="{$sValue}" {if $sValue==$oParameter->getValue()}selected="selected"{/if}>{$sValue}{if $sValue==$oParameter->getValue()} ({$aLang.plugin.admin.current}){/if}</option>
				{/foreach}
			</select>
			
		{else}
		
			{* Простой вывод значения числа как есть *}
			
			<input type="text" name="{$sInputDataName}" value="{$oParameter->getValue()|escape:'html'}" class="input-text input-width-250" />
			
		{/if}