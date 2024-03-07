<?php

declare(strict_types=1);

namespace Tests\Art4\Wegliphant\Client;

use Art4\Wegliphant\Client;
use Art4\Wegliphant\Exception\UnexpectedResponseException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

#[CoversClass(Client::class)]
final class ListOwnNoticesTest extends TestCase
{
    public function testListOwnNoticesReturnsArray(): void
    {
        $expected = [
            [
                'token' => '7a473465c3cbbc6c90781b5e798966ce',
                'status' => 'shared',
                'street' => 'Musterstraße 123',
                'city' => 'Berlin',
                'zip' => '12305',
                'latitude' => 52.5170365,
                'longitude' => 13.3888599,
                'registration' => 'EX AM 713',
                'color' => 'white',
                'brand' => 'Car brand',
                'charge' => [
                    'tbnr' => '141312',
                    'description' => 'Sie parkten im absoluten Haltverbot (Zeichen 283).',
                    'fine' => '25.0',
                    'bkat' => '§ 41 Abs. 1 iVm Anlage 2, § 49 StVO; § 24 Abs. 1, 3 Nr. 5 StVG; 52 BKat',
                    'penalty' => null,
                    'fap' => null,
                    'points' => 0,
                    'valid_from' => '2021-11-09T00:00:00.000+01:00',
                    'valid_to' => null,
                    'implementation' => null,
                    'classification' => 5,
                    'variant_table_id' => 741017,
                    'rule_id' => 39,
                    'table_id' => null,
                    'required_refinements' => '00000000000000000000000000000000',
                    'number_required_refinements' => 0,
                    'max_fine' => '0.0',
                    'created_at' => '2023-09-18T15:30:43.312+02:00',
                    'updated_at' => '2023-09-18T15:30:43.312+02:00',
                ],
                'tbnr' => '141312',
                'start_date' => '2023-11-12T11:31:00.000+01:00',
                'end_date' => '2023-11-12T11:36:00.000+01:00',
                'note' => 'Some user notes',
                'photos' => [
                    [
                        'filename' => 'IMG_20231124_113156.jpg',
                        'url' => 'https://example.com/storage/IMG_20231124_113156.jpg',
                    ],
                ],
                'created_at' => '2023-11-12T11:33:29.423+01:00',
                'updated_at' => '2023-11-12T11:49:24.383+01:00',
                'sent_at' => '2023-11-12T11:49:24.378+01:00',
                'vehicle_empty' => true,
                'hazard_lights' => false,
                'expired_tuv' => false,
                'expired_eco' => false,
                'over_2_8_tons' => false,
            ],
        ];

        $request = $this->createMock(RequestInterface::class);
        $request->expects($this->exactly(1))->method('withHeader')->willReturn($request);

        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $requestFactory->expects($this->exactly(1))->method('createRequest')->with('GET', 'https://www.weg.li/api/notices')->willReturn($request);

        $stream = $this->createConfiguredMock(
            StreamInterface::class,
            [
                '__toString' => json_encode($expected),
            ],
        );

        $response = $this->createConfiguredMock(
            ResponseInterface::class,
            [
                'getStatusCode' => 200,
                'getHeaderLine' => 'application/json',
                'getBody' => $stream,
            ]
        );

        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->expects($this->exactly(1))->method('sendRequest')->willReturn($response);

        $client = Client::create(
            $httpClient,
            $requestFactory,
        );

        $response = $client->listOwnNotices();

        $this->assertSame(
            $expected,
            $response,
        );
    }

    public function testListOwnNoticesThrowsClientException(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $request->expects($this->exactly(1))->method('withHeader')->willReturn($request);

        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $requestFactory->expects($this->exactly(1))->method('createRequest')->with('GET', 'https://www.weg.li/api/notices')->willReturn($request);

        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->expects($this->exactly(1))->method('sendRequest')->willThrowException(
            $this->createMock(ClientExceptionInterface::class),
        );

        $client = Client::create(
            $httpClient,
            $requestFactory,
        );

        $this->expectException(ClientExceptionInterface::class);
        $this->expectExceptionMessage('');

        $client->listOwnNotices();
    }

