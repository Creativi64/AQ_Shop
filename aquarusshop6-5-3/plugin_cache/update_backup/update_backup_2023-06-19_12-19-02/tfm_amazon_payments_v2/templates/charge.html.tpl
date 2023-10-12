<!-- TOOL -->
<!-- :START: plugins/tfm_amazon_payments_v2/templates/charge.html.tpl -->
<div>
    <h1>Charge</h1>
    <form action="{$link}" method="get" name="">
        <input name="charge_id">
        <button type="submit">start</button>
    </form>
    <pre>{$charge|@print_r:true}</pre>
</div>
<!-- :ENDE: plugins/tfm_amazon_payments_v2/templates/charge.html.tpl -->