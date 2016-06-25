<?php namespace Matura\Tests;

use Matura\Test\Group;
use Matura\Test\User;

describe('Simple Example', function ($ctx) {
    before(function ($ctx) {
        $bob = new User('bob');
        $admins = new Group('admins');

        $bob->first_name = 'bob';
        $bob->group = $admins;

        $ctx->bob = $bob;
        $ctx->admins = $admins;
    });

    it('should set the bob user', function ($ctx) {
        $ctx->sibling_value = 10;
        expect($ctx->bob)->to->be->a('Matura\Test\User');
    });

    it('should not inherit a sibling\'s context modifications', function ($ctx) {
        expect($ctx->sibling_value)->to->be(null);
    });

    it('should set the admins group', function ($ctx) {
        expect($ctx->admins)->to->be->a('Matura\Test\Group');
    });

    it('should skip this test when invoked', function ($ctx) {
        skip();
    });

    xit('should skip this test when constructed', function ($ctx) {
    });

    // This test is expected to fail.
    it('should be strict about undefined variables', function ($ctx) {
        $arr = array(0);
        $result = $arr[0] + $arr[1];
    });

    // Nested blocks help organize tests and allow progressive augmentation of
    // test context.
    describe('Inner Block with Before All and Context Clobbering', function ($ctx) {
        before_all(function ($ctx) {
            // Do something costly like purge and re-seed a database.
            $ctx->purged_database = true;
        });

        before(function ($ctx) {
            $ctx->admins = new Group('modified_admins');
        });

        it('should inherit context from outer before blocks', function ($ctx) {
            expect($ctx->bob)->to->be->a('Matura\Test\User');
        });

        it('should shadow context variables from outer contexts if assigned', function ($ctx) {
            expect($ctx->admins->name)->to->eql('modified_admins');
        });
    });
});
