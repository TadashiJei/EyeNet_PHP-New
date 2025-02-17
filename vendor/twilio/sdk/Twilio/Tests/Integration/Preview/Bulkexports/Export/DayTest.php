<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Tests\Integration\Preview\Bulkexports\Export;

use Twilio\Exceptions\DeserializeException;
use Twilio\Exceptions\TwilioException;
use Twilio\Http\Response;
use Twilio\Tests\HolodeckTestCase;
use Twilio\Tests\Request;

class DayTest extends HolodeckTestCase {
    public function testReadRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->preview->bulkExports->exports("resourceType")
                                               ->days->read();
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $this->assertRequest(new Request(
            'get',
            'https://preview.twilio.com/BulkExports/Exports/resourceType/Days'
        ));
    }

    public function testReadResponse() {
        $this->holodeck->mock(new Response(
            200,
            '
            {
                "days": [
                    {
                        "day": "2025-05-01",
                        "size": 1234,
                        "resource_type": "Calls"
                    }
                ],
                "meta": {
                    "key": "days",
                    "page_size": 50,
                    "url": "https://preview.twilio.com/BulkExports/Exports/Calls/Days?PageSize=50&Page=0",
                    "page": 0,
                    "first_page_url": "https://preview.twilio.com/BulkExports/Exports/Calls/Days?PageSize=50&Page=0",
                    "previous_page_url": null,
                    "next_page_url": null
                }
            }
            '
        ));

        $actual = $this->twilio->preview->bulkExports->exports("resourceType")
                                                     ->days->read();

        $this->assertNotNull($actual);
    }
}