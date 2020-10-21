#3_single_routes.feature
# run this like so : vendor/bin/behat --config tests/behat/behat.yml --tags fileList
@fileList

Feature: Testing single backend routes, with valid credentials


Background:
  Given that I have a "valid" auth

Scenario: get files
  When I request "/backend/files" with "GET"
  Then the response status code should be 200
  And I want to see the response
  And response should contain "file not existing"