@mod @mod_datalynx @mod_peter @wip2 @mink:selenium2
Feature:When creating a view and adding a tag to the template with the viewname
  The viewname was displayed and the tag was broken

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username | firstname | lastname | email                   |
      | teacher1 | Teacher   | 1        | teacher1@mailinator.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | course | idnumber | name                   | approval |
      | datalynx | C1     | 12345    | Datalynx Test Instance | 1        |
    And "Datalynx Test Instance" has following fields:
      | type | name      | visible | edits | param1 | param2 | param3 | param4 |
      | text | testfield | 2       | -1    |        |        |        |        |
    And "Datalynx Test Instance" has following views:
      | type | name     | status              | redirect | filter | section                                                  |
      | grid | testview | default, edit, more | testview |        | #{{viewsesslink:testview;Neuen Eintrag anlegen;new=1;}}# |


  Scenario: Login and look at a view with a tag
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Datalynx Test Instance"
    And I follow "Manage"
    And I should see "testview"
    Then I click "Edit" button of "testview" item
    And I follow "View template"
    And I press "Show more buttons"
    And I press "HTML"
    And I press "HTML"
    And I press "Save changes"
    Then I follow "Browse"
    And I should see "Neuen Eintrag anlegen"
    But I should not see "viewsesslink"
    
    