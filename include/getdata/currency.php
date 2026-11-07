<?php

trait GetdataCurrencyTrait
{
    //currency
    public function formatPrice($price)
    {
        $currencySymbol = explode('-', CURRENCY)[1]; // currency format is "CODE-SYMBOL"
        $formattedPrice = number_format($price, 2, DECIMAL_SEPERATOR, THOUSAND_SEPERATOR);
        $formattedPrice = $currencySymbol . $formattedPrice;

        return $formattedPrice;
    }
}

