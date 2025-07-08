<?php

use Laradigs\Tweaker\Projection;
use Illuminate\Database\Eloquent\Model;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;

describe('Exception Logic', function () {
    beforeEach(function() {
        Mockery::globalHelpers();

        $this->columListing = ['a', 'b'];
        $this->model = mock(Model::class);
        $this->model->shouldReceive('columnListing')
            ->andReturn($this->columListing);
    });

    it('should throw an exception if one of projectable fields is invalid', function () {
        // Prepare
        $invalidProjectableFields = ['c'];

        $projection = new Projection($this->model, []);

        // Act & Assert
        expect(fn () => $projection->setSelectFields($invalidProjectableFields))->toThrow(InvalidFieldsException::class);
    })->done();

    it('should throw an exception if one of defined fields is invalid', function () {
        // Prepare
        $definedFields = new \stdClass;
        $definedFields->columns = ['c'];
        $this->model->shouldReceive('getQuery')
            ->andReturn($definedFields);

        $projection = new Projection($this->model, []);

        // Act & Assert
        expect(fn () => $projection->setSelectFields($this->columListing))->toThrow(InvalidFieldsException::class);
    })->done();

    it('should not perform any action if the projectable field\'s value is falsy', function () {
        // Prepare
        $projection = new Projection($this->model, []);

        // Act & Assert
        expect($projection->setSelectFields([]))->toBeNull();
    })->done();
});
