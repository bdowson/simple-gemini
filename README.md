# Simple Gemini

**Simple Gemini** is a lightweight PHP package that lets you send prompts to Google's Gemini AI and retrieve generated responses. It handles authentication using your Google service account credentials and makes API calls to the Google Cloud AI Platform.

> **Note:** You must have a valid Google service account JSON file with the necessary permissions and your Google Cloud project must be properly configured.

---

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [Configuration](#configuration)
- [To Dos](#todo)
- [License](#license)
- [Support](#support)

---

## Installation

Install the package via Composer:

```bash
composer require bdowson/simple-gemini
```

---

## Usage

Below is a simple example to get you started:

```php
<?php

require 'vendor/autoload.php';

use SimpleGemini\SimpleGemini;
use SimpleGemini\Exceptions\SimpleGeminiException;

try {
    $googleCredentialsPath = '/path/to/credentials.json';
    $project  = 'your-project-id';
    $location = 'asia-southeast1';
    $model    = 'gemini-1.5-flash-002';

    // Optional: Specify generation configuration and safety settings
    $generationConfig = [
        'maxOutputTokens' => 8000,
        // Additional configuration parameters (e.g., temperature, topP, etc.) can be added here.
    ];
    $safetySettings = [
        // Optional safety settings can be specified here.
    ];

    // Instantiate the SimpleGemini client
    $simpleGemini = new SimpleGemini(
        $googleCredentialsPath,
        $project,
        $location,
        $model,
        $generationConfig,
        $safetySettings
    );

    // Send a prompt and get the generated response
    $prompt   = "Write me a poem about software development";
    $response = $simpleGemini->prompt($prompt);

    echo "AI Response:\n" . $response . "\n";
} catch (SimpleGeminiException $e) {
    echo "An error occurred: " . $e->getMessage() . "\n";
}
```

---

## Configuration

When creating a new instance of `SimpleGemini`, you need to provide:

- **Google Credentials Path:**  
  The file path to your Google service account JSON file.

- **Project:**  
  Your Google Cloud project ID.

- **Location:**  
  The location/region where your model is hosted. See here for supported locations: https://cloud.google.com/vertex-ai/docs/general/locations#available-regions

- **Model:**  
  The name of the model you want to use. See here for supported models: https://cloud.google.com/vertex-ai/generative-ai/docs/model-reference/inference#supported-models

- **Generation Config (Optional):**  
  An array of configuration options for text generation. For example:

  ```php
  $generationConfig = [
      'maxOutputTokens' => 8000,
      'temperature' => 1,
      // You can also include other parameters like 'topP', 'topK', etc.
  ];
  ```
  For more options, see here: https://cloud.google.com/vertex-ai/generative-ai/docs/model-reference/inference#generationconfig

- **Safety Settings (Optional):**  
  An array specifying safety settings for the generated content. Available settings: https://cloud.google.com/vertex-ai/generative-ai/docs/model-reference/inference#safetysetting

---

## To DOs

- Automated testing
- Improve documentation
---

## License

This project is licensed under the GNU General Public License. See the [LICENSE](LICENSE) file for details.

---

## Support

If you encounter issues or have suggestions, please open an issue on this repo

---

Happy coding!

