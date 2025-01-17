<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\AdminBundle\Tests\Admin;

use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Exception\AdminCodeNotFoundException;
use Sonata\AdminBundle\Templating\MutableTemplateRegistryInterface;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PoolTest extends TestCase
{
    use ExpectDeprecationTrait;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Pool
     */
    private $pool;

    protected function setUp(): void
    {
        $this->container = new Container();
        // NEXT_MAJOR: Only pass the container to Pool: "new Pool($this->container)".
        $this->pool = new Pool($this->container);
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @group legacy
     */
    public function testGetGroups(): void
    {
        $this->container->set('sonata.user.admin.group1', $this->createMock(AdminInterface::class));

        $this->pool->setAdminServiceIds(['sonata.user.admin.group1']);

        $this->pool->setAdminGroups([
            'adminGroup1' => ['sonata.user.admin.group1' => []],
        ]);

        $this->expectDeprecation('Method "Sonata\AdminBundle\Admin\Pool::getGroups()" is deprecated since sonata-project/admin-bundle 3.83 and will be removed in version 4.0.');

        $result = $this->pool->getGroups();
        static::assertArrayHasKey('adminGroup1', $result);
        static::assertArrayHasKey('sonata.user.admin.group1', $result['adminGroup1']);
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testHasGroup(): void
    {
        $this->pool->setAdminGroups([
            'adminGroup1' => [],
        ]);

        $this->expectDeprecation('Method "Sonata\AdminBundle\Admin\Pool::hasGroup()" is deprecated since sonata-project/admin-bundle 3.83 and will be removed in version 4.0.');

        static::assertTrue($this->pool->hasGroup('adminGroup1'));
        static::assertFalse($this->pool->hasGroup('adminGroup2'));
    }

    public function testGetDashboardGroups(): void
    {
        $adminGroup1 = $this->createMock(AdminInterface::class);
        $adminGroup1->expects(static::once())->method('showIn')->willReturn(true);

        $adminGroup2 = $this->createMock(AdminInterface::class);
        $adminGroup2->expects(static::once())->method('showIn')->willReturn(false);

        $adminGroup3 = $this->createMock(AdminInterface::class);
        $adminGroup3->expects(static::once())->method('showIn')->willReturn(false);

        $this->container->set('sonata.user.admin.group1', $adminGroup1);
        $this->container->set('sonata.user.admin.group2', $adminGroup2);
        $this->container->set('sonata.user.admin.group3', $adminGroup3);

        $pool = new Pool(
            $this->container,
            ['sonata.user.admin.group1', 'sonata.user.admin.group2', 'sonata.user.admin.group3'],
            [
                'adminGroup1' => [
                    'items' => ['itemKey' => $this->getItemArray('sonata.user.admin.group1')],
                ],
                'adminGroup2' => [
                    'items' => ['itemKey' => $this->getItemArray('sonata.user.admin.group2')],
                ],
                'adminGroup3' => [
                    'items' => ['itemKey' => $this->getItemArray('sonata.user.admin.group3')],
                ],
                'adminGroup4' => [
                    'items' => ['itemKey' => $this->getItemArray()],
                ],
            ]
        );

        $groups = $pool->getDashboardGroups();

        static::assertCount(1, $groups);
        static::assertSame($adminGroup1, $groups['adminGroup1']['items']['itemKey']);
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testGetAdminsByGroupWhenGroupNotSet(): void
    {
        $this->pool->setAdminGroups([
                'adminGroup1' => [],
            ]);

        $this->expectException(\InvalidArgumentException::class);

        $this->pool->getAdminsByGroup('adminGroup2');
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testGetAdminsByGroupWhenGroupIsEmpty(): void
    {
        $this->pool->setAdminGroups([
                'adminGroup1' => [],
            ]);

        $this->expectDeprecation('Method "Sonata\AdminBundle\Admin\Pool::getAdminsByGroup()" is deprecated since sonata-project/admin-bundle 3.83 and will be removed in version 4.0.');

        static::assertSame([], $this->pool->getAdminsByGroup('adminGroup1'));
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testGetAdminsByGroup(): void
    {
        $this->container->set('sonata.admin1', $this->createMock(AdminInterface::class));
        $this->container->set('sonata.admin2', $this->createMock(AdminInterface::class));
        $this->container->set('sonata.admin3', $this->createMock(AdminInterface::class));

        $this->pool->setAdminServiceIds(['sonata.admin1', 'sonata.admin2', 'sonata.admin3']);
        $this->pool->setAdminGroups([
            'adminGroup1' => [
                'items' => [
                    $this->getItemArray('sonata.admin1'),
                    $this->getItemArray('sonata.admin2'),
                    $this->getItemArray(),
                ],
            ],
            'adminGroup2' => [
                'items' => [$this->getItemArray('sonata.admin3')],
            ],
        ]);

        $this->expectDeprecation('Method "Sonata\AdminBundle\Admin\Pool::getAdminsByGroup()" is deprecated since sonata-project/admin-bundle 3.83 and will be removed in version 4.0.');

        static::assertCount(2, $this->pool->getAdminsByGroup('adminGroup1'));
        static::assertCount(1, $this->pool->getAdminsByGroup('adminGroup2'));
    }

    public function testGetAdminForClassWithTooManyRegisteredAdmin(): void
    {
        $pool = new Pool($this->container, ['sonata.user.admin.group1'], [], [
            'someclass' => ['sonata.user.admin.group1', 'sonata.user.admin.group2'],
        ]);

        static::assertTrue($pool->hasAdminByClass('someclass'));

        // NEXT_MAJOR: Remove this line and uncomment the following one.
        $this->expectException(\RuntimeException::class);
        // $this->expectException(TooManyAdminClassException::class);

        $pool->getAdminByClass('someclass');
    }

    public function testGetAdminForClassWithTooManyRegisteredAdminButOneDefaultAdmin(): void
    {
        $this->container->set('sonata.user.admin.group1', $this->createMock(AdminInterface::class));

        $pool = new Pool($this->container, ['sonata.user.admin.group1'], [], [
            'someclass' => [Pool::DEFAULT_ADMIN_KEY => 'sonata.user.admin.group1', 'sonata.user.admin.group2'],
        ]);

        static::assertTrue($pool->hasAdminByClass('someclass'));
        static::assertInstanceOf(AdminInterface::class, $pool->getAdminByClass('someclass'));
    }

    public function testGetAdminForClassWhenAdminClassIsSet(): void
    {
        $this->container->set('sonata.user.admin.group1', $this->createMock(AdminInterface::class));

        $pool = new Pool($this->container, ['sonata.user.admin.group1'], [], ['someclass' => ['sonata.user.admin.group1']]);

        static::assertTrue($pool->hasAdminByClass('someclass'));
        static::assertInstanceOf(AdminInterface::class, $pool->getAdminByClass('someclass'));
    }

    public function testGetInstanceWithUndefinedServiceId(): void
    {
        $this->expectException(AdminCodeNotFoundException::class);
        $this->expectExceptionMessage('Admin service "sonata.news.admin.post" not found in admin pool.');

        $this->pool->getInstance('sonata.news.admin.post');
    }

    public function testGetInstanceWithUndefinedServiceIdAndExistsOther(): void
    {
        $pool = new Pool($this->container, [
            'sonata.news.admin.post',
            'sonata.news.admin.category',
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Admin service "sonata.news.admin.pos" not found in admin pool. Did you mean "sonata.news.admin.post" or one of those: [sonata.news.admin.category]?');

        $pool->getInstance('sonata.news.admin.pos');
    }

    public function testGetAdminByAdminCode(): void
    {
        $this->container->set('sonata.news.admin.post', $this->createMock(AdminInterface::class));

        $pool = new Pool($this->container, ['sonata.news.admin.post']);

        static::assertInstanceOf(AdminInterface::class, $pool->getAdminByAdminCode('sonata.news.admin.post'));
    }

    public function testGetAdminByAdminCodeForChildClass(): void
    {
        $adminMock = $this->createMock(AdminInterface::class);
        $adminMock
            ->method('hasChild')
            ->willReturn(true);

        $childAdmin = $this->createMock(AdminInterface::class);

        $adminMock->expects(static::once())
            ->method('getChild')
            ->with(static::equalTo('sonata.news.admin.comment'))
            ->willReturn($childAdmin);

        $this->container->set('sonata.news.admin.post', $adminMock);

        $pool = new Pool($this->container, ['sonata.news.admin.post', 'sonata.news.admin.comment']);

        static::assertSame($childAdmin, $pool->getAdminByAdminCode('sonata.news.admin.post|sonata.news.admin.comment'));
    }

    /**
     * @group legacy
     *
     * @expectedDeprecation Passing an invalid admin code as argument 1 for Sonata\AdminBundle\Admin\Pool::getAdminByAdminCode() is deprecated since sonata-project/admin-bundle 3.50 and will throw an exception in 4.0.
     */
    public function testGetAdminByAdminCodeWithInvalidCode(): void
    {
        $adminMock = $this->createMock(AdminInterface::class);
        $adminMock
            ->method('hasChild')
            ->willReturn(false);

        $this->container->set('sonata.news.admin.post', $adminMock);
        $this->pool->setAdminServiceIds(['sonata.news.admin.post']);

        // NEXT_MAJOR: remove the assertion around getAdminByAdminCode(), remove the "@group" and "@expectedDeprecation" annotations, and uncomment the following line
        // $this->expectException(AdminCodeNotFoundException::class);
        static::assertFalse($this->pool->getAdminByAdminCode('sonata.news.admin.post|sonata.news.admin.invalid'));
    }

    /**
     * @dataProvider getNonStringAdminServiceNames
     *
     * @group legacy
     *
     * @expectedDeprecation Passing a non string value as argument 1 for Sonata\AdminBundle\Admin\Pool::getAdminByAdminCode() is deprecated since sonata-project/admin-bundle 3.51 and will cause a TypeError in 4.0.
     */
    public function testGetAdminByAdminCodeWithNonStringCode($adminId): void
    {
        // NEXT_MAJOR: remove the assertion around getAdminByAdminCode(), remove the "@group" and "@expectedDeprecation" annotations, and uncomment the following line
        // $this->expectException(\TypeError::class);
        static::assertFalse($this->pool->getAdminByAdminCode($adminId));
    }

    public function getNonStringAdminServiceNames(): array
    {
        return [
            [null],
            [false],
            [1],
            [['some_value']],
            [new \stdClass()],
        ];
    }

    /**
     * @group legacy
     *
     * @expectedDeprecation Passing an invalid admin hierarchy inside argument 1 for Sonata\AdminBundle\Admin\Pool::getAdminByAdminCode() is deprecated since sonata-project/admin-bundle 3.51 and will throw an exception in 4.0.
     */
    public function testGetAdminByAdminCodeWithCodeNotChild(): void
    {
        $adminMock = $this->createMock(AdminInterface::class);
        $adminMock
            ->method('hasChild')
            ->willReturn(false);

        $this->container->set('sonata.news.admin.post', $adminMock);
        $this->pool->setAdminServiceIds(['sonata.news.admin.post', 'sonata.news.admin.valid']);
        static::assertFalse($this->pool->getAdminByAdminCode('sonata.news.admin.post|sonata.news.admin.invalid'));

        // NEXT_MAJOR: remove the "@group" and "@expectedDeprecation" annotations, the previous assertion and uncomment the following lines
        // $this->expectException(AdminCodeNotFoundException::class);
        // $this->expectExceptionMessage('Argument 1 passed to Sonata\AdminBundle\Admin\Pool::getAdminByAdminCode() must contain a valid admin hierarchy, "sonata.news.admin.valid" is not a valid child for "sonata.news.admin.post"');
        //
        // $this->pool->getAdminByAdminCode('sonata.news.admin.post|sonata.news.admin.valid');
    }

    /**
     * @dataProvider getEmptyRootAdminServiceNames
     */
    public function testGetAdminByAdminCodeWithInvalidRootCode(string $adminId): void
    {
        $adminMock = $this->createMock(AdminInterface::class);
        $adminMock->expects(static::never())
            ->method('hasChild');

        $pool = new Pool($this->container, [$adminId]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Admin code must contain a valid admin reference, empty string given.');
        $pool->getAdminByAdminCode($adminId);
    }

    public function getEmptyRootAdminServiceNames()
    {
        return [
            [''],
            ['   '],
            ['|sonata.news.admin.child_of_empty_code'],
        ];
    }

    /**
     * @dataProvider getInvalidChildAdminServiceNames
     *
     * @group legacy
     *
     * @expectedDeprecation Passing an invalid admin code as argument 1 for Sonata\AdminBundle\Admin\Pool::getAdminByAdminCode() is deprecated since sonata-project/admin-bundle 3.50 and will throw an exception in 4.0.
     */
    public function testGetAdminByAdminCodeWithInvalidChildCode(string $adminId): void
    {
        $adminMock = $this->createMock(AdminInterface::class);
        $adminMock
            ->method('hasChild')
            ->willReturn(false);
        $adminMock->expects(static::never())
            ->method('getChild');

        $this->container->set('admin1', $adminMock);
        $pool = new Pool($this->container, ['admin1']);

        // NEXT_MAJOR: remove the assertion around getAdminByAdminCode(), remove the "@group" and "@expectedDeprecation" annotations, and uncomment the following line
        // $this->expectException(AdminCodeNotFoundException::class);
        static::assertFalse($pool->getAdminByAdminCode($adminId));
    }

    public function getInvalidChildAdminServiceNames()
    {
        return [
            ['admin1|'],
            ['admin1|nonexistent_code'],
            ['admin1||admin3'],
        ];
    }

    /**
     * @dataProvider getAdminServiceNamesToCheck
     */
    public function testHasAdminByAdminCode(string $adminId): void
    {
        $adminMock = $this->createMock(AdminInterface::class);

        if (false !== strpos($adminId, '|')) {
            $childAdminMock = $this->createMock(AdminInterface::class);
            $adminMock
                ->method('hasChild')
                ->willReturn(true);
            $adminMock->expects(static::once())
                ->method('getChild')
                ->with(static::equalTo('sonata.news.admin.comment'))
                ->willReturn($childAdminMock);
        } else {
            $adminMock->expects(static::never())
                ->method('hasChild');
            $adminMock->expects(static::never())
                ->method('getChild');
        }

        $this->container->set('sonata.news.admin.post', $adminMock);

        $pool = new Pool($this->container, ['sonata.news.admin.post', 'sonata.news.admin.comment']);

        static::assertTrue($pool->hasAdminByAdminCode($adminId));
    }

    public function getAdminServiceNamesToCheck()
    {
        return [
            ['sonata.news.admin.post'],
            ['sonata.news.admin.post|sonata.news.admin.comment'],
        ];
    }

    /**
     * @dataProvider getNonStringAdminServiceNames
     */
    public function testHasAdminByAdminCodeWithNonStringCode($adminId): void
    {
        $this->expectException(\TypeError::class);
        $this->pool->hasAdminByAdminCode($adminId);
    }

    /**
     * @dataProvider getInvalidAdminServiceNamesToCheck
     */
    public function testHasAdminByAdminCodeWithInvalidCodes(string $adminId): void
    {
        $adminMock = $this->createMock(AdminInterface::class);
        $adminMock
            ->method('hasChild')
            ->willReturn(false);
        $adminMock->expects(static::never())
            ->method('getChild');

        static::assertFalse($this->pool->hasAdminByAdminCode($adminId));
    }

    public function getInvalidAdminServiceNamesToCheck()
    {
        return [
            [''],
            ['   '],
            ['|sonata.news.admin.child_of_empty_code'],
        ];
    }

    public function testHasAdminByAdminCodeWithNonExistentCode(): void
    {
        static::assertFalse($this->pool->hasAdminByAdminCode('sonata.news.admin.nonexistent_code'));
    }

    /**
     * @dataProvider getInvalidChildAdminServiceNamesToCheck
     *
     * @group legacy
     *
     * @expectedDeprecation Passing an invalid admin %s argument 1 for Sonata\AdminBundle\Admin\Pool::getAdminByAdminCode() is deprecated since sonata-project/admin-bundle 3.%s and will throw an exception in 4.0.
     */
    public function testHasAdminByAdminCodeWithInvalidChildCodes(string $adminId): void
    {
        $adminMock = $this->createMock(AdminInterface::class);
        $adminMock
            ->method('hasChild')
            ->willReturn(false);
        $adminMock->expects(static::never())
            ->method('getChild');

        $this->container->set('sonata.news.admin.post', $adminMock);

        $this->pool->setAdminServiceIds(['sonata.news.admin.post']);

        static::assertFalse($this->pool->hasAdminByAdminCode($adminId));
    }

    public function getInvalidChildAdminServiceNamesToCheck(): array
    {
        return [
            ['sonata.news.admin.post|'],
            ['sonata.news.admin.post|nonexistent_code'],
            ['sonata.news.admin.post||admin3'],
        ];
    }

    public function testGetAdminClasses(): void
    {
        $pool = new Pool($this->container, [], [], ['someclass' => 'sonata.user.admin.group1']);
        static::assertSame(['someclass' => 'sonata.user.admin.group1'], $pool->getAdminClasses());
    }

    public function testGetAdminGroups(): void
    {
        $pool = new Pool($this->container, [], ['adminGroup1' => 'sonata.user.admin.group1']);
        static::assertSame(['adminGroup1' => 'sonata.user.admin.group1'], $pool->getAdminGroups());
    }

    public function testGetAdminServiceIds(): void
    {
        $pool = new Pool($this->container, ['sonata.user.admin.group1', 'sonata.user.admin.group2', 'sonata.user.admin.group3']);
        static::assertSame(['sonata.user.admin.group1', 'sonata.user.admin.group2', 'sonata.user.admin.group3'], $pool->getAdminServiceIds());
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testGetContainer(): void
    {
        static::assertInstanceOf(ContainerInterface::class, $this->pool->getContainer());
    }

    /**
     * @group legacy
     */
    public function testTemplate(): void
    {
        $templateRegistry = $this->createMock(MutableTemplateRegistryInterface::class);
        $templateRegistry->expects(static::once())->method('getTemplate')->with('ajax')
            ->willReturn('Foo.html.twig');

        $this->pool->setTemplateRegistry($templateRegistry);

        static::assertSame('Foo.html.twig', $this->pool->getTemplate('ajax'));
    }

    /**
     * @group legacy
     */
    public function testSetGetTemplates(): void
    {
        $templates = [
            'ajax' => 'Foo.html.twig',
            'layout' => 'Bar.html.twig',
        ];

        $templateRegistry = $this->createMock(MutableTemplateRegistryInterface::class);
        $templateRegistry->expects(static::once())->method('setTemplates')->with($templates);
        $templateRegistry->expects(static::once())->method('getTemplates')->willReturn($templates);

        $this->pool->setTemplateRegistry($templateRegistry);

        $this->pool->setTemplates($templates);

        static::assertSame($templates, $this->pool->getTemplates());
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testDeprecatedConstructor(): void
    {
        $this->expectDeprecation('Passing other type than array as argument 2 to "Sonata\AdminBundle\Admin\Pool::__construct()" is deprecated since sonata-project/admin-bundle 3.86 and will throw "TypeError" exception in 4.0.');
        $this->expectDeprecation('Passing other type than array as argument 3 to "Sonata\AdminBundle\Admin\Pool::__construct()" is deprecated since sonata-project/admin-bundle 3.86 and will throw "TypeError" exception in 4.0.');

        new Pool($this->container, 'Sonata Admin', '/path/to/pic.png', ['foo' => 'bar']);
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testGetTitleLogo(): void
    {
        $pool = new Pool($this->container, 'Sonata Admin', '/path/to/pic.png', ['foo' => 'bar']);

        $this->expectDeprecation('The "Sonata\AdminBundle\Admin\Pool::getTitleLogo()" method is deprecated since version 3.83 and will be removed in 4.0. Use "Sonata\AdminBundle\SonataConfiguration::getLogo()" instead.');

        static::assertSame('/path/to/pic.png', $pool->getTitleLogo());
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testGetTitle(): void
    {
        $pool = new Pool($this->container, 'Sonata Admin', '/path/to/pic.png', ['foo' => 'bar']);

        $this->expectDeprecation('The "Sonata\AdminBundle\Admin\Pool::getTitle()" method is deprecated since version 3.83 and will be removed in 4.0. Use "Sonata\AdminBundle\SonataConfiguration::getTitle()" instead.');

        static::assertSame('Sonata Admin', $pool->getTitle());
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testGetOption(): void
    {
        $pool = new Pool($this->container, 'Sonata Admin', '/path/to/pic.png', ['foo' => 'bar']);

        static::assertSame('bar', $pool->getOption('foo'));

        $this->expectDeprecation('The "Sonata\AdminBundle\Admin\Pool::getOption()" method is deprecated since version 3.83 and will be removed in 4.0. Use "Sonata\AdminBundle\SonataConfiguration::getOption()" instead.');

        static::assertNull($pool->getOption('non_existent_option'));
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testOptionDefault(): void
    {
        $this->expectDeprecation('The "Sonata\AdminBundle\Admin\Pool::getOption()" method is deprecated since version 3.83 and will be removed in 4.0. Use "Sonata\AdminBundle\SonataConfiguration::getOption()" instead.');

        static::assertSame([], $this->pool->getOption('nonexistantarray', []));
    }

    private function getItemArray(?string $serviceId = null): array
    {
        $item = [
            'label' => '',
            'route' => '',
            'route_params' => [],
        ];

        if (null !== $serviceId) {
            $item['admin'] = $serviceId;
        }

        return $item;
    }
}
