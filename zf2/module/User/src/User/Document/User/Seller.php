<?php

namespace User\Document\User;

use MyZend\Document\Document as Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\EmbeddedDocument */
class Seller extends Document {

     /** @ODM\String */
    protected $url;

}
