<?php

namespace CKSource\CKFinder\Plugin\xtcommerce;

use CKSource\CKFinder\Acl\Permission;
use CKSource\CKFinder\Filesystem\Folder\WorkingFolder;
use CKSource\CKFinder\Command\CommandAbstract;

class GetFilesEmpty extends CommandAbstract
{
    protected $requires = array(Permission::FILE_VIEW);

    public function execute()
    {
        $data = new \stdClass();
        $data->files = array();

        return $data;
    }
}
