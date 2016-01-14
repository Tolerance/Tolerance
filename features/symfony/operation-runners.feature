Feature:
  In order to use easily the operation runners
  As a developer
  I want a lot a fancy integration so I can just use the bundle and focus on my code :D

  Scenario: Buffered operations are not ran if we only send a request
    Given there is an operation in a buffered runner
    When I send a request
    Then the buffered operation should not have been run

  Scenario: Operation in buffered runners are automatically ran when the kernel terminates
    Given there is an operation in a buffered runner
    When the kernel terminates
    Then the buffered operation should have been run
