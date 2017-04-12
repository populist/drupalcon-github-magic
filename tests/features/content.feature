Feature: Content
  In order to test some basic Behat functionality
  As a website user
  I need to be able to see that the Drupal and Drush drivers are working

#  @api
#  Scenario: Create a node
#    Given I am logged in as a user with the "administrator" role
#    When I am viewing an "article" content with the title "My article"
#    Then I should see the heading "My article"

  @api
  Scenario: A user should see "El Museo de Arte" on the homepage
    Given I am on the homepage
    Then I should see the text "El Museo de Arte"
