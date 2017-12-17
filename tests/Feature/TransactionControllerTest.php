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
    const NON_EXISTING_TRANSACTION = 10000;
    const VALID_FAKER_AMOUNT = 155;
    const INVALID_FAKER_AMOUNT = 0;
    const INVALID_FAKER_AMOUNT_CHAR = 'A';

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

    /**
     * Tests the creation of a transaction
     *
     * @dataProvider createTransactionProvider
     *
     * @return void
     */
    public function testCreateTransaction($payload, $httpStatus, $expectedResponse)
    {
        $response = $this->json('POST', route('create-transaction'), $payload);
        // dd($response);
        $response->assertStatus($httpStatus);
        $response->assertJsonStructure($expectedResponse);
    }

    /**
     * Tests the update of a transaction
     *
     * @dataProvider updateTransactionProvider
     *
     * @return void
     */
    public function testUpdateTransaction($payload, $httpStatus, $expectedResponse)
    {
        $response = $this->json('PUT', route('update-transaction'), $payload);

        $response->assertStatus($httpStatus);
        $response->assertJsonStructure($expectedResponse);
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
                '*' => [
                    'id',
                    'amount',
                    'date'
                ]
            ]
        ];

        $emptyResponseMessage = ['message' => []];

        $filters = [
            'amount' => self::VALID_FAKER_AMOUNT,
            'year' => '2017',
            'month' => date('n'),
            'day' => date('d'),
            'offset' => '1',
            'limit' => '5'
        ];

        $invalidAmountFilter = [
            'amount' => self::INVALID_FAKER_AMOUNT,
            'year' => '2017',
            'month' => date('n'),
            'day' => date('d'),
            'offset' => '1',
            'limit' => '5'
        ];

        $invalidAmountCharFilter = [
            'amount' => self::INVALID_FAKER_AMOUNT_CHAR,
            'year' => '2017',
            'month' => date('n'),
            'day' => date('d'),
            'offset' => '1',
            'limit' => '5'
        ];

        return array(
            array(self::NON_EXISTING_CUSTOMER, $filters, $emptyResponseMessage, Response::HTTP_NOT_FOUND),
            array(self::EXISTING_CUSTOMER, $filters, $validResponse, Response::HTTP_OK),
            array(self::EXISTING_CUSTOMER, $invalidAmountFilter, $emptyResponseMessage, Response::HTTP_UNPROCESSABLE_ENTITY),
            array(self::EXISTING_CUSTOMER, $invalidAmountCharFilter, $emptyResponseMessage, Response::HTTP_UNPROCESSABLE_ENTITY),
        );
    }

    public function createTransactionProvider()
    {
        $response = ['transaction' =>
            [
                'transactionId',
                'customerId',
                'amount',
                'date'
            ]
        ];

        $notFound = ['message'];
        $error = ['message', 'errors'];

        $validPayload = [
            'customerId' => self::EXISTING_CUSTOMER,
            'amount' => self::VALID_FAKER_AMOUNT
        ];

        $invalidAmountPayload = [
            'customerId' => self::EXISTING_CUSTOMER,
            'amount' => self::INVALID_FAKER_AMOUNT
        ];

        $invalidAmountWithCharPayload = [
            'customerId' => self::EXISTING_CUSTOMER,
            'amount' => self::INVALID_FAKER_AMOUNT_CHAR
        ];

        $invalidNonExistingCustomerPayload = [
            'customerId' => self::NON_EXISTING_CUSTOMER,
            'amount' => self::VALID_FAKER_AMOUNT
        ];

        return array(
            array($validPayload, Response::HTTP_CREATED, $response),
            array($invalidAmountPayload, Response::HTTP_UNPROCESSABLE_ENTITY, $error),
            array($invalidAmountWithCharPayload, Response::HTTP_UNPROCESSABLE_ENTITY, $error),
            array($invalidNonExistingCustomerPayload, Response::HTTP_NOT_FOUND, $notFound),
        );
    }

    public function updateTransactionProvider()
    {
         $response = ['transaction' =>
            [
                'transactionId',
                'customerId',
                'amount',
                'date'
            ]
        ];

        $notFound = ['message'];
        $error = ['message', 'errors'];

        $validPayload = [
            'transactionId' => self::EXISTING_TRANSACTION,
            'amount' => self::VALID_FAKER_AMOUNT
        ];

        $invalidAmountPayload = [
            'transactionId' => self::EXISTING_TRANSACTION,
            'amount' => self::INVALID_FAKER_AMOUNT
        ];

        $invalidNonExistingTransaction = [
            'transactionId' => self::NON_EXISTING_TRANSACTION,
            'amount' => self::VALID_FAKER_AMOUNT
        ];

        return array(
            array($validPayload, Response::HTTP_OK, $response),
            array($invalidAmountPayload, Response::HTTP_UNPROCESSABLE_ENTITY, $error),
            array($invalidNonExistingTransaction, Response::HTTP_NOT_FOUND, $notFound),
        );
    }
}
