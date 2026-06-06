<?php

namespace App\Support;

/** @deprecated Use API assignment file download; apps must not open legacy www URLs. */
class AssignmentUrl
{
    public static function openUrl(object $row): string
    {
        if (($row->type ?? '') === 'link') {
            return (string) $row->document;
        }

        return '';
    }
}
