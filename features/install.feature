Feature: Install and activate plugin
  In order to use the plugin
  As an administrator
  I need to be able to install and activate the plugin
  
  Scenario: See plugin in plugin overview
    Given a fresh WordPress is installed
    And the plugin "language-redirect" is installed (from source)
    And I am logged as an administrator
    When I go to "/wp-admin/plugins.php"
    Then I should see "Language Redirect"

  Scenario: Activate plugin
    Given a fresh WordPress is installed
    And the plugin "language-redirect" is installed (from source)
    And I am logged as an administrator
    When I go to "/wp-admin/plugins.php"
    And I activate the plugin "Language Redirect"
    Then I should see the message "Plugin activated"
    And the plugin "language-redirect" is activated
