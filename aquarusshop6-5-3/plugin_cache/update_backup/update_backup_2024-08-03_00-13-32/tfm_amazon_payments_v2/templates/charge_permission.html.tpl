<!-- TOOL -->
<!-- :START: plugins/tfm_amazon_payments_v2/templates/charge_permission.html.tpl -->
<div>
    <h1>ChargePermission</h1>
    <form action="{$link}" method="get" name="">
        <input name="charge_permission_id">
        <button type="submit">start</button>
    </form>
    <pre>{$charge_permission|@print_r:true}</pre>
</div>
<!-- :ENDE: plugins/tfm_amazon_payments_v2/templates/charge_permission.html.tpl -->