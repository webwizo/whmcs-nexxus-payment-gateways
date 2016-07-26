<?php
/*
Gateway Module Name: Nexxus Payment Group
Description: Nexxus payment gateway module for WHMCS
Version: 1.0
Author: Asif Iqbal
Author URI: https://www.webwizo.com
*/

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related capabilities and
 * settings.
 *
 * @see http://docs.whmcs.com/Gateway_Module_Meta_Data_Parameters
 *
 * @return array
 */
function nexxuspay_MetaData()
{
    return array(
        'DisplayName' => 'Nexxus Payment Gateway Module',
        'APIVersion' => '1.0',
        'DisableLocalCredtCardInput' => true,
        'TokenisedStorage' => false,
    );
}

/**
 * Define gateway configuration options.
 *
 * The fields you define here determine the configuration options that are
 * presented to administrator users when activating and configuring your
 * payment gateway module for use.
 *
 * Supported field types include:
 * * text
 * * password
 * * yesno
 * * dropdown
 * * radio
 * * textarea
 *
 * Examples of each field type and their possible configuration parameters are
 * provided in the sample function below.
 *
 * @return array
 */
function nexxuspay_config()
{
    return array(
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'NexxUS Pay',
        ),
        'gatewayId' => array(
            'FriendlyName' => 'Gateway ID',
            'Type' => 'text',
            'Size' => '10',
            'Default' => '',
            'Description' => 'Enter your gateway ID here',
        ),
        'secretKey' => array(
            'FriendlyName' => 'Secret Key',
            'Type' => 'text',
            'Size' => '20',
            'Default' => '',
            'Description' => 'Enter secret key here',
        )
    );
}

/**
 * Payment link.
 *
 * Required by third party payment gateway modules only.
 *
 * Defines the HTML output displayed on an invoice. Typically consists of an
 * HTML form that will take the user to the payment gateway endpoint.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see http://docs.whmcs.com/Payment_Gateway_Module_Parameters
 *
 * @return string
 */
function nexxuspay_link($params)
{
    // Gateway Configuration Parameters
    $gatewayId = $params['gatewayId'];
    $secretKey = $params['secretKey'];

    // Invoice Parameters
    $invoiceId = $params['invoiceid'];
    $description = $params["description"];
    $amount = $params['amount'];
    $currencyCode = $params['currency'];

    // Client Parameters
    $firstname = $params['clientdetails']['firstname'];
    $lastname = $params['clientdetails']['lastname'];
    $fullname = $params['clientdetail']['fullname'];
    $email = $params['clientdetails']['email'];
    $address1 = $params['clientdetails']['address1'];
    $address2 = $params['clientdetails']['address2'];
    $city = $params['clientdetails']['city'];
    $state = $params['clientdetails']['state'];
    $postcode = $params['clientdetails']['postcode'];
    $country = $params['clientdetails']['country'];
    $phone = $params['clientdetails']['phonenumber'];

    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $returnUrl = $params['returnurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];

    $url = 'https://wsclient.nexxuspay.com/api/gateway/v1.0';

    $postfields = array();
    $postfields['gatewayId'] = $gatewayId;
    $postfields['secretKey'] = $secretKey;
    $postfields['invoice_id'] = $invoiceId;
    $postfields['description'] = $description;
    $postfields['amount'] = $amount;
    $postfields['currency'] = $currencyCode;
    $postfields['name'] = $fullname;
    $postfields['email'] = $email;
    $postfields['address'] = $address1;
    $postfields['city'] = $city;
    $postfields['state'] = $state;
    $postfields['country'] = $country;
    $postfields['phone'] = $phone;
    $postfields['action'] = 'capture';
    $postfields['mode'] = 'LIVE';
    $postfields['callback_url'] = $systemUrl . '/modules/gateways/callback/' . $moduleName . '.php';
    $postfields['return_url'] = $returnUrl;

    $htmlOutput = '<form method="post" action="' . $url . '">';
    foreach ($postfields as $k => $v) {
        $htmlOutput .= '<input type="hidden" name="' . $k . '" value="' . $v . '" />';
    }
    $htmlOutput .= '<input type="submit" value="' . $langPayNow . '" />';
    $htmlOutput .= '</form>';

    return $htmlOutput;
}
