<?php

namespace Sevaske\PayfortApi;

use Sevaske\PayfortApi\Exceptions\PayfortSignatureException;
use Sevaske\PayfortApi\Interfaces\CredentialInterface;
use Throwable;

class Signature
{
    /**
     * The phrase used as part of the signature calculation
     */
    protected string $shaPhrase;

    /**
     * The hashing algorithm to use (default is 'sha256')
     */
    protected string $shaType;

    /**
     * Constructor for the PayfortSignature class.
     *
     * @param  string  $shaPhrase  The SHA phrase used for signature calculation.
     * @param  string  $shaType  The hashing algorithm to use (default: 'sha256').
     */
    public function __construct(string $shaPhrase, string $shaType = 'sha256')
    {
        $this->shaPhrase = $shaPhrase;
        $this->shaType = $shaType;
    }

    /**
     * Creates a Signature instance using credential data.
     *
     * This factory method constructs a Signature object based on the provided credential,
     * selecting either the request SHA phrase or the response SHA phrase based on the
     * `requestPhrase` flag. It also retrieves the hashing algorithm from the credential.
     *
     * @param  CredentialInterface  $credential  The credential object containing SHA phrases and hashing algorithm.
     * @param  bool  $requestPhrase  Determines whether to use the request SHA phrase (true) or the response SHA phrase (false).
     * @return Signature The generated Signature instance.
     */
    public static function fromCredential(CredentialInterface $credential, bool $requestPhrase): Signature
    {
        return new self(
            $requestPhrase ? $credential->shaRequestPhrase() : $credential->shaResponsePhrase(),
            $credential->shaType()
        );
    }

    /**
     * Calculates the signature based on the provided data.
     *
     * @param  array  $data  The data to be included in the signature.
     * @return string The generated SHA signature.
     *
     * @throws PayfortSignatureException
     */
    public function calculate(array $data): string
    {
        // sort data by key in ascending order
        ksort($data);

        // build the SHA string by concatenating the SHA phrase, parameters, and the SHA phrase again
        $shaString = $this->shaPhrase.$this->implodeParamsToString($data).$this->shaPhrase;

        // generate the hash using the specified SHA type
        try {
            return hash($this->shaType, $shaString);
        } catch (Throwable $e) {
            throw new PayfortSignatureException(
                'Failed to calculate signature.',
                $data,
                '',
                '',
                $this->shaPhrase,
                $this->shaType,
                $e,
            );
        }
    }

    /**
     * Concatenates array parameters into a string for signature calculation.
     * Applies special rules for specific parameter types (e.g., Apple data).
     *
     * @param  array  $arrayData  The array to be concatenated.
     * @return string The concatenated string of parameters.
     */
    protected function implodeParamsToString(array $arrayData): string
    {
        $shaString = '';

        foreach ($arrayData as $index => $value) {
            // skip installment detail parameter
            if ($index === 'installment_detail') {
                continue;
            }

            // special handling for Apple-specific parameters
            if (in_array($index, ['apple_header', 'apple_paymentMethod'])) {
                $shaString .= $index.'={'.$this->getAppleShaString($value).'}';
            } else {
                // default concatenation for other parameters
                $shaString .= $index.'='.$value;
            }
        }

        return $shaString;
    }

    /**
     * Handles concatenation for Apple-specific data.
     * Combines all elements into a single string separated by commas.
     *
     * @param  array  $appleParams  The array of Apple-related parameters.
     * @return string The concatenated string for Apple parameters.
     */
    protected function getAppleShaString(array $appleParams): string
    {
        $appleShaString = '';

        foreach ($appleParams as $index => $value) {
            // add a comma separator if this is not the first element
            if ($appleShaString) {
                $appleShaString .= ', ';
            }

            $appleShaString .= $index.'='.$value;
        }

        return $appleShaString;
    }

    /**
     * Verifies the signature in the provided data.
     *
     * This method checks if the `signature` exists in the data and verifies it
     * against the expected signature computed from the payload.
     *
     * @param  array  $data  The data containing the signature and payload.
     *
     * @throws PayfortSignatureException If the signature is missing or does not match.
     */
    public function verify(array $data): void
    {
        // ensure the signature field exists in the data
        if (! isset($data['signature'])) {
            throw new PayfortSignatureException(
                'The signature is not provided.',
                $data,
                '',
                '',
                $this->shaPhrase,
                $this->shaType,
            );
        }

        // extract payload excluding the signature
        $payload = $data;
        unset($payload['signature']);

        // calculate the expected signature
        $calculatedSignature = $this->calculate($payload);

        // compare the provided signature with the calculated one
        if ($data['signature'] !== $calculatedSignature) {
            throw new PayfortSignatureException(
                'Incorrect signature.',
                $payload,
                $calculatedSignature,
                $data['signature'],
                $this->shaPhrase,
                $this->shaType,
            );
        }
    }
}