    public function testListOwnNoticesThrowsUnexpectedResponseExceptionOnWrongStatusCode(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $request->expects($this->exactly(1))->method('withHeader')->willReturn($request);

        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $requestFactory->expects($this->exactly(1))->method('createRequest')->with('GET', 'https://www.weg.li/api/notices')->willReturn($request);

        $response = $this->createConfiguredMock(
            ResponseInterface::class,
            [
                'getStatusCode' => 500,
            ]
        );

        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->expects($this->exactly(1))->method('sendRequest')->willReturn($response);

        $client = Client::create(
            $httpClient,
            $requestFactory,
        );

        $this->expectException(UnexpectedResponseException::class);
        $this->expectExceptionMessage('Server replied with the status code 500, but 200 was expected.');

        $client->listOwnNotices();
    }

    public function testListOwnNoticesThrowsUnexpectedResponseExceptionOnWrongContentTypeHeader(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $request->expects($this->exactly(1))->method('withHeader')->willReturn($request);

        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $requestFactory->expects($this->exactly(1))->method('createRequest')->with('GET', 'https://www.weg.li/api/notices')->willReturn($request);

        $response = $this->createConfiguredMock(
            ResponseInterface::class,
            [
                'getStatusCode' => 200,
                'getHeaderLine' => 'text/html',
            ]
        );

        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->expects($this->exactly(1))->method('sendRequest')->willReturn($response);

        $client = Client::create(
            $httpClient,
            $requestFactory,
        );

        $this->expectException(UnexpectedResponseException::class);
        $this->expectExceptionMessage('Server replied not with JSON content.');

        $client->listOwnNotices();
    }

    public function testListOwnNoticesThrowsUnexpectedResponseExceptionOnInvalidJsonBody(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $request->expects($this->exactly(1))->method('withHeader')->willReturn($request);

        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $requestFactory->expects($this->exactly(1))->method('createRequest')->with('GET', 'https://www.weg.li/api/notices')->willReturn($request);

        $stream = $this->createConfiguredMock(
            StreamInterface::class,
            [
                '__toString' => 'invalid json',
            ],
        );

        $response = $this->createConfiguredMock(
            ResponseInterface::class,
            [
                'getStatusCode' => 200,
                'getHeaderLine' => 'application/json',
                'getBody' => $stream,
            ]
        );

        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->expects($this->exactly(1))->method('sendRequest')->willReturn($response);

        $client = Client::create(
            $httpClient,
            $requestFactory,
        );

        $this->expectException(UnexpectedResponseException::class);
        $this->expectExceptionMessage('Response body contains no valid JSON: invalid json');

        $client->listOwnNotices();
    }

    public function testListOwnNoticesThrowsUnexpectedResponseExceptionOnJsonBodyWithoutArray(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $request->expects($this->exactly(1))->method('withHeader')->willReturn($request);

        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $requestFactory->expects($this->exactly(1))->method('createRequest')->with('GET', 'https://www.weg.li/api/notices')->willReturn($request);

        $stream = $this->createConfiguredMock(
            StreamInterface::class,
            [
                '__toString' => '"this is not an array"',
            ],
        );

        $response = $this->createConfiguredMock(
            ResponseInterface::class,
            [
                'getStatusCode' => 200,
                'getHeaderLine' => 'application/json',
                'getBody' => $stream,
            ]
        );

        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->expects($this->exactly(1))->method('sendRequest')->willReturn($response);

        $client = Client::create(
            $httpClient,
            $requestFactory,
        );

        $this->expectException(UnexpectedResponseException::class);
        $this->expectExceptionMessage('Response JSON does not contain an array: "this is not an array"');

        $client->listOwnNotices();
    }
}
