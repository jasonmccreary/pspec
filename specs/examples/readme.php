<?php
describe('string functions', function () {
    describe('strtolower', function () {
        it('should return a string with all its characters lower case', function () {
            expect(strtolower('PSpec'))->toEqual('pspec');
        });
    });

    describe('strpos', function () {
        context('when needle is found', function () {
            it('should return the first character index of needle within haystack', function () {
                expect(strpos('PSpec', 'Spec'))->toBe(1);
            });
        });

        context('when needle is not found', function () {
            it('should return false', function () {
                expect(strpos('PSpec', 'PHP'))->toBeFalse();
            });
        });
    });
});