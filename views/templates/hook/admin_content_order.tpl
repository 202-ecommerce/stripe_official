<div class="tab-pane" id="StripePayment">
    <p>
        <span><strong>{l s='Charge' mod='stripe_official'}</strong></span><br/>
        <span>{$stripe_charge}</span>
    </p>
    <p>

        <span><strong>{l s='Payment Intent' mod='stripe_official'}</strong></span><br/>
        <span><a href="{$stripe_dashboardUrl|escape:'url'}" target="blank">{$stripe_paymentIntent}</a></span>
    </p>
    <p>

        <span><strong>{l s='Payment date' mod='stripe_official'}</strong></span><br/>
        <span>{$stripe_date}</span>
    </p>
</div>