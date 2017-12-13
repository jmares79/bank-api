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
     * The test prepares, via TestCase, the DDBB with specific seed values.
     *
     * @dataProvider getTransactionProvider
     *
     * @return void
     */
    public function testGetTransaction($customerId, $transactionId, $expectedStructure, $expectedHttpStatus)
    {
        $response = $this->get(route('get-transaction', ['customerId' => $customerId, 'transactionId' => $transactionId]));
        // dd($response->getContent());
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
}
