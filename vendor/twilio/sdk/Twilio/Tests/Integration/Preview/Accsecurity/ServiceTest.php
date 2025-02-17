<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Tests\Integration\Preview\Accsecurity;

use Twilio\Exceptions\DeserializeException;
use Twilio\Exceptions\TwilioException;
use Twilio\Http\Response;
use Twilio\Tests\HolodeckTestCase;
use Twilio\Tests\Request;

class ServiceTest extends HolodeckTestCase {
    public function testCreateRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->preview->accSecurity->services->create("name");
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $values = array('Name' => "name",);

        $this->assertRequest(new Request(
            'post',
            'https://preview.twilio.com/Verification/Services',
            null,
            $values
        ));
    }

    public function testCreateRecordResponse() {
        $this->holodeck->mock(new Response(
            201,
            '
            {
                "sid": "VAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "account_sid": "ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "name": "name",
                "code_length": 4,
                "date_created": "2025-07-30T20:00:00Z",
                "date_updated": "2025-07-30T20:00:00Z",
                "url": "https://preview.twilio.com/Verification/Services/VAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "links": {
                    "verification_checks": "https://preview.twilio.com/Verification/Services/VAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/VerificationCheck",
                    "verifications": "https://preview.twilio.com/Verification/Services/VAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Verifications"
                }
            }
            '
        ));

        $actual = $this->twilio->preview->accSecurity->services->create("name");

        $this->assertNotNull($actual);
    }

    public function testFetchRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->preview->accSecurity->services("VAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->fetch();
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $this->assertRequest(new Request(
            'get',
            'https://preview.twilio.com/Verification/Services/VAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'
        ));
    }

    public function testFetchRecordResponse() {
        $this->holodeck->mock(new Response(
            200,
            '
            {
                "sid": "VAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "account_sid": "ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "name": "name",
                "code_length": 4,
                "date_created": "2025-07-30T20:00:00Z",
                "date_updated": "2025-07-30T20:00:00Z",
                "url": "https://preview.twilio.com/Verification/Services/VAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "links": {
                    "verification_checks": "https://preview.twilio.com/Verification/Services/VAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/VerificationCheck",
                    "verifications": "https://preview.twilio.com/Verification/Services/VAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Verifications"
                }
            }
            '
        ));

        $actual = $this->twilio->preview->accSecurity->services("VAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->fetch();

        $this->assertNotNull($actual);
    }

    public function testReadRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->preview->accSecurity->services->read();
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $this->assertRequest(new Request(
            'get',
            'https://preview.twilio.com/Verification/Services'
        ));
    }

    public function testReadAllResponse() {
        $this->holodeck->mock(new Response(
            200,
            '
            {
                "meta": {
                    "page": 0,
                    "page_size": 50,
                    "first_page_url": "https://preview.twilio.com/Verification/Services?PageSize=50&Page=0",
                    "previous_page_url": null,
                    "next_page_url": null,
                    "key": "services",
                    "url": "https://preview.twilio.com/Verification/Services?PageSize=50&Page=0"
                },
                "services": [
                    {
                        "sid": "VAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                        "account_sid": "ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                        "name": "name",
                        "code_length": 4,
                        "date_created": "2025-07-30T20:00:00Z",
                        "date_updated": "2025-07-30T20:00:00Z",
                        "url": "https://preview.twilio.com/Verification/Services/VAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                        "links": {
                            "verification_checks": "https://preview.twilio.com/Verification/Services/VAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/VerificationCheck",
                            "verifications": "https://preview.twilio.com/Verification/Services/VAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Verifications"
                        }
                    }
                ]
            }
            '
        ));

        $actual = $this->twilio->preview->accSecurity->services->read();

        $this->assertNotNull($actual);
    }

    public function testUpdateRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->preview->accSecurity->services("VAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->update();
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $this->assertRequest(new Request(
            'post',
            'https://preview.twilio.com/Verification/Services/VAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'
        ));
    }

    public function testUpdateRecordResponse() {
        $this->holodeck->mock(new Response(
            200,
            '
            {
                "sid": "VAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "account_sid": "ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "name": "name",
                "code_length": 4,
                "date_created": "2025-07-30T20:00:00Z",
                "date_updated": "2025-07-30T20:00:00Z",
                "url": "https://preview.twilio.com/Verification/Services/VAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "links": {
                    "verification_checks": "https://preview.twilio.com/Verification/Services/VAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/VerificationCheck",
                    "verifications": "https://preview.twilio.com/Verification/Services/VAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Verifications"
                }
            }
            '
        ));

        $actual = $this->twilio->preview->accSecurity->services("VAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->update();

        $this->assertNotNull($actual);
    }
}