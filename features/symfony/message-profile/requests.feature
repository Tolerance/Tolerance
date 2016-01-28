Feature:
  As a system engineer
  In order to track what is happening in my application
  I want the system to store profiles

  Scenario: A successful request stores a trace
    When I send a request
    And the kernel terminates
    Then a request profile should have been stored

  Scenario: A request that throw an exception will also have a trace
    When I send a request to "/will-throw-an-exception"
    And the kernel terminates
    Then a request profile should have been stored
