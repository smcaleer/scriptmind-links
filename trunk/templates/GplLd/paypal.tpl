<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <title>{l}Processing Payment...{/l}</title>
</head>
{strip}
<body onload="document.form.submit();">
   <h3>{l}Processing Payment...{/l}</h3>
   <form name="form" action="https://www.paypal.com/cgi-bin/webscr" method="post">
      <input type="hidden" name="cmd" value="_xclick" />
      <input type="hidden" name="business" value="{$smarty.const.PAYPAL_ACCOUNT}" />
      <input type="hidden" name="item_name" value="Link to {$URL|trim} from http://{$smarty.server.SERVER_NAME}{$smarty.const.DOC_ROOT}/" />
      <input type="hidden" name="item_number" value="{$ID}" />
      <input type="hidden" name="amount" value="{$PAYMENT.AMOUNT}" />
      <input type="hidden" name="quantity" value="{$PAYMENT.QUANTITY}" />
      <input type="hidden" name="no_shipping" value="1" />
      <input type="hidden" name="return" value="http://{$smarty.server.SERVER_NAME}{$smarty.const.DOC_ROOT}/payment.php?payed={$ID}" />
      <input type="hidden" name="cancel_return" value="http://{$smarty.server.SERVER_NAME}{$smarty.const.DOC_ROOT}/payment.php?canceled={$ID}" />
      <input type="hidden" name="notify_url" value="http://{$smarty.server.SERVER_NAME}{$smarty.const.DOC_ROOT}/ipn.php?pid={$PAYMENT.ID}" />
      <input type="hidden" name="custom" value="{$PAYMENT.ID}" />
      <input type="hidden" name="no_note" value="1" />
      <input type="hidden" name="email" value="{$OWNER_EMAIL|escape|trim}" />
   </form>
</body>
</html>
{/strip}