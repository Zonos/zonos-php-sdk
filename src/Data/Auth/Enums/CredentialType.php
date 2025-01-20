<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Data\Auth\Enums;

enum CredentialType: string
{
  case PUBLIC_TOKEN = 'PUBLIC_TOKEN';
  case API_TOKEN = 'API_TOKEN';
}
