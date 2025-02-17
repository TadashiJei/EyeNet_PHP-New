<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Tests\Integration\Proxy\V1\Service;

use Twilio\Exceptions\DeserializeException;
use Twilio\Exceptions\TwilioException;
use Twilio\Http\Response;
use Twilio\Tests\HolodeckTestCase;
use Twilio\Tests\Request;

class SessionTest extends HolodeckTestCase {
    public function testFetchRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->proxy->v1->services("KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                    ->sessions("KCaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->fetch();
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $this->assertRequest(new Request(
            'get',
            'https://proxy.twilio.com/v1/Services/KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Sessions/KCaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'
        ));
    }

    public function testFetchResponse() {
        $this->holodeck->mock(new Response(
            200,
            '
            {
                "service_sid": "KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "status": "in-progess",
                "unique_name": "unique_name",
                "date_started": "2025-07-30T20:00:00Z",
                "date_ended": "2025-07-30T20:00:00Z",
                "date_last_interaction": "2025-07-30T20:00:00Z",
                "date_expiry": "2025-07-30T20:00:00Z",
                "ttl": 3600,
                "closed_reason": "",
                "sid": "KCaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "date_updated": "2025-07-30T20:00:00Z",
                "date_created": "2025-07-30T20:00:00Z",
                "account_sid": "ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "url": "https://proxy.twilio.com/v1/Services/KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Sessions/KCaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "links": {
                    "interactions": "https://proxy.twilio.com/v1/Services/KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Sessions/KCaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Interactions",
                    "participants": "https://proxy.twilio.com/v1/Services/KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Sessions/KCaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Participants"
                }
            }
            '
        ));

        $actual = $this->twilio->proxy->v1->services("KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                          ->sessions("KCaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->fetch();

        $this->assertNotNull($actual);
    }

    public function testReadRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->proxy->v1->services("KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                    ->sessions->read();
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $this->assertRequest(new Request(
            'get',
            'https://proxy.twilio.com/v1/Services/KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Sessions'
        ));
    }

    public function testReadEmptyResponse() {
        $this->holodeck->mock(new Response(
            200,
            '
            {
                "sessions": [],
                "meta": {
                    "previous_page_url": null,
                    "next_page_url": null,
                    "url": "https://proxy.twilio.com/v1/Services/KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Sessions?PageSize=50&Page=0",
                    "page": 0,
                    "first_page_url": "https://proxy.twilio.com/v1/Services/KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Sessions?PageSize=50&Page=0",
                    "page_size": 50,
                    "key": "sessions"
                }
            }
            '
        ));

        $actual = $this->twilio->proxy->v1->services("KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                          ->sessions->read();

        $this->assertNotNull($actual);
    }

    public function testCreateRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->proxy->v1->services("KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                    ->sessions->create();
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $this->assertRequest(new Request(
            'post',
            'https://proxy.twilio.com/v1/Services/KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Sessions'
        ));
    }

    public function testCreateResponse() {
        $this->holodeck->mock(new Response(
            201,
            '
            {
                "service_sid": "KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "status": "in-progess",
                "unique_name": "unique_name",
                "date_started": "2025-07-30T20:00:00Z",
                "date_ended": "2025-07-30T20:00:00Z",
                "date_last_interaction": "2025-07-30T20:00:00Z",
                "date_expiry": "2025-07-30T20:00:00Z",
                "ttl": 3600,
                "closed_reason": "",
                "sid": "KCaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "date_updated": "2025-07-30T20:00:00Z",
                "date_created": "2025-07-30T20:00:00Z",
                "account_sid": "ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "url": "https://proxy.twilio.com/v1/Services/KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Sessions/KCaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "links": {
                    "interactions": "https://proxy.twilio.com/v1/Services/KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Sessions/KCaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Interactions",
                    "participants": "https://proxy.twilio.com/v1/Services/KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Sessions/KCaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Participants"
                }
            }
            '
        ));

        $actual = $this->twilio->proxy->v1->services("KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                          ->sessions->create();

        $this->assertNotNull($actual);
    }

    public function testDeleteRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->proxy->v1->services("KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                    ->sessions("KCaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->delete();
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $this->assertRequest(new Request(
            'delete',
            'https://proxy.twilio.com/v1/Services/KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Sessions/KCaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'
        ));
    }

    public function testDeleteResponse() {
        $this->holodeck->mock(new Response(
            204,
            null
        ));

        $actual = $this->twilio->proxy->v1->services("KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                          ->sessions("KCaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->delete();

        $this->assertTrue($actual);
    }

    public function testUpdateRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->proxy->v1->services("KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                    ->sessions("KCaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->update();
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $this->assertRequest(new Request(
            'post',
            'https://proxy.twilio.com/v1/Services/KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Sessions/KCaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'
        ));
    }

    public function testUpdateResponse() {
        $this->holodeck->mock(new Response(
            200,
            '
            {
                "service_sid": "KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "status": "in-progess",
                "unique_name": "unique_name",
                "date_started": "2025-07-30T20:00:00Z",
                "date_ended": "2025-07-30T20:00:00Z",
                "date_last_interaction": "2025-07-30T20:00:00Z",
                "date_expiry": "2025-07-30T20:00:00Z",
                "ttl": 3600,
                "closed_reason": "",
                "sid": "KCaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "date_updated": "2025-07-30T20:00:00Z",
                "date_created": "2025-07-30T20:00:00Z",
                "account_sid": "ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "url": "https://proxy.twilio.com/v1/Services/KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Sessions/KCaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "links": {
                    "interactions": "https://proxy.twilio.com/v1/Services/KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Sessions/KCaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Interactions",
                    "participants": "https://proxy.twilio.com/v1/Services/KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Sessions/KCaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Participants"
                }
            }
            '
        ));

        $actual = $this->twilio->proxy->v1->services("KSaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                          ->sessions("KCaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->update();

        $this->assertNotNull($actual);
    }
}