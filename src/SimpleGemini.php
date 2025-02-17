<?php

namespace SimpleGemini;

use SimpleGemini\Exceptions\SimpleGeminiException;

class SimpleGemini
{
	private GeminiClient $client;

	public function __construct(
		string $googleCredentialsPath,
		string $project,
		string $location,
		string $model,
		?array $generationConfig = null,
		?array $safetySettings = null,
	)
	{
		$this->client = new GeminiClient($googleCredentialsPath, $project, $location, $model, $generationConfig, $safetySettings);
	}

	public function prompt(string $prompt): string
	{
		try {
			return $this->client->sendPrompt($prompt);
		} catch (\Throwable $e) {
			throw new SimpleGeminiException('Error generating response: ' . $e->getMessage());
		}
	}
}
