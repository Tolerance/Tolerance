Feature:
  In order to use an operation runner with an existing service
  As a developer
  I want to be able to only add a tag in the Symfony DIC

  @aop @php_http
  Scenario: Uses a retry operation runner with successful run
    Given the 3rd party API will fail at the 1st run
    And the 3rd party API will succeed at the 2nd run
    When I call my local client service
    Then I should see the call as successful

  @php_http
  Scenario: Uses a retry operation runner without successful run
    Given the 3rd party API will fail until the 10th run
    When I call my local client service
    Then I should see the call as error
