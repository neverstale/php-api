<?php

use neverstale\api\Client;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use neverstale\api\exceptions\ApiException;
use neverstale\api\models\Content;

function ClientFactory(array $responses): Client
{
    $mock = new MockHandler($responses);
    $handlerStack = HandlerStack::create($mock);
    $guzzleClient = new GuzzleClient(['handler' => $handlerStack]);

    return new Client([
        'apiKey' => '12345',
        'baseUri' => 'https://api.example.com',
    ], $guzzleClient);
}

it('has a healthcheck endpoint', function () {
    $client = ClientFactory([
        new Response(200, [], 'Success'),
        new Response(401, [], 'Not Authorized'),
    ]);

    $response = $client->health();
    expect($response)->toBeTrue();
    $response = $client->health();
    expect($response)->toBeFalse();
});

it('throws an ApiException for bad requests', function () {
    $client = ClientFactory([
        new Response(400, [], json_encode([
            'status' => 'error',
            'message' => 'Bad Request',
        ])),
    ]);

    expect(fn () => $client->retrieve(''))->toThrow(ApiException::class, 'Bad Request');
});


it('retrieves content as a Content model', function () {
    $client = ClientFactory([
        new Response(200, [], json_encode([
            'data' => [
                'id' => 'content-ulid-assigned-by-neverstale',
                'custom_id' => 'custom-id-provided-by-you',
                'analyzed_at' => '2024-11-11T20:51:43.000000Z',
                'expired_at' => null,
                'analysis_status' => 'pending-initial-analysis',
                'flags' => [
                    [
                        'id' => '01JCG8FHS9Z2FX7302XB7B3CFW',
                        'flag' => 'outdated security advice',
                        'reason' => 'The section discusses cryptographic hash functions such as MD5 and SHA-1, which are already considered insecure. Recommendations on these standards could become outdated as advancements in cryptography continue to evolve.',
                        'snippet' => 'MD5, SHA-1, or SHA-2 hash digests are sometimes published on websites or forums to allow verification of integrity for downloaded files.',
                        'last_analyzed_at' => '2024-11-12T13:19:49.000000Z',
                        'expired_at' => '2025-06-15T00:00:00.000000Z',
                        'ignored_at' => null,
                    ],
                    [
                        'id' => '01JCG8FHSRY7XD7J5DMS6353KC',
                        'flag' => 'outdated advice',
                        'reason' => 'The discussion around the longevity and applicability of hash functions like SHA-1, which has already been proven to be insecure, suggests that the recommendations could be outdated as new standards or functions might replace them in security protocols.',
                        'snippet' => 'Collisions against the full SHA-1 algorithm can be produced using the shattered attack and the hash function should be considered broken.',
                        'last_analyzed_at' => '2024-11-12T13:19:49.000000Z',
                        'expired_at' => '2025-10-01T00:00:00.000000Z',
                        'ignored_at' => null,
                    ],
                ],
            ],
        ])),
    ]);

    $content = $client->retrieve('custom-id-provided-by-you');

    expect($content)->toBeInstanceOf(Content::class)
        ->and($content->id)->toBe('content-ulid-assigned-by-neverstale')
        ->and($content->custom_id)->toBe('custom-id-provided-by-you')
        ->and($content->analyzed_at)->toBeInstanceOf(DateTime::class)
        ->and($content->analyzed_at->format('Y-m-d H:i:s'))->toBe('2024-11-11 20:51:43')
        ->and($content->analysis_status)->toBeInstanceOf(\neverstale\api\enums\AnalysisStatus::class)
        ->and($content->analysis_status->value)->toBe('pending-initial-analysis')
        ->and($content->flags)->toBeArray()->toHaveCount(2);
});

it('returns a TransactionResult for mutations', function () {
    $client = ClientFactory([
        new Response(200, [], json_encode([
            'status' => 'success',
            'message' => 'Flag ignored',
        ])),
        new Response(200, [], json_encode([
            'status' => 'success',
            'message' => 'Flag rescheduled',
        ])),
    ]);

    $result = $client->ignoreFlag('01JCG8FHS9Z2FX7302XB7B3CFW');

    expect($result)->toBeInstanceOf(\neverstale\api\models\TransactionResult::class)
        ->and($result->status)->toBe('success')
        ->and($result->message)->toBe('Flag ignored');

    $result = $client->rescheduleFlag('01JCG8FHS9Z2FX7302XB7B3CFW', new DateTime('2025-06-15 00:00:00'));

    expect($result)->toBeInstanceOf(\neverstale\api\models\TransactionResult::class)
        ->and($result->status)->toBe('success')
        ->and($result->message)->toBe('Flag rescheduled');
});
