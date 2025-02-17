<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Tests\Integration\Api\V2010\Account;

use Twilio\Exceptions\DeserializeException;
use Twilio\Exceptions\TwilioException;
use Twilio\Http\Response;
use Twilio\Tests\HolodeckTestCase;
use Twilio\Tests\Request;

class NotificationTest extends HolodeckTestCase {
    public function testFetchRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->api->v2010->accounts("ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                     ->notifications("NOaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->fetch();
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $this->assertRequest(new Request(
            'get',
            'https://api.twilio.com/2010-04-01/Accounts/ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Notifications/NOaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa.json'
        ));
    }

    public function testFetchResponse() {
        $this->holodeck->mock(new Response(
            200,
            '
            {
                "account_sid": "ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "api_version": "2008-08-01",
                "call_sid": "CAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "date_created": "Mon, 13 Sep 2010 20:02:01 +0000",
                "date_updated": "Mon, 13 Sep 2010 20:02:01 +0000",
                "error_code": "11200",
                "log": "0",
                "message_date": "Mon, 13 Sep 2010 20:02:00 +0000",
                "message_text": "EmailNotification=false&LogLevel=ERROR&sourceComponent=12000&Msg=&httpResponse=500&ErrorCode=11200&url=http%3A%2F%2Fvoiceforms4000.appspot.com%2Ftwiml",
                "more_info": "http://www.twilio.com/docs/errors/11200",
                "request_method": "get",
                "request_url": "https://voiceforms4000.appspot.com/twiml/9436/question/0",
                "request_variables": "AccountSid=ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa&CallStatus=in-progress&ToZip=94937&ToCity=INVERNESS&ToState=CA&Called=%2B14156694923&To=%2B14156694923&ToCountry=US&CalledZip=94937&Direction=inbound&ApiVersion=2010-04-01&Caller=%2B17378742833&CalledCity=INVERNESS&CalledCountry=US&CallSid=CAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa&CalledState=CA&From=%2B17378742833",
                "response_body": "blah blah",
                "response_headers": "Date=Mon%2C+13+Sep+2010+20%3A02%3A00+GMT&Content-Length=466&Connection=close&Content-Type=text%2Fhtml%3B+charset%3DUTF-8&Server=Google+Frontend",
                "sid": "NOaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                "uri": "/2010-04-01/Accounts/ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Notifications/NOaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa.json"
            }
            '
        ));

        $actual = $this->twilio->api->v2010->accounts("ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                           ->notifications("NOaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->fetch();

        $this->assertNotNull($actual);
    }

    public function testDeleteRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->api->v2010->accounts("ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                     ->notifications("NOaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->delete();
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $this->assertRequest(new Request(
            'delete',
            'https://api.twilio.com/2010-04-01/Accounts/ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Notifications/NOaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa.json'
        ));
    }

    public function testDeleteResponse() {
        $this->holodeck->mock(new Response(
            204,
            null
        ));

        $actual = $this->twilio->api->v2010->accounts("ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                           ->notifications("NOaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")->delete();

        $this->assertTrue($actual);
    }

    public function testReadRequest() {
        $this->holodeck->mock(new Response(500, ''));

        try {
            $this->twilio->api->v2010->accounts("ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                     ->notifications->read();
        } catch (DeserializeException $e) {}
          catch (TwilioException $e) {}

        $this->assertRequest(new Request(
            'get',
            'https://api.twilio.com/2010-04-01/Accounts/ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Notifications.json'
        ));
    }

    public function testReadFullResponse() {
        $this->holodeck->mock(new Response(
            200,
            '
            {
                "end": 0,
                "first_page_uri": "/2010-04-01/Accounts/ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Notifications.json?PageSize=1&Page=0",
                "last_page_uri": "/2010-04-01/Accounts/ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Notifications.json?PageSize=1&Page=100",
                "next_page_uri": null,
                "notifications": [
                    {
                        "account_sid": "ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                        "api_version": "2008-08-01",
                        "call_sid": "CAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                        "date_created": "Thu, 30 Apr 2025 16:47:33 +0000",
                        "date_updated": "Thu, 30 Apr 2025 16:47:35 +0000",
                        "error_code": "21609",
                        "log": "1",
                        "message_date": "Thu, 30 Apr 2025 16:47:32 +0000",
                        "message_text": "LogLevel=WARN&invalidStatusCallbackUrl=&Msg=Invalid+Url+for+callSid%3A+CAaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa+invalid+statusCallbackUrl%3A+&ErrorCode=21609",
                        "more_info": "https://www.twilio.com/docs/errors/21609",
                        "request_method": null,
                        "request_url": "",
                        "sid": "NOaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
                        "uri": "/2010-04-01/Accounts/ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Notifications/NOaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa"
                    }
                ],
                "num_pages": 101,
                "page": 0,
                "page_size": 1,
                "previous_page_uri": null,
                "start": 0,
                "total": 101,
                "uri": "/2010-04-01/Accounts/ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Notifications.json?PageSize=1&Page=0"
            }
            '
        ));

        $actual = $this->twilio->api->v2010->accounts("ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                           ->notifications->read();

        $this->assertGreaterThan(0, count($actual));
    }

    public function testReadEmptyResponse() {
        $this->holodeck->mock(new Response(
            200,
            '
            {
                "end": 0,
                "first_page_uri": "/2010-04-01/Accounts/ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Notifications.json?PageSize=1&Page=0",
                "last_page_uri": "/2010-04-01/Accounts/ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Notifications.json?PageSize=1&Page=100",
                "next_page_uri": null,
                "notifications": [],
                "num_pages": 101,
                "page": 0,
                "page_size": 1,
                "previous_page_uri": null,
                "start": 0,
                "total": 101,
                "uri": "/2010-04-01/Accounts/ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa/Notifications.json?PageSize=1&Page=0"
            }
            '
        ));

        $actual = $this->twilio->api->v2010->accounts("ACaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa")
                                           ->notifications->read();

        $this->assertNotNull($actual);
    }
}