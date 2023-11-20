<?php

use Response\Render\HTMLRenderer;

return [
    '' => [
        'GET' => function (): HTMLRenderer {
            return new HTMLRenderer('component/top');
        }
    ]
];
