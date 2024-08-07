<?php declare(strict_types=1);

namespace Zonos\ZonosSdk\Responses;

use Saloon\Http\Response;

abstract class ZonosResponse extends Response
{
    abstract public function resolve(); // Figure out typing
}