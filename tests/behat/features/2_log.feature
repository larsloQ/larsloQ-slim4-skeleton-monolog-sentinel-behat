#2_log.feature
# run this like so : vendor/bin/behat --config tests/behat/behat.yml --tags logs
@logs

Feature: See if logging is working


#Background:
#  Given the "usage" log of today exists
#  # use "on" or "off" for the next step
#  And "usage" log is turn "on"  
#  Then I remember number of logs

Scenario: Usage Log
  Given the "usage" log of today exists
  # use "on" or "off" for the next step
  And "usage" log is turn "on"  
  Then I remember number of logs
  When I request "/hi" with "GET"
  Then the response status code should be 200
  And the "usage" log should contain a new entry
  Then last "usage" log entry should contain "13" at "message"



Scenario: HTTP Error Log, app is answering with 403 for all non-existing routes
   Given the "http-error" log of today exists
  # use "on" or "off" for the next step
  And "http-error" log is turn "on"  
  Then I remember number of logs
  When I request "/non-existing-route" with "GET"
  Then the response status code should be 404
  And the "http-error" log should contain a new entry
  Then last "http-error" log entry should contain "404" at "message"
  

Scenario: Raise a PHP notice by visiting testroute "error"
  # make sure that php error exists
  When I request "/error" with "GET" 
  Given the "php-error" log of today exists
  # use "on" or "off" for the next step
  And "php-error" log is turn "on"  
  Then I remember number of logs
  When I request "/error" with "GET" 
  Then the response status code should be 400
  And the "php-error" log should contain a new entry
  Then last "php-error" log entry should contain "Undefined index" at "message"