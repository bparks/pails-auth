<?php

use PHPUnit\Framework\TestCase;

/**
 * Smoke tests for Permission: declare_permission, declared_permissions, undeclared warnings.
 * No database required.
 */
class PermissionTest extends TestCase
{
    private static function resetDeclared(): void
    {
        $ref = new ReflectionClass('Permission');
        $prop = $ref->getProperty('declared');
        $prop->setAccessible(true);
        $prop->setValue(null, []);
    }

    protected function setUp(): void
    {
        self::resetDeclared();
    }

    public function test_declare_permission_adds_to_list(): void
    {
        $this->assertSame([], Permission::declared_permissions());

        Permission::declare_permission('manage_users');
        $this->assertSame(['manage_users'], Permission::declared_permissions());

        Permission::declare_permission('edit_posts');
        $this->assertEqualsCanonicalizing(['edit_posts', 'manage_users'], Permission::declared_permissions());
    }

    public function test_declared_permissions_returns_sorted(): void
    {
        Permission::declare_permission('zebra');
        Permission::declare_permission('alpha');
        Permission::declare_permission('middle');
        $this->assertSame(['alpha', 'middle', 'zebra'], Permission::declared_permissions());
    }

    public function test_declare_permission_normalizes_to_lowercase(): void
    {
        Permission::declare_permission('Manage_Users');
        $this->assertSame(['manage_users'], Permission::declared_permissions());
    }

    public function test_declare_permission_ignores_empty_string(): void
    {
        Permission::declare_permission('');
        Permission::declare_permission('  ');
        $this->assertSame([], Permission::declared_permissions());
    }

    public function test_undeclared_permission_triggers_warning(): void
    {
        $captured = null;
        $prev = set_error_handler(function ($errno, $errstr) use (&$captured) {
            $captured = $errstr;
            return true;
        });

        Permission::grant('users', 'alice', 'undeclared_perm');

        set_error_handler($prev);

        $this->assertNotNull($captured);
        $this->assertStringContainsString('Undeclared permission', $captured);
        $this->assertStringContainsString('undeclared_perm', $captured);
        $this->assertStringContainsString('Permission::declare_permission', $captured);
        $this->assertStringContainsString('initializer', $captured);
    }

    public function test_declared_permission_does_not_trigger_warning_when_granted(): void
    {
        Permission::declare_permission('known_perm');

        $warned = false;
        $prev = set_error_handler(function () use (&$warned) {
            $warned = true;
            return true;
        });

        Permission::grant('users', 'alice', 'known_perm');

        set_error_handler($prev);
        $this->assertFalse($warned);
    }
}
