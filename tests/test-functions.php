<?php

class Katawp_Functions_Test extends WP_UnitTestCase {

    public function test_get_coptic_date_sanitization() {
        global $wpdb;

        // Malicious input to test sanitization
        $malicious_date = "' OR 1=1 --";

        // Expected query after sanitization
        $expected_query = "SELECT coptic_day, coptic_month FROM " . KATAWP_DB_PREFIX . "daily_readings
            WHERE gregorian_date = '' OR 1=1 --'";

        // Mock the wpdb->prepare method to ensure it's called correctly
        $wpdb = $this->getMockBuilder('wpdb')
            ->setMethods(['prepare'])
            ->getMock();

        $wpdb->expects($this->once())
            ->method('prepare')
            ->with(
                $this->stringContains("SELECT coptic_day, coptic_month FROM " . KATAWP_DB_PREFIX . "daily_readings WHERE gregorian_date = %s"),
                $malicious_date
            )
            ->willReturn($expected_query);

        // Call the function with malicious input
        katawp_get_coptic_date($malicious_date);
    }
}
