<?php

namespace App\Library\Redx;

interface RedxAPIInterface
{

    public function trackParcel($tracking_id);

    public function getParcelDetails($tracking_id);

    public function createParcel($data);

    public function getAreas();

    public function createPickUpStore($data);
}
