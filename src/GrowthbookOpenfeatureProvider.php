<?php

declare(strict_types=1);

namespace Gathern\GrowthbookOpenfeatureProvider;

use Growthbook\FeatureResult;
use Growthbook\Growthbook;
use OpenFeature\implementation\common\Metadata;
use OpenFeature\implementation\provider\AbstractProvider;
use OpenFeature\implementation\provider\ResolutionDetailsBuilder;
use OpenFeature\interfaces\flags\EvaluationContext;
use OpenFeature\interfaces\provider\Provider;
use OpenFeature\interfaces\provider\ResolutionDetails as IResolutionDetails;

class GrowthbookOpenfeatureProvider extends AbstractProvider implements Provider
{
    protected static string $NAME = self::class;

    public function getMetadata(): Metadata
    {
        return new Metadata(static::class);
    }

    public function __construct(
        private readonly Growthbook $growthbook,
        public readonly string $clientKey,
        public readonly string $apiHost = '',
        public readonly string $decryptionKey = '',
    ) {
        $this->initialize();
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

    public function initialize(): void
    {
        $this->growthbook->initialize(
            clientKey: $this->clientKey,
            apiHost: $this->apiHost,
            decryptionKey: $this->decryptionKey
        );
    }

    /**
     * @template T
     *
     * @param  \Growthbook\FeatureResult<T>  $feature
     * @param  T  $defaultValue
     */
    private function toResolutionDetails(FeatureResult $feature, mixed $defaultValue): IResolutionDetails
    {

        return (new ResolutionDetailsBuilder)
            ->withValue(value: $feature->value ?? $defaultValue)// @phpstan-ignore-line
            ->withVariant(variant: $feature->source)
            ->build();

    }

    /**
     * @template  T
     *
     * @param  T  $defaultValue
     */
    public function resolveValue(string $flagKey, ?EvaluationContext $context, mixed $defaultValue): IResolutionDetails
    {
        /**
         * @var array<string,mixed>
         */
        $attributes = $context?->getAttributes()->toArray() ?? [];
        $this->growthbook->withAttributes(attributes: $attributes);

        return $this->toResolutionDetails(
            feature: $this->growthbook->getFeature(
                key: $flagKey,
                stack: [],
            ),
            defaultValue: $defaultValue
        );
    }
}
