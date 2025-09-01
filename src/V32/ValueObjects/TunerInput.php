<?php

namespace Laradigs\Tweaker\V32\ValueObjects;

use Illuminate\Support\Facades\Cache;

class TunerInput
{
    protected array $config;

    public function __construct(protected array $input)
    {
        $this->config = config('tweaker');

        // $this->projectionValidation();
    }

    // private function projectionValidation()
    // {
    //     $config = $this->config['projection'];

    //     $inputKeys = array_keys($this->input);

    //     if (
    //         in_array($config['intersect_key'], $inputKeys) &&
    //         in_array($config['except_key'], $inputKeys)
    //     ) {
    //         throw new \Exception('Cannot use ' . $config['intersect_key'] . ' and ' . $config['except_key'] . ' at the same time!');
    //     }
    // }

    public static function sanitize($input)
    {
        $tuner = new static($input);

        $acceptedKeys = Cache::rememberForever('tuner-config-keys', function () use ($tuner) {
            $keys = [];
            foreach ($tuner->config as $options) {
                foreach ($options as $key => $optionValue) {
                    if (str_contains($key, 'key')) {
                        $keys[] = $optionValue;
                    }
                }
            }

            return $keys;
        });

        $tuner->input = array_filter($tuner->input, fn ($key): bool => in_array($key, $acceptedKeys), ARRAY_FILTER_USE_KEY);

        return $tuner;
    }

    public function get()
    {
        return $this->input;
    }
}
