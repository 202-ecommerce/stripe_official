</div>
<div id="StripeAdminCart" class="panel">
    <h3>{l s='Stripe Informations' mod='stripe_official'}</h3>
    <table class="table table-transaction">
        <thead>
            <tr>
                <th>{l s='Date (last update)' mod='stripe_official'}</th>
                <th>{l s='Stripe Payment ID' mod='stripe_official'}</th>
                <th>{l s='Name' mod='stripe_official'}</th>
                <th>{l s='Payment method' mod='stripe_official'}</th>
                <th>{l s='Amount Paid' mod='stripe_official'}</th>
                <th>{l s='Refund' mod='stripe_official'}</th>
                <th>{l s='Result' mod='stripe_official'}</th>
                <th>{l s='Mode' mod='stripe_official'}</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>{$paymentInformations->date_add}</td>
                <td><a href="{$paymentInformations->url_dashboard.paymentIntent|escape:'htmlall'}" target="blank">{$paymentInformations->id_payment_intent}</a></td>
                <td>{$paymentInformations->name}</td>
                <td><img src="{$module_dir}/views/img/cc-{$paymentInformations->type}.png" alt="payment method" style="width:43px;"/></td>
                <td>{$paymentInformations->amount} {$paymentInformations->currency}</td>
                <td>{$paymentInformations->refund} {$paymentInformations->currency}</td>
                {if $paymentInformations->result == 2}
                    <td>{l s='Refund' mod='stripe_official'}</td>
                {elseif $paymentInformations->result == 3}
                    <td>{l s='Partial Refund' mod='stripe_official'}</td>
                {elseif $paymentInformations->result == 4}
                    <td>{l s='Waiting' mod='stripe_official'}</td>
                {else}
                    <td><img src="{$module_dir}/views/img/{$paymentInformations->result}ok.gif" alt="result" /></td>
                {/if}
                <td class="uppercase">{$paymentInformations->state}</td>
            </tr>
        </tbody>
    </table>
</div>
<div>