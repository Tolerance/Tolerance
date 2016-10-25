Feature:
  In order to have a better understanding of my application's requests
  As a system engineer
  I want to be able to have traces for the incoming traces

  Scenario: A successful request stores a trace
    When I send a request
    And the kernel terminates
    Then at least 2 span should have been stored

  Scenario: A request that throw an exception will also have a trace
    When I send a request to "/will-throw-an-exception"
    And the kernel terminates
    Then at least 2 span should have been stored
