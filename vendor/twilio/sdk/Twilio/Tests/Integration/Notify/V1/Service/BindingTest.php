<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Tests\Integration\Notify\V1\Service;

use Twilio\Exceptions\DeserializeException;
use Twilio\Exceptions\TwilioException;
use Twilio\Http\Response;
use Twilio\Tests\HolodeckTestCase;
use Twilio\Tests\Request;

class BindingTest extends HolodeckTestCase {
    public function testFetchRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->notify->v1->services("ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                     ->bindings("BSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->fetch();
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $this->assertRequest(new Request(
            'get',
            'https://notify.twilio.com/v1/Services/ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Bindings/BSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'
        ));
    }

    public function testFetchResponse() {
        $this->holodeck->mock(new Response(
            200,
            '
            {
                "account_sid": "ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "address": "a7c658f4111ec4ff5a1a647f9d0edd819025b9f20522d2fae897049f32873e73",
                "binding_type": "apn",
                "credential_sid": null,
                "date_created": "2025-07-30T20:00:00Z",
                "date_updated": "2025-07-30T20:00:00Z",
                "endpoint": "26607274",
                "identity": "24987039",
                "notification_protocol_version": "3",
                "service_sid": "ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "sid": "BSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "tags": [
                    "26607274"
                ],
                "links": {
                    "user": "https://notify.twilio.com/v1/Services/ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Users/24987039"
                },
                "url": "https://notify.twilio.com/v1/Services/ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Bindings/BSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa"
            }
            '
        ));

        $actual = $this->twilio->notify->v1->services("ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                           ->bindings("BSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->fetch();

        $this->assertNotNull($actual);
    }

    public function testDeleteRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->notify->v1->services("ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                     ->bindings("BSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->delete();
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $this->assertRequest(new Request(
            'delete',
            'https://notify.twilio.com/v1/Services/ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Bindings/BSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'
        ));
    }

    public function testDeleteResponse() {
        $this->holodeck->mock(new Response(
            204,
            null
        ));

        $actual = $this->twilio->notify->v1->services("ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                           ->bindings("BSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->delete();

        $this->assertTrue($actual);
    }

    public function testCreateRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->notify->v1->services("ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                     ->bindings->create("identity", "apn", "address");
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $values = array('Identity' => "identity", 'BindingType' => "apn", 'Address' => "address",);

        $this->assertRequest(new Request(
            'post',
            'https://notify.twilio.com/v1/Services/ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Bindings',
            null,
            $values
        ));
    }

    public function testCreateResponse() {
        $this->holodeck->mock(new Response(
            201,
            '
            {
                "account_sid": "ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "address": "a7c658f4111ec4ff5a1a647f9d0edd819025b9f20522d2fae897049f32873e73",
                "binding_type": "apn",
                "credential_sid": null,
                "date_created": "2025-07-30T20:00:00Z",
                "date_updated": "2025-07-30T20:00:00Z",
                "endpoint": "26607274",
                "identity": "24987039",
                "notification_protocol_version": "3",
                "service_sid": "ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "sid": "BSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "tags": [
                    "26607274"
                ],
                "links": {
                    "user": "https://notify.twilio.com/v1/Services/ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Users/24987039"
                },
                "url": "https://notify.twilio.com/v1/Services/ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Bindings/BSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa"
            }
            '
        ));

        $actual = $this->twilio->notify->v1->services("ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                           ->bindings->create("identity", "apn", "address");

        $this->assertNotNull($actual);
    }

    public function testReadRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->notify->v1->services("ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                     ->bindings->read();
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $this->assertRequest(new Request(
            'get',
            'https://notify.twilio.com/v1/Services/ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Bindings'
        ));
    }

    public function testReadEmptyResponse() {
        $this->holodeck->mock(new Response(
            200,
            '
            {
                "bindings": [],
                "meta": {
                    "first_page_url": "https://notify.twilio.com/v1/Services/ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Bindings?PageSize=50&Page=0",
                    "key": "bindings",
                    "next_page_url": null,
                    "page": 0,
                    "page_size": 50,
                    "previous_page_url": null,
                    "url": "https://notify.twilio.com/v1/Services/ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Bindings?PageSize=50&Page=0"
                }
            }
            '
        ));

        $actual = $this->twilio->notify->v1->services("ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                           ->bindings->read();

        $this->assertNotNull($actual);
    }

    public function testReadFullResponse() {
        $this->holodeck->mock(new Response(
            200,
            '
            {
                "bindings": [
                    {
                        "account_sid": "ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                        "address": "a7c658f4111ec4ff5a1a647f9d0edd819025b9f20522d2fae897049f32873e73",
                        "binding_type": "apn",
                        "credential_sid": null,
                        "date_created": "2025-07-30T20:00:00Z",
                        "date_updated": "2025-07-30T20:00:00Z",
                        "endpoint": "26607274",
                        "identity": "24987039",
                        "notification_protocol_version": "3",
                        "service_sid": "ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                        "sid": "BSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                        "tags": [
                            "26607274"
                        ],
                        "links": {
                            "user": "https://notify.twilio.com/v1/Services/ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Users/24987039"
                        },
                        "url": "https://notify.twilio.com/v1/Services/ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Bindings/BSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa"
                    }
                ],
                "meta": {
                    "first_page_url": "https://notify.twilio.com/v1/Services/ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Bindings?PageSize=50&Page=0",
                    "key": "bindings",
                    "next_page_url": null,
                    "page": 0,
                    "page_size": 50,
                    "previous_page_url": null,
                    "url": "https://notify.twilio.com/v1/Services/ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Bindings?PageSize=50&Page=0"
                }
            }
            '
        ));

        $actual = $this->twilio->notify->v1->services("ISaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                           ->bindings->read();

        $this->assertGreaterThan(0, count($actual));
    }
}