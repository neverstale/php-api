<?php

namespace neverstale\api;

use DateTime;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use neverstale\api\exceptions\ApiException;
use neverstale\api\models\Content;
use neverstale\api\models\TransactionResult;

/**
 * Neverstale API Client
 *
 * @author Neverstale
 * @package neverstale/api
 * @since 1.0.0
 * @see http://docs.neverstale.io/api/
 */
class Client
{
    public const STATUS_SUCCESS = 'success';
    public const STATUS_ERROR = 'error';
    protected GuzzleClient $_client;
    public string $apiKey;
    public string $baseUri = 'https://app.neverstale.io/api/v1/';

    /**
     * @param array<string,mixed> $config
     */
    public function __construct(array $config = [], GuzzleClient $guzzleClient = null)
    {
        $this->apiKey = $config['apiKey'];
        $this->baseUri = $config['baseUri'] ?? $this->baseUri;

        $this->_client = $guzzleClient ?? new GuzzleClient([
            'base_uri' => $this->baseUri,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }
    /**
     * Ping the Neverstale API to check it is available and the API key is valid
     * @return bool
     */
    public function health(): bool
    {
        try {
            $response = $this->_client->get('health');
            return $response->getStatusCode() === 200;
        } catch (GuzzleException $e) {
            return false;
        }
    }

    /**
     * Ingest Content to Neverstale
     *
     * @see https://neverstale.io/docs/content.html#ingesting-content
     *
     * @param array<string,mixed> $data
     * @param array<string,mixed> $callbackConfig
     * @throws ApiException|Exception
     */
    public function ingest(array $data, array $callbackConfig = []): TransactionResult
    {
        try {
            $response = $this->_client->post('ingest', [
                'json' => array_merge($data, $callbackConfig),
            ]);

            $body = self::decodeResponseBody($response);
            $body['data'] = new Content($body['data']);

            return new TransactionResult($body);
        } catch (RequestException $e) {
            $headers = $e->getResponse()?->getHeaders() ?? [];
            throw new ApiException($e->getCode(), $e->getMessage(), $e, $headers);
        } catch (GuzzleException $e) {
            throw new ApiException($e->getCode(), $e->getMessage(), $e);
        }
    }

    /**
     * Batch delete content from Neverstale by content ID or custom ID
     *
     * @see https://neverstale.io/docs/content.html#deleting-content
     * @param array<int,string> $ids
     * @throws ApiException|Exception
     */
    public function batchDelete(array $ids): TransactionResult
    {
        try {
            $response = $this->_client->delete('content/batch', [
                'json' => [
                    'content_ids' => $ids,
                ],
            ]);

            $body = self::decodeResponseBody($response);
            $body['data'] = $body['deleted_contents'];

            return new TransactionResult($body);
        } catch (RequestException $e) {
            $headers = $e->getResponse()?->getHeaders() ?? [];
            throw new ApiException($e->getCode(), $e->getMessage(), $e, $headers);
        } catch (GuzzleException $e) {
            throw new ApiException($e->getCode(), $e->getMessage(), $e);
        }
    }
    /**
     * Retrieve content from Neverstale by content ID or custom ID
     *
     * @see https://neverstale.io/docs/content.html#retrieving-content
     *
     * @param string $id
     * @return Content
     * @throws ApiException|Exception
     */
    public function retrieve(string $id): Content
    {
        try {
            $response = $this->_client->get("content/$id");
            $body = self::decodeResponseBody($response);

            return new Content($body->first());
        } catch (RequestException $e) {
            $headers = $e->getResponse()?->getHeaders() ?? [];
            throw new ApiException($e->getCode(), $e->getMessage(), $e, $headers);
        } catch (GuzzleException $e) {
            throw new ApiException($e->getCode(), $e->getMessage(), $e);
        }
    }
    /**
     * Ignore a flag in Neverstale
     *
     * @see https://neverstale.io/docs/flags.html#ignoring-flags
     *
     * @throws ApiException|Exception
     */
    public function ignoreFlag(string $flagId): TransactionResult
    {
        try {
            $response = $this->_client->post("flags/$flagId/ignore");
            $body = self::decodeResponseBody($response);
            return new TransactionResult($body);
        } catch (RequestException $e) {
            $headers = $e->getResponse()?->getHeaders() ?? [];
            throw new ApiException($e->getCode(), $e->getMessage(), $e, $headers);
        } catch (GuzzleException $e) {
            throw new ApiException($e->getCode(), $e->getMessage(), $e);
        }
    }
    /**
     * Reschedule a flag in Neverstale
     *
     * @see https://neverstale.io/docs/flags.html#rescheduling-flags
     *
     * @throws ApiException|Exception
     */
    public function rescheduleFlag(string $flagId, DateTime $expiredAt): TransactionResult
    {
        try {
            $response = $this->_client->post("flags/$flagId/reschedule", [
                'json' => [
                    'expired_at' => $expiredAt->format('Y-m-d H:i:s'),
                ],
            ]);
            $body = self::decodeResponseBody($response);
            return new TransactionResult($body);
        } catch (RequestException $e) {
            $headers = $e->getResponse()?->getHeaders() ?? [];
            throw new ApiException($e->getCode(), $e->getMessage(), $e, $headers);
        } catch (GuzzleException $e) {
            throw new ApiException($e->getCode(), $e->getMessage(), $e);
        }
    }
    /**
     * Decode the response body from a Guzzle response
     *
     * @param ResponseInterface $response
     * @return Collection<string,mixed>
     */
    private static function decodeResponseBody(ResponseInterface $response): Collection
    {
        return self::collect(json_decode($response->getBody()->getContents(), true));
    }
    /**
     * Collect data into a keyed collection
     *
     * @param array<string,mixed> $data
     * @return Collection<string,mixed>
     */
    private static function collect(array $data): Collection
    {
        return collect($data);
    }
}
