<?php declare(strict_types=1);

namespace Dravencms\Tag;

use Dravencms\User\Acl\{AclPermission, AclProviderInterface};

final class AclProvider implements AclProviderInterface
{
    public function getPermissions(): iterable
    {
        yield new AclPermission('tag', 'edit', 'tag.acl.permissions.edit.label', resourceLabel: 'tag.acl.resource');
        yield new AclPermission('tag', 'delete', 'tag.acl.permissions.delete.label', resourceLabel: 'tag.acl.resource');
    }
}
