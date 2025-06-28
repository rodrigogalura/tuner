<?php

namespace RGalura\ApiIgniter;

trait HasDefaultValue
{
    private function getProjectableFields(): array
    {
        return ['*'];
    }

    private function getSearchableFields()
    {
        return ['*'];
    }

    private function getMinimumKeywordCharForSearch()
    {
        return 2;
    }

    private function canInspect()
    {
        return true;
        // return false;
    }

    private function columnListing()
    {
        return array_diff(
            $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable()),
            $this->getHidden()
        );
    }
}
