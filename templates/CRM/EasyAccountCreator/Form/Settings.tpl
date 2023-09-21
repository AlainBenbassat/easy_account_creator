
{foreach from=$elementNames item=elementName}
  <div class="crm-section">
    <div class="label">{$form.$elementName.label}</div>
    <div class="content">{$form.$elementName.html}</div>
    <div class="clear"></div>
  </div>
{/foreach}

<div class="crm-section">
  <div class="label">{$msg_template_label}</div>
  <div class="content"><a href="messageTemplates/add?action=update&id={$msg_template_id}&reset=1">{$msg_template_subject}</a></div>
  <div class="clear"></div>
</div>

  {* FOOTER *}
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
