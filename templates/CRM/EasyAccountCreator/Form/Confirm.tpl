<div class="messages status no-popup">
  <p>{$intro}</p>
</div>

{foreach from=$elementNames item=elementName}
  <div class="crm-section">
    {if $form.$elementName.id eq 'send_mail'}
      <div class="label">{$form.$elementName.label} {help id='send_mail'}</div>
    {else}
      <div class="label">{$form.$elementName.label}</div>
    {/if}

    <div class="content">{$form.$elementName.html}</div>
    <div class="clear"></div>
  </div>
{/foreach}

<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
