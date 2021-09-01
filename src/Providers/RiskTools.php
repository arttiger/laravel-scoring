<?php

namespace Arttiger\Scoring\Providers;

use Arttiger\Scoring\Interfaces\Provider;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;

class RiskTools implements Provider
{
    const URL_GET_SCORING = '/scoring/get_score'; // get scoring
    const URL_REPORT_UBKI = '/ubki/get_reports'; // get UBKI reports
    const URL_FEEDBACK = '/apps/upsert_bulk'; // updates of statuses of loans
    const URL_GET_PRE_SCORING = '/scoring/get_prescore'; // get pre-scoring

    protected $requestUrl;
    protected $secretKey;
    protected $params;
    protected $request;
    protected $mapScoring;
    protected $sync;

    protected $exeptions = [
        'ubki',
        'status_id',
        'closed_at',
        'amount_to_pay',
        'total_paid',
        'overdue_days',
    ];

    /**
     * RiskTools constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (! isset($config['url']) || $config['url'] === '') {
            throw new \InvalidArgumentException('URL must be string and not empty');
        }

        if (! isset($config['key']) || $config['key'] === '') {
            throw new \InvalidArgumentException('Key must be string and not empty');
        }
        $this->requestUrl = $config['url'] ?? null;
        $this->secretKey = $config['key'] ?? null;
        $this->sync = $config['sync'] ?? null;
        $this->mapScoring = $config['model_data'] ?? null;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getScoring(array $params = []): array
    {
        $this->params = $params;
        $this->request = self::requestScoring();

        return self::query(self::URL_GET_SCORING);
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getPreScoring(array $params = []): array
    {
        $this->params = $params;
        $this->request = self::requestPreScoring();

        return self::query(self::URL_GET_PRE_SCORING);
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function sendFeedback(array $params = []): array
    {
        $this->params = $params;
        $this->request = self::requestFeedback();

        return self::query(self::URL_FEEDBACK);
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getUbki(array $params = []): array
    {
        $this->params = $params;
        $this->request = self::requestGetUbki();

        return self::query(self::URL_REPORT_UBKI);
    }

    /**
     * @return string[]
     */
    private function headers()
    {
        return [
            'Content-type' => 'application/json',
            'AuthKey'      => $this->secretKey,
        ];
    }

    /**
     * Send a request to RiskTools and get a response.
     *
     * @param $method
     *
     * @return mixed
     */
    private function query($method)
    {
        if ($this->requestUrl && $this->request) {
            $client = new Client();
            $request = new Request(
                'POST',
                $this->requestUrl.$method,
                self::headers(),
                $this->request
            );

            try {
                $response = $client->send($request);
                if ($response->getStatusCode() == 200) {
                    $result = json_decode($response->getBody()->getContents(), true);
                    if ($result['status'] == 'error') {
                        Log::error('Response error', [static::class, $result]);
                    }

                    return $result;
                } else {
                    Log::error('No response to scoring request received.', [static::class, $response]);
                }
            } catch (\Exception $e) {
                $error = $e->getMessage();
                Log::error('Error GuzzleHttp Client.', [static::class, $error]);
            }
        }

        return [];
    }

    /**
     * @return false|string|void
     */
    private function requestScoring()
    {
        if (is_array($this->mapScoring)) {
            foreach ($this->mapScoring as $map_key => $map_value) {
                if (! in_array($map_key, $this->exeptions)) {
                    if (isset($this->params[$map_value])) {
                        $parameters[$map_key] = $this->params[$map_value];
                    } else {
                        Log::error('Mapping model does not contain an parameter (attribute): '.$map_value, [static::class]);
                    }
                }
            }
        } else {
            Log::error('Mapping model data is empty.', [static::class]);
        }

        $application['application'] = isset($parameters) ? $parameters : '';
        if (isset($this->params['ubki'])) {
            $application['ubki'] = $this->params['ubki'];
        }

        return json_encode($application);
    }

    /**
     * @return false|string|void
     */
    private function requestPreScoring()
    {
        $social_number = $this->params['social_number'];

        return json_encode(['social_number' => isset($social_number) ? $social_number : '']);
    }

    /**
     * @return false|string|void
     */
    private function requestFeedback()
    {
        if (is_array($this->mapScoring)) {
            foreach ($this->mapScoring as $map_key => $map_value) {
                if ($map_key != 'ubki') {
                    if (isset($this->params[$map_value])) {
                        $parameters[$map_key] = $this->params[$map_value];
                    }
                }
            }
        } else {
            Log::error('Mapping model data is empty.', [static::class]);
        }
        if ($this->sync) {
            $application['sync'] = $this->sync;
        }
        $application['applications'][] = isset($parameters) ? $parameters : '';

        return json_encode($application);
    }

    /**
     * @return false|string|void
     */
    private function requestGetUbki()
    {
        if (is_array($this->mapScoring)) {
            $map_value = $this->mapScoring['id'];
            if (isset($this->params[$map_value])) {
                $application_id = $this->params[$map_value];
            } else {
                Log::error('Mapping model does not contain an parameter (attribute): '.$map_value, [static::class]);
            }
        } else {
            Log::error('Mapping model data is empty.', [static::class]);
        }

        return json_encode(['application_id' => isset($application_id) ? $application_id : '']);
    }
}
