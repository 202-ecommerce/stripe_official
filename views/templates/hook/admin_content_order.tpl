<div class="tab-pane" id="StripePayment">
    <p>
        <span><strong>{l s='Charge' mod='stripe_official'}</strong></span><br/>
        <span><a href="{$stripe_dashboardUrl.charge|escape:'htmlall'}" target="blank">{$stripe_charge}</a></span>
    </p>
    <p>
        <span><strong>{l s='Payment Intent' mod='stripe_official'}</strong></span><br/>
        <span><a href="{$stripe_dashboardUrl.paymentIntent|escape:'htmlall'}" target="blank">{$stripe_paymentIntent}</a></span>
    </p>
    <p>
        <span><strong>{l s='Payment date' mod='stripe_official'}</strong></span><br/>
        <span>{$stripe_date}</span>
    </p>
    <p>
        <span><strong>{l s='Payment Type' mod='stripe_official'}</strong></span><br/>
        <span><img src="{$module_dir}/views/img/cc-{$stripe_paymentType}.png" alt="payment method" style="width:43px;"/></span>
    </p>
</div>