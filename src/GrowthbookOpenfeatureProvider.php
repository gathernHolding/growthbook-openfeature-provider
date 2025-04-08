<?php

namespace Gathern\GrowthbookOpenfeatureProvider;

use Growthbook\FeatureResult;
use Growthbook\Growthbook;
use OpenFeature\implementation\common\Metadata;
use OpenFeature\implementation\provider\AbstractProvider;
use OpenFeature\implementation\provider\ResolutionDetailsBuilder;
use OpenFeature\interfaces\common\LoggerAwareTrait;
use OpenFeature\interfaces\flags\EvaluationContext;
use OpenFeature\interfaces\provider\Provider;
use OpenFeature\interfaces\provider\ResolutionDetails as IResolutionDetails;

class GrowthbookOpenfeatureProvider extends AbstractProvider implements Provider
{
    use LoggerAwareTrait;

    protected static string $NAME = self::class;

    public function getMetadata(): Metadata
    {
        return new Metadata(static::class);
    }

    public function __construct(
        private Growthbook $growthbook,
        public readonly string $clientKey,
        public readonly string $apiHost = '',
        public readonly string $decryptionKey = '',
    ) {
        $this->growthbook->initialize(
            clientKey: $clientKey,
            apiHost: $apiHost,
            decryptionKey: $decryptionKey
        );
    }

    public function resolveBooleanValue(string $flagKey, bool $defaultValue, ?EvaluationContext $context = null): IResolutionDetails
    {
        return $this->resolveValue($flagKey, context: $context, defaultValue: $defaultValue);

    }

    public function resolveStringValue(string $flagKey, string $defaultValue, ?EvaluationContext $context = null): IResolutionDetails
    {
        return $this->resolveValue($flagKey, context: $context, defaultValue: $defaultValue);
    }

    public function resolveIntegerValue(string $flagKey, int $defaultValue, ?EvaluationContext $context = null): IResolutionDetails
    {
        return $this->resolveValue(flagKey: $flagKey, context: $context, defaultValue: $defaultValue);
    }

    public function resolveFloatValue(string $flagKey, float $defaultValue, ?EvaluationContext $context = null): IResolutionDetails
    {
        return $this->resolveValue($flagKey, context: $context, defaultValue: $defaultValue);

    }

    public function resolveObjectValue(string $flagKey, mixed $defaultValue, ?EvaluationContext $context = null): IResolutionDetails
    {
        return $this->resolveValue(flagKey: $flagKey, context: $context, defaultValue: $defaultValue);

    }

    public function initialize()
    {
        $this->growthbook->initialize(
            clientKey: $this->clientKey,
            apiHost: $this->apiHost,
            decryptionKey: $this->decryptionKey
        );
    }

    private function toResolutionDetails(FeatureResult $feature, mixed $defaultValue): IResolutionDetails
    {

        return (new ResolutionDetailsBuilder)
            ->withValue($feature->value ?? $defaultValue) // @phpstan-ignore-line
            ->withVariant($feature->source ?? '')
            ->build();

    }

    /**
     * @template  T
     *
     * @param  T  $defaultValue
     */
    public function resolveValue(string $flagKey, ?EvaluationContext $context, mixed $defaultValue): IResolutionDetails
    {
        $this->growthbook->withAttributes(attributes: (array) $context->getAttributes()->toArray());

        return $this->toResolutionDetails(
            feature: $this->growthbook->getFeature(
                key: $flagKey,
                stack: [],
            ),
            defaultValue: $defaultValue
        );
    }
}
