#1_auth.feature
# run this like so : vendor/bin/behat --config tests/behat/behat.yml --tags auth
@auth

Feature: See if general api routes gives answers and are protected

  
Scenario: Valid Auth Cookie
  Given that I have a "valid" auth
  When I request "/backend/files" with "GET"
  Then the response status code should be 200
  When I request "/backend/current" with "GET"
  Then the response status code should be 200

  
Scenario: Invalid Auth Cookie
   Given that I have a "invalid" auth
   When I request "/backend/files" with "GET"
   Then the response status code should be 403
   When I request "/backend/current" with "GET"
   Then the response status code should be 403