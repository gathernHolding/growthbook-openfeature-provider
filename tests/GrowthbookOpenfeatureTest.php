<?php

use Gathern\GrowthbookOpenfeatureProvider\GrowthbookOpenfeatureProvider;
use Growthbook\Growthbook;
use OpenFeature\OpenFeatureAPI;

it('provider works successfully with openfeature', function () {

    $api = OpenFeatureAPI::getInstance();
    // configure a provider
    $api->setProvider(new GrowthbookOpenfeatureProvider(
        growthbook: Growthbook::create(),
        clientKey: getenv("GROWTHBOOK_CLIENT_KEY"),
        apiHost: getenv("GROWTHBOOK_API_HOST"),
    ));

    // create a `client`
    $client = $api->getClient(GrowthbookOpenfeatureProvider::class, "v1.17");

    expect($client->getBooleanValue(flagKey: 'test-boolean', defaultValue: false))->toBeFalse();

});
