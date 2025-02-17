<?php

namespace SimpleGemini;

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;
use SimpleGemini\Exceptions\SimpleGeminiException;

class GeminiClient
{
	private string $accessToken;
	private Client $httpClient;
	private string $project;
	private string $location;
	private string $model;
	private ?array $safetySettings;
	private ?array $generationConfig;

	public function __construct(
		string $googleCredentialsPath,
		string $project,
		string $location,
		string $model,
		?array $safetySettings = null,
		?array $generationConfig = null
	)
	{
		$this->project = $project;
		$this->location = $location;
		$this->model = $model;
		$this->safetySettings = $safetySettings;
		$this->generationConfig = $generationConfig;

		$scopes = ['https://www.googleapis.com/auth/cloud-platform'];
		$credentials = new ServiceAccountCredentials($scopes, $googleCredentialsPath);
		$this->accessToken = $credentials->fetchAuthToken()['access_token'];
		$this->httpClient = new Client();
	}

	public function sendPrompt(string $prompt): array
	{
		$endpoint = "https://{$this->location}-aiplatform.googleapis.com/v1/projects/{$this->project}/locations/{$this->location}/publishers/google/models/{$this->model}:generateContent";

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
		} else {
			$requestBody['generationConfig'] = [
				'temperature' => 1,
    			'maxOutputTokens' => 8000,
				'responseMimeType' => 'application/json',
			];
		}

		try {
			$response = $this->httpClient->post($endpoint, [
				'headers' => [
					'Authorization' => 'Bearer {$this->accessToken}',
					'Content-Type' => 'application/json',
				],
				'json' => $requestBody,
			]);

			$responseBody = json_decode($response->getBody()->getContents(), true);

			if (empty($responseBody['candidates'][0]['content']['parts'][0]['text'])) {
				throw new SimpleGeminiException('Empty response from Gemini AI.');
			}

			return json_decode($responseBody['candidates'][0]['content']['parts'][0]['text'], true);
		} catch (\Throwable $e) {
			throw new SimpleGeminiException('API request failed: ' . $e->getMessage());
		}
	}
}
