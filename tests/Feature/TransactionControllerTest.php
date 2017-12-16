<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;

class TransactionControllerTest extends TestCase
{
    const EXISTING_CUSTOMER = 1;
    const NON_EXISTING_CUSTOMER = 100;
    const EXISTING_TRANSACTION = 1;
    const NON_EXISTING_TRANSACTION = 100;
    const VALID_FAKER_AMOUNT = 155;
    const INVALID_FAKER_AMOUNT = 100;

    use DatabaseTransactions;

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
                                'year' => $filters['year'],
                                'month' => $filters['month'],
                                'day' => $filters['day'],
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
        $validResponse = [
            'transactions' => [
                'id',
                'amount',
                'date'
            ]
        ];

        $emptyResponse = ['message' => []];

        $filters = [
            'amount' => self::VALID_FAKER_AMOUNT,
            'year' => '2017',
            'month' => date('n'),
            'day' => date('d'),
            'offset' => '1',
            'limit' => '5'
        ];

        $emptyResultsFilter = [
            'amount' => self::INVALID_FAKER_AMOUNT,
            'year' => '2017',
            'month' => date('n'),
            'day' => date('d'),
            'offset' => '1',
            'limit' => '5'
        ];

        return array(
            array(self::NON_EXISTING_CUSTOMER, $filters, $emptyResponse, Response::HTTP_NOT_FOUND),
            array(self::EXISTING_CUSTOMER, $filters, $validResponse, Response::HTTP_OK),
            array(self::EXISTING_CUSTOMER, $emptyResultsFilter, $emptyResponse, Response::HTTP_OK),
        );
    }
}
