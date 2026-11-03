<?php declare(strict_types=1);

namespace Dravencms\Tag;

use Dravencms\Admin\Menu\{AdminMenuItem, AdminMenuProviderInterface};

final class AdminMenuProvider implements AdminMenuProviderInterface
{
    public function getItems(): iterable
    {
        yield new AdminMenuItem('tag.overview', 'tag.menu.overview', destination: ':Admin:Tag:Tag:default', icon: 'fa-tags', order: 500, resource: 'tag', privilege: 'edit');
    }
}
