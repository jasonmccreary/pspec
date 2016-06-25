<?php namespace PSpec\Tests;

/**
 * Tests the construction of our test graph via our DSL.

describe('Matura', function ($ctx) {
    describe('Describe', function ($ctx) {
        before(function ($ctx) {
            $ctx->describe = $ctx->block->parentBlock();
        });

        it('should be a Describe Block', function ($ctx) {
            expect($ctx->describe)->to->be->a('PSpec\Blocks\Describe');
        });

        it('should have the correct parent Block', function ($ctx) {
            expect($ctx->describe->parentBlock())->to->be(null);
        });
    });

    describe('TestMethod', function ($ctx) {
        before(function ($ctx) {
            $ctx->test = $ctx->find('Suite:Fixture:TestMethod');
        });

        it('should be a TestMethod', function ($ctx) {
            expect($ctx->test)->to->be->a('PSpec\Blocks\Methods\TestMethod');
        });

        it('should have the correct parent Block', function ($ctx) {
            expect($ctx->test->parentBlock())->to->be->a('PSpec\Blocks\Describe');
        });
    });

    describe('BeforeHook', function ($ctx) {
        before(function ($ctx) {
            $ctx->describe = $ctx->find('Suite:Fixture');
        });

        it('should have 1 BeforeHook', function ($ctx) {
            $befores = $ctx->describe->befores();
            expect($befores)->to->have->length(1);
            expect($befores[0])->to->be->a('PSpec\Blocks\Methods\BeforeHook');
        });
    });

    describe('AfterHook', function ($ctx) {
        before(function ($ctx) {
            $ctx->describe = $ctx->find('Suite:Fixture');
        });

        after(function($ctx) {

        });

        it('should have 1 AfterHook', function ($ctx) {
            $afters = $ctx->describe->afters();
            expect($afters)->to->have->length(1);
            expect($afters[0])->to->be->a('PSpec\Blocks\Methods\AfterHook');
        });
    });
});
 */