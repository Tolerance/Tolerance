Feature:
  In order to gain knowledge about the application behaviours
  As a developer
  I want to be able to track metrics with Tolerance

  Scenario: It sends the request timing
    When I send a request
    And the kernel terminates
    Then the metric "my.namespace" should have been published
