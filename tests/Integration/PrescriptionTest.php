<?php

namespace Tests\Integration;

use Tests\TestCase;

class PrescriptionTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testPostReturns200()
    {
        $this->post('/prescriptions');

        $this->assertEquals(
            '',
            $this->response->getContent()
        );
        $this->assertEquals(200, $this->response->getStatusCode());
    }
}
