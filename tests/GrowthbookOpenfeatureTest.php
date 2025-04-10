<?php

use Gathern\GrowthbookOpenfeatureProvider\GrowthbookOpenfeatureProvider;
use Growthbook\Growthbook;
use OpenFeature\OpenFeatureAPI;

describe('provider works successfully with openfeature', function (): void {

    beforeEach(function (): void {
        $growthbook = \Mockery::mock(Growthbook::class)->makePartial();
        $growthbook->shouldReceive('initialize')->andReturnNull();

        $features = [
            'test-string' => [
                'defaultValue' => 'ok',
            ],
            'test-number' => [
                'defaultValue' => 1,
            ],
            'test-object' => [
                'defaultValue' => [
                    'sucess' => 'ok',
                ],
            ],
        ];
        $growthbook->withFeatures($features);
        $api = OpenFeatureAPI::getInstance();
        // configure a provider
        $api->setProvider(new GrowthbookOpenfeatureProvider(
            growthbook: Growthbook::create(),
            clientKey: getenv('GROWTHBOOK_CLIENT_KEY'),
            apiHost: getenv('GROWTHBOOK_API_HOST'),
        ));

        // create a `client`
        $this->client = $api->getClient(GrowthbookOpenfeatureProvider::class, 'v1.17');

    });

    it('gets the boolean value successfully', function (): void {
        expect($this->client->getBooleanValue(flagKey: 'test-boolean', defaultValue: false))->toBeFalse();
    });
    it('gets the number value successfully', function (): void {
        expect($this->client->getStringValue(flagKey: 'test-string', defaultValue: 'wrong'))->toEqual('ok');
    });
    it('gets the integer value successfully', function (): void {
        expect($this->client->getIntegerValue(flagKey: 'test-number', defaultValue: 0.0))->toEqual(1);
    });
    it('gets the float value successfully', function (): void {
        expect($this->client->getFloatValue(flagKey: 'test-number', defaultValue: 0.0))->toEqual(0.0);
    });
    it('gets the object value successfully', function (): void {

        expect($this->client->getObjectValue(flagKey: 'test-object', defaultValue: []))->toEqual(['sucess' => 'ok']);
    });
});
