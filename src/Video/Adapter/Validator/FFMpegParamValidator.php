<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace Soluble\MediaTools\Video\Adapter\Validator;

use Soluble\MediaTools\Video\Exception\ParamValidationException;
use Soluble\MediaTools\Video\VideoConvertParamsInterface;

/**
 * To get better error reporting, we try to fail early (instead of at the shell exec level)
 * Here's a starter class to add some common validation, use it as a base, will need
 * refactor when more rules are added.
 */
final class FFMpegParamValidator
{
    /** @var VideoConvertParamsInterface */
    private $params;

    public function __construct(VideoConvertParamsInterface $conversionParams)
    {
        $this->params = $conversionParams;
    }

    /**
     * @throws ParamValidationException
     */
    public function validate(): void
    {
        $this->ensureValidCrf();
    }

    /**
     * Ensure that is CRF have been set, values for VP9 and H264 are in valid ranges.
     *
     * @throws ParamValidationException
     */
    protected function ensureValidCrf(): void
    {
        if (!$this->params->hasParam(VideoConvertParamsInterface::PARAM_CRF)) {
            return;
        }
        $crf = $this->params->getParam(VideoConvertParamsInterface::PARAM_CRF);

        // Check allowed values for CRF
        $codec = $this->params->getParam(VideoConvertParamsInterface::PARAM_VIDEO_CODEC, '');

        if (mb_stripos($codec, 'vp9') !== false && ($crf < 0 || $crf > 63)) {
            throw new ParamValidationException(
                sprintf(
                    'Invalid value for CRF, \'%s\' requires a number between 0 and 63: %s given.',
                    $codec,
                    $crf
                )
            );
        }

        if (mb_stripos($codec, '264') !== false && ($crf < 0 || $crf > 51)) {
            throw new ParamValidationException(
                sprintf(
                    'Invalid value for CRF, \'%s\' requires a number between 0 and 61: %s given.',
                    $codec,
                    $crf
                )
            );
        }
    }
}
