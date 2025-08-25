<?php

namespace Laradigs\Tweaker\V31\Projection;

enum DefinedErrorCodes : int
{
    case LaravelDefaultError = 4;
    case QNotInColumns = 5;
    case QNotInProjectable = 6;
}
