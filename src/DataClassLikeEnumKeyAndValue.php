<?php

namespace HichemtabTech\LaravelProjectAssist;

abstract class DataClassLikeEnumKeyAndValue
{
    const __NONE__ = 0;

    public abstract static function ALL(): array;

    public abstract static function LABELS(): array;

    public abstract static function VALUES(): array;

    public abstract static function getLabelByValue(): ?string;

    public abstract static function getValueByLabel(): ?int;
}