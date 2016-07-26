<?php
/*
Gateway Module Name: Nexxus Payment Group Callback
Description: Nexxus payment gateway module for WHMCS
Version: 1.0
Author: Asif Iqbal
Author URI: https://www.webwizo.com
*/

// Require libraries needed for gateway module functions.
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';

// Detect module name from filename.
$gatewayModuleName = basename(__FILE__, '.php');

// Fetch gateway configuration parameters.
$gatewayParams = getGatewayVariables($gatewayModuleName);

// Die if module is not active.
if (!$gatewayParams['type']) {
    die("Module Not Activated");
}

//www.onevpn.com/thank-you/?status=success&referenceId=61&transactionId=5694713436616224001132&datetime=2016-07-25+22%3A23%3A49.0&amount=48.000&reason=card+rejected&redirectUrl=http%3A%2F%2Fwww.onevpn.com%2Fbilling%2Fmodules%2Fgateways%2Fcallback%2Fnexxuspay.php

// Retrieve data returned in payment gateway callback
// Varies per payment gateway
$status = $_GET["status"];
$invoiceId = $_GET["invoice_id"];
$transactionId = $_GET["transactionId"];
$paymentAmount = $_GET["amount"];
$paymentFee = $_POST["x_fee"];


$transactionStatus = ucwords($status);

/**
 * Validate Callback Invoice ID.
 *
 * Checks invoice ID is a valid invoice number. Note it will count an
 * invoice in any status as valid.
 *
 * Performs a die upon encountering an invalid Invoice ID.
 *
 * Returns a normalised invoice ID.
 */
$invoiceId = checkCbInvoiceID($invoiceId, $gatewayParams['name']);

/**
 * Check Callback Transaction ID.
 *
 * Performs a check for any existing transactions with the same given
 * transaction number.
 *
 * Performs a die upon encountering a duplicate.
 */
checkCbTransID($transactionId);

/**
 * Log Transaction.
 *
 * Add an entry to the Gateway Log for debugging purposes.
 *
 * The debug data can be a string or an array. In the case of an
 * array it will be
 *
 * @param string $gatewayName        Display label
 * @param string|array $debugData    Data to log
 * @param string $transactionStatus  Status
 */
logTransaction($gatewayParams['name'], $_GET, $transactionStatus);

if ($status == 'success') {

    /**
     * Add Invoice Payment.
     *
     * Applies a payment transaction entry to the given invoice ID.
     *
     * @param int $invoiceId         Invoice ID
     * @param string $transactionId  Transaction ID
     * @param float $paymentAmount   Amount paid (defaults to full balance)
     * @param float $paymentFee      Payment fee (optional)
     * @param string $gatewayModule  Gateway module name
     */
    addInvoicePayment(
        $invoiceId,
        $transactionId,
        $paymentAmount,
        $paymentFee,
        $gatewayModuleName
    );

}
