<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tests\Stubs\FluentQueryStub;

class FluentQueryTest extends TestCase
{
    public function testLimit(): void
    {
        $fm = new FluentQueryStub();
        $fm->limit(10);
        $this->assertEquals(10, $fm->getQuery()['limit']);
    }

    public function testOffset(): void
    {
        $fm = new FluentQueryStub();
        $fm->offset(10);
        $this->assertEquals(10, $fm->getQuery()['offset']);
    }

    public function testSortAsc(): void
    {
        $fm = new FluentQueryStub();
        $fm->sortAsc('field');
        $this->assertEquals([['fieldName' => 'field', 'sortOrder' => 'ascend']], $fm->getQuery()['sort']);
    }

    public function testSortDesc(): void
    {
        $fm = new FluentQueryStub();
        $fm->sortDesc('field');
        $this->assertEquals([['fieldName' => 'field', 'sortOrder' => 'descend']], $fm->getQuery()['sort']);
    }

    public function testAndSortAscending(): void
    {
        $fm = new FluentQueryStub();
        $fm->sort('field');
        $fm->andSort('field2');
        $this->assertEquals(
            [
                ['fieldName' => 'field', 'sortOrder' => 'ascend'],
                ['fieldName' => 'field2', 'sortOrder' => 'ascend'],
            ],
            $fm->getQuery()['sort']
        );
    }

    public function testAndSortDescending(): void
    {
        $fm = new FluentQueryStub();
        $fm->sort('field', false);
        $fm->andSort('field2', false);
        $this->assertEquals(
            [
                ['fieldName' => 'field', 'sortOrder' => 'descend'],
                ['fieldName' => 'field2', 'sortOrder' => 'descend'],
            ],
            $fm->getQuery()['sort']
        );
    }

    public function testWithPortals(): void
    {
        $fm = new FluentQueryStub();
        $this->assertEquals(false, $fm->getWithPortals());

        $fm->withPortals();
        $this->assertEquals(true, $fm->getWithPortals());

        $fm->withoutPortals();
        $this->assertEquals(false, $fm->getWithPortals());
    }

    public function testWhereEmpty(): void
    {
        $fm = new FluentQueryStub();
        $fm->whereEmpty('field');

        $this->assertEquals(['query' => [ [ 'field' => '=' ] ]], $fm->getQuery());
    }

    public function testWhereWithOneParameter(): void
    {
        $fm = new FluentQueryStub();
        $fm->where('field', 'value');

        $this->assertEquals(['query' => [ [ 'field' => '=value' ] ]], $fm->getQuery());
    }

    public function testWhereWithTwoParameter(): void
    {
        $fm = new FluentQueryStub();
        $fm->where('field', 'value', 'extra');

        $this->assertEquals(['query' => [ [ 'field' => 'valueextra' ] ]], $fm->getQuery());
    }

    public function testWhereWithMultipleParameters(): void
    {
        $fm = new FluentQueryStub();
        $fm->where('field', 'value', 'extra', 'extra');

        $this->assertEquals(['query' => [ [ 'field' => '*' ] ]], $fm->getQuery());
    }

    public function testWhereCriteria(): void
    {
        $fm = new FluentQueryStub();
        $fm->whereCriteria(['criteria' => '123']);
        $this->assertEquals(['query' => ['criteria' => '123']], $fm->getQuery());
    }
    public function testWhereNotEmpty(): void
    {
        $fm = new FluentQueryStub();
        $fm->whereNotEmpty('field');

        $this->assertEquals(['query' => [ [ 'field' => '=*' ] ]], $fm->getQuery());
    }

    public function testHas(): void
    {
        $fm = new FluentQueryStub();
        $fm->has('field');

        $this->assertEquals(['query' => [ [ 'field' => '=*' ] ]], $fm->getQuery());
    }

    public function testQueryString(): void
    {
        $fm = new FluentQueryStub();
        $fm->has('hasField')->sortDesc('sort')->limit(10)->script('test');
        $this->assertEquals([
            '_query' => null,
            '_sort' => [['fieldName' => 'sort', 'sortOrder' => 'descend']],
            '_limit' => 10,
            'script' => 'test',
            'script.param' => null
        ], $fm->queryString());
    }

    public function testPreRequest(): void
    {
        $fm = new FluentQueryStub();
        $fm->prerequest('script', 'param');
        $this->assertEquals([
            'script.prerequest' => 'script',
            'script.prerequest.param' => 'param'
        ], $fm->getQuery());
    }

    public function testScriptWithType(): void
    {
        $fm = new FluentQueryStub();
        $fm->script('script', 'param', 'type');
        $this->assertEquals([
            'script.type' => 'script',
            'script.type.param' => 'param'
        ], $fm->getQuery());
    }

    public function testPresort(): void
    {
        $fm = new FluentQueryStub();
        $fm->presort('script', 'param');
        $this->assertEquals([
            'script.presort' => 'script',
            'script.presort.param' => 'param'
        ], $fm->getQuery());
    }

    public function testWithoutDeleted(): void
    {
        $fm = new FluentQueryStub();
        $fm->withoutDeleted();

        $this->assertFalse($fm->getWithDeleted());
    }

    public function testWithDeleted(): void
    {
        $fm = new FluentQueryStub();
        $fm->withDeleted();

        $this->assertTrue($fm->getWithDeleted());
    }
}
