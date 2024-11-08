<?php declare(strict_types=1);
require '../vendor/autoload.php';

use Zonos\ZonosSdk\Connectors\Enums\ZonosConnectorType;
use Zonos\ZonosSdk\Connectors\ZonosConnectorFactory;
use Zonos\ZonosSdk\Requests\Inputs\CheckoutSettingUpdateInput;

$sdk = ZonosConnectorFactory::createConnector( ZonosConnectorType::Wordpress, '', 'https://internal-graph.dgs.dev.zdops.net');

$thing = CheckoutSettingUpdateInput::fromArray(['successRedirectUrl' => 'https://www.zonos.com/']);

$res = $sdk->checkoutSettingsUpdate($thing)->get();
//$res = $sdk->checkoutSettingsUpdate($thing)->get('allowedCharacterSets', 'allowedDomains');
//$res = $sdk->checkoutSettings()->get();
//$res = $sdk->checkoutSettings()->get('allowedCharacterSets', 'allowedDomains');

$allowedDomains = implode(", ", $res->allowedDomains);

echo "
    allowedCharacterSets: {$res->allowedCharacterSets}<br/>
    allowedDomains: {$allowedDomains}<br/>
    createdAt: {$res->createdAt}<br/>
    createdBy: {$res->createdBy}<br/>
    id: {$res->id}<br/>
    mode: {$res->mode}<br/>
    organization: {$res->organization}<br/>
    placeOrderButtonSelector: {$res->placeOrderButtonSelector}<br/>
    status: {$res->status}<br/>
    subscriptionStatus: {$res->subscriptionStatus}<br/>
    successBehavior: {$res->successBehavior}<br/>
    successRedirectUrl: {$res->successRedirectUrl}<br/>
    updatedAt: {$res->updatedAt}<br/>
    updatedBy: {$res->updatedBy}<br/> a
    visibilityStatus: {$res->visibilityStatus}
";
