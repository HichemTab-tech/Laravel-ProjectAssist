<?php

namespace HichemtabTech\LaravelProjectAssist;

abstract class DataClassLikeEnumValueOnly
{
    const __NONE__ = 0;

    public abstract static function ALL(): array;
}