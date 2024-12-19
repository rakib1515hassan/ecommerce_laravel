<?php

namespace App\Library\Redx;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class RedxAPI implements RedxAPIInterface
{
    protected $api_key;
    protected $api_url;

    public function __construct()
    {
        $this->api_key = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI4MDYyMTUiLCJpYXQiOjE2NjU0NzYyNTAsImlzcyI6Ijk5SnFGeGxROHU5UXVCeEpFZWdGNllYWUMzSkpFSk1OIiwic2hvcF9pZCI6ODA2MjE1LCJ1c2VyX2lkIjoxOTA0NDQ4fQ.J25Uuy3G_SJQGlzGG1xL6Gw5mv61wC2FMCIHiZtZfS4";
        $this->api_url = "https://openapi.redx.com.bd/v1.0.0-beta";
    }

    private function request($method, $url, $data = [])
    {
        try {
            $client = new Client();
            $headers = [
                'API-ACCESS-TOKEN' => 'Bearer ' . $this->api_key,
                'Content-Type' => 'application/json',
            ];
            // join the base url with the given url
            $url = $this->api_url . $url;

            $request = new Request($method, $url, $headers, json_encode($data));
            $res = $client->sendAsync($request)->wait();
            return json_decode($res->getBody(), true);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function trackParcel($tracking_id)
    {
        return $this->request('GET', "/parcel/track/$tracking_id");
    }

    public function getParcelDetails($tracking_id)
    {
        return $this->request('GET', "/parcel/info/$tracking_id");
    }


    public function createParcel($data)
    {
        return $this->request('POST', "/parcel", $data);
    }


    public function getAreas()
    {
        return $this->request('GET', "/areas");
    }


    public function createPickUpStore($data)
    {
        return $this->request('POST', "/pickup/store", $data);
    }

}
