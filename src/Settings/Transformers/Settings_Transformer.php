<?php

namespace CPM\Settings\Transformers;

use CPM\Settings\Models\Settings;
use League\Fractal\TransformerAbstract;
use CPM\Common\Traits\Resource_Editors;

class Settings_Transformer extends TransformerAbstract {

    use Resource_Editors;

    protected $defaultIncludes = [
        'creator', 'updater'
    ];

    public function transform( Settings $item ) {
        return [
            'id'    => (int) $item->id,
            'key'   => $item->key,
            'value' => $item->value,
        ];
    }
}