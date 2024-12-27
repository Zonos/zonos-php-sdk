<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Pending\Checkout;

use Zonos\ZonosSdk\Connectors\Checkout\ZonosConnector;
use Zonos\ZonosSdk\Data\Checkout\CheckoutSettings;
use Zonos\ZonosSdk\Requests\ZonosRequest;
use Zonos\ZonosSdk\Responses\Queries\Checkout\CheckoutSettingsMutationResponse;
use Zonos\ZonosSdk\Utils\GqlBuilder;

class CheckoutSettingsUpdateRequest extends PendingZonosRequest
{
    protected const DEFAULT_ATTRIBUTES = [
        'allowedCharacterSets',
        'allowedDomains',
        'createdAt',
        'createdBy',
        'id',
        'mode',
        'organization',
        'placeOrderButtonSelector',
        'status',
        'subscriptionStatus',
        'successBehavior',
        'successRedirectUrl',
        'updatedAt',
        'updatedBy',
        'visibilityStatus',
        'externalServiceTokens.token',
        'externalServiceTokens.type'
    ];

    public function __construct(ZonosConnector $connector, public array $args = [])
    {
        parent::__construct($connector, GqlBuilder::make('mutation', 'checkoutSettingsUpdate', $args));
    }

    public function get(string ...$fields): CheckoutSettings
    {
        $resolved = $this->response(...$fields)->resolve();

        return $resolved;
    }

    public function response(string ...$fields): CheckoutSettingsMutationResponse
    {
        $query = $this->query->withFields($this->normalizeFields($fields));

        $response = $this->connector->send(new ZonosRequest(CheckoutSettingsMutationResponse::class, (string) $query))->throw();
        assert($response instanceof CheckoutSettingsMutationResponse);

        return $response;

    }
}