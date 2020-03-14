<?php

namespace Rokolabs\ROKOMobi;

use Rokolabs\ROKOMobi\ClientParams\UploadFile;
use Rokolabs\ROKOMobi\Result\UserSession;

class Client
{
    /**
     * @var Credential
     */
    private $credential;

    /**
     * @param Credential $credential
     */
    public function __construct(Credential $credential)
    {
        $this->credential = $credential;
    }

    /**
     * @param string $path
     * @param array $params
     * @param UserSession|null $userSession
     * @return Response
     */
    public function get($path, array $params = [], $userSession = null)
    {
        $curlHandle = curl_init($this->makeApiUrl($path, $params));

        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $this->makeApiHeaders($userSession));
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($curlHandle);
        curl_close($curlHandle);

        return new Response((string) $response);
    }

    /**
     * @param string $path
     * @param array $params
     * @param UserSession|null $userSession
     * @return Response
     */
    public function post($path, array $params = [], UserSession $userSession = null)
    {
        $json = json_encode($params);

        $curlHandle = curl_init($this->makeApiUrl($path));

        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);

        $headers = $this->makeApiHeaders($userSession);
        $headers[] = 'Content-Length: ' . strlen($json);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curlHandle, CURLOPT_POST, true);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $json);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($curlHandle);
        curl_close($curlHandle);

        return new Response((string) $response);
    }

    /**
     * @param string $path
     * @param array $params
     * @param UserSession|null $userSession
     * @return Response
     */
    public function delete($path, array $params = [], UserSession $userSession = null)
    {
        $curlHandle = curl_init($this->makeApiUrl($path, $params));

        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $this->makeApiHeaders($userSession));
        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($curlHandle);
        curl_close($curlHandle);

        return new Response((string) $response);
    }

    /**
     * @param string $url
     * @param UploadFile $file
     * @param array $headers
     * @return Response
     */
    public function customUpload($url, UploadFile $file, array $headers = [])
    {
        $curlHandle = curl_init($url);

        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curlHandle, CURLOPT_PUT, true);
        curl_setopt($curlHandle, CURLOPT_INFILE, $file->getResource());
        curl_setopt($curlHandle, CURLOPT_INFILESIZE, $file->getSize());
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($curlHandle);
        curl_close($curlHandle);

        return new Response((string) $response);
    }

    /**
     * @param string $path
     * @param array $params
     * @param UserSession|null $userSession
     * @return Response
     */
    public function put($path, array $params = [], UserSession $userSession = null)
    {
        $json = json_encode($params);

        $curlHandle = curl_init($this->makeApiUrl($path));

        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);

        $headers = $this->makeApiHeaders($userSession);
        $headers[] = 'Content-Length: ' . strlen($json);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $json);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($curlHandle);
        curl_close($curlHandle);

        return new Response((string) $response);
    }

    /**
     * @param $path
     * @param array $getParams
     * @return string
     */
    private function makeApiUrl($path, array $getParams = [])
    {
        $queryParams = [];

        foreach ($getParams as $key => $value) {
            $queryParams[] = sprintf('%s=%s', $key, $value);
        }

        $apiUrl = ltrim($this->credential->getApiBaseUrl(), '/') . '/' . trim($path, '/');

        if (!$queryParams) {
            return $apiUrl;
        }

        return  $apiUrl . '?' . implode('&', $queryParams);
    }

    /**
     * @param UserSession $session
     * @return array
     */
    private function makeApiHeaders(UserSession $session = null)
    {
        $headers =  [
            'Content-type: application/json',
            'X-ROKO-Mobi-Api-Key: ' . $this->credential->getApiKey()
        ];

        if (empty($session)) {
            $headers[] = 'Authorization: X-ROKO-Mobi-Master-Api-Key ' . $this->credential->getMasterApiKey();
        } else {
            $headers[] = 'Authorization: X-ROKO-Mobi-User-Session ' . $session->getSessionKey();
        }

        return $headers;
    }
}
