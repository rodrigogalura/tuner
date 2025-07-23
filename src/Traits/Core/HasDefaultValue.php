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

    private function getSortableFields()
    {
        return ['id', 'name'];
    }

    private function getMinimumKeywordCharForSearch(): int
    {
        return 2;
    }

    private function canInspect(): bool
    {
        return true;
    }

    private function columnListing()
    {
        return array_diff(
            $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable()),
            $this->getHidden()
        );
    }
}
