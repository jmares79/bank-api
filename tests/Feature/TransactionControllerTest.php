<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\Response;

class TransactionControllerTest extends TestCase
{
    const EXISTING_CUSTOMER = 1;
    const NON_EXISTING_CUSTOMER = 100;
    const EXISTING_TRANSACTION = 1;
    const NON_EXISTING_TRANSACTION = 100;

    /**
     * Tests getTransaction method of TransactionController.
     *
     * @dataProvider getTransactionProvider
     *
     * @return void
     */
    public function testGetTransaction($customerId, $transactionId, $expectedStructure, $expectedHttpStatus)
    {
        $response = $this->get(route('get-transaction', ['customerId' => $customerId, 'transactionId' => $transactionId]));

        $response->assertStatus($expectedHttpStatus);
        $response->assertJsonStructure($expectedStructure);
    }

    /**
     * Tests getTransactions method of TransactionController.
     *
     * @dataProvider filteredProvider
     *
     * @return void
     */
    public function testGetTransactions($customerId, $filters, $expectedStructure, $expectedHttpStatus)
    {
        $response = $this->get(
                        route(
                            'get-filtered-transaction',
                            [
                                'customerId' => $customerId,
                                'amount' => $filters['amount'],
                                'date' => $filters['date'],
                                'offset' => $filters['offset'],
                                'limit' => $filters['limit'],
                            ]
                        )
                    );

        $response->assertStatus($expectedHttpStatus);
        $response->assertJsonStructure($expectedStructure);
    }

    public function getTransactionProvider()
    {
        $transaction = [
            'transaction' => ['id', 'amount', 'date']
        ];

        $emptyResponse = ['message'];

        return array(
            array(self::EXISTING_CUSTOMER, self::EXISTING_TRANSACTION, $transaction, Response::HTTP_OK),
            array(self::NON_EXISTING_CUSTOMER, self::EXISTING_TRANSACTION, $emptyResponse, Response::HTTP_NOT_FOUND),
            array(self::EXISTING_CUSTOMER, self::NON_EXISTING_TRANSACTION, $emptyResponse, Response::HTTP_NOT_FOUND),
            array(self::NON_EXISTING_CUSTOMER, self::NON_EXISTING_TRANSACTION, $emptyResponse, Response::HTTP_NOT_FOUND)
        );
    }

    public function filteredProvider()
    {
        $transactions = ['transactions' => ['*']];
        $emptyResponse = ['message' => []];

        $filtersArray = [
            [
                'amount' => 122.1,
                'date' => '2017-06-14',
                'offset' => '1',
                'limit' => '5'
            ]
        ];

        $emptyFilters = [
            'amount' => null,
            'date' => null,
            'offset' => null,
            'limit' => null
        ];

        return array(
            array(null, $emptyFilters, $emptyResponse, Response::HTTP_NOT_FOUND),
        );
    }
}
