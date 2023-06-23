<?php

$include_paths = explode(PATH_SEPARATOR, get_include_path());
$zf1_include_idx = null;
$end_mage_includes_idx = null;

for ($i = 0, $path_count = count($include_paths); $i < $path_count; $i++) {
    if (str_contains($include_paths[$i], 'shardj/zf1-future/library')) {
        $zf1_include_idx = $i;
    }
    if ($include_paths[$i] === '.') {
        $end_mage_includes_idx = $i;
        break;
    }
}

if ($zf1_include_idx !== null && $end_mage_includes_idx !== null) {
    $zf1_include = $include_paths[$zf1_include_idx];
    unset($include_paths[$zf1_include_idx]);
    $include_paths = array_merge(
        array_slice($include_paths, 0, $end_mage_includes_idx - 1),
        [$zf1_include],
        array_slice($include_paths, $end_mage_includes_idx - 1)
    );
}

set_include_path(implode(PATH_SEPARATOR, $include_paths));


