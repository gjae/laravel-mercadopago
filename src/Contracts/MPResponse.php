<?php

namespace Gjae\MercadoPago\Contracts;

interface MPResponse {

    public function getCollectionId();
    public function getCollectionStatus();
    public function getExternalReference();
    public function getPaymentType();
    public function getMerchantOrderId();
    public function getPreferenceId();
    public function getSiteId();
    public function getProcessingMode();
    public function getMerchantAccountId();

}