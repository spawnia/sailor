<?php

namespace Spawnia\Sailor\Polymorphic\UserOrPost\UserOrPost;

class Post extends \Spawnia\Sailor\TypedObject
{
    /** @var string */
    public $id;

    /** @var string */
    public $title;

    public function idTypeMapper(): callable
    {
        return new \Spawnia\Sailor\Mapper\DirectMapper();
    }

    public function titleTypeMapper(): callable
    {
        return new \Spawnia\Sailor\Mapper\DirectMapper();
    }
}
