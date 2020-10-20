#3_single_routes.feature
# run this like so : vendor/bin/behat --config tests/behat/behat.yml --tags routes
@routes

Feature: Testing single backend routes, with valid credentials


Background:
  Given that I have a "valid" auth

Scenario: get files
  When I request "/backend/files" with "GET"
  Then the response status code should be 200
  And I want to see the response


Scenario: Get Contents of an non-existing file
  When I request "/backend/files/non-existing-file.cheeson" with "GET"
  Then the response status code should be 404
  And I want to see the response


Scenario: get current
  When I request "/backend/current" with "GET"
  Then the response status code should be 200
  And response should contain "edit"

Scenario: Post Wrong Key
  When I post with payload "wrong_key" to "/backend/save"
  Then the response status code should be 400
  And I want to see the response

@fileproduced
Scenario: Post right Key
  When I post with payload "correct_key" to "/backend/save"
  Then the response status code should be 200
  And response should contain "will get saved to file"


Scenario: calling save with a non-existing fileID
  When I post with payload "wrong_fileId" to "/backend/setcurrent"
  # And I want to see the response
  Then the response status code should be 400
  And response should contain "file not existing"

@rewind
Scenario: overwriting current file
  When I duplicate currently used file into repo
  And use dupe to setAsCurrent
  Then the response status code should be 200
  And I want to see the response

Scenario: deleting a non-existing file
  When I post with payload "wrong_fileId" to "/backend/delete"
  Then the response status code should be 400
  And I want to see the response
  And response should contain "file not existing"