<?php

namespace FoF\Masquerade;

use Flarum\Api\Controller\ShowForumController;
use Psr\Http\Message\ServerRequestInterface;

class LoadAllMasqueradeFieldsRelationship
{
    /**
     * @param ShowForumController $controller
     * @param $data
     * @param ServerRequestInterface $request
     */
    public function __invoke(ShowForumController $controller, array &$data, ServerRequestInterface $request)
    {
        // Expose the complete field list to clients by adding it as a
        // relationship to the /api endpoint. Since the Forum model
        // doesn't actually have a fields relationship, we will manually load and
        // assign the fields data to it using an event listener.
        $data['masqueradeFields'] = Field::all();

        return $data;
    }
}
