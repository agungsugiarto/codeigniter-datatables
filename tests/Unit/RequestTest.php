<?php

namespace Fluent\DataTables\Tests\Unit;

use Fluent\DataTables\Tests\TestCase;
use Fluent\DataTables\Utilities\Request;

class RequestTest extends TestCase
{
    public function test_is_searchable()
    {
        $_GET['search']['value'] = '';
        request()->merge($_GET);
        $request = $this->getRequest();
        $this->assertFalse($request->isSearchable());

        $_GET['search']['value'] = 'foo';
        request()->merge($_GET);
        $request = $this->getRequest();
        $this->assertTrue($request->isSearchable());

        $_GET['search']['value'] = '0';
        request()->merge($_GET);
        $request = $this->getRequest();
        $this->assertTrue($request->isSearchable());
    }

    public function test_column_keyword()
    {
        $_GET['columns']   = [];
        $_GET['columns'][] = [
            'search' => [
                'value' => 'foo',
            ],
        ];
        $_GET['columns'][] = [
            'search' => [
                'value' => 'bar',
            ],
        ];

        request()->merge($_GET);
        $request = $this->getRequest();
        $this->assertEquals('foo', $request->columnKeyword(0));
        $this->assertEquals('bar', $request->columnKeyword(1));
    }

    public function test_orderable_columns()
    {
        $_GET['columns']   = [];
        $_GET['columns'][] = [
            'orderable' => 'true',
            'search'    => [
                'value' => 'foo',
            ],
        ];
        $_GET['order']   = [];
        $_GET['order'][] = [
            'column' => 0,
            'dir'    => 'asc',
        ];
        request()->merge($_GET);
        $request = $this->getRequest();
        $this->assertEquals([
            ['column' => 0, 'direction' => 'asc'],
        ], $request->orderableColumns());

        $this->assertTrue($request->isOrderable());
        $this->assertTrue($request->isColumnOrderable(0));
    }

    public function test_orderable_columns_will_set_descending_on_other_values()
    {
        $_GET['columns']   = [];
        $_GET['columns'][] = [
            'orderable' => 'true',
            'search'    => [
                'value' => 'foo',
            ],
        ];
        $_GET['order']   = [];
        $_GET['order'][] = [
            'column' => 0,
            'dir'    => 'bar',
        ];
        request()->merge($_GET);
        $request = $this->getRequest();
        $this->assertEquals([
            ['column' => 0, 'direction' => 'desc'],
        ], $request->orderableColumns());

        $this->assertTrue($request->isOrderable());
        $this->assertTrue($request->isColumnOrderable(0));
    }

    public function test_searchable_column_index()
    {
        $_GET['columns']   = [];
        $_GET['columns'][] = ['name' => 'foo', 'searchable' => 'true', 'search' => ['value' => 'foo']];
        $_GET['columns'][] = ['name' => 'bar', 'searchable' => 'false', 'search' => ['value' => 'foo']];
        request()->merge($_GET);
        $request = $this->getRequest();
        $this->assertEquals([0], $request->searchableColumnIndex());

        $this->assertTrue($request->isColumnSearchable(0, false));
        $this->assertFalse($request->isColumnSearchable(1, false));

        $this->assertTrue($request->isColumnSearchable(0, true));
        $this->assertFalse($request->isColumnSearchable(1, false));

        $this->assertEquals('foo', $request->columnName(0));
        $this->assertEquals('bar', $request->columnName(1));
    }

    public function test_keyword()
    {
        $_GET['search'] = [];
        $_GET['search'] = ['value' => 'foo'];
        request()->merge($_GET);
        $request = $this->getRequest();
        $this->assertEquals('foo', $request->keyword());
    }

    public function test_is_paginationable()
    {
        $_GET['start']  = 1;
        $_GET['length'] = 10;
        request()->merge($_GET);
        $request = $this->getRequest();
        $this->assertTrue($request->isPaginationable());

        $_GET['start']  = 1;
        $_GET['length'] = -1;
        request()->merge($_GET);
        $request = $this->getRequest();
        $this->assertFalse($request->isPaginationable());

        $_GET['start']  = null;
        $_GET['length'] = 1;
        request()->merge($_GET);
        $request = $this->getRequest();
        $this->assertFalse($request->isPaginationable());
    }

    /**
     * @return \Fluent\DataTables\Utilities\Request
     */
    protected function getRequest()
    {
        $request = new Request();

        return $request;
    }
}
