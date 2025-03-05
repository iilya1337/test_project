<?php

namespace App\Services;

class GenerateTextAI
{
    public function __construct(
        private readonly string $key,
        private ?string $query = null
    )
    {
    }

    public function createQueryString(mixed $query): static
    {
        $this->query = implode('%20', explode(' ', $query));

        return $this;
    }

    public function generateTexT(): string
    {
        $curl = curl_init();

        curl_setopt_array($curl,
            [CURLOPT_URL =>
                "https://generate-text-ai-gemini.p.rapidapi.com/api/v1/text?prompt=$this->query",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => [
                    "x-rapidapi-host: generate-text-ai-gemini.p.rapidapi.com",
                    "x-rapidapi-key: $this->key",],
            ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }
}