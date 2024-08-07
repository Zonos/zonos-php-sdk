<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Requests\Pending;

use Zonos\ZonosSdk\Connectors\ZonosConnector;
use Zonos\ZonosSdk\Data\CheckoutSettings;
use Zonos\ZonosSdk\Requests\PendingZonosRequest;
use Zonos\ZonosSdk\Requests\ZonosRequest;
use Zonos\ZonosSdk\Responses\Queries\CheckoutSettingsMutationResponse;
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