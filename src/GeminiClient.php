<?php

namespace Bdowson\SimpleGemini;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Cloud\AIPlatform\V1\Client\PredictionServiceClient;
use GuzzleHttp\Client;
use Bdowson\SimpleGemini\Exceptions\SimpleGeminiException;

class GeminiClient
{
	private string $accessToken;
	private Client $httpClient;
	private string $location;
	private string $model;
	private ?array $safetySettings;
	private ?array $generationConfig;

	public function __construct(
		string $googleCredentialsPath,
		string $project,
		string $location,
		string $model,
		?array $generationConfig = null,
		?array $safetySettings = null,
	)
	{
		$this->location = $location;
		$this->safetySettings = $safetySettings;
		$this->generationConfig = $generationConfig;

		$scopes = ['https://www.googleapis.com/auth/cloud-platform'];
		$credentials = new ServiceAccountCredentials($scopes, $googleCredentialsPath);
		$this->accessToken = $credentials->fetchAuthToken()['access_token'];
		$this->httpClient = new Client();

		$this->model = PredictionServiceClient::projectLocationPublisherModelName(
			$project,
			$location,
			'google',
			$model
		);
	}

	public function sendPrompt(string $prompt): string
	{
		$endpoint = "https://{$this->location}-aiplatform.googleapis.com/v1/{$this->model}:generateContent";

		$requestBody = [
			'contents' => [
				[
					'role' => 'user',
					'parts' => [['text' => $prompt]]
				]
			]
		];

		if ($this->safetySettings) {
			$requestBody['safetySettings'] = $this->safetySettings;
		}

		if ($this->generationConfig) {
			$requestBody['generationConfig'] = $this->generationConfig;
		}

		if(empty($requestBody['generationConfig']['maxOutputTokens'])) {
			$requestBody['generationConfig'] = [
				'maxOutputTokens' => 8000,
			];
		}

		try {
			$response = $this->httpClient->post($endpoint, [
				'headers' => [
					'Authorization' => "Bearer {$this->accessToken}",
					'Content-Type' => 'application/json',
				],
				'json' => $requestBody,
			]);

			$responseBody = json_decode($response->getBody()->getContents(), true);
			if (empty($responseBody['candidates'][0]['content']['parts'][0]['text'])) {
				throw new SimpleGeminiException('Empty response from Gemini AI.');
			}

			return $responseBody['candidates'][0]['content']['parts'][0]['text'];
		} catch (\Throwable $e) {
			throw new SimpleGeminiException('API request failed: ' . $e->getMessage());
		}
	}
}
