<?php

namespace App\Services\V1;

use Illuminate\Http\Request;

class AttendanceFilterQuery
{
    protected $allowableParams = [
        'checkIn' => ['eq', 'gt', 'gte', 'lt', 'lte'],
        'checkOut' => ['eq', 'gt', 'gte', 'lt', 'lte']
    ];

    protected $columnMap = [
        'checkIn' => 'check_in',
        'checkOut' => 'check_out'
    ];

    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>='
    ];

    public function transform(Request $request)
    {
        $eloQuery = [];

        foreach ($this->allowableParams as $param => $operators) {
            $query = $request->query($param);

            if (!isset($query)) {
                continue;
            }

            $column = $this->columnMap[$param] ?? $param;

            foreach ($operators as $operator) {
                if (isset($query[$operator])) {
                    $value = $query[$operator];
                    if ($value === 'null') {
                        $eloQuery[] = [$column, '=', NULL];
                    } else {
                        $eloQuery[] = [$column, $this->operatorMap[$operator], $value];
                    }
                }
            }
        }

        return $eloQuery;
    }
}
