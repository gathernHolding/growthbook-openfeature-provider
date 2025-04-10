<?php

use Gathern\GrowthbookOpenfeatureProvider\GrowthbookOpenfeatureProvider;
use Growthbook\Growthbook;
use OpenFeature\implementation\flags\Attributes;
use OpenFeature\implementation\flags\MutableEvaluationContext;
use OpenFeature\OpenFeatureAPI;

describe('provider works successfully with openfeature', function (): void {

    beforeEach(function (): void {
        $growthbook = \Mockery::mock(Growthbook::class)->makePartial();
        $growthbook->shouldReceive('initialize')->andReturnNull();

        $this->features = [
            'test-boolean' => [
                'defaultValue' => true,
            ],
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

        $growthbook->withFeatures($this->features);
        $api = OpenFeatureAPI::getInstance();
        // configure a provider
        $api->setProvider(new GrowthbookOpenfeatureProvider(
            growthbook: $growthbook,
            clientKey: getenv('GROWTHBOOK_CLIENT_KEY'),
            apiHost: getenv('GROWTHBOOK_API_HOST'),
        ));

        // create a `client`
        $this->client = $api->getClient(GrowthbookOpenfeatureProvider::class, 'v1.17');

    });

    it('gets the boolean value successfully', function (): void {
        $flagKey = 'test-boolean';
        expect($this->client->getBooleanValue(flagKey: 'test-boolean', defaultValue: false))->toBe($this->features[$flagKey]['defaultValue']);
    });
    it('gets the boolean value successfully with user details', function (): void {
        $flagKey = 'test-boolean';
        $user_data = [
            'name' => 'john Doe',
            'age' => '20',
            'gender' => 'male',
            'nationality' => 'martian',
        ];
        expect(
            value: $this->client->getBooleanValue(
                flagKey: 'test-boolean',
                defaultValue: false,
                context: new MutableEvaluationContext(
                    targetingKey: 'targeting-key-value',
                    attributes: new Attributes(attributesMap: $user_data)
                )
            )
        )->toBe($this->features[$flagKey]['defaultValue']);
    });
    it('gets the number value successfully', function (): void {
        $flagKey = 'test-string';
        expect($this->client->getStringValue(flagKey: $flagKey, defaultValue: 'wrong'))->toEqual($this->features[$flagKey]['defaultValue']);
    });
    it('gets the integer value successfully', function (): void {
        $flagKey = 'test-number';

        expect($this->client->getIntegerValue(flagKey: $flagKey, defaultValue: 0.0))->toEqual($this->features[$flagKey]['defaultValue']);
    });
    it('gets the float value successfully', function (): void {
        $flagKey = 'test-number';

        expect($this->client->getFloatValue(flagKey: $flagKey, defaultValue: 0.0))->toEqual(0.0);
    });
    it('gets the object value successfully', function (): void {
        $flagKey = 'test-object';

        expect($this->client->getObjectValue(flagKey: $flagKey, defaultValue: []))->toEqual($this->features[$flagKey]['defaultValue']);
    });
});
